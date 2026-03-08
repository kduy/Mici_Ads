/**
 * Portfolio gallery data and filtering logic for Mici Ads design portfolio.
 * Canva-style filter pills with dropdown panels for category/style.
 */

const portfolioItems = [
  // --- Nail Salon Designs --- (colors: [60% primary, 30% secondary, 7% accent, 3% pop])
  { id: 1, name: "Olivia Nail Art Salon", industry: "nail", category: "logo", style: "minimal", theme: "card-theme-nail-2", logo: "OLIVIA", sub: "Nail Art Salon", detail: "Logo Design", colors: ["#fff5f5", "#fce4ec", "#f06292", "#ad1457"] },
  { id: 2, name: "Licera & Co Studio", industry: "nail", category: "logo", style: "elegant", theme: "card-theme-nail-1", logo: "Licera & Co", sub: "Nail Art Studio", detail: "Brand Identity", colors: ["#fce4ec", "#f4c2c2", "#e91e63", "#880e4f"] },
  { id: 3, name: "Nails Salon Price List", industry: "nail", category: "menu", style: "feminine", theme: "card-theme-nail-4", logo: "Nails Salon", sub: "Price List", detail: "Manicure & Pedicure", colors: ["#fdf2f8", "#fbcfe8", "#ec4899", "#831843"] },
  { id: 4, name: "Beauty Studio Services", industry: "nail", category: "flyer", style: "professional", theme: "card-theme-nail-3", logo: "Beauty Studio", sub: "Nail Services", detail: "50% Off Promotion", colors: ["#faf5f0", "#f5ebe0", "#d4a373", "#6b4226"] },
  { id: 5, name: "Nail Lounge Loyalty", industry: "nail", category: "loyalty-card", style: "luxury", theme: "card-theme-nail-5", logo: "Nail Lounge", sub: "Loyalty Card", detail: "Collect 10 Get 1 Free", colors: ["#f9f5f0", "#ede0d4", "#bc8a5f", "#5c4033"] },
  { id: 6, name: "Anna Wilson Studio", industry: "nail", category: "flyer", style: "clean", theme: "card-theme-nail-2", logo: "Anna Wilson", sub: "Nail Studio", detail: "Business Card", colors: ["#fff5f5", "#fce4ec", "#e57373", "#c62828"] },
  { id: 7, name: "Larana Pure Polish", industry: "nail", category: "flyer", style: "modern", theme: "card-theme-nail-3", logo: "LARANA", sub: "Pure Polish", detail: "Grand Opening Flyer", colors: ["#faf5f0", "#f5ebe0", "#c9a87c", "#8d6e4c"] },
  { id: 8, name: "Mani & Pedi Gift Card", industry: "nail", category: "voucher", style: "beautiful", theme: "card-theme-nail-1", logo: "Gift Card", sub: "Mani & Pedi", detail: "$50 Value", colors: ["#fce4ec", "#f8bbd0", "#e91e63", "#ad1457"] },
  { id: 9, name: "Nail Art Website", industry: "nail", category: "website", style: "aesthetic", theme: "card-theme-nail-5", logo: "NailArt.co", sub: "Website Design", detail: "Landing Page", colors: ["#f9f5f0", "#ede0d4", "#c4a882", "#7c5e42"] },
  // --- Restaurant Designs ---
  { id: 10, name: "Rosa Maria Restaurant", industry: "restaurant", category: "logo", style: "classic", theme: "card-theme-rest-1", logo: "Rosa Maria", sub: "Filipino Restaurant", detail: "Since 1990", colors: ["#fefae0", "#dda15e", "#bc6c25", "#283618"] },
  { id: 11, name: "Main Courses Menu", industry: "restaurant", category: "menu", style: "minimalist", theme: "card-theme-rest-3", logo: "Main Courses", sub: "Restaurant Menu", detail: "Seasonal Edition", colors: ["#fefae0", "#e9edc9", "#588157", "#344e41"] },
  { id: 12, name: "Rimberio Restaurant", industry: "restaurant", category: "logo", style: "bold", theme: "card-theme-rest-2", logo: "Rimberio", sub: "Restaurant", detail: "Brand Identity", colors: ["#fefae0", "#dda15e", "#5b0e0e", "#2c1810"] },
  { id: 13, name: "Kitchen Delicious Logo", industry: "restaurant", category: "logo", style: "simple", theme: "card-theme-rest-4", logo: "KITCHEN", sub: "Delicious", detail: "Minimal Logo", colors: ["#f5f0e8", "#d5c4a1", "#a68a64", "#4a3728"] },
  { id: 14, name: "Food Menu Borcelle", industry: "restaurant", category: "menu", style: "elegant", theme: "card-theme-rest-5", logo: "FOOD MENU", sub: "Borcelle Restaurant", detail: "Full Menu Layout", colors: ["#1a1a2e", "#16213e", "#0f3460", "#e2e8f0"] },
  { id: 15, name: "Tasty Food Flyer", industry: "restaurant", category: "flyer", style: "vibrant", theme: "card-theme-rest-1", logo: "Tasty Food", sub: "Western Cuisine", detail: "Promotional Flyer", colors: ["#fefae0", "#faedcd", "#d4a373", "#bc6c25"] },
  { id: 16, name: "Restaurant Loyalty Card", industry: "restaurant", category: "loyalty-card", style: "gold", theme: "card-theme-rest-4", logo: "Dine & Earn", sub: "Loyalty Program", detail: "Stamp Card", colors: ["#f5f0e8", "#e6d5b8", "#b8956a", "#3a2a1a"] },
  { id: 17, name: "Chef Special Voucher", industry: "restaurant", category: "voucher", style: "creative", theme: "card-theme-rest-2", logo: "Gift Voucher", sub: "Chef's Special", detail: "Dining Experience", colors: ["#fefae0", "#c9a96e", "#7a3b2e", "#2c1810"] },
  { id: 18, name: "Restaurant Website", industry: "restaurant", category: "website", style: "clean", theme: "card-theme-rest-3", logo: "ThynkKitchen", sub: "Website Design", detail: "Responsive Site", colors: ["#fefae0", "#e9edc9", "#588157", "#2d4032"] },
  // --- Beauty Designs ---
  { id: 19, name: "Glow Spa Studio", industry: "beauty", category: "logo", style: "elegant", theme: "card-theme-beauty-1", logo: "GLOW", sub: "Spa & Wellness", detail: "Brand Identity", colors: ["#ede9fe", "#ddd6fe", "#8b5cf6", "#5b21b6"] },
  { id: 20, name: "Hair Salon Promo", industry: "beauty", category: "flyer", style: "modern", theme: "card-theme-beauty-2", logo: "Luxe Hair", sub: "Hair Studio", detail: "Summer Special", colors: ["#faf5ff", "#e9d5ff", "#a855f7", "#7c3aed"] },
  { id: 21, name: "Skincare Treatment Menu", industry: "beauty", category: "menu", style: "minimalist", theme: "card-theme-beauty-3", logo: "Derma Skin", sub: "Treatment Menu", detail: "Facial & Body Care", colors: ["#fdf4ff", "#f5d0fe", "#d946ef", "#a21caf"] },
  { id: 22, name: "Beauty Rewards Card", industry: "beauty", category: "loyalty-card", style: "luxury", theme: "card-theme-beauty-1", logo: "Belle Card", sub: "Loyalty Program", detail: "Earn Points", colors: ["#ede9fe", "#c4b5fd", "#7c3aed", "#4c1d95"] },
  { id: 23, name: "Spa Gift Voucher", industry: "beauty", category: "voucher", style: "beautiful", theme: "card-theme-beauty-2", logo: "Gift Card", sub: "Spa Experience", detail: "$100 Value", colors: ["#faf5ff", "#e9d5ff", "#c084fc", "#7e22ce"] },
  // --- Cafe Designs ---
  { id: 24, name: "Brew & Bean Cafe", industry: "cafe", category: "logo", style: "minimal", theme: "card-theme-cafe-1", logo: "BREW", sub: "Coffee House", detail: "Est. 2020", colors: ["#fef9ee", "#f5e6c8", "#b8860b", "#5c3a1e"] },
  { id: 25, name: "Coffee Menu Board", industry: "cafe", category: "menu", style: "clean", theme: "card-theme-cafe-3", logo: "Daily Brew", sub: "Coffee Menu", detail: "Hot & Cold Drinks", colors: ["#3b2314", "#5c3a1e", "#d4a373", "#f5e6c8"] },
  { id: 26, name: "Bakery Grand Opening", industry: "cafe", category: "flyer", style: "creative", theme: "card-theme-cafe-2", logo: "Sweet Crust", sub: "Artisan Bakery", detail: "Opening Day Deals", colors: ["#f0fdf4", "#dcfce7", "#4ade80", "#166534"] },
  { id: 27, name: "Cafe Stamp Card", industry: "cafe", category: "loyalty-card", style: "simple", theme: "card-theme-cafe-1", logo: "10th Free", sub: "Coffee Loyalty", detail: "Buy 9 Get 1 Free", colors: ["#fef9ee", "#e8d5b0", "#92702a", "#3b2314"] },
  // --- Others ---
  { id: 28, name: "Focus Photography", industry: "others", category: "logo", style: "professional", theme: "card-theme-other-1", logo: "FOCUS", sub: "Photography Studio", detail: "Brand Identity", colors: ["#f0f9ff", "#dbeafe", "#3b82f6", "#1e40af"] },
  { id: 29, name: "FitZone Gym Flyer", industry: "others", category: "flyer", style: "bold", theme: "card-theme-other-3", logo: "FIT ZONE", sub: "Fitness Center", detail: "Join Today 50% Off", colors: ["#1e293b", "#334155", "#f97316", "#ea580c"] },
  { id: 30, name: "Pet Grooming Voucher", industry: "others", category: "voucher", style: "beautiful", theme: "card-theme-other-2", logo: "Pawfect", sub: "Pet Grooming", detail: "$30 Gift Card", colors: ["#f8fafc", "#e2e8f0", "#60a5fa", "#2563eb"] },
  { id: 31, name: "Yoga Studio Website", industry: "others", category: "website", style: "aesthetic", theme: "card-theme-other-1", logo: "ZenFlow", sub: "Yoga & Meditation", detail: "Landing Page", colors: ["#f0f9ff", "#bae6fd", "#0ea5e9", "#0369a1"] },
  // --- Additional Nail Salon Designs (Canva-inspired) ---
  { id: 32, name: "Luxe Nails Price Menu", industry: "nail", category: "menu", style: "elegant", theme: "card-theme-nail-5", logo: "LUXE NAILS", sub: "Price Menu", detail: "Full Service List", colors: ["#f9f5f0", "#ede0d4", "#c4a882", "#5c4033"] },
  { id: 33, name: "Pink Blossom Nail Promo", industry: "nail", category: "flyer", style: "feminine", theme: "card-theme-nail-4", logo: "Pink Blossom", sub: "Nail Studio", detail: "50% Off Opening", colors: ["#fdf2f8", "#fce7f3", "#f472b6", "#be185d"] },
  { id: 34, name: "Diamond Nails VIP Card", industry: "nail", category: "loyalty-card", style: "luxury", theme: "card-theme-nail-1", logo: "DIAMOND", sub: "VIP Loyalty", detail: "Premium Member", colors: ["#fce4ec", "#f8bbd0", "#c2185b", "#880e4f"] },
  { id: 35, name: "Gel Polish Studio Logo", industry: "nail", category: "logo", style: "modern", theme: "card-theme-nail-3", logo: "GEL STUDIO", sub: "Polish & Care", detail: "Brand Mark", colors: ["#faf5f0", "#f5ebe0", "#d4a373", "#8d6e4c"] },
  // --- Additional Restaurant Designs (Canva-inspired) ---
  { id: 36, name: "Catering Delicious Menu", industry: "restaurant", category: "menu", style: "classic", theme: "card-theme-rest-2", logo: "CATERING", sub: "Delicious Food", detail: "Full Course Menu", colors: ["#fefae0", "#dda15e", "#7a3b2e", "#2c1810"] },
  { id: 37, name: "Seafood Feast Flyer", industry: "restaurant", category: "flyer", style: "bold", theme: "card-theme-rest-1", logo: "SEAFOOD", sub: "Fresh Catch", detail: "Weekend Special", colors: ["#fefae0", "#faedcd", "#bc6c25", "#283618"] },
  { id: 38, name: "Italian Cuisine Menu", industry: "restaurant", category: "menu", style: "minimalist", theme: "card-theme-rest-5", logo: "CUCINA", sub: "Italian Fine Dining", detail: "Seasonal Menu", colors: ["#1a1a2e", "#16213e", "#0f3460", "#e2e8f0"] },
  { id: 39, name: "BBQ Grill House Logo", industry: "restaurant", category: "logo", style: "bold", theme: "card-theme-rest-4", logo: "BBQ GRILL", sub: "Smokehouse", detail: "Est. 2018", colors: ["#f5f0e8", "#d5c4a1", "#8b5e3c", "#3a2a1a"] },
  { id: 40, name: "Fast Food Restaurant", industry: "restaurant", category: "flyer", style: "vibrant", theme: "card-theme-rest-1", logo: "FAST FOOD", sub: "Quick Bites", detail: "Combo Deals", colors: ["#fefae0", "#faedcd", "#e76f51", "#bc6c25"] },
  // --- Additional Beauty Designs (Canva-inspired) ---
  { id: 41, name: "Eyelash Studio Promo", industry: "beauty", category: "flyer", style: "elegant", theme: "card-theme-beauty-3", logo: "LASH BAR", sub: "Eyelash Studio", detail: "Extension Special", colors: ["#fdf4ff", "#f5d0fe", "#c026d3", "#86198f"] },
  { id: 42, name: "Keithston Beauty Logo", industry: "beauty", category: "logo", style: "professional", theme: "card-theme-beauty-1", logo: "KEITHSTON", sub: "Beauty Salon", detail: "Brand Identity", colors: ["#ede9fe", "#ddd6fe", "#7c3aed", "#4c1d95"] },
  { id: 43, name: "Beauty Room Services", industry: "beauty", category: "menu", style: "feminine", theme: "card-theme-beauty-2", logo: "Beauty Room", sub: "Hair Makeup Nails", detail: "Service Menu", colors: ["#faf5ff", "#e9d5ff", "#a855f7", "#7e22ce"] },
  { id: 44, name: "Glow Spa Website", industry: "beauty", category: "website", style: "aesthetic", theme: "card-theme-beauty-1", logo: "GLOW SPA", sub: "Wellness Center", detail: "Landing Page", colors: ["#ede9fe", "#c4b5fd", "#8b5cf6", "#5b21b6"] },
  { id: 45, name: "Hair Salon Gift Card", industry: "beauty", category: "voucher", style: "luxury", theme: "card-theme-beauty-3", logo: "Gift Card", sub: "Hair Treatment", detail: "$75 Value", colors: ["#fdf4ff", "#e9d5ff", "#d946ef", "#a21caf"] },
  // --- Additional Cafe Designs (Canva-inspired) ---
  { id: 46, name: "Borcelle Cafe Menu", industry: "cafe", category: "menu", style: "elegant", theme: "card-theme-cafe-3", logo: "BORCELLE", sub: "Cafe & Bakery", detail: "Full Menu", colors: ["#3b2314", "#5c3a1e", "#c9a87c", "#f5e6c8"] },
  { id: 47, name: "Iced Coffee Promo", industry: "cafe", category: "flyer", style: "creative", theme: "card-theme-cafe-2", logo: "ICED", sub: "Coffee Special", detail: "$2.99 Cups", colors: ["#f0fdf4", "#bbf7d0", "#22c55e", "#166534"] },
  { id: 48, name: "Coffee Shop Price List", industry: "cafe", category: "menu", style: "simple", theme: "card-theme-cafe-1", logo: "COFFEE", sub: "Price List", detail: "Hot & Cold Drinks", colors: ["#fef9ee", "#f5e6c8", "#92702a", "#5c3a1e"] },
  { id: 49, name: "Artisan Roast Logo", industry: "cafe", category: "logo", style: "minimal", theme: "card-theme-cafe-3", logo: "ARTISAN", sub: "Roast Coffee", detail: "Since 2019", colors: ["#3b2314", "#6b4a2e", "#d4a373", "#f5e6c8"] },
  { id: 50, name: "Cafe Gift Voucher", industry: "cafe", category: "voucher", style: "beautiful", theme: "card-theme-cafe-1", logo: "Gift Card", sub: "Coffee Lovers", detail: "$25 Value", colors: ["#fef9ee", "#e8d5b0", "#b8860b", "#3b2314"] },
  // --- Additional Others Designs (Canva-inspired) ---
  { id: 51, name: "Fitness Studio Loyalty", industry: "others", category: "loyalty-card", style: "bold", theme: "card-theme-other-3", logo: "FIT CLUB", sub: "Gym Membership", detail: "10 Sessions Card", colors: ["#1e293b", "#334155", "#f97316", "#ea580c"] },
  { id: 52, name: "Photography Portfolio", industry: "others", category: "website", style: "minimalist", theme: "card-theme-other-1", logo: "LENS", sub: "Photo Portfolio", detail: "Gallery Website", colors: ["#f0f9ff", "#dbeafe", "#3b82f6", "#1e40af"] },
  { id: 53, name: "Pet Care Service Menu", industry: "others", category: "menu", style: "clean", theme: "card-theme-other-2", logo: "PawCare", sub: "Pet Grooming", detail: "Service Prices", colors: ["#f8fafc", "#e2e8f0", "#60a5fa", "#2563eb"] }
];

// Active filter state (category/style are arrays for multi-select; empty = all)
const filters = { industry: "all", category: [], style: [], search: "" };

/** Industry display config */
const INDUSTRY_LABELS = { nail: "Tiệm Nail", beauty: "Thẩm mỹ", restaurant: "Nhà hàng", cafe: "Quán cafe", others: "Khác" };

/** Industry SVG icons (16x16, stroke-based) */
const INDUSTRY_ICONS = {
  nail: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v10M8 6c0-2 1.5-4 4-4s4 2 4 4v6H8V6z"/><path d="M10 12v8a2 2 0 0 0 4 0v-8"/></svg>',
  beauty: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 16.8l-6.2 4.5 2.4-7.4L2 9.4h7.6z"/></svg>',
  restaurant: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2M7 2v20M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3zm0 0v7"/></svg>',
  cafe: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 8h1a4 4 0 0 1 0 8h-1M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V8zM6 2v3M10 2v3M14 2v3"/></svg>',
  others: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>'
};

/** Creates the HTML for a single gallery card */
function createCardHTML(item) {
  const industryTag = `gallery-card__tag--${item.industry}`;
  const industryLabel = INDUSTRY_LABELS[item.industry] || capitalize(item.industry);
  // 60-30-7-3 proportional color bar
  const widths = [60, 30, 7, 3];
  const barHTML = item.colors.map((c, i) =>
    `<span class="gallery-card__bar-seg" style="background:${c};width:${widths[i]}%" title="${c}"></span>`
  ).join("");

  return `
    <div class="gallery-card" data-id="${item.id}" data-industry="${item.industry}" data-category="${item.category}" data-style="${item.style}">
      <div class="gallery-card__image">
        <div class="gallery-card__image-inner ${item.theme}">
          <div class="card-design">
            <div class="card-design__logo">${item.logo}</div>
            <div class="card-design__divider"></div>
            <div class="card-design__sub">${item.sub}</div>
            <div class="card-design__detail">${item.detail}</div>
          </div>
          <div class="gallery-card__overlay">
            <span class="gallery-card__overlay-btn">Xem thiết kế</span>
          </div>
        </div>
      </div>
      <div class="gallery-card__palette-bar">${barHTML}</div>
      <div class="gallery-card__info">
        <div class="gallery-card__meta">
          <span class="gallery-card__tag ${industryTag}">${INDUSTRY_ICONS[item.industry] || ''}${industryLabel}</span>
          <span class="gallery-card__tag">${capitalize(item.category)}</span>
          <span class="gallery-card__tag">${capitalize(item.style)}</span>
        </div>
      </div>
    </div>`;
}

/** Vietnamese translation map for categories and styles */
const VI_LABELS = {
  menu: "Thực đơn", flyer: "Tờ rơi", logo: "Logo", "loyalty-card": "Thẻ tích điểm",
  voucher: "Phiếu quà tặng", website: "Website",
  feminine: "Nữ tính", professional: "Chuyên nghiệp", modern: "Hiện đại",
  elegant: "Sang trọng", minimalist: "Tối giản", colorful: "Nhiều màu",
  simple: "Đơn giản", beautiful: "Tinh tế", aesthetic: "Thẩm mỹ",
  clean: "Gọn gàng", pastel: "Pastel", luxury: "Cao cấp",
  bold: "Nổi bật", vibrant: "Rực rỡ", gold: "Vàng gold",
  minimal: "Tối giản", creative: "Sáng tạo", classic: "Cổ điển"
};

/** Returns Vietnamese label or capitalizes English fallback */
function capitalize(str) {
  return VI_LABELS[str] || str.replace(/-/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());
}

/** Login gate state — true means user "logged in" and can see all cards */
let isLoggedIn = false;

/** Ratio of cards shown to guests (30%) */
const GUEST_CARD_RATIO = 0.3;

/** Filters and re-renders the gallery grid */
function renderGallery() {
  const grid = document.getElementById("galleryGrid");
  const countEl = document.getElementById("resultCount");
  const loginGate = document.getElementById("loginGate");

  const filtered = portfolioItems.filter((item) => {
    if (filters.industry !== "all" && item.industry !== filters.industry) return false;
    if (filters.category.length > 0 && !filters.category.includes(item.category)) return false;
    if (filters.style.length > 0 && !filters.style.includes(item.style)) return false;
    if (filters.search) {
      const q = filters.search.toLowerCase();
      const searchable = `${item.name} ${item.industry} ${item.category} ${item.style} ${item.logo} ${item.sub}`.toLowerCase();
      if (!searchable.includes(q)) return false;
    }
    return true;
  });

  if (filtered.length === 0) {
    grid.innerHTML = '<p style="grid-column:1/-1;text-align:center;color:var(--gray-400);padding:3rem;">Không tìm thấy mẫu phù hợp. Hãy thử điều chỉnh bộ lọc.</p>';
    if (loginGate) loginGate.classList.remove("is-visible");
    countEl.textContent = 0;
    updateClearButton();
    return;
  }

  const visibleCount = isLoggedIn ? filtered.length : Math.max(6, Math.ceil(filtered.length * GUEST_CARD_RATIO));
  const visible = filtered.slice(0, visibleCount);
  const hiddenCount = filtered.length - visibleCount;

  grid.innerHTML = visible.map(createCardHTML).join("");
  countEl.textContent = filtered.length;

  // Show/hide login gate
  if (loginGate) {
    if (!isLoggedIn && hiddenCount > 0) {
      loginGate.classList.add("is-visible");
      const countSpan = loginGate.querySelector(".login-gate__count");
      if (countSpan) countSpan.textContent = hiddenCount;
    } else {
      loginGate.classList.remove("is-visible");
    }
  }

  updateClearButton();
}

/** Simulate login — reveals all cards */
function handleLoginGate() {
  isLoggedIn = true;
  renderGallery();
  // Smooth scroll to newly revealed cards
  const grid = document.getElementById("galleryGrid");
  const lastVisibleCard = grid.children[Math.ceil(portfolioItems.length * GUEST_CARD_RATIO)];
  if (lastVisibleCard) {
    lastVisibleCard.scrollIntoView({ behavior: "smooth", block: "center" });
  }
}

/** Shows/hides the clear-all button based on active filters */
function updateClearButton() {
  const clearBtn = document.getElementById("clearFilters");
  const hasActiveFilter = filters.industry !== "all" || filters.category.length > 0 || filters.style.length > 0;
  clearBtn.style.display = hasActiveFilter ? "flex" : "none";
}

/** Updates dropdown trigger text to show active selection (multi-select) */
function updateDropdownLabel(triggerId, filterKey) {
  const trigger = document.getElementById(triggerId);
  if (!trigger) return;
  const selected = filters[filterKey];
  const baseLabel = filterKey === "category" ? "Danh mục" : "Phong cách";
  const svg = trigger.querySelector("svg");
  if (selected.length === 0) {
    trigger.textContent = baseLabel + " ";
  } else if (selected.length === 1) {
    trigger.textContent = capitalize(selected[0]) + " ";
  } else {
    trigger.textContent = baseLabel + " (" + selected.length + ") ";
  }
  if (svg) trigger.appendChild(svg);
  trigger.classList.toggle("filter-pill--active", selected.length > 0);
}

/** Sets up click handlers for a filter pill group (multi-select for category/style) */
function initFilterGroup(containerId, filterKey) {
  const container = document.getElementById(containerId);
  if (!container) return;
  const isMulti = filterKey === "category" || filterKey === "style";

  container.addEventListener("click", (e) => {
    const btn = e.target.closest(".filter-pill");
    if (!btn || btn.classList.contains("filter-pill--dropdown")) return;
    const value = btn.dataset.filter;

    if (isMulti) {
      if (value === "all") {
        // "All" clears selection
        filters[filterKey] = [];
        container.querySelectorAll(".filter-pill").forEach((b) => {
          b.classList.toggle("filter-pill--active", b.dataset.filter === "all");
        });
      } else {
        // Toggle individual pill
        const idx = filters[filterKey].indexOf(value);
        if (idx > -1) {
          filters[filterKey].splice(idx, 1);
          btn.classList.remove("filter-pill--active");
        } else {
          filters[filterKey].push(value);
          btn.classList.add("filter-pill--active");
        }
        // Deactivate "All" pill when specific items selected; reactivate if none selected
        const allBtn = container.querySelector('[data-filter="all"]');
        if (allBtn) allBtn.classList.toggle("filter-pill--active", filters[filterKey].length === 0);
      }
      if (filterKey === "category") updateDropdownLabel("categoryDropdown", "category");
      if (filterKey === "style") updateDropdownLabel("styleDropdown", "style");
    } else {
      // Single-select for industry
      container.querySelectorAll(".filter-pill").forEach((b) => b.classList.remove("filter-pill--active"));
      btn.classList.add("filter-pill--active");
      filters[filterKey] = value;

      // "All Designs" resets all filters
      if (filterKey === "industry" && value === "all") {
        filters.category = [];
        filters.style = [];
        filters.search = "";
        document.getElementById("searchInput").value = "";
        document.querySelectorAll(".dropdown-panel__options .filter-pill").forEach((b) => {
          b.classList.toggle("filter-pill--active", b.dataset.filter === "all");
        });
        updateDropdownLabel("categoryDropdown", "category");
        updateDropdownLabel("styleDropdown", "style");
        document.getElementById("categoryPanel").classList.remove("is-open");
        document.getElementById("stylePanel").classList.remove("is-open");
      }
    }
    renderGallery();
  });
}

/** Dropdown panel toggle logic */
function initDropdowns() {
  const categoryBtn = document.getElementById("categoryDropdown");
  const styleBtn = document.getElementById("styleDropdown");
  const categoryPanel = document.getElementById("categoryPanel");
  const stylePanel = document.getElementById("stylePanel");

  function togglePanel(btn, panel, otherBtn, otherPanel) {
    const isOpen = panel.classList.contains("is-open");
    // Close all
    categoryPanel.classList.remove("is-open");
    stylePanel.classList.remove("is-open");
    categoryBtn.classList.remove("is-open");
    styleBtn.classList.remove("is-open");
    // Toggle target
    if (!isOpen) {
      panel.classList.add("is-open");
      btn.classList.add("is-open");
    }
  }

  categoryBtn.addEventListener("click", () => togglePanel(categoryBtn, categoryPanel, styleBtn, stylePanel));
  styleBtn.addEventListener("click", () => togglePanel(styleBtn, stylePanel, categoryBtn, categoryPanel));
}

/** Clear all filters */
function initClearButton() {
  const clearBtn = document.getElementById("clearFilters");
  clearBtn.addEventListener("click", () => {
    filters.industry = "all";
    filters.category = [];
    filters.style = [];
    filters.search = "";

    // Reset all pill groups
    document.querySelectorAll(".filter-pill-group .filter-pill, .dropdown-panel__options .filter-pill").forEach((btn) => {
      btn.classList.toggle("filter-pill--active", btn.dataset.filter === "all");
    });

    // Reset search
    document.getElementById("searchInput").value = "";

    // Reset dropdown labels
    updateDropdownLabel("categoryDropdown", "category");
    updateDropdownLabel("styleDropdown", "style");

    // Close panels
    document.getElementById("categoryPanel").classList.remove("is-open");
    document.getElementById("stylePanel").classList.remove("is-open");

    renderGallery();
  });
}

/** Mobile menu toggle */
function initMobileMenu() {
  const menuBtn = document.querySelector(".header__menu-btn");
  const nav = document.querySelector(".header__nav");
  if (!menuBtn || !nav) return;
  menuBtn.addEventListener("click", () => nav.classList.toggle("header__nav--open"));
}

/** Search input with debounce */
function initSearch() {
  const input = document.getElementById("searchInput");
  if (!input) return;
  let timeout;
  input.addEventListener("input", () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
      filters.search = input.value.trim();
      renderGallery();
    }, 200);
  });
}

/** Contact form handler */
function initContactForm() {
  const form = document.getElementById("contactForm");
  if (!form) return;
  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const btn = form.querySelector('button[type="submit"]');
    btn.textContent = "Đã gửi thành công!";
    btn.style.background = "#16a34a";
    setTimeout(() => { btn.textContent = "Gửi tin nhắn"; btn.style.background = ""; form.reset(); }, 2500);
  });
}

// Initialize on DOM ready
document.addEventListener("DOMContentLoaded", () => {
  initFilterGroup("industryFilters", "industry");
  initFilterGroup("categoryFilters", "category");
  initFilterGroup("styleFilters", "style");
  initDropdowns();
  initClearButton();
  initSearch();
  initMobileMenu();
  initContactForm();
  initDesignModal();
  renderGallery();
});
