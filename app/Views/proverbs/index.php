<!-- Proverbs Index View -->
<?php
$page    = $paged['current_page'] ?? 1;
$total   = $paged['total'] ?? 0;
$perPage = $paged['per_page'] ?? 20;
$last    = $paged['last_page'] ?? 1;
$items   = $paged['items'] ?? [];
?>

<div style="background:var(--clr-bg-surface);border-bottom:1px solid var(--clr-border);padding:2rem 0">
  <div class="container">
    <h1 class="section-title">Ohabolana — Malagasy Proverbs</h1>
    <p class="text-muted" style="margin-top:.5rem">Ancient wisdom passed down through generations. <?= number_format($total) ?> proverbs and counting.</p>

    <!-- Dialect filter -->
    <?php if (!empty($dialects)): ?>
    <form method="GET" action="/proverbs" style="margin-top:1rem;display:flex;gap:.75rem;flex-wrap:wrap;align-items:center">
      <label style="font-size:.875rem;font-weight:600;color:var(--clr-text-muted)">Filter by dialect:</label>
      <select name="dialect" onchange="this.form.submit()" class="form-control" style="width:auto;min-width:180px">
        <option value="">All Dialects</option>
        <?php foreach ($dialects as $d): ?>
        <option value="<?= $d['id'] ?>" <?= ($dialect ?? 0) == $d['id'] ? 'selected' : '' ?>><?= e($d['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <?php if (!empty($dialect)): ?><a href="/proverbs" class="btn btn-ghost btn-sm">Clear</a><?php endif; ?>
    </form>
    <?php endif; ?>
  </div>
</div>

<section class="section" style="padding-top:2rem">
  <div class="container">
    <?php if (empty($items)): ?>
    <div style="text-align:center;padding:4rem;color:var(--clr-text-muted)">
      <i class="fa fa-scroll" style="font-size:3rem;opacity:.3;display:block;margin-bottom:1rem"></i>
      <p>No proverbs found. <a href="/contribute" style="color:var(--clr-primary);font-weight:600">Be the first to submit one!</a></p>
    </div>
    <?php else: ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:1.5rem">
      <?php foreach ($items as $i => $p): ?>
      <a href="/proverb/<?= $p['id'] ?>" class="proverb-card animate-fade-up" style="animation-delay:<?= $i * .03 ?>s;display:block;text-decoration:none" aria-label="<?= e(truncate($p['text'], 60)) ?>">
        <div class="proverb-card__text">"<?= e($p['text']) ?>"</div>
        <?php if ($p['translation_fr']): ?>
          <div class="proverb-card__trans">🇫🇷 <?= e(truncate($p['translation_fr'], 80)) ?></div>
        <?php endif; ?>
        <?php if (!empty($p['dialect_name'])): ?>
          <div style="margin-top:.75rem"><span class="badge badge-beige"><i class="fa fa-map-marker-alt"></i> <?= e($p['dialect_name']) ?></span></div>
        <?php endif; ?>
      </a>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($last > 1): ?>
    <nav class="pagination" aria-label="Proverbs pagination">
      <?php if ($page > 1): ?><a class="pagination__item" href="/proverbs?page=<?= $page - 1 ?><?= $dialect ? '&dialect=' . $dialect : '' ?>">‹ Prev</a><?php endif; ?>
      <?php for ($i = max(1, $page - 2); $i <= min($last, $page + 2); $i++): ?>
        <a class="pagination__item <?= $i === $page ? 'active' : '' ?>" href="/proverbs?page=<?= $i ?><?= $dialect ? '&dialect=' . $dialect : '' ?>"><?= $i ?></a>
      <?php endfor; ?>
      <?php if ($page < $last): ?><a class="pagination__item" href="/proverbs?page=<?= $page + 1 ?><?= $dialect ? '&dialect=' . $dialect : '' ?>">Next ›</a><?php endif; ?>
    </nav>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</section>
