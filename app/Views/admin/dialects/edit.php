<?php // Admin: Dialect edit/create — $dialect (null=new)
$csrfToken = \App\Core\CSRF::generate();
$isNew  = empty($dialect);
$action = $isNew ? '/admin/dialects/create' : "/admin/dialects/{$dialect['id']}/edit";
?>
<div class="flex-between" style="margin-bottom:1.5rem">
  <h1 style="font-size:1.5rem;font-family:var(--font-heading)"><?= $isNew ? 'Add Dialect' : 'Edit: ' . e($dialect['name']) ?></h1>
  <a href="/admin/dialects" class="btn btn-ghost btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
</div>
<form action="<?= $action ?>" method="POST">
  <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
  <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem">
    <div>
      <div class="card" style="margin-bottom:1rem">
        <div class="card__body">
          <div class="form-group">
            <label class="form-label">Dialect Name *</label>
            <input type="text" name="name" class="form-control" value="<?= e($dialect['name'] ?? '') ?>" required
                   placeholder="e.g. Merina, Sakalava, Betsimisaraka">
          </div>
          <div class="form-group">
            <label class="form-label">Region / Geographic Area</label>
            <input type="text" name="region" class="form-control" value="<?= e($dialect['region'] ?? '') ?>"
                   placeholder="e.g. Central Highlands, West Coast">
          </div>
          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="5"
                      placeholder="Linguistic characteristics, speakers, notes…"><?= e($dialect['description'] ?? '') ?></textarea>
          </div>
        </div>
      </div>
    </div>
    <div>
      <div class="card" style="margin-bottom:1rem">
        <div class="card__body">
          <h3 style="font-size:.9rem;color:var(--clr-text-muted);margin-bottom:1rem">Map Coordinates (optional)</h3>
          <div class="form-group">
            <label class="form-label">Latitude</label>
            <input type="number" name="lat" class="form-control" step="0.000001"
                   value="<?= e($dialect['lat'] ?? '') ?>" placeholder="-18.9000">
          </div>
          <div class="form-group">
            <label class="form-label">Longitude</label>
            <input type="number" name="lng" class="form-control" step="0.000001"
                   value="<?= e($dialect['lng'] ?? '') ?>" placeholder="47.5333">
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card__body">
          <button type="submit" class="btn btn-primary w-full">
            <i class="fa fa-save"></i> <?= $isNew ? 'Create Dialect' : 'Save Changes' ?>
          </button>
        </div>
      </div>
    </div>
  </div>
</form>
