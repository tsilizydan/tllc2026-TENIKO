/**
 * TENIKO — Main Application JS
 * Uses Alpine.js for reactive UI + vanilla JS for utilities
 */

document.addEventListener('DOMContentLoaded', () => {

  // ── Theme Toggle ──────────────────────────────────────────
  const savedTheme = localStorage.getItem('teniko-theme') ||
    (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  document.documentElement.setAttribute('data-theme', savedTheme);

  window.toggleTheme = () => {
    const current = document.documentElement.getAttribute('data-theme');
    const next    = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('teniko-theme', next);
  };

  // ── Mobile Nav Drawer ─────────────────────────────────────
  const burger  = document.querySelector('.nav__burger');
  const drawer  = document.querySelector('.nav__drawer');
  const backdrop = document.querySelector('.nav__drawer__backdrop');
  const closeBtn = document.querySelector('.nav__drawer__close');

  if (burger && drawer) {
    burger.addEventListener('click', () => drawer.classList.add('open'));
    backdrop?.addEventListener('click', () => drawer.classList.remove('open'));
    closeBtn?.addEventListener('click',  () => drawer.classList.remove('open'));
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') drawer.classList.remove('open');
    });
  }

  // ── Flash Toast Auto-dismiss ──────────────────────────────
  document.querySelectorAll('.alert[data-auto-dismiss]').forEach(el => {
    setTimeout(() => {
      el.style.transition = 'opacity 0.4s ease, max-height 0.4s ease';
      el.style.opacity = '0';
      setTimeout(() => el.remove(), 400);
    }, 5000);
  });

  // ── Copy Utilities ────────────────────────────────────────
  window.copyToClipboard = (text, btn) => {
    navigator.clipboard.writeText(text).then(() => {
      const orig = btn.innerHTML;
      btn.innerHTML = '<i class="fa fa-check"></i> Copied!';
      btn.classList.add('btn-primary');
      setTimeout(() => { btn.innerHTML = orig; btn.classList.remove('btn-primary'); }, 2000);
    });
  };

  // ── Share ─────────────────────────────────────────────────
  window.shareEntry = async (title, url) => {
    if (navigator.share) {
      try { await navigator.share({ title, url }); return; } catch {}
    }
    await navigator.clipboard.writeText(url);
    showToast('Link copied to clipboard!', 'success');
  };

  // ── Toast Notifications ────────────────────────────────────
  window.showToast = (msg, type = 'info') => {
    let container = document.getElementById('toast-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toast-container';
      document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.className = `toast toast-${type} animate-fade-in`;
    const icon = { success: 'fa-check-circle', error: 'fa-times-circle', info: 'fa-info-circle', warning: 'fa-exclamation-circle' }[type] || 'fa-info-circle';
    toast.innerHTML = `<i class="fa ${icon}"></i><span>${msg}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transition = 'opacity .3s ease';
      setTimeout(() => toast.remove(), 300);
    }, 4000);
  };

  // ── Audio Player ──────────────────────────────────────────
  document.querySelectorAll('.audio-player__play').forEach(btn => {
    const player = btn.closest('.audio-player');
    const src    = player?.dataset.src;
    if (!src) return;
    let audio;
    btn.addEventListener('click', () => {
      if (!audio) {
        audio = new Audio(src);
        audio.addEventListener('ended', () => { btn.innerHTML = '<i class="fa fa-play"></i>'; });
      }
      if (audio.paused) {
        audio.play();
        btn.innerHTML = '<i class="fa fa-pause"></i>';
      } else {
        audio.pause();
        btn.innerHTML = '<i class="fa fa-play"></i>';
      }
    });
  });

  // ── Reactions ─────────────────────────────────────────────
  document.querySelectorAll('.reaction-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
      if (!document.body.dataset.loggedIn) {
        showToast('Please log in to react.', 'info');
        return;
      }
      const { entityType, entityId, type } = btn.dataset;
      try {
        const res = await fetch('/react', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content },
          body: JSON.stringify({ entity_type: entityType, entity_id: entityId, type, _csrf_token: document.querySelector('input[name="_csrf_token"]')?.value })
        });
        const data = await res.json();
        if (data.success) {
          btn.classList.toggle('active', data.toggled);
          const countEl = btn.querySelector('.reaction-count');
          if (countEl) countEl.textContent = data.total;
        }
      } catch { showToast('Failed to react. Please try again.', 'error'); }
    });
  });

  // ── Comment Form Ajax ─────────────────────────────────────
  const commentForm = document.getElementById('comment-form');
  if (commentForm) {
    commentForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn  = commentForm.querySelector('[type="submit"]');
      const body = commentForm.querySelector('[name="body"]').value.trim();
      if (!body) { showToast('Please enter a comment.', 'warning'); return; }
      btn.disabled = true; btn.textContent = 'Posting…';
      try {
        const fd  = new FormData(commentForm);
        const res = await fetch('/comment', { method: 'POST', body: fd });
        const data= await res.json();
        if (data.success) {
          commentForm.reset();
          showToast('Comment posted!', 'success');
          location.reload();
        } else { showToast('Error posting comment.', 'error'); }
      } catch { showToast('Network error.', 'error'); }
      btn.disabled = false; btn.textContent = 'Post Comment';
    });
  }

  // ── Mark notifications read on open ──────────────────────
  const notifBtn = document.getElementById('notif-btn');
  if (notifBtn && document.body.dataset.loggedIn) {
    notifBtn.addEventListener('click', async () => {
      await fetch('/api/notifications/read', {
        method: 'POST',
        headers: { 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content }
      });
      document.querySelectorAll('.notif-badge').forEach(el => el.remove());
    });
  }

});
