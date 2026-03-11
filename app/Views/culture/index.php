<?php // Culture / Articles index — variables: $articles (paged), $categories, $featured ?>
<div style="background:var(--clr-bg-surface);border-bottom:1px solid var(--clr-border);padding:2rem 0">
  <div class="container">
    <h1 class="section-title">Malagasy Culture & Knowledge</h1>
    <p class="text-muted" style="margin-top:.5rem">Explore traditions, history, folklore, and linguistic research about Madagascar.</p>

    <!-- Category tabs -->
    <?php if (!empty($categories)): ?>
    <div class="flex flex-wrap" style="gap:.5rem;margin-top:1.25rem" role="tablist">
      <a href="/culture" class="btn btn-sm <?= empty($category) ? 'btn-primary' : 'btn-ghost' ?>" role="tab" aria-selected="<?= empty($category) ? 'true' : 'false' ?>">All</a>
      <?php foreach ($categories as $cat): ?>
      <a href="/culture?category=<?= $cat['id'] ?>" class="btn btn-sm <?= ($category ?? null) == $cat['id'] ? 'btn-primary' : 'btn-ghost' ?>" role="tab"><?= e($cat['name']) ?></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<section class="section" style="padding-top:2rem">
  <div class="container">
    <?php $items = $articles['items'] ?? []; ?>
    <?php if (empty($items)): ?>
      <div style="text-align:center;padding:4rem;color:var(--clr-text-muted)">
        <i class="fa fa-landmark" style="font-size:3rem;opacity:.3;display:block;margin-bottom:1rem"></i>
        <p>No articles found in this category yet.</p>
      </div>
    <?php else: ?>
    <div class="grid grid-3">
      <?php foreach ($items as $i => $a): ?>
      <a href="/article/<?= e($a['slug']) ?>" class="card article-card animate-fade-up" style="animation-delay:<?= $i % 6 * .05 ?>s;text-decoration:none">
        <?php if ($a['cover_image']): ?>
          <img class="card__img" src="<?= e($a['cover_image']) ?>" alt="<?= e($a['title']) ?>" loading="lazy">
        <?php else: ?>
          <div class="card__img" style="background:linear-gradient(135deg,var(--clr-beige),#d5c4a1);display:flex;align-items:center;justify-content:center">
            <i class="fa fa-landmark" style="font-size:2.5rem;color:var(--clr-brown);opacity:.4"></i>
          </div>
        <?php endif; ?>
        <div class="card__body">
          <span class="article-card__type"><?= e($a['type'] ?? $a['category_name'] ?? 'Article') ?></span>
          <h2 class="article-card__title" style="font-size:1rem"><?= e($a['title']) ?></h2>
          <?php if ($a['excerpt']): ?><p class="article-card__excerpt"><?= e(truncate($a['excerpt'], 100)) ?></p><?php endif; ?>
          <div style="font-size:.75rem;color:var(--clr-text-muted);margin-top:.75rem"><?= formatDate($a['created_at']) ?></div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php $last = $articles['last_page'] ?? 1; $page = $articles['current_page'] ?? 1; if ($last > 1): ?>
    <nav class="pagination" aria-label="Articles pagination">
      <?php if ($page > 1): ?><a class="pagination__item" href="/culture?page=<?= $page - 1 ?><?= isset($category) ? '&category=' . $category : '' ?>">‹ Prev</a><?php endif; ?>
      <?php for ($i = max(1, $page - 2); $i <= min($last, $page + 2); $i++): ?>
        <a class="pagination__item <?= $i === $page ? 'active' : '' ?>" href="/culture?page=<?= $i ?><?= isset($category) ? '&category=' . $category : '' ?>"><?= $i ?></a>
      <?php endfor; ?>
      <?php if ($page < $last): ?><a class="pagination__item" href="/culture?page=<?= $page + 1 ?><?= isset($category) ? '&category=' . $category : '' ?>">Next ›</a><?php endif; ?>
    </nav>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</section>
