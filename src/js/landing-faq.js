/**
 * FAQ accordion toggle for landing page.
 * Clicking a question expands/collapses its answer.
 */
function initFAQ() {
  const faqItems = document.querySelectorAll(".l-faq__item");
  if (faqItems.length === 0) return;

  faqItems.forEach((item) => {
    const btn = item.querySelector(".l-faq__question");
    if (!btn) return;

    btn.addEventListener("click", () => {
      const isOpen = item.classList.contains("l-faq__item--open");

      // Close all other items
      faqItems.forEach((other) => {
        if (other !== item) {
          other.classList.remove("l-faq__item--open");
          const otherBtn = other.querySelector(".l-faq__question");
          if (otherBtn) otherBtn.setAttribute("aria-expanded", "false");
        }
      });

      // Toggle current item
      item.classList.toggle("l-faq__item--open", !isOpen);
      btn.setAttribute("aria-expanded", String(!isOpen));
    });
  });
}

/* Run immediately if DOM already loaded, otherwise wait */
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initFAQ);
} else {
  initFAQ();
}
