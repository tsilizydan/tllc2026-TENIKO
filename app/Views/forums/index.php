<?php // Forums Index — variables: $forums (array of forum categories with topic_count/post_count) ?>
<div style="background:var(--clr-bg-surface);border-bottom:1px solid var(--clr-border);padding:2rem 0">
  <div class="container">
    <h1 class="section-title">Community Forums</h1>
    <p class="text-muted" style="margin-top:.5rem">Discuss Malagasy language, culture, dialects, and linguistics with the community.</p>
  </div>
</div>

<section class="section" style="padding-top:2rem">
  <div class="container" style="display:grid;grid-template-columns:1fr 280px;gap:2.5rem;align-items:start">
    <div>
      <?php if (empty($forums)): ?>
      <div style="text-align:center;padding:4rem;color:var(--clr-text-muted)">
        <i class="fa fa-comments" style="font-size:3rem;opacity:.3;display:block;margin-bottom:1rem"></i>
        <p>No forum categories set up yet.</p>
      </div>
      <?php else: ?>
      <?php foreach ($forums as $forum): ?>
      <div class="forum-card" style="background:var(--clr-bg-card);border:1px solid var(--clr-border);border-radius:var(--radius-lg);margin-bottom:1rem;overflow:hidden">
        <a href="/forum/<?= e($forum['slug']) ?>" style="display:flex;align-items:center;gap:1.25rem;padding:1.5rem;text-decoration:none;color:inherit;transition:background .15s" onmouseover="this.style.background='var(--clr-bg-surface)'" onmouseout="this.style.background=''">
          <div style="width:52px;height:52px;background:linear-gradient(135deg,var(--clr-primary),var(--clr-primary-dk));border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa fa-<?= e($forum['icon'] ?? 'comments-alt') ?>" style="color:white;font-size:1.3rem"></i>
          </div>
          <div style="flex:1;min-width:0">
            <h2 style="font-size:1.05rem;font-weight:700;margin:0 0 .25rem;color:var(--clr-text)"><?= e($forum['name']) ?></h2>
            <?php if ($forum['description']): ?><p style="font-size:.875rem;color:var(--clr-text-muted);margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e($forum['description']) ?></p><?php endif; ?>
          </div>
          <div style="text-align:center;min-width:80px">
            <div style="font-size:1.25rem;font-weight:700;color:var(--clr-primary)"><?= number_format($forum['topic_count'] ?? 0) ?></div>
            <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;color:var(--clr-text-muted)">Topics</div>
          </div>
          <div style="text-align:center;min-width:80px">
            <div style="font-size:1.25rem;font-weight:700;color:var(--clr-charcoal)"><?= number_format($forum['post_count'] ?? 0) ?></div>
            <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;color:var(--clr-text-muted)">Posts</div>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <aside>
      <?php if (\App\Core\Auth::check()): ?>
      <div class="card" style="margin-bottom:1rem">
        <div class="card__body" style="text-align:center">
          <i class="fa fa-plus-circle" style="font-size:2rem;color:var(--clr-primary);display:block;margin-bottom:.75rem"></i>
          <h3 style="font-size:1rem;margin-bottom:.5rem">Start a Discussion</h3>
          <p style="font-size:.85rem;color:var(--clr-text-muted);margin-bottom:1rem">Have a question about Malagasy?</p>
          <a href="/forums" class="btn btn-primary btn-sm w-full" style="pointer-events:auto">View Forums to Post</a>
        </div>
      </div>
      <?php else: ?>
      <div class="card" style="margin-bottom:1rem;text-align:center">
        <div class="card__body">
          <p style="font-size:.9rem;margin-bottom:1rem">Log in to participate in forums</p>
          <a href="/login"    class="btn btn-primary btn-sm w-full"  style="margin-bottom:.5rem">Login</a>
          <a href="/register" class="btn btn-outline btn-sm w-full">Register Free</a>
        </div>
      </div>
      <?php endif; ?>
      <div class="card">
        <div class="card__body">
          <h3 style="font-size:.9rem;font-weight:700;margin-bottom:.75rem;color:var(--clr-text-muted)">FORUM RULES</h3>
          <ol style="font-size:.875rem;line-height:1.8;padding-left:1.25rem;color:var(--clr-text)">
            <li>Be respectful and kind</li>
            <li>Stay on topic</li>
            <li>No spam or self-promotion</li>
            <li>Use appropriate language</li>
            <li>Cite your sources</li>
          </ol>
        </div>
      </div>
    </aside>
  </div>
</section>
