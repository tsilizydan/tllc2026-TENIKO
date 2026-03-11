<?php // Admin: Site Settings — $settings (key-value array)
$csrfToken = \App\Core\CSRF::generate(); ?>
<div style="margin-bottom:1.5rem">
  <h1 style="font-size:1.5rem;font-family:var(--font-heading)">Site Settings</h1>
</div>
<form action="/admin/settings" method="POST">
  <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
  <div class="card" style="margin-bottom:1.5rem">
    <div class="card__body">
      <h2 style="font-size:1rem;margin-bottom:1.25rem">General</h2>
      <?php $fields = [
        ['site_name',        'Site Name',        'text',     'TENIKO'],
        ['site_tagline',     'Tagline',           'text',     'The Malagasy Language & Culture Encyclopedia'],
        ['site_email',       'Contact Email',     'email',    'hello@teniko.mg'],
        ['site_url',         'Site URL',          'url',      'https://teniko.tsilizy.com'],
        ['maintenance_mode', 'Maintenance Mode',  'select',   '0'],
      ]; ?>
      <?php foreach ($fields as [$key, $label, $type, $default]): ?>
      <div class="form-group">
        <label class="form-label"><?= $label ?></label>
        <?php if ($type === 'select'): ?>
        <select name="settings[<?= $key ?>]" class="form-control">
          <option value="0" <?= ($settings[$key] ?? '0') === '0' ? 'selected' : '' ?>>Off</option>
          <option value="1" <?= ($settings[$key] ?? '0') === '1' ? 'selected' : '' ?>>On</option>
        </select>
        <?php else: ?>
        <input type="<?= $type ?>" name="settings[<?= $key ?>]" class="form-control" value="<?= e($settings[$key] ?? $default) ?>">
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="card" style="margin-bottom:1.5rem">
    <div class="card__body">
      <h2 style="font-size:1rem;margin-bottom:1.25rem">Announcement Banner</h2>
      <div class="form-group">
        <label class="form-label">Message</label>
        <input type="text" name="settings[announcement_text]" class="form-control" value="<?= e($settings['announcement_text'] ?? '') ?>" placeholder="Leave blank to hide banner">
      </div>
      <div class="form-group">
        <label class="form-label">Type</label>
        <select name="settings[announcement_type]" class="form-control">
          <?php foreach (['info','success','warning'] as $t): ?>
          <option value="<?= $t ?>" <?= ($settings['announcement_type'] ?? 'info') === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  </div>
  <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> Save All Settings</button>
</form>
