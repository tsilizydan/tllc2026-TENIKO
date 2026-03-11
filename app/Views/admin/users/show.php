<?php // Admin: User detail — $profile
$csrfToken = \App\Core\CSRF::generate(); ?>
<div class="flex-between" style="margin-bottom:1.5rem">
  <h1 style="font-size:1.5rem;font-family:var(--font-heading)">User: <?= e($profile['display_name'] ?? $profile['username']) ?></h1>
  <a href="/admin/users" class="btn btn-ghost btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
  <div class="card">
    <div class="card__body">
      <h2 style="font-size:1rem;margin-bottom:1rem">Profile</h2>
      <table style="width:100%;font-size:.9rem;border-collapse:collapse">
        <?php foreach ([
          'ID' => $profile['id'],
          'Username' => '@' . $profile['username'],
          'Email' => $profile['email'],
          'Display Name' => $profile['display_name'] ?? '—',
          'Role' => ucfirst($profile['role']),
          'Status' => ucfirst($profile['status']),
          'Reputation' => number_format($profile['reputation'] ?? 0),
          'Member Since' => formatDate($profile['created_at']),
          'Last Login' => $profile['last_login_at'] ? timeAgo($profile['last_login_at']) : 'Never',
        ] as $label => $value): ?>
        <tr>
          <td style="padding:.5rem 0;color:var(--clr-text-muted);font-weight:600;width:140px"><?= $label ?></td>
          <td style="padding:.5rem 0"><?= e((string)$value) ?></td>
        </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
  <div>
    <div class="card" style="margin-bottom:1rem">
      <div class="card__body">
        <h2 style="font-size:1rem;margin-bottom:1rem">Change Role</h2>
        <form action="/admin/users/<?= $profile['id'] ?>/role" method="POST" style="display:flex;gap:.75rem;align-items:center">
          <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
          <select name="role" class="form-control">
            <?php foreach (['user','moderator','admin'] as $r): ?>
            <option value="<?= $r ?>" <?= $profile['role'] === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-primary btn-sm">Update</button>
        </form>
      </div>
    </div>
    <div class="card">
      <div class="card__body">
        <h2 style="font-size:1rem;margin-bottom:1rem">Change Status</h2>
        <form action="/admin/users/<?= $profile['id'] ?>/status" method="POST" style="display:flex;gap:.75rem;align-items:center">
          <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
          <select name="status" class="form-control">
            <?php foreach (['active','pending','suspended','banned'] as $s): ?>
            <option value="<?= $s ?>" <?= $profile['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-primary btn-sm">Update</button>
        </form>
      </div>
    </div>
  </div>
</div>
