<?php
/**
 * Word Detail View — /word/{slug}
 * Variables: $word (full entry with definitions, translations, audio, dialects, related), $comments, $reactions
 */
$csrfToken = \App\Core\CSRF::generate();
$reactionMap = [];
foreach ($reactions as $r) { $reactionMap[$r['type']] = (int)$r['count']; }
$reactionTypes = [
    'love'          => ['❤️', 'Love'],
    'useful'        => ['👍', 'Useful'],
    'popular'       => ['🔥', 'Popular'],
    'educational'   => ['📚', 'Educational'],
    'interesting'   => ['🤔', 'Interesting'],
];
?>

<!-- Breadcrumb -->
<div class="container" style="padding-top:1.5rem">
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="/">Home</a><span class="breadcrumb__sep">/</span>
    <a href="/dictionary">Dictionary</a><span class="breadcrumb__sep">/</span>
    <span aria-current="page"><?= e($word['word']) ?></span>
  </nav>
</div>

<!-- Word Header -->
<div class="section" style="padding-top:1.5rem;padding-bottom:2rem">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 340px;gap:3rem;align-items:start">

      <!-- Main Entry -->
      <div>
        <div class="flex" style="gap:1rem;align-items:center;margin-bottom:.5rem;flex-wrap:wrap">
          <h1 style="font-family:var(--font-heading);font-size:3rem;color:var(--clr-primary);margin:0"><?= e($word['word']) ?></h1>
          <?php if ($word['part_of_speech']): ?>
            <span class="pos-pill"><?= e($word['part_of_speech']) ?></span>
          <?php endif; ?>
        </div>

        <!-- Pronunciation -->
        <?php if ($word['pronunciation']): ?>
        <div class="flex" style="gap:.5rem;align-items:center;margin-bottom:1rem">
          <span style="font-family:monospace;font-size:1.1rem;color:var(--clr-text-muted)">[<?= e($word['pronunciation']) ?>]</span>
        </div>
        <?php endif; ?>

        <!-- Audio files -->
        <?php foreach ($word['audio_files'] as $audio): ?>
        <div class="audio-player" data-src="/uploads/audio/<?= e($audio['filename']) ?>" style="margin-bottom:1rem" role="group" aria-label="Audio pronunciation">
          <button class="audio-player__play" aria-label="Play pronunciation"><i class="fa fa-play"></i></button>
          <div class="audio-player__label">
            <strong>Listen:</strong> <?= $audio['dialect_id'] ? e($audio['dialect_name'] ?? 'dialect') : 'Standard pronunciation' ?>
          </div>
        </div>
        <?php endforeach; ?>

        <!-- Definitions -->
        <?php if (!empty($word['definitions'])): ?>
        <div style="margin:1.5rem 0">
          <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:1rem;color:var(--clr-text-muted);text-transform:uppercase;letter-spacing:.5px">Definitions</h2>
          <?php
          $byLang = [];
          foreach ($word['definitions'] as $d) $byLang[$d['lang']][] = $d;
          $langNames = ['mg' => '🇲🇬 Malagasy', 'fr' => '🇫🇷 French', 'en' => '🇬🇧 English'];
          foreach ($byLang as $lang => $defs): ?>
          <div style="margin-bottom:1.25rem">
            <span class="badge badge-green" style="margin-bottom:.5rem"><?= $langNames[$lang] ?? strtoupper($lang) ?></span>
            <?php foreach ($defs as $i => $d): ?>
            <div style="padding:.5rem 0;border-bottom:1px solid var(--clr-border)">
              <span style="font-weight:600;color:var(--clr-accent);margin-right:.5rem"><?= $i + 1 ?>.</span>
              <?= e($d['text']) ?>
              <?php if ($d['example']): ?>
                <div style="font-style:italic;color:var(--clr-text-muted);margin-top:.25rem;font-size:.9rem">e.g. <?= e($d['example']) ?></div>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Translations -->
        <?php if (!empty($word['translations'])): ?>
        <div style="margin:1.5rem 0;padding:1.25rem;background:var(--clr-beige);border-radius:var(--radius-lg)">
          <h2 style="font-size:1rem;font-weight:700;margin-bottom:.75rem">Translations</h2>
          <div class="flex flex-wrap gap-4">
            <?php foreach ($word['translations'] as $t): ?>
            <div>
              <span style="font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;color:var(--clr-text-muted)"><?= ['fr'=>'🇫🇷 French','en'=>'🇬🇧 English'][$t['lang']] ?? $t['lang'] ?>:</span>
              <strong> <?= e($t['translation']) ?></strong>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Dialect Variants -->
        <?php if (!empty($word['dialect_variants'])): ?>
        <div style="margin:1.5rem 0">
          <h2 style="font-size:1rem;font-weight:700;margin-bottom:.75rem"><i class="fa fa-map-marked-alt" style="color:var(--clr-primary)"></i> Dialect Variations</h2>
          <div class="grid grid-3">
            <?php foreach ($word['dialect_variants'] as $v): ?>
            <div style="padding:.75rem;border:1px solid var(--clr-border);border-radius:var(--radius-md)">
              <div style="font-weight:700;color:var(--clr-primary)"><?= e($v['variant']) ?></div>
              <div style="font-size:.8rem;color:var(--clr-text-muted)"><?= e($v['dialect_name']) ?> — <?= e($v['region']) ?></div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Etymology -->
        <?php if ($word['etymology']): ?>
        <div style="margin:1.5rem 0;padding:1rem 1.25rem;border-left:3px solid var(--clr-primary);background:var(--clr-bg-surface)">
          <strong>Etymology:</strong> <?= e($word['etymology']) ?>
        </div>
        <?php endif; ?>

        <!-- Reactions -->
        <div style="margin:1.5rem 0">
          <p style="font-size:.85rem;font-weight:600;color:var(--clr-text-muted);margin-bottom:.5rem">Was this entry helpful?</p>
          <div class="reactions" role="group" aria-label="React to this entry">
            <?php foreach ($reactionTypes as $type => [$emoji, $label]): ?>
            <button class="reaction-btn" data-entity-type="word" data-entity-id="<?= $word['id'] ?>" data-type="<?= $type ?>" aria-label="<?= $label ?>" title="<?= $label ?>">
              <?= $emoji ?> <span class="reaction-count"><?= $reactionMap[$type] ?? 0 ?></span>
            </button>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Share & Copy Bar -->
        <div class="share-bar" role="toolbar" aria-label="Share this entry">
          <span class="share-bar__label">Share:</span>
          <button class="btn btn-sm btn-ghost" onclick="copyToClipboard('<?= e($word['word']) ?>', this)"><i class="fa fa-copy"></i> Copy Word</button>
          <button class="btn btn-sm btn-ghost" onclick="shareEntry('<?= e($word['word']) ?> on TENIKO', window.location.href)"><i class="fa fa-share-alt"></i> Share</button>
          <button class="btn btn-sm btn-ghost" onclick="copyToClipboard(window.location.href, this)"><i class="fa fa-link"></i> Copy Link</button>
          <button class="btn btn-sm btn-ghost" onclick="copyToClipboard('<?= e($word['word']) ?> (<?= e($word['part_of_speech']) ?>). TENIKO Dictionary. Retrieved from ' + window.location.href, this)"><i class="fa fa-quote-right"></i> Cite</button>
        </div>
      </div>

      <!-- Sidebar -->
      <div>
        <!-- Related Words -->
        <?php if (!empty($word['related'])): ?>
        <div class="card" style="margin-bottom:1rem">
          <div class="card__body">
            <h3 style="font-size:1rem;margin-bottom:1rem">Related Words</h3>
            <?php foreach ($word['related'] as $rel): ?>
            <a href="/word/<?= e($rel['slug']) ?>" style="display:flex;align-items:center;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid var(--clr-border);text-decoration:none;color:inherit;transition:color .15s" onmouseover="this.style.color='var(--clr-primary)'" onmouseout="this.style.color=''">
              <span style="font-weight:600"><?= e($rel['word']) ?></span>
              <span class="badge badge-gray"><?= e($rel['type']) ?></span>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- View Count -->
        <div class="card">
          <div class="card__body" style="text-align:center">
            <div style="font-size:2rem;font-weight:700;color:var(--clr-primary)"><?= number_format($word['view_count']) ?></div>
            <div style="font-size:.8rem;color:var(--clr-text-muted)">times viewed</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Comments -->
<section class="section section--beige" style="padding-top:2rem" aria-label="Comments">
  <div class="container container-narrow">
    <h2 class="section-title">Comments <span style="font-size:1rem;font-weight:400;color:var(--clr-text-muted)"><?= count($comments) ?></span></h2>

    <?php foreach ($comments as $c): ?>
    <div class="comment" role="article">
      <div class="comment__avatar" aria-hidden="true"><?= strtoupper(substr($c['display_name'] ?? $c['username'], 0, 1)) ?></div>
      <div class="comment__body">
        <div class="comment__meta"><strong><?= e($c['display_name'] ?? $c['username']) ?></strong> · <?= timeAgo($c['created_at']) ?></div>
        <div class="comment__text"><?= e($c['body']) ?></div>
      </div>
    </div>
    <?php endforeach; ?>

    <!-- Comment Form -->
    <?php if (\App\Core\Auth::check()): ?>
    <form id="comment-form" style="margin-top:1.5rem">
      <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
      <input type="hidden" name="entity_type" value="word">
      <input type="hidden" name="entity_id"   value="<?= $word['id'] ?>">
      <div class="form-group">
        <label class="form-label" for="comment-body">Add a comment</label>
        <textarea class="form-control" id="comment-body" name="body" rows="3" placeholder="Share your insight…" required minlength="3" maxlength="2000"></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Post Comment <i class="fa fa-paper-plane"></i></button>
    </form>
    <?php else: ?>
    <div class="alert alert-info" style="margin-top:1rem">
      <i class="fa fa-info-circle"></i> <a href="/login" style="color:var(--clr-info);font-weight:600">Log in</a> to leave a comment.
    </div>
    <?php endif; ?>
  </div>
</section>
