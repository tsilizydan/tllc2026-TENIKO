<?php
/**
 * Homepage — TENIKO
 * Available: $wordOfDay, $proverbOfDay, $featured, $latestWords, $announcements, $campaign
 */
$csrfToken = \App\Core\CSRF::generate();
?>
<!-- ── Hero ─────────────────────────────────────────────── -->
<section class="hero" aria-labelledby="hero-title">
  <div class="container">
    <p class="hero__subtitle animate__animated animate__fadeInDown" style="animation-delay:.1s">
      <em>Teny</em> — The Living Archive of Malagasy Language & Culture
    </p>
    <h1 class="hero__title animate__animated animate__fadeIn" id="hero-title">
      Discover the Malagasy Language
    </h1>

    <!-- Search Bar -->
    <div class="search-hero animate__animated animate__fadeInUp" style="animation-delay:.2s">
      <form action="/search" method="GET" role="search" aria-label="Dictionary search">
        <span class="search-hero__icon" aria-hidden="true"><i class="fa fa-search"></i></span>
        <input type="search" name="q" id="hero-search" class="search-hero__input"
               placeholder="Search a Malagasy word, proverb, or definition…"
               autocomplete="off" autocorrect="off" spellcheck="false"
               aria-label="Search Malagasy dictionary" aria-autocomplete="list"
               aria-controls="autocomplete-dropdown">
        <button type="submit" class="search-hero__btn" aria-label="Search">Search</button>
      </form>
      <div id="autocomplete-dropdown" class="autocomplete hidden" role="listbox" aria-label="Search suggestions"></div>
    </div>

    <!-- Quick stats -->
    <div class="flex flex-wrap" style="gap:2rem;justify-content:center;margin-top:2.5rem">
      <?php
      $db = \App\Core\Database::getInstance();
      $wordCount       = $db->count("SELECT COUNT(*) FROM words WHERE status='published'");
      $proverbCount    = $db->count("SELECT COUNT(*) FROM proverbs WHERE status='published'");
      $contributorCount= $db->count("SELECT COUNT(*) FROM users WHERE status='active'");
      foreach ([
        [$wordCount,        'Words',        'fa-book'],
        [$proverbCount,     'Proverbs',     'fa-scroll'],
        [8,                 'Dialects',     'fa-map'],
        [$contributorCount, 'Contributors', 'fa-users'],
      ] as [$n, $label, $icon]): ?>
        <div class="flex" style="align-items:center;gap:.75rem;color:rgba(255,255,255,.9)" aria-label="<?= e($n) ?> <?= $label ?>">
          <i class="fa <?= $icon ?>" style="opacity:.7"></i>
          <span><strong><?= number_format($n) ?></strong> <?= $label ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── Word of Day + Proverb of Day ──────────────────────── -->
<?php if ($wordOfDay || $proverbOfDay): ?>
<section class="section section--beige" aria-label="Daily content">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem">

      <!-- Word of the Day -->
      <?php if ($wordOfDay): ?>
      <div class="wod animate-fade-up" role="article" aria-label="Word of the Day">
        <div class="wod__label"><i class="fa fa-star"></i> Word of the Day</div>
        <div class="wod__word"><?= e($wordOfDay['word']) ?></div>
        <?php if ($wordOfDay['part_of_speech']): ?>
          <div class="wod__pos"><?= e($wordOfDay['part_of_speech']) ?></div>
        <?php endif; ?>
        <?php
          $db  = \App\Core\Database::getInstance();
          $def = $db->fetch("SELECT text FROM definitions WHERE word_id=? AND lang='mg' LIMIT 1", [$wordOfDay['id']]);
          if ($def): ?>
          <div class="wod__def"><?= e(truncate($def['text'], 120)) ?></div>
        <?php endif; ?>
        <?php if (!empty($wordOfDay['trans_fr'])): ?>
          <div style="margin-top:.5rem;font-size:.85rem;opacity:.8">🇫🇷 <?= e($wordOfDay['trans_fr']) ?></div>
        <?php endif; ?>
        <div class="wod__btn">
          <a href="/word/<?= e($wordOfDay['slug']) ?>" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:white;border:1px solid rgba(255,255,255,.3)">
            See Full Entry <i class="fa fa-arrow-right"></i>
          </a>
        </div>
      </div>
      <?php else: ?>
        <div class="wod"><div class="wod__label">Word of the Day</div><div class="wod__word" style="opacity:.5">Coming soon…</div></div>
      <?php endif; ?>

      <!-- Proverb of the Day -->
      <?php if ($proverbOfDay): ?>
      <div class="card animate-fade-up" style="animation-delay:.1s" role="article" aria-label="Proverb of the Day">
        <div class="card__body" style="display:flex;flex-direction:column;height:100%">
          <div class="badge badge-beige" style="margin-bottom:1rem;width:fit-content"><i class="fa fa-feather"></i> Ohabolana — Proverb of the Day</div>
          <blockquote style="font-family:var(--font-heading);font-style:italic;font-size:1.2rem;color:var(--clr-charcoal);border-left:3px solid var(--clr-primary);padding-left:1rem;margin:0 0 1rem;flex:1">
            "<?= e($proverbOfDay['text']) ?>"
          </blockquote>
          <?php if ($proverbOfDay['translation_fr']): ?>
            <p style="font-size:.9rem;color:var(--clr-text-muted);font-style:italic;margin:0 0 1rem">🇫🇷 <?= e($proverbOfDay['translation_fr']) ?></p>
          <?php endif; ?>
          <a href="/proverb/<?= e($proverbOfDay['id']) ?>" class="btn btn-outline btn-sm" style="width:fit-content">Explore <i class="fa fa-arrow-right"></i></a>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </div>
</section>
<?php endif; ?>

<!-- ── Featured Articles ───────────────────────────────── -->
<?php if (!empty($featured)): ?>
<section class="section" aria-labelledby="featured-title">
  <div class="container">
    <div class="section__header flex-between">
      <div>
        <h2 class="section-title" id="featured-title">Cultural Knowledge</h2>
        <p class="text-muted" style="margin-top:.5rem">Explore Malagasy traditions, history, and linguistic research</p>
      </div>
      <a href="/culture" class="btn btn-outline">View All <i class="fa fa-arrow-right"></i></a>
    </div>
    <div class="grid grid-4">
      <?php foreach ($featured as $i => $article): ?>
      <a href="/article/<?= e($article['slug']) ?>" class="card article-card animate-fade-up" style="animation-delay:<?= $i * .05 ?>s" aria-label="<?= e($article['title']) ?>">
        <?php if ($article['cover_image']): ?>
          <img class="card__img" src="<?= e($article['cover_image']) ?>" alt="<?= e($article['title']) ?>" loading="lazy">
        <?php else: ?>
          <div class="card__img" style="background:linear-gradient(135deg,var(--clr-beige),var(--clr-beige-dk));display:flex;align-items:center;justify-content:center">
            <i class="fa fa-landmark" style="font-size:3rem;color:var(--clr-brown);opacity:.4"></i>
          </div>
        <?php endif; ?>
        <div class="card__body">
          <span class="article-card__type"><?= e($article['type'] ?? 'article') ?></span>
          <h3 class="article-card__title"><?= e($article['title']) ?></h3>
          <?php if ($article['excerpt']): ?>
            <p class="article-card__excerpt"><?= e(truncate($article['excerpt'], 100)) ?></p>
          <?php endif; ?>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ── Latest Dictionary Words ────────────────────────── -->
<?php if (!empty($latestWords)): ?>
<section class="section section--beige" aria-labelledby="latest-words-title">
  <div class="container">
    <div class="section__header flex-between">
      <div>
        <h2 class="section-title" id="latest-words-title">Latest Words Added</h2>
        <p class="text-muted" style="margin-top:.5rem">Recently contributed to the Malagasy dictionary</p>
      </div>
      <a href="/dictionary" class="btn btn-outline">Browse All <i class="fa fa-arrow-right"></i></a>
    </div>
    <div class="grid grid-4">
      <?php foreach ($latestWords as $i => $w): ?>
      <a href="/word/<?= e($w['slug']) ?>" class="word-card animate-fade-up" style="animation-delay:<?= $i * .04 ?>s" aria-label="<?= e($w['word']) ?>">
        <div class="word-card__word"><?= e($w['word']) ?></div>
        <?php if ($w['part_of_speech']): ?><div class="word-card__pos"><?= e($w['part_of_speech']) ?></div><?php endif; ?>
        <?php if ($w['trans_fr']): ?><div class="word-card__def"><?= e(truncate($w['trans_fr'], 60)) ?></div><?php endif; ?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ── Donation Campaign ──────────────────────────────── -->
<?php if (!empty($campaign)): ?>
<section class="section section--cta" aria-label="Support TENIKO">
  <div class="container container-narrow">
    <div class="donation-card">
      <div class="badge badge-beige" style="margin-bottom:1rem;width:fit-content"><i class="fa fa-heart"></i> Support TENIKO</div>
      <h2 style="font-size:1.75rem;margin-bottom:.75rem"><?= e($campaign['title'] ?? 'Help Us Grow') ?></h2>
      <?php if (!empty($campaign['description'])): ?><p style="opacity:.85;margin-bottom:1.5rem"><?= e($campaign['description']) ?></p><?php endif; ?>
      <?php
        $raised = (float)($campaign['raised'] ?? 0);
        $goal   = (float)($campaign['goal'] ?? 0);
        $pct    = $goal > 0 ? min(100, round($raised / $goal * 100)) : 0;
      ?>
      <div class="progress-bar" role="progressbar" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100">
        <div class="progress-bar__fill" style="width:<?= $pct ?>%"></div>
      </div>
      <div class="flex-between" style="margin:.5rem 0 1.5rem;font-size:.9rem;opacity:.85">
        <span><strong>€<?= number_format($raised, 2) ?></strong> raised</span>
        <span>Goal: <strong>€<?= number_format($goal, 2) ?></strong></span>
      </div>
      <a href="/donate" class="btn btn-primary">Donate Now <i class="fa fa-heart"></i></a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ── Contribute CTA ─────────────────────────────────── -->
<section class="section section--cta" aria-label="Call to action">
  <div class="container" style="text-align:center">
    <h2 class="section--cta__heading">Help Build the Archive</h2>
    <p class="section--cta__text">
      TENIKO grows through community contributions. Add words, proverbs, cultural articles, and audio pronunciations.
    </p>
    <div class="flex flex-wrap" style="gap:1rem;justify-content:center">
      <a href="/contribute" class="btn btn-primary btn-lg"><i class="fa fa-plus"></i> Contribute a Word</a>
      <a href="/register"   class="btn btn-outline btn-lg section--cta__outline"><i class="fa fa-user-plus"></i> Join the Community</a>
    </div>
  </div>
</section>
