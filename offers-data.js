/*
 * CENTRALIZED OFFERS CONFIG
 * Edit this file to add/update/remove offers across all pages.
 * finalPrice: set explicitly or leave null to auto-calculate from discountPct.
 */
const OFFERS_DATA = [
  {
    product: 'BOMB Calorimeter',
    originalPrice: 120000,
    discountPct: 15,
    finalPrice: 102000,
    warrantyYrs: null,
    icon: 'fas fa-fire',
    badge: 'Hot Deal',
    cta: 'Enquire Now',
    ctaHref: 'index.html#contact'
  },
  {
    product: '1 Ton Laboratory Chiller',
    originalPrice: 150000,
    discountPct: 20,
    finalPrice: null,
    warrantyYrs: 2,
    icon: 'fas fa-snowflake',
    badge: 'Best Value',
    cta: 'Get Quote',
    ctaHref: 'index.html#contact'
  }
];

function buildOfferCard(o) {
  const fmt = n => '₹' + n.toLocaleString('en-IN');
  const final = (o.finalPrice != null)
    ? o.finalPrice
    : Math.round(o.originalPrice * (1 - o.discountPct / 100));
  return `<div class="offer-card">
    <div class="offer-disc-badge"><i class="${o.icon}"></i> ${o.discountPct}% OFF</div>
    <h3>${o.product}</h3>
    <div class="offer-original">${fmt(o.originalPrice)}</div>
    <div class="offer-final">${fmt(final)}</div>
    ${o.warrantyYrs ? `<div class="offer-warranty"><i class="fas fa-shield-alt"></i> ${o.warrantyYrs} Year Warranty Included</div>` : ''}
    <a href="${o.ctaHref}" class="btn btn-primary"><i class="fas fa-paper-plane"></i> ${o.cta}</a>
  </div>`;
}
