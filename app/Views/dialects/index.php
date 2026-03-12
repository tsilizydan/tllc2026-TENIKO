<?php // Dialects Index — variables: $dialects ?>
<div style="background:var(--clr-bg-surface);border-bottom:1px solid var(--clr-border);padding:2rem 0">
  <div class="container">
    <h1 class="section-title">Malagasy Dialects</h1>
    <p class="text-muted" style="margin-top:.5rem">Madagascar is home to 18 ethnic groups, each with unique dialect variations of the Malagasy language.</p>
  </div>
</div>

<section class="section" style="padding-top:2rem">
  <div class="container">
    <?php if (empty($dialects)): ?>
    <div style="text-align:center;padding:4rem;color:var(--clr-text-muted)">
      <i class="fa fa-map" style="font-size:3rem;opacity:.3;display:block;margin-bottom:1rem"></i>
      <p>Dialect data coming soon.</p>
    </div>
    <?php else: ?>
    <div class="grid grid-3">
      <?php foreach ($dialects as $i => $d): ?>
      <a href="/dialect/<?= e($d['code']) ?>" class="card dialect-card animate-fade-up" style="animation-delay:<?= $i * .04 ?>s;text-decoration:none" aria-label="<?= e($d['name']) ?> dialect">
        <div class="card__body">
          <div style="font-size:2rem;margin-bottom:.75rem"><?= $d['emoji'] ?? '🗺️' ?></div>
          <h2 style="font-size:1.1rem;font-weight:700;color:var(--clr-primary);margin:0 0 .25rem"><?= e($d['name']) ?></h2>
          <?php if ($d['region']): ?><div style="font-size:.85rem;color:var(--clr-text-muted);margin-bottom:.5rem"><i class="fa fa-map-marker-alt"></i> <?= e($d['region']) ?></div><?php endif; ?>
          <?php if ($d['speaker_count']): ?><div style="font-size:.8rem;color:var(--clr-text-muted)"><?= number_format($d['speaker_count']) ?> speakers</div><?php endif; ?>
          <?php if ($d['description']): ?><p style="font-size:.875rem;color:var(--clr-text-muted);margin:.75rem 0 0;line-height:1.6"><?= e(truncate($d['description'], 100)) ?></p><?php endif; ?>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Madagascar overview -->
    <div style="margin-top:3rem;padding:2rem;background:var(--clr-beige);border-radius:var(--radius-xl);">
      <h2 style="font-size:1.25rem;margin-bottom:1rem"><i class="fa fa-info-circle" style="color:var(--clr-primary)"></i> About Malagasy Dialects</h2>
      <p style="line-height:1.8;color:var(--clr-charcoal)">
        The Malagasy language, spoken by the 28 million people of Madagascar, belongs to the Austronesian language family — specifically the Malayo-Polynesian branch.
        While officially one language, Malagasy has significant regional variations across the island's 18 ethnic groups (<em>foko</em>).
        The official standard, <strong>Official Malagasy (Merina)</strong>, is based on the dialect of the central highlands.
      </p>
    </div>
  </div>
</section>

<!-- Donate CTA strip -->
<div class="container" style="padding-bottom:3rem">
  <div class="donate-strip">
    <div class="donate-strip__icon"><i class="fa fa-globe"></i></div>
    <div class="donate-strip__text">
      <strong>Help us map every Malagasy dialect.</strong>
      Support our mission to document, preserve, and share every regional variation of Malagasy.
    </div>
    <a href="/donate" class="btn btn-primary btn-sm">Donate</a>
  </div>
</div>
