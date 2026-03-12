<?php // Admin: Ads Management — $ads ?>
<div class="flex-between" style="margin-bottom:1.5rem">
  <div>
    <h1 style="font-size:1.5rem;font-family:var(--font-heading)">Advertising</h1>
    <p style="color:var(--clr-text-muted);font-size:.875rem;margin-top:.25rem">Manage display ads, banners, and sidebar placements across TENIKO</p>
  </div>
  <button class="btn btn-primary" onclick="document.getElementById('ad-create-modal').classList.add('open')"><i class="fa fa-plus"></i> New Ad</button>
</div>

<!-- Stats -->
<div class="kpi-grid" style="margin-bottom:1.5rem">
  <?php
  $db = \App\Core\Database::getInstance();
  $kpis = [
    ['Total Ads', count($ads ?? []), 'fa-ad'],
    ['Active', count(array_filter($ads ?? [], fn($a) => $a['is_active'])), 'fa-check-circle'],
    ['Placements', count(array_unique(array_column($ads ?? [], 'placement'))), 'fa-layer-group'],
  ];
  foreach ($kpis as [$label, $val, $icon]):
  ?>
  <div class="kpi-card"><div class="kpi-icon kpi-icon-green"><i class="fa <?= $icon ?>"></i></div><div><div class="kpi-value"><?= number_format($val) ?></div><div class="kpi-label"><?= $label ?></div></div></div>
  <?php endforeach; ?>
</div>

<?php if (empty($ads)): ?>
<div class="card">
  <div class="card__body" style="text-align:center;padding:3rem">
    <i class="fa fa-ad" style="font-size:3rem;color:var(--clr-gray-300);display:block;margin-bottom:1rem"></i>
    <p style="color:var(--clr-text-muted)">No ads configured yet. Create your first ad placement to start monetizing TENIKO.</p>
    <button class="btn btn-primary" style="margin-top:1rem" onclick="document.getElementById('ad-create-modal').classList.add('open')"><i class="fa fa-plus"></i> Create First Ad</button>
  </div>
</div>
<?php else: ?>
<div class="table-wrap">
  <table class="table">
    <thead><tr><th>Name</th><th>Placement</th><th>Type</th><th>Status</th><th>Dates</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($ads as $ad): ?>
    <tr>
      <td><strong><?= e($ad['name']) ?></strong><?php if ($ad['link_url']): ?><br><small style="color:var(--clr-text-muted)"><?= e(parse_url($ad['link_url'], PHP_URL_HOST)) ?></small><?php endif; ?></td>
      <td><span class="badge badge-beige"><?= e($ad['placement']) ?></span></td>
      <td style="font-size:.875rem"><?= e(ucfirst($ad['type'])) ?></td>
      <td><span class="badge <?= $ad['is_active'] ? 'badge-green' : 'badge-gray' ?>"><?= $ad['is_active'] ? 'Active' : 'Paused' ?></span></td>
      <td style="font-size:.75rem;color:var(--clr-text-muted)"><?= $ad['start_date'] ? date('d M', strtotime($ad['start_date'])) : '' ?><?= ($ad['start_date'] && $ad['end_date']) ? ' – ' : '' ?><?= $ad['end_date'] ? date('d M Y', strtotime($ad['end_date'])) : '—' ?></td>
      <td>
        <button class="btn btn-ghost btn-sm" onclick="editAd(<?= htmlspecialchars(json_encode($ad)) ?>)"><i class="fa fa-edit"></i></button>
        <form action="/admin/ads/<?= $ad['id'] ?>/delete" method="POST" style="display:inline" onsubmit="return confirm('Delete this ad?')">
          <input type="hidden" name="_csrf_token" value="<?= e(\App\Core\CSRF::generate()) ?>">
          <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--clr-accent)"><i class="fa fa-trash"></i></button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<!-- Create/Edit Ad Modal -->
<div id="ad-create-modal" class="modal">
  <div class="modal__backdrop" onclick="this.parentElement.classList.remove('open')"></div>
  <div class="modal__panel">
    <div class="modal__header">
      <h2 class="modal__title" id="ad-modal-title">New Ad</h2>
      <button class="btn-icon btn-ghost" onclick="document.getElementById('ad-create-modal').classList.remove('open')"><i class="fa fa-times"></i></button>
    </div>
    <form id="ad-form" action="/admin/ads/create" method="POST">
      <input type="hidden" name="_csrf_token" value="<?= e(\App\Core\CSRF::generate()) ?>">
      <div class="form-group"><label class="form-label">Ad Name *</label><input type="text" name="name" id="ad-name" class="form-control" required placeholder="e.g. Sidebar Banner - Partner X"></div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
        <div class="form-group">
          <label class="form-label">Placement</label>
          <select name="placement" id="ad-placement" class="form-control">
            <?php foreach (['sidebar','header','footer','content_top','content_bottom','between_entries'] as $p): ?>
            <option value="<?= $p ?>"><?= ucfirst(str_replace('_', ' ', $p)) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Type</label>
          <select name="type" id="ad-type" class="form-control" onchange="toggleAdType(this.value)">
            <option value="image">Image Banner</option>
            <option value="code">Custom HTML/JS</option>
          </select>
        </div>
      </div>
      <div id="ad-image-fields">
        <div class="form-group"><label class="form-label">Image URL</label><input type="url" name="image_url" id="ad-image_url" class="form-control" placeholder="https://…/banner.jpg"></div>
        <div class="form-group"><label class="form-label">Click-through URL</label><input type="url" name="link_url" id="ad-link_url" class="form-control" placeholder="https://…"></div>
      </div>
      <div id="ad-code-fields" style="display:none">
        <div class="form-group"><label class="form-label">Ad Code (HTML/JS)</label><textarea name="code" id="ad-code" class="form-control" rows="5" placeholder="<script>…"></textarea></div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
        <div class="form-group"><label class="form-label">Start Date</label><input type="date" name="start_date" id="ad-start_date" class="form-control"></div>
        <div class="form-group"><label class="form-label">End Date</label><input type="date" name="end_date" id="ad-end_date" class="form-control"></div>
      </div>
      <label class="flex" style="gap:.5rem;cursor:pointer;margin-bottom:1.25rem"><input type="checkbox" name="is_active" id="ad-is_active" value="1" checked> Active (show immediately)</label>
      <div style="display:flex;gap:.75rem">
        <button type="button" class="btn btn-ghost" onclick="document.getElementById('ad-create-modal').classList.remove('open')">Cancel</button>
        <button type="submit" class="btn btn-primary" style="flex:1"><i class="fa fa-save"></i> Save Ad</button>
      </div>
    </form>
  </div>
</div>

<script>
function toggleAdType(val) {
  document.getElementById('ad-image-fields').style.display = val === 'image' ? '' : 'none';
  document.getElementById('ad-code-fields').style.display  = val === 'code'  ? '' : 'none';
}
function editAd(ad) {
  const modal = document.getElementById('ad-create-modal');
  document.getElementById('ad-modal-title').textContent = 'Edit Ad';
  document.getElementById('ad-form').action = '/admin/ads/' + ad.id + '/edit';
  ['name','placement','type','image_url','link_url','code','start_date','end_date'].forEach(f => {
    const el = document.getElementById('ad-' + f);
    if (el) el.value = ad[f] ?? '';
  });
  document.getElementById('ad-is_active').checked = !!ad.is_active;
  toggleAdType(ad.type ?? 'image');
  modal.classList.add('open');
}
</script>
