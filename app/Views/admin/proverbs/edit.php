<?php // Admin: Proverb edit — $proverb (null=new), $dialects
$csrfToken = \App\Core\CSRF::generate();
$isNew = empty($proverb);
$action = $isNew ? '/admin/proverbs/create' : "/admin/proverbs/{$proverb['id']}/edit";
?>
<div class="flex-between" style="margin-bottom:1.5rem">
  <h1 style="font-size:1.5rem;font-family:var(--font-heading)"><?= $isNew ? 'Add Proverb' : 'Edit Proverb' ?></h1>
  <a href="/admin/proverbs" class="btn btn-ghost btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
</div>
<form action="<?= $action ?>" method="POST">
  <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
  <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem">
    <div class="card">
      <div class="card__body">
        <div class="form-group">
          <label class="form-label">Proverb Text (Malagasy) *</label>
          <textarea name="text" class="form-control" rows="3" required><?= e($proverb['text'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">French Translation</label>
          <textarea name="translation_fr" class="form-control" rows="2"><?= e($proverb['translation_fr'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">English Translation</label>
          <textarea name="translation_en" class="form-control" rows="2"><?= e($proverb['translation_en'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Meaning / Explanation</label>
          <textarea name="meaning" class="form-control" rows="3"><?= e($proverb['meaning'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Cultural Explanation</label>
          <textarea name="cultural_explanation" class="form-control" rows="3"><?= e($proverb['cultural_explanation'] ?? '') ?></textarea>
        </div>
      </div>
    </div>
    <div>
      <div class="card" style="margin-bottom:1rem">
        <div class="card__body">
          <div class="form-group">
            <label class="form-label">Dialect</label>
            <select name="dialect_id" class="form-control">
              <option value="">— None —</option>
              <?php foreach ($dialects as $d): ?>
              <option value="<?= $d['id'] ?>" <?= ($proverb['dialect_id'] ?? '') == $d['id'] ? 'selected' : '' ?>><?= e($d['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
              <?php foreach (['draft','published','rejected'] as $s): ?>
              <option value="<?= $s ?>" <?= ($proverb['status'] ?? 'draft') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card__body">
          <button type="submit" class="btn btn-primary w-full"><i class="fa fa-save"></i> Save</button>
        </div>
      </div>
    </div>
  </div>
</form>
