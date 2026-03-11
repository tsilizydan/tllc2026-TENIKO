<?php // Topic detail — variables: $topic, $posts (paged), $forum
$isLoggedIn = \App\Core\Auth::check();
$csrfToken  = \App\Core\CSRF::generate();
$items = $posts['items'] ?? [];
$last  = $posts['last_page'] ?? 1;
$cur   = $posts['current_page'] ?? 1;
?>
<div class="container" style="padding-top:1.5rem">
  <nav class="breadcrumb"><a href="/">Home</a><span class="breadcrumb__sep">/</span><a href="/forums">Forums</a><span class="breadcrumb__sep">/</span><a href="/forum/<?= e($forum['slug'] ?? '') ?>"><?= e($forum['name'] ?? 'Forum') ?></a><span class="breadcrumb__sep">/</span><span><?= e(truncate($topic['title'], 50)) ?></span></nav>
</div>
<section class="section" style="padding-top:1.5rem">
  <div class="container container-narrow">
    <h1 style="font-family:var(--font-heading);margin-bottom:1.5rem"><?= e($topic['title']) ?></h1>

    <?php foreach ($items as $post): ?>
    <div class="forum-post" style="background:var(--clr-bg-card);border:1px solid var(--clr-border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:1rem;display:grid;grid-template-columns:80px 1fr;gap:1.25rem">
      <div style="text-align:center">
        <div style="width:48px;height:48px;background:var(--clr-primary);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;margin:0 auto .5rem"><?= strtoupper(substr($post['display_name'] ?? $post['username'] ?? 'U', 0, 1)) ?></div>
        <div style="font-size:.75rem;font-weight:600;color:var(--clr-text-muted)"><?= e($post['display_name'] ?? $post['username'] ?? 'User') ?></div>
        <div style="font-size:.65rem;color:var(--clr-text-muted)"><?= e($post['role'] ?? 'member') ?></div>
      </div>
      <div>
        <div style="font-size:.75rem;color:var(--clr-text-muted);margin-bottom.75rem"><?= timeAgo($post['created_at']) ?></div>
        <div style="line-height:1.8;margin-top:.5rem"><?= nl2br(e($post['body'])) ?></div>
      </div>
    </div>
    <?php endforeach; ?>

    <?php if ($last > 1): ?>
    <nav class="pagination">
      <?php if ($cur > 1): ?><a class="pagination__item" href="/topic/<?= $topic['id'] ?>?page=<?= $cur - 1 ?>">‹</a><?php endif; ?>
      <?php for ($i = max(1, $cur - 2); $i <= min($last, $cur + 2); $i++): ?><a class="pagination__item <?= $i === $cur ? 'active' : '' ?>" href="/topic/<?= $topic['id'] ?>?page=<?= $i ?>"><?= $i ?></a><?php endfor; ?>
      <?php if ($cur < $last): ?><a class="pagination__item" href="/topic/<?= $topic['id'] ?>?page=<?= $cur + 1 ?>">›</a><?php endif; ?>
    </nav>
    <?php endif; ?>

    <!-- Reply form -->
    <?php if ($isLoggedIn): ?>
    <div style="margin-top:2rem">
      <h2 style="font-size:1.1rem;margin-bottom:1rem">Post a Reply</h2>
      <form action="/post/reply" method="POST">
        <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
        <input type="hidden" name="topic_id" value="<?= $topic['id'] ?>">
        <div class="form-group">
          <textarea name="body" class="form-control" rows="5" required minlength="5" maxlength="5000" placeholder="Share your thoughts or knowledge…"></textarea>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fa fa-reply"></i> Post Reply</button>
      </form>
    </div>
    <?php else: ?>
    <div class="alert alert-info" style="margin-top:1.5rem"><i class="fa fa-info-circle"></i> <a href="/login" style="color:var(--clr-info);font-weight:600">Log in</a> to reply to this topic.</div>
    <?php endif; ?>
  </div>
</section>
