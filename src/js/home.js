/**
 * Home page logic: mobile menu toggle and contact form handler.
 * Gallery/filter logic lives in main.js (templates page only).
 */

/** Mobile menu toggle */
function initMobileMenu() {
  const menuBtn = document.querySelector(".header__menu-btn");
  const nav = document.querySelector(".header__nav");
  if (!menuBtn || !nav) return;
  menuBtn.addEventListener("click", () => nav.classList.toggle("header__nav--open"));
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

document.addEventListener("DOMContentLoaded", () => {
  initMobileMenu();
  initContactForm();
});
