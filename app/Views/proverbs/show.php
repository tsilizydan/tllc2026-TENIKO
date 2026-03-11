<?php
// Proverb detail view
// Variables: $proverb, $similar
$csrfToken = \App\Core\CSRF::generate();
?>
<div class="container" style="padding-top:1.5rem">
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="/">Home</a><span class="breadcrumb__sep">/</span>
    <a href="/proverbs">Proverbs</a><span class="breadcrumb__sep">/</span>
    <span aria-current="page"><?= e(truncate($proverb['text'], 40)) ?></span>
  </nav>
</div>

<section class="section" style="padding-top:1.5rem">
  <div class="container" style="display:grid;grid-template-columns:1fr 300px;gap:2.5rem;align-items:start">

    <div>
      <!-- Proverb Text -->
      <div style="padding:2.5rem;background:var(--clr-beige);border-radius:var(--radius-xl);border-left:5px solid var(--clr-primary);margin-bottom:2rem">
        <div style="font-size:.75rem;text-transform:uppercase;letter-spacing:1px;color:var(--clr-text-muted);margin-bottom:1rem"><i class="fa fa-scroll" style="color:var(--clr-primary)"></i> Malagasy Proverb</div>
        <blockquote style="font-family:var(--font-heading);font-style:italic;font-size:1.75rem;color:var(--clr-charcoal);margin:0 0 1rem;line-height:1.4">
          "<?= e($proverb['text']) ?>"
        </blockquote>
        <?php if (!empty($proverb['dialect_name'])): ?>
          <span class="badge badge-beige"><i class="fa fa-map-marker-alt"></i> <?= e($proverb['dialect_name']) ?> dialect</span>
        <?php endif; ?>
      </div>

      <!-- Translations -->
      <?php if ($proverb['translation_fr'] || $proverb['translation_en']): ?>
      <div style="margin-bottom:1.5rem">
        <h2 style="font-size:1rem;font-weight:700;margin-bottom:.75rem;text-transform:uppercase;letter-spacing:.5px;color:var(--clr-text-muted)">Translations</h2>
        <?php if ($proverb['translation_fr']): ?>
          <div style="padding:.75rem 1rem;background:var(--clr-bg-surface);border-radius:var(--radius-md);margin-bottom:.5rem">
            <span style="font-size:.75rem;font-weight:700;color:var(--clr-text-muted)">🇫🇷 FRENCH</span>
            <p style="margin:.25rem 0 0;font-style:italic"><?= e($proverb['translation_fr']) ?></p>
          </div>
        <?php endif; ?>
        <?php if ($proverb['translation_en']): ?>
          <div style="padding:.75rem 1rem;background:var(--clr-bg-surface);border-radius:var(--radius-md)">
            <span style="font-size:.75rem;font-weight:700;color:var(--clr-text-muted)">🇬🇧 ENGLISH</span>
            <p style="margin:.25rem 0 0;font-style:italic"><?= e($proverb['translation_en']) ?></p>
          </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- Meaning -->
      <?php if ($proverb['meaning']): ?>
      <div style="margin-bottom:1.5rem">
        <h2 style="font-size:1rem;font-weight:700;margin-bottom:.75rem;text-transform:uppercase;letter-spacing:.5px;color:var(--clr-text-muted)">Meaning</h2>
        <p style="line-height:1.8;color:var(--clr-text)"><?= nl2br(e($proverb['meaning'])) ?></p>
      </div>
      <?php endif; ?>

      <!-- Cultural Explanation -->
      <?php if ($proverb['cultural_explanation']): ?>
      <div style="margin-bottom:1.5rem;padding:1.5rem;background:var(--clr-bg-surface);border-radius:var(--radius-lg)">
        <h2 style="font-size:1rem;font-weight:700;margin-bottom:.75rem"><i class="fa fa-landmark" style="color:var(--clr-primary)"></i> Cultural Context</h2>
        <p style="line-height:1.8"><?= nl2br(e($proverb['cultural_explanation'])) ?></p>
      </div>
      <?php endif; ?>

      <!-- Audio -->
      <?php if (!empty($proverb['audio'])): ?>
      <div style="margin-bottom:1.5rem">
        <?php foreach ($proverb['audio'] as $audio): ?>
        <div class="audio-player" data-src="/uploads/audio/<?= e($audio['filename']) ?>">
          <button class="audio-player__play" aria-label="Play"><i class="fa fa-play"></i></button>
          <div class="audio-player__label">Listen to pronunciation</div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- Share -->
      <div class="share-bar">
        <span class="share-bar__label">Share:</span>
        <button class="btn btn-sm btn-ghost" onclick="copyToClipboard(<?= json_encode($proverb['text']) ?>, this)"><i class="fa fa-copy"></i> Copy</button>
        <button class="btn btn-sm btn-ghost" onclick="shareEntry('Malagasy Proverb on TENIKO', window.location.href)"><i class="fa fa-share-alt"></i> Share</button>
        <button class="btn btn-sm btn-ghost" onclick="copyToClipboard(window.location.href, this)"><i class="fa fa-link"></i> Link</button>
      </div>
    </div>

    <!-- Sidebar -->
    <aside>
      <?php if (!empty($similar)): ?>
      <div class="card">
        <div class="card__body">
          <h3 style="font-size:1rem;margin-bottom:1rem">More Proverbs</h3>
          <?php foreach ($similar as $s): ?>
          <a href="/proverb/<?= $s['id'] ?>" style="display:block;padding:.75rem 0;border-bottom:1px solid var(--clr-border);text-decoration:none;color:inherit;font-size:.875rem;font-style:italic;transition:color .15s" onmouseover="this.style.color='var(--clr-primary)'" onmouseout="this.style.color=''">
            "<?= e(truncate($s['text'], 60)) ?>"
          </a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
      <div style="margin-top:1rem;text-align:center">
        <a href="/proverbs" class="btn btn-outline btn-sm w-full"><i class="fa fa-arrow-left"></i> All Proverbs</a>
        <a href="/contribute" class="btn btn-primary btn-sm w-full" style="margin-top:.5rem"><i class="fa fa-plus"></i> Submit a Proverb</a>
      </div>
    </aside>
  </div>
</section>
