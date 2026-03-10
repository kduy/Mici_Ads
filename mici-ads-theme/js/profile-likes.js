/**
 * Profile Likes — Like toggle on design cards + unlike from profile.
 *
 * Uses AJAX to toggle likes. SVG heart icon injected into gallery cards.
 * Config from window.miciLikes: { ajaxUrl, nonce, likedIds, loggedIn, loginUrl }.
 */
(function () {
  var cfg = window.miciLikes || {};
  if (!cfg.ajaxUrl) return;

  var likedSet = new Set((cfg.likedIds || []).map(Number));

  // SVG heart icons — outline and filled.
  var HEART_OUTLINE =
    '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>';

  // --- Heart buttons on gallery cards ---
  function injectHearts() {
    document.querySelectorAll('.gallery-card').forEach(function (card) {
      if (card.querySelector('.mici-like-btn')) return;
      var id = parseInt(card.dataset.id, 10);
      if (!id) return;

      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className =
        'mici-like-btn' + (likedSet.has(id) ? ' mici-like-btn--liked' : '');
      btn.dataset.designId = id;
      btn.innerHTML = HEART_OUTLINE;
      btn.setAttribute(
        'aria-label',
        likedSet.has(id) ? 'Bỏ thích' : 'Yêu thích'
      );

      // Place inside the image container.
      var imgWrap = card.querySelector('.gallery-card__image');
      if (imgWrap) {
        imgWrap.style.position = 'relative';
        imgWrap.appendChild(btn);
      }
    });
  }

  // Inject after initial render and after filter changes.
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', injectHearts);
  } else {
    injectHearts();
  }

  // Re-inject when gallery re-renders (MutationObserver on gallery container).
  var gallery = document.querySelector(
    '.gallery-grid, .portfolio-grid, #portfolioGrid'
  );
  if (gallery) {
    new MutationObserver(function () {
      setTimeout(injectHearts, 50);
    }).observe(gallery, { childList: true });
  }

  // --- Like toggle click handler (delegation) ---
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.mici-like-btn, .mici-profile__unlike-btn');
    if (!btn) return;

    e.preventDefault();
    e.stopPropagation();

    if (!cfg.loggedIn) {
      window.location.href = cfg.loginUrl || '/auth/';
      return;
    }

    var designId = parseInt(btn.dataset.designId, 10);
    if (!designId || btn.disabled) return;

    btn.disabled = true;

    var data = new FormData();
    data.append('action', 'mici_toggle_like_design');
    data.append('_ajax_nonce', cfg.nonce);
    data.append('design_id', designId);

    fetch(cfg.ajaxUrl, { method: 'POST', body: data, credentials: 'same-origin' })
      .then(function (r) {
        return r.json();
      })
      .then(function (res) {
        btn.disabled = false;
        if (!res.success) return;

        if (res.data.liked) {
          likedSet.add(designId);
        } else {
          likedSet.delete(designId);
        }

        // Update all like buttons for this design.
        document
          .querySelectorAll('[data-design-id="' + designId + '"]')
          .forEach(function (b) {
            if (b.classList.contains('mici-like-btn')) {
              b.classList.toggle('mici-like-btn--liked', res.data.liked);
              b.setAttribute(
                'aria-label',
                res.data.liked ? 'Bỏ thích' : 'Yêu thích'
              );
            }
          });

        // Remove card from profile liked grid if unliked.
        if (
          !res.data.liked &&
          btn.classList.contains('mici-profile__unlike-btn')
        ) {
          var card = btn.closest('.mici-profile__liked-card');
          if (card) {
            card.style.opacity = '0';
            setTimeout(function () {
              card.remove();
              checkEmptyLikes();
            }, 300);
          }
        }
      })
      .catch(function () {
        btn.disabled = false;
      });
  });

  function checkEmptyLikes() {
    var grid = document.querySelector('.mici-profile__liked-grid');
    if (grid && !grid.children.length) {
      var templatesUrl =
        document.querySelector('[data-templates-url]')?.dataset.templatesUrl ||
        '/';
      grid.outerHTML =
        '<div class="mici-profile__empty">' +
        '<svg class="mici-profile__empty-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="1.5"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>' +
        '<p>Bạn chưa thích mẫu thiết kế nào.</p>' +
        '<a href="' + templatesUrl + '" class="mici-profile__empty-cta">Khám phá mẫu thiết kế</a>' +
        '</div>';
    }
  }
})();
