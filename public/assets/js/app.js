/**
 * TENIKO — Main Application JS (v2)
 * Fixes: dark/light mode, mobile nav, password toggle, AJAX features
 */

// ── Theme (Dark/Light) ─────────────────────────────────────────────────────
// Run BEFORE DOMContentLoaded to eliminate flash-of-wrong-theme
(function () {
  const saved   = localStorage.getItem('teniko-theme');
  const prefers = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  const theme   = saved || prefers;
  document.documentElement.setAttribute('data-theme', theme);
})();

window.toggleTheme = function () {
  const current = document.documentElement.getAttribute('data-theme') || 'light';
  const next    = current === 'dark' ? 'light' : 'dark';
  document.documentElement.setAttribute('data-theme', next);
  localStorage.setItem('teniko-theme', next);
  syncThemeIcon();
};

function syncThemeIcon() {
  const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
  document.querySelectorAll('.theme-icon').forEach(el => {
    el.className = isDark ? 'fa fa-sun theme-icon' : 'fa fa-moon theme-icon';
  });
}

// ── DOMContentLoaded ──────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {

  // Sync theme icon on load
  syncThemeIcon();

  // ── Mobile Nav Drawer ────────────────────────────────────────────────
  const burger   = document.querySelector('.nav__burger');
  const drawer   = document.querySelector('.nav__drawer');
  const backdrop = document.querySelector('.nav__drawer__backdrop');
  const closeBtn = document.querySelector('.nav__drawer__close');

  if (burger && drawer) {
    const openDrawer  = () => { drawer.classList.add('open');  document.body.style.overflow = 'hidden'; burger.setAttribute('aria-expanded', 'true'); };
    const closeDrawer = () => { drawer.classList.remove('open'); document.body.style.overflow = ''; burger.setAttribute('aria-expanded', 'false'); };
    burger.addEventListener('click',     openDrawer);
    backdrop?.addEventListener('click',  closeDrawer);
    closeBtn?.addEventListener('click',  closeDrawer);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });
  }

  // ── Password Toggle ───────────────────────────────────────────────────
  document.querySelectorAll('.pwd-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = document.getElementById(btn.dataset.target);
      if (!input) return;
      if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = '<i class="fa fa-eye-slash"></i>';
        btn.setAttribute('aria-label', 'Hide password');
      } else {
        input.type = 'password';
        btn.innerHTML = '<i class="fa fa-eye"></i>';
        btn.setAttribute('aria-label', 'Show password');
      }
    });
  });

  // ── Flash Toast Auto-dismiss ──────────────────────────────────────────
  document.querySelectorAll('.alert[data-auto-dismiss]').forEach(el => {
    setTimeout(() => {
      el.style.transition = 'opacity .4s ease, max-height .4s ease';
      el.style.opacity    = '0';
      setTimeout(() => el.remove(), 450);
    }, 5000);
  });

  // ── Toast Notifications ────────────────────────────────────────────────
  window.showToast = function (msg, type = 'info') {
    let container = document.getElementById('toast-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toast-container';
      document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    const icons = { success: 'fa-check-circle', error: 'fa-times-circle', info: 'fa-info-circle', warning: 'fa-exclamation-circle' };
    toast.innerHTML = `<i class="fa ${icons[type] || icons.info}"></i><span>${msg}</span>`;
    container.appendChild(toast);
    // Animate in
    requestAnimationFrame(() => { toast.style.opacity = '1'; toast.style.transform = 'translateX(0)'; });
    setTimeout(() => {
      toast.style.opacity   = '0';
      toast.style.transform = 'translateX(110%)';
      setTimeout(() => toast.remove(), 350);
    }, 4000);
  };

  // ── Copy Utilities ─────────────────────────────────────────────────────
  window.copyToClipboard = function (text, btn) {
    navigator.clipboard.writeText(text).then(() => {
      const orig = btn.innerHTML;
      btn.innerHTML = '<i class="fa fa-check"></i> Copied!';
      btn.classList.add('btn--copied');
      setTimeout(() => { btn.innerHTML = orig; btn.classList.remove('btn--copied'); }, 2200);
    }).catch(() => showToast('Could not copy to clipboard.', 'error'));
  };

  // ── Share API ──────────────────────────────────────────────────────────
  window.shareEntry = async function (title, url) {
    if (navigator.share) {
      try { await navigator.share({ title, url }); return; } catch (e) { if (e.name === 'AbortError') return; }
    }
    await navigator.clipboard.writeText(url);
    showToast('Link copied to clipboard!', 'success');
  };

  // ── Audio Player ───────────────────────────────────────────────────────
  document.querySelectorAll('.audio-player__play').forEach(btn => {
    const player = btn.closest('.audio-player');
    const src    = player?.dataset.src;
    if (!src) return;
    let audio;
    btn.addEventListener('click', () => {
      if (!audio) {
        audio = new Audio(src);
        audio.addEventListener('ended', () => { btn.innerHTML = '<i class="fa fa-play"></i>'; });
        audio.addEventListener('error', () => { showToast('Audio file not available.', 'error'); });
      }
      if (audio.paused) {
        document.querySelectorAll('.audio-player__play').forEach(b => { if (b !== btn && b._audio && !b._audio.paused) { b._audio.pause(); b.innerHTML = '<i class="fa fa-play"></i>'; } });
        audio.play();
        btn.innerHTML = '<i class="fa fa-pause"></i>';
      } else {
        audio.pause();
        btn.innerHTML = '<i class="fa fa-play"></i>';
      }
      btn._audio = audio;
    });
  });

  // ── Reaction Buttons ───────────────────────────────────────────────────
  document.querySelectorAll('.reaction-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
      if (!document.body.dataset.loggedIn) {
        showToast('Please log in to react.', 'info');
        return;
      }
      const { entityType, entityId, type } = btn.dataset;
      const csrf = document.querySelector('meta[name="csrf-token"]')?.content ||
                   document.querySelector('input[name="_csrf_token"]')?.value || '';
      try {
        const res  = await fetch('/react', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
          body: JSON.stringify({ entity_type: entityType, entity_id: entityId, type, _csrf_token: csrf }),
        });
        const data = await res.json();
        if (data.success) {
          btn.classList.toggle('active', !!data.toggled);
          const countEl = btn.querySelector('.reaction-count');
          if (countEl) countEl.textContent = data.total;
        }
      } catch { showToast('Could not react. Please try again.', 'error'); }
    });
  });

  // ── AJAX Comment Form ──────────────────────────────────────────────────
  const commentForm = document.getElementById('comment-form');
  if (commentForm) {
    commentForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn  = commentForm.querySelector('[type="submit"]');
      const body = commentForm.querySelector('[name="body"]')?.value.trim();
      if (!body || body.length < 3) { showToast('Please enter a comment (min. 3 characters).', 'warning'); return; }
      btn.disabled = true; btn.textContent = 'Posting…';
      try {
        const res  = await fetch('/comment', { method: 'POST', body: new FormData(commentForm) });
        const data = await res.json();
        if (data.success) {
          commentForm.reset();
          showToast('Comment posted!', 'success');
          setTimeout(() => location.reload(), 600);
        } else {
          showToast(data.message || 'Error posting comment.', 'error');
        }
      } catch { showToast('Network error. Please try again.', 'error'); }
      btn.disabled = false; btn.textContent = 'Post Comment';
    });
  }

  // ── Mark notifications read ────────────────────────────────────────────
  const notifBtn = document.getElementById('notif-btn');
  if (notifBtn && document.body.dataset.loggedIn) {
    notifBtn.addEventListener('click', async () => {
      try {
        await fetch('/api/notifications/read', {
          method: 'POST',
          headers: { 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || '' },
        });
        document.querySelectorAll('.notif-badge').forEach(el => el.remove());
      } catch {}
    });
  }

  // ── Smooth scroll for anchor links ────────────────────────────────────
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const target = document.querySelector(a.getAttribute('href'));
      if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    });
  });

});
