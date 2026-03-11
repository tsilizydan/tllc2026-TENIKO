<?php // Dialect detail — variables: $dialect (includes word_variants) ?>
<div class="container" style="padding-top:1.5rem">
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="/">Home</a><span class="breadcrumb__sep">/</span>
    <a href="/dialects">Dialects</a><span class="breadcrumb__sep">/</span>
    <span aria-current="page"><?= e($dialect['name']) ?></span>
  </nav>
</div>
<section class="section" style="padding-top:1.5rem">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 280px;gap:2.5rem;align-items:start">
      <div>
        <div style="margin-bottom:2rem">
          <span style="font-size:3rem"><?= $dialect['emoji'] ?? '🗺️' ?></span>
          <h1 style="font-family:var(--font-heading);font-size:2.5rem;color:var(--clr-primary);margin:.5rem 0"><?= e($dialect['name']) ?></h1>
          <?php if ($dialect['region']): ?><p style="color:var(--clr-text-muted)"><i class="fa fa-map-marker-alt"></i> <?= e($dialect['region']) ?></p><?php endif; ?>
          <?php if ($dialect['speaker_count']): ?><p style="color:var(--clr-text-muted)"><i class="fa fa-users"></i> ~<?= number_format($dialect['speaker_count']) ?> speakers</p><?php endif; ?>
        </div>
        <?php if ($dialect['description']): ?>
          <p style="line-height:1.9;font-size:1.05rem;margin-bottom:2rem"><?= nl2br(e($dialect['description'])) ?></p>
        <?php endif; ?>

        <?php if (!empty($dialect['word_variants'])): ?>
        <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:1rem"><i class="fa fa-book" style="color:var(--clr-primary)"></i> Word Variants in <?= e($dialect['name']) ?></h2>
        <div class="table-wrap">
          <table class="table" role="grid">
            <thead><tr><th>Standard Form</th><th><?= e($dialect['name']) ?> Variant</th><th>Notes</th></tr></thead>
            <tbody>
              <?php foreach ($dialect['word_variants'] as $v): ?>
              <tr>
                <td><a href="/word/<?= e($v['word_slug']) ?>" style="color:var(--clr-primary);font-weight:600"><?= e($v['standard_word']) ?></a></td>
                <td><strong><?= e($v['variant']) ?></strong></td>
                <td style="font-size:.875rem;color:var(--clr-text-muted)"><?= e($v['notes'] ?? '') ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
        <div style="text-align:center;padding:2rem;color:var(--clr-text-muted);background:var(--clr-bg-surface);border-radius:var(--radius-lg)">
          <p>No word variants recorded yet for this dialect.</p>
          <a href="/contribute" class="btn btn-primary btn-sm" style="margin-top:1rem">Contribute a Variant</a>
        </div>
        <?php endif; ?>
      </div>
      <aside>
        <div class="card">
          <div class="card__body">
            <h3 style="font-size:1rem;margin-bottom:1rem">All Dialects</h3>
            <?php
            $db = \App\Core\Database::getInstance();
            $all = $db->fetchAll("SELECT name, code FROM dialects ORDER BY name");
            foreach ($all as $d): ?>
            <a href="/dialect/<?= e($d['code']) ?>" style="display:block;padding:.5rem 0;border-bottom:1px solid var(--clr-border);text-decoration:none;font-size:.875rem;color:<?= $d['code'] === $dialect['code'] ? 'var(--clr-primary)' : 'inherit' ?>;font-weight:<?= $d['code'] === $dialect['code'] ? '700' : '400' ?>"><?= e($d['name']) ?></a>
            <?php endforeach; ?>
          </div>
        </div>
      </aside>
    </div>
  </div>
</section>
