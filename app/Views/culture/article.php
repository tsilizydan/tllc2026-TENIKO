<?php // Article detail — variables: $article, $related, $comments, $reactions
$csrfToken = \App\Core\CSRF::generate(); ?>
<div class="container" style="padding-top:1.5rem">
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="/">Home</a><span class="breadcrumb__sep">/</span>
    <a href="/culture">Culture</a><span class="breadcrumb__sep">/</span>
    <span aria-current="page"><?= e(truncate($article['title'], 50)) ?></span>
  </nav>
</div>
<div class="section" style="padding-top:1.5rem">
  <div class="container" style="display:grid;grid-template-columns:1fr 280px;gap:2.5rem;align-items:start">
    <article>
      <?php if ($article['cover_image']): ?>
        <img src="<?= e($article['cover_image']) ?>" alt="<?= e($article['title']) ?>" style="width:100%;max-height:400px;object-fit:cover;border-radius:var(--radius-xl);margin-bottom:2rem">
      <?php endif; ?>
      <span class="article-card__type"><?= e($article['type'] ?? 'Article') ?></span>
      <h1 style="font-family:var(--font-heading);font-size:2.25rem;margin:.5rem 0 1rem;line-height:1.25"><?= e($article['title']) ?></h1>
      <div style="color:var(--clr-text-muted);font-size:.875rem;margin-bottom:2rem">
        <?php if ($article['author_name']): ?><span><i class="fa fa-user"></i> <?= e($article['author_name']) ?></span> · <?php endif; ?>
        <span><i class="fa fa-calendar"></i> <?= formatDate($article['published_at'] ?? $article['created_at']) ?></span>
      </div>
      <div style="font-size:1.05rem;line-height:1.9;color:var(--clr-text)">
        <?= $article['body'] ? nl2br(e($article['body'])) : '<p>Content coming soon.</p>' ?>
      </div>
      <div class="share-bar" style="margin-top:2rem">
        <span class="share-bar__label">Share:</span>
        <button class="btn btn-sm btn-ghost" onclick="shareEntry(<?= json_encode($article['title']) ?>, window.location.href)"><i class="fa fa-share-alt"></i> Share</button>
        <button class="btn btn-sm btn-ghost" onclick="copyToClipboard(window.location.href, this)"><i class="fa fa-link"></i> Copy Link</button>
      </div>
    </article>

    <aside>
      <?php if (!empty($related)): ?>
      <div class="card" style="margin-bottom:1rem">
        <div class="card__body">
          <h3 style="font-size:1rem;margin-bottom:1rem">Related Articles</h3>
          <?php foreach ($related as $r): ?>
          <a href="/article/<?= e($r['slug']) ?>" style="display:flex;gap:.75rem;padding:.75rem 0;border-bottom:1px solid var(--clr-border);text-decoration:none;color:inherit;align-items:flex-start" onmouseover="this.style.color='var(--clr-primary)'" onmouseout="this.style.color=''">
            <div style="flex:1;font-size:.875rem;font-weight:600;line-height:1.4"><?= e(truncate($r['title'], 60)) ?></div>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
      <a href="/culture" class="btn btn-outline btn-sm w-full"><i class="fa fa-arrow-left"></i> All Articles</a>
    </aside>
  </div>
</div>
