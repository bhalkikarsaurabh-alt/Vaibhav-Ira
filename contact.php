<?php
/**
 * IRA Scientific Solution — Contact Form Handler
 * Place this file on your server alongside index.html
 */

// Allow requests only from your own domain (CORS protection)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Change * to your domain after deployment e.g. https://irascientific.com
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// ── CONFIG ────────────────────────────────────────────────────────────────────
$to_email   = 'irascientific@gmail.com';   // Destination inbox
$from_name  = 'IRA Scientific Website';
$from_email = 'noreply@irascientific.com'; // Change to your domain email after hosting
$subject_prefix = '[IRA Website Enquiry]';
// ─────────────────────────────────────────────────────────────────────────────

// Sanitize & validate inputs
function clean($val) {
    return htmlspecialchars(strip_tags(trim($val)), ENT_QUOTES, 'UTF-8');
}

$first_name = clean($_POST['first_name'] ?? '');
$last_name  = clean($_POST['last_name']  ?? '');
$email      = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone      = clean($_POST['phone']      ?? '');
$service    = clean($_POST['service']    ?? '');
$message    = clean($_POST['message']    ?? '');

// Required field check
$errors = [];
if (empty($first_name)) $errors[] = 'First name is required.';
if (empty($last_name))  $errors[] = 'Last name is required.';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email address is required.';
if (empty($message))    $errors[] = 'Message cannot be empty.';

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// Build email body
$full_name = "$first_name $last_name";
$subject   = "$subject_prefix $full_name — $service";

$body = "
New enquiry received from the IRA Scientific website.

────────────────────────────────────
  CONTACT DETAILS
────────────────────────────────────
Name    : $full_name
Email   : $email
Phone   : $phone
Service : $service

────────────────────────────────────
  MESSAGE
────────────────────────────────────
$message

────────────────────────────────────
Sent from: IRA Scientific Solution website
";

// Email headers
$headers  = "From: $from_name <$from_email>\r\n";
$headers .= "Reply-To: $full_name <$email>\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Send
$sent = mail($to_email, $subject, $body, $headers);

if ($sent) {
    // Auto-reply to the enquirer
    $reply_subject = 'Thank you for contacting IRA Scientific Solution';
    $reply_body = "
Dear $first_name,

Thank you for reaching out to IRA Scientific Solution!

We have received your enquiry regarding \"$service\" and our team will get back to you within 24 business hours.

If you need immediate assistance, please call us at:
  +91-8421939330

Best regards,
IRA Scientific Solution Team
Pune, Maharashtra

────────────────────────────────────────
IRA Scientific Solution
irascientific@gmail.com | +91-8421939330
";
    $reply_headers  = "From: $from_name <$from_email>\r\n";
    $reply_headers .= "Reply-To: $to_email\r\n";
    $reply_headers .= "MIME-Version: 1.0\r\n";
    $reply_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    mail($email, $reply_subject, $reply_body, $reply_headers);

    echo json_encode(['success' => true, 'message' => 'Your message has been sent. We will get back to you within 24 hours.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Mail server error. Please email us directly at irascientific@gmail.com']);
}
?>
