<?php // Admin: Donations — $stats, $donations, $campaign
$csrfToken = \App\Core\CSRF::generate();
$progress  = $stats['goal'] > 0 ? min(100, round(($stats['total_raised'] / $stats['goal']) * 100)) : 0;
?>
<div class="flex-between" style="margin-bottom:1.5rem">
  <h1 style="font-size:1.5rem;font-family:var(--font-heading)">Donations</h1>
</div>

<!-- KPI Cards -->
<div class="kpi-grid" style="margin-bottom:1.5rem">
  <div class="kpi-card"><div class="kpi-icon kpi-icon-green"><i class="fa fa-hand-holding-heart"></i></div><div><div class="kpi-value">€<?= number_format($stats['total_raised'], 0) ?></div><div class="kpi-label">Total Raised</div></div></div>
  <div class="kpi-card"><div class="kpi-icon kpi-icon-blue"><i class="fa fa-users"></i></div><div><div class="kpi-value"><?= number_format($stats['total_donors']) ?></div><div class="kpi-label">Unique Donors</div></div></div>
  <div class="kpi-card"><div class="kpi-icon kpi-icon-beige"><i class="fa fa-calendar"></i></div><div><div class="kpi-value">€<?= number_format($stats['this_month'], 0) ?></div><div class="kpi-label">This Month</div></div></div>
  <div class="kpi-card" style="overflow:hidden">
    <div style="padding:1rem 1.25rem;flex:1">
      <div style="font-size:.75rem;color:var(--clr-text-muted);margin-bottom:.5rem">Campaign Progress</div>
      <div style="font-size:1.5rem;font-weight:700;color:var(--clr-primary)"><?= $progress ?>%</div>
      <div class="progress-bar" style="margin:.5rem 0 0"><div class="progress-bar__fill" style="width:<?= $progress ?>%;background:var(--clr-primary)"></div></div>
      <div style="font-size:.75rem;color:var(--clr-text-muted);margin-top:.25rem">€<?= number_format($stats['total_raised'],0) ?> of €<?= number_format($stats['goal'],0) ?></div>
    </div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem">
  <!-- Donations table -->
  <div>
    <div class="admin-table-card">
      <div class="admin-table-card__header"><h3 class="admin-table-card__title">Recent Donations</h3></div>
      <div class="table-wrap">
        <table class="table">
          <thead><tr><th>Donor</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr></thead>
          <tbody>
          <?php if (empty($donations)): ?>
          <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--clr-text-muted)">No donations yet.</td></tr>
          <?php else: ?>
          <?php foreach ($donations as $d): ?>
          <tr>
            <td>
              <div style="font-weight:600"><?= e($d['donor_name'] ?? 'Anonymous') ?></div>
              <?php if ($d['email']): ?><div style="font-size:.75rem;color:var(--clr-text-muted)"><?= e($d['email']) ?></div><?php endif; ?>
            </td>
            <td style="font-weight:700;color:var(--clr-primary)">€<?= number_format($d['amount'], 2) ?></td>
            <td style="font-size:.875rem"><?= e(ucfirst($d['payment_method'] ?? 'stripe')) ?></td>
            <td><span class="badge <?= $d['status'] === 'completed' ? 'badge-green' : ($d['status'] === 'pending' ? 'badge-beige' : 'badge-gray') ?>"><?= ucfirst($d['status']) ?></span></td>
            <td style="font-size:.8rem;color:var(--clr-text-muted)"><?= date('d M Y', strtotime($d['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Campaign Settings -->
  <aside>
    <div class="card">
      <div class="card__body">
        <h3 style="font-size:1rem;margin-bottom:1.25rem"><i class="fa fa-bullseye" style="color:var(--clr-primary)"></i> Campaign Settings</h3>
        <form action="/admin/donations/campaign" method="POST">
          <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
          <div class="form-group">
            <label class="form-label">Fundraising Goal (€)</label>
            <input type="number" name="goal" class="form-control" value="<?= e($campaign['goal']) ?>" min="0" step="100">
          </div>
          <div class="form-group">
            <label class="form-label">Campaign Message</label>
            <textarea name="message" class="form-control" rows="4" placeholder="Why are you raising funds?"><?= e($campaign['message']) ?></textarea>
            <span class="form-hint">Shown on the public donate page</span>
          </div>
          <button type="submit" class="btn btn-primary w-full"><i class="fa fa-save"></i> Save Campaign</button>
        </form>
      </div>
    </div>
    <div class="card" style="margin-top:1rem">
      <div class="card__body">
        <h3 style="font-size:1rem;margin-bottom:.75rem">Payment Integration</h3>
        <p style="font-size:.8rem;color:var(--clr-text-muted);line-height:1.6">Connect Stripe in <a href="/admin/settings" style="color:var(--clr-primary)">Settings</a> to enable real-time donation processing. Donations are handled via HTTPS with secure transaction records stored in your database.</p>
      </div>
    </div>
  </aside>
</div>
