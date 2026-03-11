<?php // Forum show — variables: $forum, $topics (paged), $page
$isLoggedIn = \App\Core\Auth::check();
$csrfToken  = \App\Core\CSRF::generate();
$items = $topics['items'] ?? [];
$last  = $topics['last_page'] ?? 1;
$cur   = $topics['current_page'] ?? 1;
?>
<div class="container" style="padding-top:1.5rem">
  <nav class="breadcrumb"><a href="/">Home</a><span class="breadcrumb__sep">/</span><a href="/forums">Forums</a><span class="breadcrumb__sep">/</span><span><?= e($forum['name']) ?></span></nav>
</div>
<section class="section" style="padding-top:1.5rem">
  <div class="container">
    <div class="flex-between" style="margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
      <div>
        <h1 style="font-size:1.75rem;font-family:var(--font-heading)"><?= e($forum['name']) ?></h1>
        <?php if ($forum['description']): ?><p style="color:var(--clr-text-muted)"><?= e($forum['description']) ?></p><?php endif; ?>
      </div>
      <?php if ($isLoggedIn): ?>
      <button onclick="document.getElementById('new-topic-modal').classList.add('open')" class="btn btn-primary"><i class="fa fa-plus"></i> New Topic</button>
      <?php endif; ?>
    </div>

    <?php if (empty($items)): ?>
      <div style="text-align:center;padding:3rem;color:var(--clr-text-muted)"><i class="fa fa-comments" style="font-size:3rem;opacity:.3;display:block;margin-bottom:1rem"></i><p>No topics yet. Be the first to start a discussion!</p></div>
    <?php else: ?>
    <div style="background:var(--clr-bg-card);border:1px solid var(--clr-border);border-radius:var(--radius-lg);overflow:hidden">
      <?php foreach ($items as $t): ?>
      <a href="/topic/<?= $t['id'] ?>" style="display:flex;align-items:center;gap:1.25rem;padding:1.25rem 1.5rem;border-bottom:1px solid var(--clr-border);text-decoration:none;color:inherit;transition:background .15s" onmouseover="this.style.background='var(--clr-bg-surface)'" onmouseout="this.style.background=''">
        <div style="width:40px;height:40px;background:var(--clr-bg-surface);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1px solid var(--clr-border)">
          <i class="fa fa-comment-alt" style="font-size:.875rem;color:var(--clr-primary)"></i>
        </div>
        <div style="flex:1;min-width:0">
          <div style="font-weight:600;margin-bottom:.25rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e($t['title']) ?></div>
          <div style="font-size:.8rem;color:var(--clr-text-muted)">by <?= e($t['display_name'] ?? $t['username'] ?? 'User') ?> · <?= timeAgo($t['created_at']) ?></div>
        </div>
        <div style="text-align:center;min-width:60px">
          <div style="font-weight:700"><?= number_format($t['reply_count'] ?? 0) ?></div>
          <div style="font-size:.7rem;color:var(--clr-text-muted)">replies</div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

    <?php if ($last > 1): ?>
    <nav class="pagination">
      <?php if ($cur > 1): ?><a class="pagination__item" href="/forum/<?= e($forum['slug']) ?>?page=<?= $cur - 1 ?>">‹</a><?php endif; ?>
      <?php for ($i = max(1, $cur - 2); $i <= min($last, $cur + 2); $i++): ?><a class="pagination__item <?= $i === $cur ? 'active' : '' ?>" href="/forum/<?= e($forum['slug']) ?>?page=<?= $i ?>"><?= $i ?></a><?php endfor; ?>
      <?php if ($cur < $last): ?><a class="pagination__item" href="/forum/<?= e($forum['slug']) ?>?page=<?= $cur + 1 ?>">›</a><?php endif; ?>
    </nav>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<!-- New Topic Modal -->
<?php if ($isLoggedIn): ?>
<div id="new-topic-modal" class="modal" role="dialog" aria-modal="true" aria-label="New Topic">
  <div class="modal__backdrop" onclick="this.parentElement.classList.remove('open')"></div>
  <div class="modal__panel">
    <div class="modal__header">
      <h2 class="modal__title">Start a New Topic</h2>
      <button class="btn-icon btn-ghost" onclick="document.getElementById('new-topic-modal').classList.remove('open')" aria-label="Close"><i class="fa fa-times"></i></button>
    </div>
    <form action="/topic/create" method="POST">
      <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
      <input type="hidden" name="forum_id" value="<?= $forum['id'] ?>">
      <div class="form-group">
        <label class="form-label" for="topic-title">Topic Title <span style="color:red">*</span></label>
        <input type="text" id="topic-title" name="title" class="form-control" required minlength="5" maxlength="255" placeholder="What's your question or topic?">
      </div>
      <div class="form-group">
        <label class="form-label" for="topic-body">Your Message <span style="color:red">*</span></label>
        <textarea id="topic-body" name="body" class="form-control" rows="7" required minlength="10" placeholder="Describe your topic in detail…"></textarea>
      </div>
      <div style="display:flex;gap:.75rem;justify-content:flex-end">
        <button type="button" class="btn btn-ghost" onclick="document.getElementById('new-topic-modal').classList.remove('open')">Cancel</button>
        <button type="submit" class="btn btn-primary">Post Topic <i class="fa fa-paper-plane"></i></button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>
