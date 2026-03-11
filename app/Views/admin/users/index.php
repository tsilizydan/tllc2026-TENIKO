<?php // Admin: Users list — $paged ?>
<div class="flex-between" style="margin-bottom:1.5rem">
  <h1 style="font-size:1.5rem;font-family:var(--font-heading)">Users</h1>
</div>
<div class="table-wrap">
  <table class="table">
    <thead><tr><th>User</th><th>Email</th><th>Role</th><th>Status</th><th>Registered</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach (($paged['items'] ?? []) as $u): ?>
    <tr>
      <td>
        <div style="display:flex;align-items:center;gap:.75rem">
          <div style="width:36px;height:36px;background:var(--clr-primary);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;flex-shrink:0;font-size:.9rem"><?= strtoupper(substr($u['display_name'] ?? $u['username'], 0, 1)) ?></div>
          <div>
            <div style="font-weight:600"><?= e($u['display_name'] ?? $u['username']) ?></div>
            <div style="font-size:.75rem;color:var(--clr-text-muted)">@<?= e($u['username']) ?></div>
          </div>
        </div>
      </td>
      <td style="font-size:.875rem"><?= e($u['email']) ?></td>
      <td><span class="badge <?= $u['role'] === 'admin' ? 'badge-red' : ($u['role'] === 'moderator' ? 'badge-beige' : 'badge-green') ?>"><?= e($u['role']) ?></span></td>
      <td><span class="badge <?= $u['status'] === 'active' ? 'badge-green' : 'badge-gray' ?>"><?= e($u['status']) ?></span></td>
      <td style="font-size:.8rem;color:var(--clr-text-muted)"><?= formatDate($u['created_at']) ?></td>
      <td><a href="/admin/users/<?= $u['id'] ?>" class="btn btn-ghost btn-sm"><i class="fa fa-eye"></i></a></td>
    </tr>
    <?php endforeach; ?>
    <?php if (empty($paged['items'])): ?><tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--clr-text-muted)">No users found.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php $last = $paged['last_page'] ?? 1; $cur = $paged['current_page'] ?? 1; if ($last > 1): ?>
<nav class="pagination" style="justify-content:flex-start;margin-top:1rem">
  <?php if ($cur > 1): ?><a class="pagination__item" href="/admin/users?page=<?= $cur - 1 ?>">‹</a><?php endif; ?>
  <?php for ($i = max(1,$cur-2); $i <= min($last,$cur+2); $i++): ?><a class="pagination__item <?= $i===$cur?'active':''?>" href="/admin/users?page=<?= $i ?>"><?= $i ?></a><?php endfor; ?>
  <?php if ($cur < $last): ?><a class="pagination__item" href="/admin/users?page=<?= $cur + 1 ?>">›</a><?php endif; ?>
</nav>
<?php endif; ?>
