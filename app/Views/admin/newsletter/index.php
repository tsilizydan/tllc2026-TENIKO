<?php // Admin: Newsletter — $stats, $paged ?>
<div class="flex-between" style="margin-bottom:1.5rem">
  <div>
    <h1 style="font-size:1.5rem;font-family:var(--font-heading)">Newsletter</h1>
    <p style="color:var(--clr-text-muted);font-size:.875rem;margin-top:.25rem">Manage your subscriber list and send updates</p>
  </div>
  <a href="/admin/newsletter/export" class="btn btn-outline btn-sm"><i class="fa fa-download"></i> Export CSV</a>
</div>

<!-- KPIs -->
<div class="kpi-grid" style="margin-bottom:1.5rem">
  <div class="kpi-card"><div class="kpi-icon kpi-icon-green"><i class="fa fa-envelope-open-text"></i></div><div><div class="kpi-value"><?= number_format($stats['total'] ?? 0) ?></div><div class="kpi-label">Total Subscribers</div></div></div>
  <div class="kpi-card"><div class="kpi-icon kpi-icon-blue"><i class="fa fa-check-double"></i></div><div><div class="kpi-value"><?= number_format($stats['confirmed'] ?? 0) ?></div><div class="kpi-label">Confirmed</div></div></div>
  <div class="kpi-card"><div class="kpi-icon kpi-icon-beige"><i class="fa fa-calendar-plus"></i></div><div><div class="kpi-value"><?= number_format($stats['this_month'] ?? 0) ?></div><div class="kpi-label">This Month</div></div></div>
</div>

<div class="table-wrap">
  <table class="table">
    <thead>
      <tr>
        <th>Email</th>
        <th>Confirmed</th>
        <th>Source</th>
        <th>Subscribed</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php if (empty($paged['items'])): ?>
    <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--clr-text-muted)">No subscribers yet. Share your newsletter sign-up form.</td></tr>
    <?php else: ?>
    <?php foreach (($paged['items'] ?? []) as $s): ?>
    <tr>
      <td style="font-weight:500"><?= e($s['email']) ?></td>
      <td>
        <span class="badge <?= ($s['confirmed'] ?? 0) ? 'badge-green' : 'badge-gray' ?>">
          <i class="fa <?= ($s['confirmed'] ?? 0) ? 'fa-check' : 'fa-clock' ?>"></i>
          <?= ($s['confirmed'] ?? 0) ? 'Yes' : 'Pending' ?>
        </span>
      </td>
      <td style="font-size:.8rem;color:var(--clr-text-muted)"><?= e($s['source'] ?? 'footer') ?></td>
      <td style="font-size:.8rem;color:var(--clr-text-muted)"><?= date('d M Y', strtotime($s['created_at'])) ?></td>
      <td>
        <form action="/admin/newsletter/remove" method="POST" style="display:inline" onsubmit="return confirm('Remove this subscriber?')">
          <input type="hidden" name="_csrf_token" value="<?= e(\App\Core\CSRF::generate()) ?>">
          <input type="hidden" name="email" value="<?= e($s['email']) ?>">
          <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--clr-accent)"><i class="fa fa-trash"></i></button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<?php $last = $paged['last_page'] ?? 1; $cur = $paged['current_page'] ?? 1; if ($last > 1): ?>
<nav class="pagination" style="justify-content:flex-start;margin-top:1rem">
  <?php if ($cur > 1): ?><a class="pagination__item" href="/admin/newsletter?page=<?= $cur - 1 ?>">‹</a><?php endif; ?>
  <?php for ($i = max(1,$cur-2); $i <= min($last,$cur+2); $i++): ?><a class="pagination__item <?= $i===$cur?'active':''?>" href="/admin/newsletter?page=<?= $i ?>"><?= $i ?></a><?php endfor; ?>
  <?php if ($cur < $last): ?><a class="pagination__item" href="/admin/newsletter?page=<?= $cur + 1 ?>">›</a><?php endif; ?>
</nav>
<?php endif; ?>
