/**
 * TENIKO — Search Autocomplete JS
 * Debounced autocomplete with keyboard navigation
 */

(function () {
  const input    = document.getElementById('hero-search') || document.getElementById('dict-search');
  const dropdown = document.getElementById('autocomplete-dropdown');
  if (!input || !dropdown) return;

  let timer, focused = -1, results = [];

  const render = (items) => {
    results = items;
    focused = -1;
    if (!items.length) { dropdown.classList.add('hidden'); return; }
    dropdown.innerHTML = items.map((item, i) =>
      `<a class="autocomplete__item" href="/word/${encodeURIComponent(item.slug)}" data-index="${i}">
        <span class="autocomplete__item__word">${highlight(item.word, input.value)}</span>
        ${item.part_of_speech ? `<span class="autocomplete__item__pos">${item.part_of_speech}</span>` : ''}
      </a>`
    ).join('') + `<div class="autocomplete__footer">
      Press Enter to search all results for <strong>${e(input.value)}</strong>
    </div>`;
    dropdown.classList.remove('hidden');
  };

  const highlight = (word, query) => {
    if (!query) return e(word);
    const re = new RegExp('(' + query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
    return e(word).replace(re, '<mark>$1</mark>');
  };

  const e = str => String(str).replace(/[&<>"']/g, c =>
    ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c])
  );

  const doSearch = async (q) => {
    if (!q || q.length < 2) { dropdown.classList.add('hidden'); return; }
    try {
      const res = await fetch(`/api/search?q=${encodeURIComponent(q)}`);
      const data = await res.json();
      render(data);
    } catch { dropdown.classList.add('hidden'); }
  };

  input.addEventListener('input', () => {
    clearTimeout(timer);
    timer = setTimeout(() => doSearch(input.value.trim()), 280);
  });

  input.addEventListener('keydown', (e) => {
    if (dropdown.classList.contains('hidden')) return;
    const items = dropdown.querySelectorAll('.autocomplete__item');
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      focused = Math.min(focused + 1, items.length - 1);
      items.forEach((el, i) => el.classList.toggle('focused', i === focused));
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      focused = Math.max(focused - 1, -1);
      items.forEach((el, i) => el.classList.toggle('focused', i === focused));
    } else if (e.key === 'Enter') {
      if (focused >= 0 && items[focused]) {
        e.preventDefault();
        window.location.href = items[focused].href;
      }
    } else if (e.key === 'Escape') {
      dropdown.classList.add('hidden');
    }
  });

  document.addEventListener('click', (e) => {
    if (!input.contains(e.target) && !dropdown.contains(e.target)) {
      dropdown.classList.add('hidden');
    }
  });
})();
