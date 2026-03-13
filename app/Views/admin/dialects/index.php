<?php // Admin: Dialects list — $dialects
$csrfToken = \App\Core\CSRF::generate();
?>
<div class="flex-between" style="margin-bottom:1.5rem">
  <h1 style="font-size:1.5rem;font-family:var(--font-heading)">Dialects</h1>
  <a href="/admin/dialects/create" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add Dialect</a>
</div>
<div class="table-wrap">
  <table class="table">
    <thead><tr><th>Name</th><th>Code</th><th>Region</th><th>Coordinates</th><th>Actions</th></tr></thead>
    <tbody>
    <?php if (empty($dialects)): ?>
      <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--clr-text-muted)">No dialects yet. <a href="/admin/dialects/create">Add the first one →</a></td></tr>
    <?php endif; ?>
    <?php foreach ($dialects as $d): ?>
    <tr>
      <td style="font-weight:600"><?= e($d['name']) ?></td>
      <td><code class="badge badge-beige"><?= e($d['code']) ?></code></td>
      <td style="font-size:.875rem"><?= e($d['region'] ?? '—') ?></td>
      <td style="font-size:.8rem;color:var(--clr-text-muted)">
        <?= $d['lat'] ? e($d['lat']).','.e($d['lng']) : '—' ?>
      </td>
      <td style="display:flex;gap:.5rem">
        <a href="/admin/dialects/<?= $d['id'] ?>/edit" class="btn btn-sm btn-ghost"><i class="fa fa-edit"></i></a>
        <form action="/admin/dialects/<?= $d['id'] ?>/delete" method="POST"
              onsubmit="return confirm('Delete <?= e($d['name']) ?>?')">
          <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
          <button class="btn btn-sm btn-ghost" style="color:var(--clr-accent)"><i class="fa fa-trash"></i></button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
