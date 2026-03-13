<?php
$csrfToken  = \App\Core\CSRF::generate();
$authUser   = \App\Core\Auth::user();
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

$navItems = [
  ['/admin',             'fa-tachometer-alt',        'Dashboard'],
  // ── Content ──────────────────────────────
  ['---content', '', ''],
  ['/admin/words',       'fa-book-open',             'Words'],
  ['/admin/proverbs',    'fa-scroll',                'Proverbs'],
  ['/admin/articles',    'fa-newspaper',             'Articles'],
  ['/admin/dialects',    'fa-map-marker-alt',        'Dialects'],
  ['/admin/moderation',  'fa-shield-alt',            'Submissions'],
  // ── Community ──────────────────────────────
  ['---community', '', ''],
  ['/admin/users',       'fa-users',                 'Users'],
  ['/admin/comments',    'fa-comments',              'Comments'],
  ['/admin/forums',      'fa-comment-dots',          'Forums'],
  // ── Communication ──────────────────────────
  ['---communication', '', ''],
  ['/admin/contact',     'fa-inbox',                 'Contact Inbox'],
  ['/admin/newsletter',  'fa-envelope-open-text',    'Newsletter'],
  ['/admin/ads',         'fa-ad',                    'Advertising'],
  ['/admin/donations',   'fa-hand-holding-heart',    'Donations'],
  // ── System ──────────────────────────────
  ['---system', '', ''],
  ['/admin/settings',    'fa-cog',                   'Settings'],
  ['/admin/analytics',   'fa-chart-bar',             'Analytics'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ?? 'Admin') ?> — TENIKO</title>
  <script>/* Prevent FOUC */
  (function(){var t=localStorage.getItem('teniko-theme')||(window.matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light');document.documentElement.setAttribute('data-theme',t);})();
  </script>
  <meta name="csrf-token" content="<?= e($csrfToken) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/assets/css/main.css">
  <link rel="stylesheet" href="/assets/css/admin.css">
  <link rel="stylesheet" href="/assets/css/supplement.css">
</head>
<body class="admin-body">

<!-- ── Sidebar ──────────────────────────────────────────── -->
<aside class="admin-sidebar" id="admin-sidebar" role="navigation" aria-label="Admin navigation">
  <a href="/admin" class="admin-sidebar__brand">
    <img src="/assets/imgs/teniko2.png" alt="TENIKO" width="28" height="28" style="object-fit:contain;border-radius:4px;flex-shrink:0;">
    <span>TENIKO</span>
    <span class="badge-admin">Admin</span>
  </a>
  <nav class="admin-nav">
    <?php $section = ''; foreach ($navItems as [$href, $icon, $label]):
      if (strpos($href, '---') === 0) { $section = str_replace('---','', $href); continue; }
    ?>
    <?php if ($section): ?>
      <div class="admin-nav__section"><?= ucfirst($section) ?></div>
      <?php $section = ''; ?>
    <?php endif; ?>
    <a href="<?= $href ?>" class="admin-nav__item <?= (strpos($currentPath, $href) === 0 && $href !== '/admin') || $currentPath === $href ? 'active' : '' ?>">
      <i class="fa <?= $icon ?>"></i>
      <span><?= $label ?></span>
    </a>
    <?php endforeach; ?>
    <div style="padding:1rem 1.25rem;margin-top:auto">
      <a href="/" class="admin-nav__item" target="_blank" rel="noopener"><i class="fa fa-external-link-alt"></i><span>View Site</span></a>
      <a href="/logout" class="admin-nav__item"><i class="fa fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
  </nav>
</aside>

<!-- ── Main ─────────────────────────────────────────────── -->
<div class="admin-main">
  <header class="admin-topbar">
    <button class="btn-icon btn-ghost" id="sidebar-toggle" aria-label="Toggle sidebar">
      <i class="fa fa-bars"></i>
    </button>
    <h1 class="admin-topbar__title"><?= e($pageTitle ?? 'Dashboard') ?></h1>
    <div class="admin-topbar__actions">
      <button class="btn-icon btn-ghost" onclick="toggleTheme()" title="Toggle theme"><i class="fa fa-moon" id="theme-icon"></i></button>
      <a href="/contribute" class="btn btn-sm btn-outline" target="_blank"><i class="fa fa-eye"></i> View Site</a>
      <div class="admin-topbar__user">
        <span style="font-weight:600"><?= e($authUser['display_name'] ?? $authUser['username'] ?? 'Admin') ?></span>
        <span class="badge badge-green"><?= e($authUser['role'] ?? 'admin') ?></span>
      </div>
    </div>
  </header>

  <!-- Flash messages as floating toasts -->
  <div id="toast-container" role="region" aria-live="polite" aria-label="Notifications"></div>
  <?php
  $_adminToasts = [];
  if (!empty($flash_success)) $_adminToasts[] = ['success', 'fa-check-circle',      e($flash_success)];
  if (!empty($flash_error))   $_adminToasts[] = ['error',   'fa-times-circle',      e($flash_error)];
  if (!empty($flash_info))    $_adminToasts[] = ['info',    'fa-info-circle',        e($flash_info)];
  ?>
  <?php if (!empty($_adminToasts)): ?>
  <script>
  document.addEventListener('DOMContentLoaded', function(){
    var toasts = <?= json_encode($_adminToasts, JSON_HEX_TAG | JSON_HEX_AMP) ?>;
    toasts.forEach(function(t){ window.__showToast && window.__showToast(t[0], t[1], t[2]); });
  });
  </script>
  <?php endif; ?>

  <main class="admin-content">
    <?= $content ?>
  </main>
</div>

<script src="/assets/js/app.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script src="/assets/js/admin.js"></script>
<script>
  // Sidebar toggle for mobile
  document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
    document.getElementById('admin-sidebar')?.classList.toggle('open');
  });
  // Theme icon sync
  const theme = document.documentElement.getAttribute('data-theme');
  const icon  = document.getElementById('theme-icon');
  if (icon) icon.className = theme === 'dark' ? 'fa fa-sun' : 'fa fa-moon';
</script>
</body>
</html>
