<?php // Dialects Index — variables: $dialects ?>
<div style="background:var(--clr-bg-surface);border-bottom:1px solid var(--clr-border);padding:2.5rem 0">
  <div class="container">
    <h1 class="section-title" style="margin-bottom:.5rem">Malagasy Dialects</h1>
    <p class="text-muted" style="max-width:600px">
      Madagascar is home to 18 ethnic groups, each with unique dialect variations of the Malagasy language.
      Explore each dialect's region, vocabulary, and cultural identity.
    </p>
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
      <?php
        $dName   = $d['name']        ?? '';
        $dCode   = $d['code']        ?? '';
        $dRegion = $d['region']      ?? '';
        $dDesc   = $d['description'] ?? '';
        // Map region keywords to flag emojis
        $emoji = '🗺️';
        $rLow = strtolower($dRegion);
        if (strpos($rLow, 'nord') !== false || strpos($rLow, 'north') !== false)        $emoji = '🌍';
        elseif (strpos($rLow, 'sud') !== false || strpos($rLow, 'south') !== false)     $emoji = '🌟';
        elseif (strpos($rLow, 'est') !== false || strpos($rLow, 'east') !== false)      $emoji = '🌊';
        elseif (strpos($rLow, 'ouest') !== false || strpos($rLow, 'west') !== false)    $emoji = '⛰️';
        elseif (strpos($rLow, 'central') !== false || strpos($rLow, 'haut') !== false)  $emoji = '🏔️';
        elseif (strpos($rLow, 'côte') !== false || strpos($rLow, 'coast') !== false)    $emoji = '🌴';
      ?>
      <a href="/dialect/<?= e($dCode) ?>" class="card dialect-card animate-fade-up"
         style="animation-delay:<?= $i * .06 ?>s;text-decoration:none;display:block"
         aria-label="<?= e($dName) ?> dialect">
        <div class="card__body">
          <div style="font-size:2rem;margin-bottom:.75rem"><?= $emoji ?></div>
          <h2 style="font-size:1.1rem;font-weight:700;color:var(--clr-primary);margin:0 0 .25rem"><?= e($dName) ?></h2>
          <?php if ($dRegion): ?>
          <div style="font-size:.85rem;color:var(--clr-text-muted);margin-bottom:.5rem">
            <i class="fa fa-map-marker-alt"></i> <?= e($dRegion) ?>
          </div>
          <?php endif; ?>
          <?php if ($dDesc): ?>
          <p style="font-size:.875rem;color:var(--clr-text-muted);margin:.75rem 0 0;line-height:1.6">
            <?= e(truncate($dDesc, 100)) ?>
          </p>
          <?php endif; ?>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Madagascar overview -->
    <div style="margin-top:3rem;padding:2rem;background:var(--clr-bg-surface);border:1px solid var(--clr-border);border-radius:var(--radius-xl)">
      <h2 style="font-size:1.25rem;margin-bottom:1rem">
        <i class="fa fa-info-circle" style="color:var(--clr-primary)"></i> About Malagasy Dialects
      </h2>
      <p style="line-height:1.8;color:var(--clr-text-muted)">
        The Malagasy language, spoken by the 28 million people of Madagascar, belongs to the Austronesian
        language family — specifically the Malayo-Polynesian branch. While officially one language, Malagasy
        has significant regional variations across the island's 18 ethnic groups (<em>foko</em>).
        The official standard, <strong>Official Malagasy (Merina)</strong>, is based on the dialect of
        the central highlands.
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
