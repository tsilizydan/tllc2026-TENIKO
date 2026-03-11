<?php
/**
 * Admin Dashboard View
 * Variables: $stats, $recentWords, $recentUsers
 */
?>
<!-- KPI Cards -->
<div class="kpi-grid">
  <?php
  $kpis = [
    ['total_words',     'fa-book-open',           'kpi-icon-green', 'Words Published'],
    ['total_proverbs',  'fa-scroll',              'kpi-icon-beige', 'Proverbs'],
    ['total_users',     'fa-users',               'kpi-icon-blue',  'Registered Users'],
    ['total_articles',  'fa-newspaper',           'kpi-icon-green', 'Articles'],
    ['pending_words',   'fa-hourglass-half',      'kpi-icon-beige', 'Pending Words'],
    ['pending_contrib', 'fa-inbox',               'kpi-icon-red',   'Pending Submissions'],
    ['forum_topics',    'fa-comments',            'kpi-icon-blue',  'Forum Topics'],
    ['newsletter_subs', 'fa-envelope-open-text',  'kpi-icon-green', 'Newsletter Subscribers'],
  ];
  foreach ($kpis as [$key, $icon, $iconClass, $label]):
    $val = $stats[$key] ?? 0;
  ?>
  <div class="kpi-card">
    <div class="kpi-card__icon <?= $iconClass ?>"><i class="fa <?= $icon ?>"></i></div>
    <div class="kpi-card__body">
      <div class="kpi-card__number"><?= number_format($val) ?></div>
      <div class="kpi-card__label"><?= $label ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Charts -->
<div class="chart-grid">
  <div class="chart-card">
    <div class="chart-card__header">
      <h3 class="chart-card__title">Traffic & Search (Last 30 Days)</h3>
      <span class="badge badge-green"><i class="fa fa-chart-line"></i> Live</span>
    </div>
    <div class="chart-container">
      <canvas id="chart-traffic" aria-label="Traffic chart"></canvas>
    </div>
  </div>
  <div class="chart-card">
    <div class="chart-card__header">
      <h3 class="chart-card__title">Top Searched Words</h3>
    </div>
    <div class="chart-container">
      <canvas id="chart-top-words" aria-label="Top words chart"></canvas>
    </div>
  </div>
</div>

<!-- Recent Content -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">

  <!-- Recent Words -->
  <div class="admin-table-card">
    <div class="admin-table-card__header">
      <h3 class="admin-table-card__title">Recent Words</h3>
      <a href="/admin/words" class="btn btn-sm btn-ghost">View All <i class="fa fa-arrow-right"></i></a>
    </div>
    <div class="table-wrap">
      <table class="table" role="grid">
        <thead>
          <tr>
            <th>Word</th>
            <th>Author</th>
            <th>Status</th>
            <th>Added</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentWords as $w): ?>
          <tr>
            <td><a href="/word/<?= e($w['slug']) ?>" style="color:var(--clr-primary);font-weight:600"><?= e($w['word']) ?></a></td>
            <td><?= e($w['author'] ?? '—') ?></td>
            <td><span class="status-badge status-<?= e($w['status']) ?>"><?= ucfirst($w['status']) ?></span></td>
            <td style="white-space:nowrap"><?= formatDate($w['created_at']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Recent Users -->
  <div class="admin-table-card">
    <div class="admin-table-card__header">
      <h3 class="admin-table-card__title">Recent Users</h3>
      <a href="/admin/users" class="btn btn-sm btn-ghost">View All <i class="fa fa-arrow-right"></i></a>
    </div>
    <div class="table-wrap">
      <table class="table" role="grid">
        <thead>
          <tr>
            <th>User</th>
            <th>Role</th>
            <th>Status</th>
            <th>Joined</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentUsers as $u): ?>
          <tr>
            <td>
              <a href="/admin/users/<?= $u['id'] ?>" style="font-weight:600;color:var(--clr-text)"><?= e($u['username']) ?></a>
              <div style="font-size:.75rem;color:var(--clr-text-muted)"><?= e($u['email']) ?></div>
            </td>
            <td><span class="badge <?= $u['role'] === 'admin' ? 'badge-red' : 'badge-green' ?>"><?= ucfirst($u['role']) ?></span></td>
            <td><span class="status-badge status-<?= e($u['status']) ?>"><?= ucfirst($u['status']) ?></span></td>
            <td style="white-space:nowrap"><?= formatDate($u['created_at']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="card" style="margin-top:1.5rem">
  <div class="card__body">
    <h3 style="margin-bottom:1rem">Quick Actions</h3>
    <div class="flex flex-wrap gap-4">
      <a href="/admin/words/create"    class="btn btn-primary"><i class="fa fa-plus"></i> Add Word</a>
      <a href="/admin/proverbs/create" class="btn btn-outline"><i class="fa fa-plus"></i> Add Proverb</a>
      <a href="/admin/articles/create" class="btn btn-outline"><i class="fa fa-plus"></i> New Article</a>
      <a href="/admin/moderation"      class="btn btn-outline"><i class="fa fa-shield-alt"></i> Moderation Queue
        <?php if (($stats['pending_contrib'] ?? 0) > 0): ?>
          <span class="admin-nav__badge"><?= $stats['pending_contrib'] ?></span>
        <?php endif; ?>
      </a>
      <a href="/admin/settings"        class="btn btn-ghost"><i class="fa fa-cog"></i> Settings</a>
    </div>
  </div>
</div>
