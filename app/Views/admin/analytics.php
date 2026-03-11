<?php // Admin: Analytics — no extra variables needed ?>
<div style="margin-bottom:1.5rem">
  <h1 style="font-size:1.5rem;font-family:var(--font-heading)">Analytics Overview</h1>
</div>
<div class="kpi-grid" style="margin-bottom:2rem">
  <?php
  $db = \App\Core\Database::getInstance();
  $kpis = [
    ['Words', $db->count("SELECT COUNT(*) FROM words WHERE status='published' AND deleted_at IS NULL"), 'fa-book-open', 'kpi-icon-green'],
    ['Proverbs', $db->count("SELECT COUNT(*) FROM proverbs WHERE status='published' AND deleted_at IS NULL"), 'fa-scroll', 'kpi-icon-brown'],
    ['Users', $db->count("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL"), 'fa-users', 'kpi-icon-green'],
    ['Today Searches', $db->count("SELECT COUNT(*) FROM search_logs WHERE DATE(created_at) = CURDATE()"), 'fa-search', 'kpi-icon-brown'],
  ];
  foreach ($kpis as [$label, $value, $icon, $cls]): ?>
  <div class="kpi-card">
    <div class="kpi-icon <?= $cls ?>"><i class="fa <?= $icon ?>"></i></div>
    <div>
      <div class="kpi-value"><?= number_format($value) ?></div>
      <div class="kpi-label"><?= $label ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<p style="color:var(--clr-text-muted);font-size:.875rem">Detailed chart analytics require integration with a charting library. Charts are available on the main admin dashboard.</p>
<a href="/admin" class="btn btn-outline btn-sm"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
