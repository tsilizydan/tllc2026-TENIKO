<?php // Admin: Word edit/create — $word (null=create), $dialects
$csrfToken = \App\Core\CSRF::generate();
$isNew     = empty($word);
$action    = $isNew ? '/admin/words/create' : "/admin/words/{$word['id']}/edit";
?>
<div class="flex-between" style="margin-bottom:1.5rem">
  <h1 style="font-size:1.5rem;font-family:var(--font-heading)"><?= $isNew ? 'Add New Word' : 'Edit: ' . e($word['word']) ?></h1>
  <a href="/admin/words" class="btn btn-ghost btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
</div>
<form action="<?= $action ?>" method="POST">
  <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
  <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;align-items:start">
    <div>
      <div class="card" style="margin-bottom:1rem">
        <div class="card__body">
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
            <div class="form-group">
              <label class="form-label">Malagasy Word *</label>
              <input type="text" name="word" class="form-control" value="<?= e($word['word'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Part of Speech</label>
              <select name="part_of_speech" class="form-control">
                <?php foreach (['','noun','verb','adjective','adverb','pronoun','preposition','conjunction','interjection','expression'] as $p): ?>
                  <option value="<?= $p ?>" <?= ($word['part_of_speech'] ?? '') === $p ? 'selected' : '' ?>><?= $p ?: '— Select —' ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">IPA Pronunciation</label>
            <input type="text" name="ipa" class="form-control" value="<?= e($word['ipa'] ?? '') ?>" placeholder="/fitia.va.na/">
          </div>
          <div class="form-group">
            <label class="form-label">Etymology / Origin</label>
            <textarea name="etymology" class="form-control" rows="2"><?= e($word['etymology'] ?? '') ?></textarea>
          </div>
        </div>
      </div>
      <div class="card" style="margin-bottom:1rem">
        <div class="card__body">
          <h3 style="font-size:1rem;margin-bottom:1rem">Definitions & Translations</h3>
          <div class="form-group">
            <label class="form-label">Malagasy Definition</label>
            <textarea name="definition_mg" class="form-control" rows="3"><?= e($word['definition_mg'] ?? '') ?></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">French Translation</label>
            <input type="text" name="translation_fr" class="form-control" value="<?= e($word['translation_fr'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label">English Translation</label>
            <input type="text" name="translation_en" class="form-control" value="<?= e($word['translation_en'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Example Sentence</label>
            <textarea name="example" class="form-control" rows="2"><?= e($word['example'] ?? '') ?></textarea>
          </div>
        </div>
      </div>
    </div>
    <div>
      <div class="card" style="margin-bottom:1rem">
        <div class="card__body">
          <h3 style="font-size:1rem;margin-bottom:1rem">Publishing</h3>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
              <?php foreach (['draft','pending','published','rejected'] as $s): ?>
              <option value="<?= $s ?>" <?= ($word['status'] ?? 'draft') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <label class="flex" style="gap:.5rem;align-items:center;cursor:pointer;margin-top:.5rem">
            <input type="checkbox" name="featured" value="1" <?= !empty($word['featured']) ? 'checked' : '' ?>> Featured word
          </label>
        </div>
      </div>
      <div class="card">
        <div class="card__body">
          <button type="submit" class="btn btn-primary w-full"><i class="fa fa-save"></i> <?= $isNew ? 'Create Word' : 'Save Changes' ?></button>
          <a href="/admin/words" class="btn btn-ghost btn-sm w-full" style="margin-top:.5rem">Cancel</a>
        </div>
      </div>
    </div>
  </div>
</form>
