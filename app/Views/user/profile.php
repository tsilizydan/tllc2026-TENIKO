<?php // User profile — $user (public data with badges)
$authUser = \App\Core\Auth::user();
$isOwn    = $authUser && $authUser['id'] === ($user['id'] ?? null);
?>
<section class="section" style="padding-top:2rem">
  <div class="container" style="display:grid;grid-template-columns:280px 1fr;gap:2.5rem;align-items:start">
    <!-- Profile sidebar -->
    <div>
      <div class="card" style="text-align:center;padding:2rem 1.5rem">
        <?php if (!empty($user['avatar'])): ?>
          <img src="<?= e($user['avatar']) ?>" alt="<?= e($user['display_name'] ?? $user['username']) ?>'s avatar" style="width:96px;height:96px;border-radius:50%;object-fit:cover;margin-bottom:1rem;border:3px solid var(--clr-border)">
        <?php else: ?>
          <div style="width:96px;height:96px;background:var(--clr-primary);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2.5rem;font-weight:700;margin:0 auto 1rem">
            <?= strtoupper(substr($user['display_name'] ?? $user['username'], 0, 1)) ?>
          </div>
        <?php endif; ?>
        <h1 style="font-size:1.25rem;margin:0 0 .25rem"><?= e($user['display_name'] ?? $user['username']) ?></h1>
        <div style="font-size:.85rem;color:var(--clr-text-muted);margin-bottom:.75rem">@<?= e($user['username']) ?></div>
        <span class="badge <?= $user['role'] === 'admin' ? 'badge-red' : ($user['role'] === 'moderator' ? 'badge-beige' : 'badge-green') ?>"><?= ucfirst($user['role']) ?></span>
        <?php if ($user['bio']): ?>
          <p style="font-size:.875rem;color:var(--clr-text-muted);line-height:1.6;margin:1rem 0 0"><?= e($user['bio']) ?></p>
        <?php endif; ?>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-top:1.25rem;text-align:center">
          <div style="padding:.75rem;background:var(--clr-bg-surface);border-radius:var(--radius-md)">
            <div style="font-size:1.5rem;font-weight:700;color:var(--clr-primary)"><?= number_format($user['word_contributions'] ?? 0) ?></div>
            <div style="font-size:.7rem;text-transform:uppercase;color:var(--clr-text-muted)">Words</div>
          </div>
          <div style="padding:.75rem;background:var(--clr-bg-surface);border-radius:var(--radius-md)">
            <div style="font-size:1.5rem;font-weight:700;color:var(--clr-primary)"><?= number_format($user['proverb_contributions'] ?? 0) ?></div>
            <div style="font-size:.7rem;text-transform:uppercase;color:var(--clr-text-muted)">Proverbs</div>
          </div>
          <div style="padding:.75rem;background:var(--clr-bg-surface);border-radius:var(--radius-md);grid-column:1/-1">
            <div style="font-size:1.5rem;font-weight:700;color:var(--clr-charcoal)"><?= number_format($user['reputation'] ?? 0) ?></div>
            <div style="font-size:.7rem;text-transform:uppercase;color:var(--clr-text-muted)">Reputation Points</div>
          </div>
        </div>
        <div style="font-size:.75rem;color:var(--clr-text-muted);margin-top:1rem">Member since <?= formatDate($user['created_at'], 'M Y') ?></div>
        <?php if ($isOwn): ?>
          <a href="/settings" class="btn btn-outline btn-sm w-full" style="margin-top:1rem"><i class="fa fa-cog"></i> Edit Profile</a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Profile main -->
    <div>
      <!-- Badges -->
      <?php if (!empty($user['badges'])): ?>
      <div class="card" style="margin-bottom:1.5rem">
        <div class="card__body">
          <h2 style="font-size:1rem;margin-bottom:1rem"><i class="fa fa-medal" style="color:var(--clr-primary)"></i> Badges Earned</h2>
          <div class="flex flex-wrap" style="gap:.75rem">
            <?php foreach ($user['badges'] as $badge): ?>
            <div class="badge-item" title="<?= e($badge['description'] ?? '') ?>">
              <?php if ($badge['icon']): ?><span style="font-size:1.25rem"><?= e($badge['icon']) ?></span><?php endif; ?>
              <span class="badge badge-beige"><?= e($badge['name']) ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <div class="card">
        <div class="card__body" style="text-align:center;padding:3rem">
          <i class="fa fa-book-open" style="font-size:2.5rem;color:var(--clr-gray-300);display:block;margin-bottom:1rem"></i>
          <p style="color:var(--clr-text-muted)">This user's contribution history will appear here soon.</p>
        </div>
      </div>
    </div>
  </div>
</section>
