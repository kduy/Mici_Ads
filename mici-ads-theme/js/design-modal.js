/**
 * Design detail modal — image gallery, metadata, colors, and similar designs.
 * Supports up to 30 images per card with prev/next navigation.
 * Requires: portfolioItems, capitalize(), INDUSTRY_LABELS from main.js.
 */

/** Format lookup by category */
const FORMAT_MAP = {
  "logo": "Vuông (1000 × 1000 px)", "menu": "A4 (210 × 297 mm)",
  "flyer": "A5 (148 × 210 mm)", "loyalty-card": "Thẻ (85 × 55 mm)",
  "voucher": "DL (99 × 210 mm)", "website": "Desktop (1920 × 1080 px)"
};

/** View mode labels for text-based preview thumbnail strip */
const VIEW_MODES = [
  { key: "flat", label: "Phẳng" }, { key: "perspective", label: "3D" },
  { key: "dark", label: "Tối" }, { key: "detail", label: "Chi tiết" }
];

/** Generate keywords from item properties */
function getKeywords(item) {
  const tags = [item.style, item.category.replace(/-/g, " "), item.industry, "thiết kế"];
  const map = { nail: ["làm đẹp", "salon"], beauty: ["spa", "thẩm mỹ"], cafe: ["cà phê", "đồ uống"] };
  tags.push(...(map[item.industry] || ["ẩm thực", "nhà hàng"]));
  const extra = {
    "logo": ["nhận diện", "thương hiệu"], "menu": ["bảng giá", "thực đơn"],
    "flyer": ["khuyến mãi", "in ấn"], "loyalty-card": ["tích điểm", "khách hàng thân thiết"],
    "voucher": ["quà tặng", "phiếu giảm giá"], "website": ["trang web", "landing page"]
  };
  tags.push(...(extra[item.category] || []));
  return [...new Set(tags)];
}

/** Generate description from item properties */
function getDescription(item) {
  const indMap = { nail: "tiệm nail & làm đẹp", beauty: "spa & thẩm mỹ", restaurant: "nhà hàng & ẩm thực", cafe: "quán cafe & đồ uống", others: "doanh nghiệp" };
  return `Thiết kế ${capitalize(item.category).toLowerCase()} phong cách ${item.style} cho ${item.name}. Phù hợp cho ngành ${indMap[item.industry] || "doanh nghiệp"} với bảng màu chọn lọc và typography tinh tế.`;
}

/** Find similar designs (same industry or category, excluding current) */
function getSimilarDesigns(item, max) {
  return portfolioItems
    .filter(o => o.id !== item.id && (o.industry === item.industry || o.category === item.category))
    .slice(0, max || 4);
}

/** Render text-based card design preview HTML */
function renderPreview(item, sizeClass) {
  return `<div class="card-design ${sizeClass || ''}">
    <div class="card-design__logo">${item.logo}</div>
    <div class="card-design__divider"></div>
    <div class="card-design__sub">${item.sub}</div>
    <div class="card-design__detail">${item.detail}</div>
  </div>`;
}

/** Build image gallery HTML when item.images has entries (up to 30) */
function buildGalleryHTML(item) {
  const imgs = item.images;
  const total = imgs.length;
  const thumbs = imgs.map((img, i) =>
    `<img src="${img}" class="modal__gallery-thumb${i === 0 ? ' modal__gallery-thumb--active' : ''}" data-index="${i}" loading="lazy" alt="">`
  ).join("");
  return `
    <div class="modal__gallery" data-total="${total}">
      <div class="modal__gallery-main">
        <img src="${imgs[0]}" class="modal__gallery-img" alt="${item.name}">
        ${total > 1 ? `<button class="modal__gallery-prev" aria-label="Previous">&#8249;</button>
        <button class="modal__gallery-next" aria-label="Next">&#8250;</button>
        <span class="modal__gallery-counter">1 / ${total}</span>` : ''}
      </div>
      ${total > 1 ? `<div class="modal__gallery-thumbs">${thumbs}</div>` : ''}
    </div>`;
}

/** Build text-based preview with view mode thumbnails (fallback when no images) */
function buildPreviewHTML(item) {
  const thumbs = VIEW_MODES.map((m, i) =>
    `<button class="modal__thumb${i === 0 ? ' modal__thumb--active' : ''}" data-view="${m.key}">
      <div class="modal__thumb-inner ${item.theme} modal__thumb--${m.key}">${renderPreview(item, "card-design--xs")}</div>
    </button>`
  ).join("");
  return `
    <div class="modal__preview modal__preview--flat">
      <div class="modal__preview-inner ${item.theme}">${renderPreview(item, "card-design--lg")}</div>
    </div>
    <div class="modal__thumbs">${thumbs}</div>`;
}

/** Initialize gallery prev/next/thumb click navigation */
function initGalleryNav(container) {
  const gallery = container.querySelector('.modal__gallery');
  if (!gallery) return;
  const mainImg = gallery.querySelector('.modal__gallery-img');
  const counter = gallery.querySelector('.modal__gallery-counter');
  const thumbs = Array.from(gallery.querySelectorAll('.modal__gallery-thumb'));
  const total = parseInt(gallery.dataset.total);
  let current = 0;

  function goTo(idx) {
    current = ((idx % total) + total) % total;
    mainImg.src = thumbs[current].src;
    if (counter) counter.textContent = `${current + 1} / ${total}`;
    thumbs.forEach((t, i) => t.classList.toggle('modal__gallery-thumb--active', i === current));
    thumbs[current].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
  }

  const prev = gallery.querySelector('.modal__gallery-prev');
  const next = gallery.querySelector('.modal__gallery-next');
  if (prev) prev.addEventListener('click', () => goTo(current - 1));
  if (next) next.addEventListener('click', () => goTo(current + 1));
  thumbs.forEach((t, i) => t.addEventListener('click', () => goTo(i)));
}

/** Open modal for a specific design item */
function openDesignModal(itemId) {
  const item = portfolioItems.find(i => i.id === itemId);
  if (!item) return;

  const modal = document.getElementById("designModal");
  const content = document.getElementById("modalContent");
  const hasGallery = item.images && item.images.length > 0;
  const format = FORMAT_MAP[item.category] || "Custom";
  const keywords = getKeywords(item);
  const similar = getSimilarDesigns(item);

  const colorsHTML = item.colors.map(c =>
    `<span class="modal__swatch" style="background:${c}" title="${c}"><span class="modal__swatch-hex">${c}</span></span>`
  ).join("");
  const keywordsHTML = keywords.map(k => `<span class="modal__keyword">${capitalize(k)}</span>`).join("");

  // Similar designs: use photo when available, else text-based preview
  const similarHTML = similar.map(s => {
    const inner = s.image
      ? `<img src="${s.image}" alt="${s.name}" class="modal__sim-img">`
      : renderPreview(s, "card-design--sm");
    return `<div class="modal__sim-card" data-id="${s.id}">
      <div class="modal__sim-preview ${s.image ? '' : s.theme}">${inner}</div>
      <div class="modal__sim-name">${s.name}</div>
    </div>`;
  }).join("");

  content.innerHTML = `
    <div class="modal__body">
      <div class="modal__left">${hasGallery ? buildGalleryHTML(item) : buildPreviewHTML(item)}</div>
      <div class="modal__right">
        <h2 class="modal__title">${item.name}</h2>
        <p class="modal__subtitle">${item.sub} — ${item.detail}</p>
        <div class="modal__details">
          <div class="modal__row"><span class="modal__label">Ngành</span><span class="modal__value">${INDUSTRY_LABELS[item.industry] || capitalize(item.industry)}</span></div>
          <div class="modal__row"><span class="modal__label">Danh mục</span><span class="modal__value">${capitalize(item.category)}</span></div>
          <div class="modal__row"><span class="modal__label">Phong cách</span><span class="modal__value">${capitalize(item.style)}</span></div>
          <div class="modal__row"><span class="modal__label">Kích thước</span><span class="modal__value">${format}</span></div>
          <div class="modal__row"><span class="modal__label">Bảng màu</span><div class="modal__colors">${colorsHTML}</div></div>
          <div class="modal__row modal__row--stack"><span class="modal__label">Từ khóa</span><div class="modal__keywords">${keywordsHTML}</div></div>
          <div class="modal__row modal__row--stack"><span class="modal__label">Mô tả</span><p class="modal__desc">${getDescription(item)}</p></div>
        </div>
      </div>
    </div>
    ${similar.length > 0 ? `<div class="modal__similar"><h3 class="modal__similar-heading">Mẫu tương tự</h3><div class="modal__similar-grid">${similarHTML}</div></div>` : ""}`;

  modal.classList.add("is-open");
  document.body.style.overflow = "hidden";

  // Initialize nav based on content type
  if (hasGallery) {
    initGalleryNav(content);
  } else {
    content.querySelectorAll(".modal__thumb").forEach(thumb => {
      thumb.addEventListener("click", () => {
        const preview = content.querySelector(".modal__preview");
        VIEW_MODES.forEach(m => preview.classList.remove("modal__preview--" + m.key));
        preview.classList.add("modal__preview--" + thumb.dataset.view);
        content.querySelectorAll(".modal__thumb").forEach(t => t.classList.remove("modal__thumb--active"));
        thumb.classList.add("modal__thumb--active");
      });
    });
  }

  // Similar card click → reopen modal with that design
  content.querySelectorAll(".modal__sim-card").forEach(card => {
    card.addEventListener("click", () => openDesignModal(parseInt(card.dataset.id)));
  });
}

/** Close modal */
function closeDesignModal() {
  document.getElementById("designModal").classList.remove("is-open");
  document.body.style.overflow = "";
}

/** Initialize modal event listeners */
function initDesignModal() {
  const modal = document.getElementById("designModal");
  document.getElementById("modalClose").addEventListener("click", closeDesignModal);
  modal.addEventListener("click", (e) => { if (e.target === modal) closeDesignModal(); });
  document.addEventListener("keydown", (e) => { if (e.key === "Escape") closeDesignModal(); });

  // Delegate "View Design" clicks from gallery cards
  document.getElementById("galleryGrid").addEventListener("click", (e) => {
    const btn = e.target.closest(".gallery-card__overlay-btn");
    if (!btn) return;
    const card = btn.closest(".gallery-card");
    if (card) openDesignModal(parseInt(card.dataset.id));
  });
}
