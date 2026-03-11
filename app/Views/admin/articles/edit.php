<?php // Admin: Article edit — $article (null=new), $categories
$csrfToken = \App\Core\CSRF::generate();
$isNew = empty($article);
$action = $isNew ? '/admin/articles/create' : "/admin/articles/{$article['id']}/edit";
?>
<div class="flex-between" style="margin-bottom:1.5rem">
  <h1 style="font-size:1.5rem;font-family:var(--font-heading)"><?= $isNew ? 'New Article' : 'Edit: ' . e(truncate($article['title'] ?? '', 50)) ?></h1>
  <a href="/admin/articles" class="btn btn-ghost btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
</div>
<form action="<?= $action ?>" method="POST">
  <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
  <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem">
    <div>
      <div class="card" style="margin-bottom:1rem">
        <div class="card__body">
          <div class="form-group">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" value="<?= e($article['title'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label">Excerpt</label>
            <textarea name="excerpt" class="form-control" rows="2"><?= e($article['excerpt'] ?? '') ?></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Body</label>
            <textarea name="body" id="tinymce-body" class="form-control" rows="15"><?= e($article['body'] ?? '') ?></textarea>
          </div>
        </div>
      </div>
    </div>
    <div>
      <div class="card" style="margin-bottom:1rem">
        <div class="card__body">
          <div class="form-group">
            <label class="form-label">Type</label>
            <select name="type" class="form-control">
              <?php foreach (['article','cultural_note','research','folklore','history','dialect_guide'] as $t): ?>
              <option value="<?= $t ?>" <?= ($article['type'] ?? '') === $t ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$t)) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-control">
              <option value="">— None —</option>
              <?php foreach (($categories ?? []) as $c): ?>
              <option value="<?= $c['id'] ?>" <?= ($article['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
              <?php foreach (['draft','published'] as $s): ?>
              <option value="<?= $s ?>" <?= ($article['status'] ?? 'draft') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Cover Image URL</label>
            <input type="url" name="cover_image" class="form-control" value="<?= e($article['cover_image'] ?? '') ?>" placeholder="https://…">
          </div>
          <label class="flex" style="gap:.5rem;cursor:pointer;margin-top:.5rem">
            <input type="checkbox" name="featured" value="1" <?= !empty($article['featured']) ? 'checked' : '' ?>> Featured
          </label>
        </div>
      </div>
      <div class="card"><div class="card__body">
        <button type="submit" class="btn btn-primary w-full"><i class="fa fa-save"></i> Save</button>
      </div></div>
    </div>
  </div>
</form>
