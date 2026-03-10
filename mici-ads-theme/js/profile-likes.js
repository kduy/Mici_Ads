/**
 * Profile Likes — Like toggle on design cards + unlike from profile.
 *
 * Uses AJAX to toggle likes. Heart icon injected into gallery cards.
 * Config from window.miciLikes: { ajaxUrl, nonce, likedIds, loggedIn, loginUrl }.
 */
(function() {
  var cfg = window.miciLikes || {};
  if (!cfg.ajaxUrl) return;

  var likedSet = new Set((cfg.likedIds || []).map(Number));

  // --- Heart buttons on gallery cards ---
  function injectHearts() {
    document.querySelectorAll('.gallery-card').forEach(function(card) {
      if (card.querySelector('.mici-like-btn')) return;
      var id = parseInt(card.dataset.id, 10);
      if (!id) return;

      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'mici-like-btn' + (likedSet.has(id) ? ' mici-like-btn--liked' : '');
      btn.dataset.designId = id;
      btn.innerHTML = '♥';
      btn.title = likedSet.has(id) ? 'Bỏ thích' : 'Yêu thích';

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
  var gallery = document.querySelector('.gallery-grid, .portfolio-grid, #portfolioGrid');
  if (gallery) {
    new MutationObserver(function() {
      setTimeout(injectHearts, 50);
    }).observe(gallery, { childList: true });
  }

  // --- Like toggle click handler (delegation) ---
  document.addEventListener('click', function(e) {
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
      .then(function(r) { return r.json(); })
      .then(function(res) {
        btn.disabled = false;
        if (!res.success) return;

        if (res.data.liked) {
          likedSet.add(designId);
        } else {
          likedSet.delete(designId);
        }

        // Update all like buttons for this design.
        document.querySelectorAll('[data-design-id="' + designId + '"]').forEach(function(b) {
          if (b.classList.contains('mici-like-btn')) {
            b.classList.toggle('mici-like-btn--liked', res.data.liked);
            b.title = res.data.liked ? 'Bỏ thích' : 'Yêu thích';
          }
        });

        // Remove card from profile liked grid if unliked.
        if (!res.data.liked && btn.classList.contains('mici-profile__unlike-btn')) {
          var card = btn.closest('.mici-profile__liked-card');
          if (card) {
            card.style.opacity = '0';
            setTimeout(function() { card.remove(); checkEmptyLikes(); }, 300);
          }
        }
      })
      .catch(function() { btn.disabled = false; });
  });

  function checkEmptyLikes() {
    var grid = document.querySelector('.mici-profile__liked-grid');
    if (grid && !grid.children.length) {
      grid.outerHTML = '<p class="mici-profile__empty">Bạn chưa thích mẫu thiết kế nào.</p>';
    }
  }
})();
