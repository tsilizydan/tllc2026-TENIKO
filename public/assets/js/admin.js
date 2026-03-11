/**
 * TENIKO Admin JS — Chart.js, TinyMCE init, bulk actions
 */
document.addEventListener('DOMContentLoaded', () => {

  // ── Analytics Charts ──────────────────────────────────────
  if (document.getElementById('chart-traffic')) {
    fetch('/admin/analytics/data')
      .then(r => r.json())
      .then(data => {

        const palette = {
          green:       'rgba(46,125,50,1)',
          greenLight:  'rgba(46,125,50,0.15)',
          red:         'rgba(200,16,46,1)',
          redLight:    'rgba(200,16,46,0.12)',
          beige:       'rgba(122,79,44,1)',
          beigeLight:  'rgba(245,230,200,0.5)',
        };
        const commonOptions = {
          responsive: true, maintainAspectRatio: false,
          plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
          scales: {
            x: { grid: { display: false }, ticks: { font: { size: 11 }, maxTicksLimit: 10 } },
            y: { grid: { color: 'rgba(0,0,0,.05)' }, ticks: { font: { size: 11 } } }
          }
        };

        // Traffic chart
        const trafficCtx = document.getElementById('chart-traffic').getContext('2d');
        new Chart(trafficCtx, {
          type: 'line',
          data: {
            labels: data.views.map(d => d.date),
            datasets: [{
              label: 'Page Views',
              data: data.views.map(d => d.count),
              borderColor: palette.green, backgroundColor: palette.greenLight,
              fill: true, tension: 0.4, borderWidth: 2, pointRadius: 3,
            }, {
              label: 'Searches',
              data: data.searches.map(d => d.count),
              borderColor: palette.red, backgroundColor: palette.redLight,
              fill: true, tension: 0.4, borderWidth: 2, pointRadius: 3,
            }]
          },
          options: { ...commonOptions, plugins: { ...commonOptions.plugins, legend: { display: true, position: 'top' } } }
        });

        // New users chart
        const usersCtx = document.getElementById('chart-users')?.getContext('2d');
        if (usersCtx) {
          new Chart(usersCtx, {
            type: 'bar',
            data: {
              labels: data.newUsers.map(d => d.date),
              datasets: [{ label: 'New Users', data: data.newUsers.map(d => d.count), backgroundColor: palette.greenLight, borderColor: palette.green, borderWidth: 2, borderRadius: 4 }]
            },
            options: commonOptions
          });
        }

        // Top words doughnut
        const wordsCtx = document.getElementById('chart-top-words')?.getContext('2d');
        if (wordsCtx && data.topWords.length) {
          new Chart(wordsCtx, {
            type: 'doughnut',
            data: {
              labels: data.topWords.map(w => w.word),
              datasets: [{ data: data.topWords.map(w => w.view_count),
                backgroundColor: ['#2E7D32','#4CAF50','#C8102E','#E53935','#7A4F2C','#A1887F','#0277BD','#29B6F6','#F57F17','#FFB300'],
                borderWidth: 2, borderColor: '#fff'
              }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { font: { size: 11 } } } } }
          });
        }
      })
      .catch(() => console.warn('Analytics data unavailable.'));
  }

  // ── TinyMCE Init ─────────────────────────────────────────
  if (window.tinymce) {
    tinymce.init({
      selector: 'textarea.tinymce',
      height: 500,
      menubar: false,
      plugins: ['advlist','autolink','lists','link','image','charmap','preview','anchor',
                'searchreplace','visualblocks','code','fullscreen','insertdatetime',
                'media','table','help','wordcount'],
      toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | link image media | code fullscreen | help',
      content_style: 'body { font-family: Inter, sans-serif; font-size: 15px; line-height: 1.7; color: #2F2F2F; }',
      skin: document.documentElement.getAttribute('data-theme') === 'dark' ? 'oxide-dark' : 'oxide',
      content_css: document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'default',
    });
  }

  // ── Bulk Selection ───────────────────────────────────────
  const selectAll = document.getElementById('select-all');
  const bulkBar   = document.querySelector('.bulk-bar');
  const bulkCount = document.querySelector('.bulk-bar__count');

  if (selectAll && bulkBar) {
    const getCheckboxes = () => document.querySelectorAll('.row-check');

    selectAll.addEventListener('change', () => {
      getCheckboxes().forEach(cb => cb.checked = selectAll.checked);
      updateBulkBar();
    });

    document.addEventListener('change', (e) => {
      if (e.target.classList.contains('row-check')) updateBulkBar();
    });

    const updateBulkBar = () => {
      const checked = [...getCheckboxes()].filter(c => c.checked).length;
      if (checked > 0) {
        bulkBar.classList.add('visible');
        if (bulkCount) bulkCount.textContent = `${checked} selected`;
      } else {
        bulkBar.classList.remove('visible');
      }
    };
  }

  // ── Inline Status Toggle ─────────────────────────────────
  document.querySelectorAll('.toggle-status').forEach(btn => {
    btn.addEventListener('click', async () => {
      const { id, type, currentStatus } = btn.dataset;
      const next = currentStatus === 'published' ? 'draft' : 'published';
      try {
        const fd = new FormData();
        fd.append('status', next);
        fd.append('_csrf_token', document.querySelector('input[name="_csrf_token"]')?.value || '');
        const res = await fetch(`/admin/${type}s/${id}/status`, { method: 'POST', body: fd });
        if (res.ok) { btn.dataset.currentStatus = next; window.location.reload(); }
      } catch {}
    });
  });

  // ── Confirm Delete ───────────────────────────────────────
  document.querySelectorAll('form[data-confirm]').forEach(form => {
    form.addEventListener('submit', (e) => {
      if (!confirm(form.dataset.confirm || 'Are you sure? This cannot be undone.')) {
        e.preventDefault();
      }
    });
  });

  // ── Moderation approve/reject ─────────────────────────────
  document.querySelectorAll('.mod-approve, .mod-reject').forEach(btn => {
    btn.addEventListener('click', async () => {
      const { action, type, id } = btn.dataset;
      const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
      try {
        const fd = new FormData();
        fd.append('type', type); fd.append('id', id);
        fd.append('_csrf_token', csrf || '');
        const res = await fetch(`/admin/moderation/${action}`, { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          btn.closest('.mod-card')?.remove();
          window.showToast?.(`${action === 'approve' ? 'Approved' : 'Rejected'} successfully.`, action === 'approve' ? 'success' : 'info');
        }
      } catch { window.showToast?.('Action failed.', 'error'); }
    });
  });

});
