<?php // Contribute — all contribution forms on one page
$isLoggedIn = \App\Core\Auth::check();
$csrfToken  = \App\Core\CSRF::generate();
?>
<div style="background:var(--clr-bg-surface);border-bottom:1px solid var(--clr-border);padding:2rem 0">
  <div class="container">
    <h1 class="section-title">Contribute to TENIKO</h1>
    <p class="text-muted" style="margin-top:.5rem">Help grow the largest digital archive of Malagasy language and culture by submitting words, proverbs, and corrections.</p>
  </div>
</div>

<section class="section" style="padding-top:2rem">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem;margin-bottom:3rem">
      <?php foreach ([
        ['#form-word',       'fa-book-open',   'word',       'Submit a Word',       'Add a new Malagasy word with definitions, translations, and examples.'],
        ['#form-proverb',    'fa-scroll',      'proverb',    'Submit a Proverb',    'Add a traditional Malagasy ohabolana with translation and meaning.'],
        ['#form-correction', 'fa-edit',        'correction', 'Report a Correction', 'Found an error? Help us fix incorrect definitions or translations.'],
      ] as [$href, $icon, $type, $title, $desc]): ?>
      <a href="<?= $href ?>" onclick="switchTab('<?= $type ?>')" class="card" style="text-align:center;text-decoration:none;transition:box-shadow .2s" onmouseover="this.style.boxShadow='var(--shadow-lg)'" onmouseout="this.style.boxShadow=''">
        <div class="card__body">
          <i class="fa <?= $icon ?>" style="font-size:2rem;color:var(--clr-primary);display:block;margin-bottom:.75rem"></i>
          <h2 style="font-size:1rem;margin:0 0 .5rem"><?= $title ?></h2>
          <p style="font-size:.85rem;color:var(--clr-text-muted);margin:0"><?= $desc ?></p>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

    <?php if (!$isLoggedIn): ?>
    <div class="alert alert-info" style="margin-bottom:2rem">
      <i class="fa fa-info-circle"></i> You need to <a href="/login" style="color:var(--clr-info);font-weight:600">log in</a> or <a href="/register" style="color:var(--clr-info);font-weight:600">create an account</a> to contribute. Your submissions are reviewed by moderators before publishing.
    </div>
    <?php endif; ?>

    <div id="tab-container">
      <!-- Submit Word -->
      <div id="form-word" class="contribute-tab">
        <h2 style="font-size:1.25rem;font-family:var(--font-heading);margin-bottom:1.5rem"><i class="fa fa-book-open" style="color:var(--clr-primary)"></i> Submit a New Word</h2>
        <form action="/contribute/word" method="POST" <?= !$isLoggedIn ? 'onsubmit="return false;"' : '' ?>>
          <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
            <div class="form-group">
              <label class="form-label" for="word">Malagasy Word <span style="color:red">*</span></label>
              <input type="text" id="word" name="word" class="form-control" required placeholder="e.g. fitiavana">
            </div>
            <div class="form-group">
              <label class="form-label" for="pos">Part of Speech</label>
              <select id="pos" name="part_of_speech" class="form-control">
                <option value="">— select —</option>
                <?php foreach (['noun','verb','adjective','adverb','pronoun','preposition','conjunction','interjection','idiomatic expression'] as $p): ?>
                  <option value="<?= $p ?>"><?= ucfirst($p) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="def-mg">Malagasy Definition <span style="color:red">*</span></label>
            <textarea id="def-mg" name="definition_mg" class="form-control" rows="3" required placeholder="Definition in Malagasy…"></textarea>
          </div>
          <div class="form-group">
            <label class="form-label" for="def-fr">French Translation</label>
            <input type="text" id="def-fr" name="translation_fr" class="form-control" placeholder="French translation…">
          </div>
          <div class="form-group">
            <label class="form-label" for="def-en">English Translation</label>
            <input type="text" id="def-en" name="translation_en" class="form-control" placeholder="English translation…">
          </div>
          <div class="form-group">
            <label class="form-label" for="example">Example Sentence</label>
            <textarea id="example" name="example" class="form-control" rows="2" placeholder="Use the word in a sentence…"></textarea>
          </div>
          <button type="submit" class="btn btn-primary" <?= !$isLoggedIn ? 'disabled title="Login required"' : '' ?>><i class="fa fa-paper-plane"></i> Submit for Review</button>
        </form>
      </div>

      <!-- Submit Proverb -->
      <div id="form-proverb" class="contribute-tab" style="display:none">
        <h2 style="font-size:1.25rem;font-family:var(--font-heading);margin-bottom:1.5rem"><i class="fa fa-scroll" style="color:var(--clr-primary)"></i> Submit a Proverb</h2>
        <form action="/contribute/proverb" method="POST" <?= !$isLoggedIn ? 'onsubmit="return false;"' : '' ?>>
          <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
          <div class="form-group">
            <label class="form-label" for="proverb-text">Proverb (Malagasy) <span style="color:red">*</span></label>
            <textarea id="proverb-text" name="text" class="form-control" rows="3" required placeholder="Enter the original Malagasy proverb…"></textarea>
          </div>
          <div class="form-group">
            <label class="form-label" for="proverb-fr">French Translation</label>
            <textarea id="proverb-fr" name="translation_fr" class="form-control" rows="2" placeholder="French translation…"></textarea>
          </div>
          <div class="form-group">
            <label class="form-label" for="proverb-meaning">Meaning / Explanation</label>
            <textarea id="proverb-meaning" name="meaning" class="form-control" rows="3" placeholder="What does this proverb mean?"></textarea>
          </div>
          <button type="submit" class="btn btn-primary" <?= !$isLoggedIn ? 'disabled title="Login required"' : '' ?>><i class="fa fa-paper-plane"></i> Submit for Review</button>
        </form>
      </div>

      <!-- Report Correction -->
      <div id="form-correction" class="contribute-tab" style="display:none">
        <h2 style="font-size:1.25rem;font-family:var(--font-heading);margin-bottom:1.5rem"><i class="fa fa-edit" style="color:var(--clr-primary)"></i> Report a Correction</h2>
        <form action="/contribute/correction" method="POST" <?= !$isLoggedIn ? 'onsubmit="return false;"' : '' ?>>
          <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
          <div class="form-group">
            <label class="form-label" for="corr-entry">Word or Entry (URL or name) <span style="color:red">*</span></label>
            <input type="text" id="corr-entry" name="entry" class="form-control" required placeholder="e.g. /word/fitiavana or 'fitiavana'">
          </div>
          <div class="form-group">
            <label class="form-label" for="corr-type">Type of Correction</label>
            <select id="corr-type" name="type" class="form-control">
              <?php foreach (['incorrect definition','wrong translation','misspelling','cultural inaccuracy','missing content','other'] as $t): ?>
                <option value="<?= $t ?>"><?= ucfirst($t) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="corr-desc">Description <span style="color:red">*</span></label>
            <textarea id="corr-desc" name="description" class="form-control" rows="5" required placeholder="Explain what is wrong and what the correct information should be…"></textarea>
          </div>
          <button type="submit" class="btn btn-primary" <?= !$isLoggedIn ? 'disabled title="Login required"' : '' ?>><i class="fa fa-paper-plane"></i> Submit Correction</button>
        </form>
      </div>
    </div>
  </div>
</section>

<script>
function switchTab(id) {
  document.querySelectorAll('.contribute-tab').forEach(t => t.style.display = 'none');
  const el = document.getElementById('form-' + id);
  if (el) { el.style.display = ''; el.scrollIntoView({behavior:'smooth', block:'start'}); }
}
</script>
