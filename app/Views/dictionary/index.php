<?php
/**
 * Dictionary Index — /dictionary and /search
 * Variables: $results, $query, $letter, $mostViewed
 */
$alphabet = range('A', 'Z');
$csrfToken = \App\Core\CSRF::generate();
?>
<!-- Page Header -->
<div style="background:var(--clr-bg-surface);border-bottom:1px solid var(--clr-border);padding:2rem 0">
  <div class="container">
    <h1 class="section-title">Malagasy Dictionary</h1>
    <p class="text-muted">Search <?php
      try {
        $count = \App\Core\Database::getInstance()->count("SELECT COUNT(*) FROM words WHERE status='published'");
        echo number_format($count);
      } catch (\Throwable) { echo '…'; }
    ?> Malagasy words with definitions, translations, and dialect variants.</p>

    <!-- Search bar -->
    <form action="/search" method="GET" role="search" style="margin-top:1.5rem;max-width:560px" aria-label="Dictionary search">
      <div class="search-hero" style="max-width:100%">
        <input type="search" name="q" id="dict-search" class="search-hero__input" value="<?= e($query ?? '') ?>"
               placeholder="Search Malagasy, French, or English…" autocomplete="off"
               aria-label="Search dictionary" style="font-size:1rem;padding:.75rem 1rem .75rem 3rem">
        <span class="search-hero__icon"><i class="fa fa-search"></i></span>
        <button type="submit" class="search-hero__btn">Search</button>
      </div>
      <div id="autocomplete-dropdown" class="autocomplete hidden" role="listbox"></div>
    </form>
  </div>
</div>

<div class="section" style="padding-top:2rem">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 280px;gap:2.5rem;align-items:start">
      <div>
        <!-- Alphabet filter -->
        <div class="alpha-filter" role="group" aria-label="Browse by letter">
          <?php foreach ($alphabet as $l): ?>
          <a href="/dictionary?letter=<?= $l ?>" class="alpha-btn <?= ($letter ?? '') === $l ? 'active' : '' ?>" aria-label="Browse words starting with <?= $l ?>"><?= $l ?></a>
          <?php endforeach; ?>
        </div>

        <?php if (!empty($query)): ?>
          <p class="text-muted" style="margin-bottom:1.5rem"><strong><?= count($results) ?></strong> results for "<strong><?= e($query) ?></strong>"</p>
        <?php elseif (!empty($letter)): ?>
          <p class="text-muted" style="margin-bottom:1.5rem">Words starting with <strong><?= e($letter) ?></strong></p>
        <?php endif; ?>

        <?php if (!empty($results)): ?>
          <div class="grid grid-3" style="gap:1rem">
            <?php foreach ($results as $w): ?>
            <a href="/word/<?= e($w['slug']) ?>" class="word-card">
              <div class="word-card__word"><?= e($w['word']) ?></div>
              <?php if ($w['part_of_speech']): ?><div class="word-card__pos"><?= e($w['part_of_speech']) ?></div><?php endif; ?>
              <?php
                // Show first translation
                $t = is_string($w['translations'] ?? null) ? $w['translations'] : '';
                if ($t) {
                    preg_match('/fr:([^|]+)/', $t, $m);
                    if (!empty($m[1])) echo '<div class="word-card__def">' . e(truncate($m[1], 60)) . '</div>';
                }
              ?>
            </a>
            <?php endforeach; ?>
          </div>
        <?php elseif (isset($query) || isset($letter)): ?>
          <div style="text-align:center;padding:3rem;color:var(--clr-text-muted)">
            <i class="fa fa-search" style="font-size:3rem;opacity:.3;display:block;margin-bottom:1rem"></i>
            <p>No words found. <a href="/contribute" style="color:var(--clr-primary);font-weight:600">Be the first to add one!</a></p>
          </div>
        <?php else: ?>
          <div style="text-align:center;padding:3rem;color:var(--clr-text-muted)">
            <i class="fa fa-book-open" style="font-size:3rem;opacity:.3;display:block;margin-bottom:1rem"></i>
            <p>Use the search bar or click a letter above to browse the dictionary.</p>
          </div>
        <?php endif; ?>
      </div>

      <!-- Sidebar -->
      <aside>
        <?php if (!empty($mostViewed)): ?>
        <div class="card">
          <div class="card__body">
            <h3 style="font-size:1rem;margin-bottom:1rem"><i class="fa fa-fire" style="color:var(--clr-accent)"></i> Most Searched</h3>
            <?php foreach ($mostViewed as $w): ?>
            <a href="/word/<?= e($w['slug']) ?>" class="flex-between" style="padding:.5rem 0;border-bottom:1px solid var(--clr-border);text-decoration:none;color:inherit;font-size:.9rem;transition:color .15s" onmouseover="this.style.color='var(--clr-primary)'" onmouseout="this.style.color=''">
              <span style="font-weight:600"><?= e($w['word']) ?></span>
              <span style="font-size:.75rem;color:var(--clr-text-muted)"><?= number_format($w['view_count']) ?></span>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <div class="card" style="margin-top:1rem">
          <div class="card__body" style="text-align:center">
            <i class="fa fa-plus-circle" style="font-size:2.5rem;color:var(--clr-primary);display:block;margin-bottom:1rem"></i>
            <h3 style="font-size:1rem;margin-bottom:.5rem">Know a word?</h3>
            <p style="font-size:.85rem;color:var(--clr-text-muted);margin-bottom:1rem">Help expand the dictionary by contributing Malagasy words.</p>
            <a href="/contribute" class="btn btn-primary btn-sm btn-rounded w-full">Contribute a Word</a>
          </div>
        </div>
      </aside>
    </div>
  </div>
</div>
