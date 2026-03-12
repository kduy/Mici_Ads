/**
 * Visual effects module for Mici Ads portfolio.
 * - 3D card tilt on hover (Apple-style perspective effect)
 * - Scroll-reveal animations via Intersection Observer
 */

/* ========================================
   3D Card Tilt on Hover
   ======================================== */

/** Max rotation in degrees */
const TILT_MAX = 8;
/** Perspective distance */
const TILT_PERSPECTIVE = 800;

/** Applies 3D tilt tracking to all gallery cards */
function initCardTilt() {
  const grid = document.getElementById("galleryGrid");
  if (!grid) return;

  grid.addEventListener("mousemove", (e) => {
    const card = e.target.closest(".gallery-card");
    if (!card) return;

    const rect = card.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    const centerX = rect.width / 2;
    const centerY = rect.height / 2;

    // Normalize to -1..1 range, invert for natural tilt direction
    const rotateY = ((x - centerX) / centerX) * TILT_MAX;
    const rotateX = ((centerY - y) / centerY) * TILT_MAX;

    card.style.transform = `perspective(${TILT_PERSPECTIVE}px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-4px)`;
    card.style.transition = "transform 0.1s ease-out";
  });

  grid.addEventListener("mouseleave", (e) => {
    const card = e.target.closest(".gallery-card");
    if (card) resetTilt(card);
  }, true);

  // Use event delegation with capture for mouseout on individual cards
  grid.addEventListener("mouseout", (e) => {
    const card = e.target.closest(".gallery-card");
    const related = e.relatedTarget;
    if (card && (!related || !card.contains(related))) {
      resetTilt(card);
    }
  });
}

/** Resets card to flat position with smooth transition */
function resetTilt(card) {
  card.style.transform = "";
  card.style.transition = "all 0.4s ease";
}

/* ========================================
   Scroll-Reveal Animations
   ======================================== */

/** Initializes Intersection Observer for reveal-on-scroll */
function initScrollReveal() {
  // Elements that should animate in on scroll
  const revealSelectors = [
    ".gallery-card",
    ".service-card",
    ".services__title",
    ".services__subtitle",
    ".cta-section__title",
    ".cta-section__desc",
    ".cta-section__stats",
    ".contact__title",
    ".contact__subtitle",
    ".contact__form",
    ".gallery__title"
  ];

  const selectorTargets = document.querySelectorAll(revealSelectors.join(","));
  const htmlHiddenTargets = document.querySelectorAll(".scroll-hidden");

  // Merge both sets (selector-based + already-marked-in-HTML)
  const targetSet = new Set([...selectorTargets, ...htmlHiddenTargets]);
  if (targetSet.size === 0) return;

  // Mark selector targets as hidden (HTML ones already have the class)
  selectorTargets.forEach((el) => {
    el.classList.add("scroll-hidden");
  });

  const targets = Array.from(targetSet);

  // Stagger groups: cards in the same parent get incremental delays
  const staggerSelectors = [
    ".l-service-card",
    ".l-benefit-card",
    ".l-testimonial-card",
    ".l-faq__item",
    ".gallery-card"
  ];

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const el = entry.target;

          // Apply stagger delay for grouped elements
          const isStaggered = staggerSelectors.some(sel => el.matches(sel));
          if (isStaggered && el.parentElement) {
            const siblings = Array.from(
              el.parentElement.querySelectorAll(".scroll-hidden")
            );
            const idx = siblings.indexOf(el);
            if (idx >= 0) {
              el.setAttribute("data-delay", String(Math.min(idx + 1, 5)));
            }
          }

          el.classList.add("scroll-revealed");
          el.classList.remove("scroll-hidden");
          observer.unobserve(el);
        }
      });
    },
    { threshold: 0.1, rootMargin: "0px 0px -50px 0px" }
  );

  targets.forEach((el) => observer.observe(el));
}

/* ========================================
   Initialize
   ======================================== */

/** Duplicates children in a track element until it spans ≥ 2× viewport, then starts marquee */
function initMarquee(trackSelector, activeClass) {
  const track = document.querySelector(trackSelector);
  if (!track || track.children.length === 0) return;

  const items = Array.from(track.children);
  // Duplicate enough times so the track is at least 2× the viewport width.
  const viewportW = window.innerWidth;
  let copies = 1;
  const singleSetWidth = track.scrollWidth;
  if (singleSetWidth > 0) {
    copies = Math.max(1, Math.ceil((viewportW * 2) / singleSetWidth));
  }
  for (let c = 0; c < copies; c++) {
    items.forEach(item => {
      const clone = item.cloneNode(true);
      clone.setAttribute("aria-hidden", "true");
      track.appendChild(clone);
    });
  }

  // Activate marquee animation
  track.classList.add(activeClass);
}

/** Smooth sticky card stack with lerp interpolation and momentum */
function initStickyCardStack() {
  const cards = Array.from(document.querySelectorAll(".l-service-card"));
  if (cards.length < 2) return;

  // Current and target scale for each card (lerp state)
  const current = cards.map(() => 1);
  const target = cards.map(() => 1);
  const LERP_SPEED = 0.08; // Lower = more momentum/lag
  const MIN_SCALE = 0.96;
  let rafId = null;

  /** Calculate target scale based on overlap with next card */
  function updateTargets() {
    for (let i = 0; i < cards.length - 1; i++) {
      const cardRect = cards[i].getBoundingClientRect();
      const nextRect = cards[i + 1].getBoundingClientRect();
      const overlap = cardRect.bottom - nextRect.top;
      const cardH = cardRect.height;

      if (overlap > 0 && cardH > 0) {
        // Progress 0→1 as next card covers current
        const progress = Math.min(overlap / (cardH * 0.5), 1);
        target[i] = 1 - progress * (1 - MIN_SCALE);
      } else {
        target[i] = 1;
      }
    }
  }

  /** Animate current values toward targets with lerp */
  function animate() {
    let needsUpdate = false;

    for (let i = 0; i < cards.length; i++) {
      const diff = target[i] - current[i];
      if (Math.abs(diff) > 0.0005) {
        current[i] += diff * LERP_SPEED;
        needsUpdate = true;
      } else {
        current[i] = target[i];
      }
      cards[i].style.transform = `scale(${current[i]})`;
    }

    if (needsUpdate) {
      rafId = requestAnimationFrame(animate);
    } else {
      rafId = null;
    }
  }

  /** Kick off animation loop if not running */
  function scheduleAnimate() {
    if (!rafId) rafId = requestAnimationFrame(animate);
  }

  window.addEventListener("scroll", () => {
    updateTargets();
    scheduleAnimate();
  }, { passive: true });

  updateTargets();
  scheduleAnimate();
}

/** Portfolio columns: vertical infinite marquee with alternating directions.
 *  On phones (<768px), merges all items into a single column. */
function initPortfolioMarquee() {
  const cols = document.querySelectorAll(".l-portfolio__col");
  const tracks = document.querySelectorAll(".l-portfolio__track");
  if (tracks.length === 0) return;

  const isMobile = window.matchMedia("(max-width: 767px)").matches;

  if (isMobile && tracks.length > 1) {
    // Merge all items from every track into the first track
    const firstTrack = tracks[0];
    for (let i = 1; i < tracks.length; i++) {
      Array.from(tracks[i].children).forEach((item) => {
        firstTrack.appendChild(item);
      });
      // Hide extra columns
      cols[i].style.display = "none";
    }
    // Duplicate merged items for seamless loop
    const items = Array.from(firstTrack.children);
    items.forEach((item) => {
      const clone = item.cloneNode(true);
      clone.setAttribute("aria-hidden", "true");
      firstTrack.appendChild(clone);
    });
    firstTrack.classList.add("l-portfolio__track--marquee");
  } else {
    // Desktop/tablet: each column scrolls independently
    tracks.forEach((track) => {
      if (track.children.length === 0) return;
      const items = Array.from(track.children);
      items.forEach((item) => {
        const clone = item.cloneNode(true);
        clone.setAttribute("aria-hidden", "true");
        track.appendChild(clone);
      });
      track.classList.add("l-portfolio__track--marquee");
    });
  }
}

document.addEventListener("DOMContentLoaded", () => {
  initCardTilt();
  initScrollReveal();
  initMarquee(".l-hero__gallery-track", "l-hero__gallery-track--marquee");
  initMarquee(".l-trust__track", "l-trust__track--marquee");
  initMarquee(".l-testimonials__track", "l-testimonials__track--marquee");
  initStickyCardStack();
  initPortfolioMarquee();
});

// Re-apply scroll-hidden to new cards after filter re-render
const origRender = window.renderGallery;
if (typeof origRender === "function") {
  // Patch is deferred — see bottom of file
}

// Hook into renderGallery to apply reveal to newly rendered cards
(function patchRenderGallery() {
  const waitForRender = setInterval(() => {
    if (typeof renderGallery === "function" && !renderGallery.__patched) {
      const original = renderGallery;
      window.renderGallery = function () {
        original.apply(this, arguments);
        // Re-apply scroll reveal to new cards
        const cards = document.querySelectorAll(
          ".gallery-card:not(.scroll-revealed)"
        );
        if (cards.length === 0) return;

        const observer = new IntersectionObserver(
          (entries) => {
            entries.forEach((entry) => {
              if (entry.isIntersecting) {
                const el = entry.target;
                const siblings = Array.from(
                  el.parentElement.querySelectorAll(
                    ".gallery-card:not(.scroll-revealed)"
                  )
                );
                const idx = siblings.indexOf(el);
                el.style.transitionDelay = `${Math.min(idx, 6) * 60}ms`;
                el.classList.add("scroll-revealed");
                el.classList.remove("scroll-hidden");
                observer.unobserve(el);
              }
            });
          },
          { threshold: 0.1, rootMargin: "0px 0px -40px 0px" }
        );

        cards.forEach((card) => {
          card.classList.add("scroll-hidden");
          observer.observe(card);
        });
      };
      renderGallery.__patched = true;
      clearInterval(waitForRender);
    }
  }, 50);
})();
