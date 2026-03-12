<?php // Dialect detail — variables: $dialect (array with word_variants) ?>
<div class="container" style="padding-top:1.5rem">
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="/">Home</a><span class="breadcrumb__sep">/</span>
    <a href="/dialects">Dialects</a><span class="breadcrumb__sep">/</span>
    <span aria-current="page"><?= e($dialect['name'] ?? '') ?></span>
  </nav>
</div>

<section class="section" style="padding-top:1.5rem">
  <div class="container">
    <?php
      $dName    = $dialect['name']        ?? 'Unknown Dialect';
      $dRegion  = $dialect['region']      ?? '';
      $dDesc    = $dialect['description'] ?? '';
      $dCode    = $dialect['code']        ?? '';
      $dVariants = $dialect['word_variants'] ?? [];
    ?>
    <div class="dialect-layout">
      <!-- Main Column -->
      <div class="dialect-layout__main">
        <div style="margin-bottom:2rem">
          <h1 style="font-family:var(--font-heading);font-size:clamp(1.75rem,4vw,2.5rem);color:var(--clr-primary);margin:.5rem 0">
            <?= e($dName) ?>
          </h1>
          <?php if ($dRegion): ?>
          <p style="color:var(--clr-text-muted);margin-bottom:.25rem">
            <i class="fa fa-map-marker-alt"></i> <?= e($dRegion) ?>
          </p>
          <?php endif; ?>
          <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-top:.75rem">
            <span class="badge badge-green"><i class="fa fa-language"></i> Malagasy Dialect</span>
            <?php if ($dCode): ?>
            <span class="badge badge-beige"><i class="fa fa-tag"></i> <?= e(strtoupper($dCode)) ?></span>
            <?php endif; ?>
          </div>
        </div>

        <?php if ($dDesc): ?>
        <div style="background:var(--clr-bg-surface);border-left:3px solid var(--clr-primary);padding:1.25rem 1.5rem;border-radius:0 var(--radius-lg) var(--radius-lg) 0;margin-bottom:2rem">
          <p style="line-height:1.9;font-size:1.05rem;margin:0;color:var(--clr-text)"><?= nl2br(e($dDesc)) ?></p>
        </div>
        <?php endif; ?>

        <!-- Word Variants -->
        <?php if (!empty($dVariants)): ?>
        <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:1rem">
          <i class="fa fa-book" style="color:var(--clr-primary)"></i>
          Word Variants in <?= e($dName) ?>
        </h2>
        <div class="table-wrap" style="border-radius:var(--radius-lg);overflow:hidden;border:1px solid var(--clr-border)">
          <table class="table" role="grid" style="margin:0">
            <thead>
              <tr>
                <th>Standard Form</th>
                <th><?= e($dName) ?> Variant</th>
                <th>Notes</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($dVariants as $v): ?>
              <tr>
                <td>
                  <a href="/word/<?= e($v['word_slug'] ?? '') ?>"
                     style="color:var(--clr-primary);font-weight:600">
                    <?= e($v['standard_word'] ?? '') ?>
                  </a>
                </td>
                <td><strong><?= e($v['variant'] ?? '') ?></strong></td>
                <td style="font-size:.875rem;color:var(--clr-text-muted)"><?= e($v['notes'] ?? '') ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
        <div style="text-align:center;padding:2rem;color:var(--clr-text-muted);background:var(--clr-bg-surface);border-radius:var(--radius-lg);border:1px dashed var(--clr-border)">
          <i class="fa fa-book-open" style="font-size:2rem;opacity:.3;display:block;margin-bottom:.75rem"></i>
          <p style="margin-bottom:1rem">No word variants recorded yet for this dialect.</p>
          <a href="/contribute" class="btn btn-primary btn-sm">Contribute a Variant</a>
        </div>
        <?php endif; ?>

        <!-- Donate strip -->
        <div class="donate-strip" style="margin-top:2.5rem">
          <div class="donate-strip__icon"><i class="fa fa-heart"></i></div>
          <div class="donate-strip__text">
            <strong>Help us document <?= e($dName) ?>.</strong>
            Contribute words, phrases, and recordings to grow this dialect's archive.
          </div>
          <a href="/contribute" class="btn btn-primary btn-sm">Contribute</a>
        </div>
      </div>

      <!-- Sidebar -->
      <aside class="dialect-layout__sidebar">
        <div class="card" style="position:sticky;top:5rem">
          <div class="card__body">
            <h3 style="font-size:1rem;margin-bottom:1rem;font-weight:700">
              <i class="fa fa-map" style="color:var(--clr-primary)"></i> All Dialects
            </h3>
            <?php
              try {
                $db = \App\Core\Database::getInstance();
                $allDialects = $db->fetchAll("SELECT name, code FROM dialects ORDER BY name");
              } catch (\Throwable $e) {
                $allDialects = [];
              }
              foreach ($allDialects as $d):
                $isActive = ($d['code'] ?? '') === $dCode;
            ?>
            <a href="/dialect/<?= e($d['code'] ?? '') ?>"
               style="display:flex;align-items:center;padding:.5rem 0;border-bottom:1px solid var(--clr-border);text-decoration:none;font-size:.875rem;color:<?= $isActive ? 'var(--clr-primary)' : 'inherit' ?>;font-weight:<?= $isActive ? '700' : '400' ?>">
              <?php if ($isActive): ?>
              <i class="fa fa-chevron-right" style="font-size:.7rem;margin-right:.5rem;color:var(--clr-primary)"></i>
              <?php else: ?>
              <span style="display:inline-block;width:1rem"></span>
              <?php endif; ?>
              <?= e($d['name'] ?? '') ?>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
      </aside>
    </div>
  </div>
</section>
