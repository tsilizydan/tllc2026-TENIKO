<?php
$csrfToken  = \App\Core\CSRF::generate();
$authUser   = \App\Core\Auth::user();
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

$navItems = [
  ['/admin',            'fa-tachometer-alt', 'Dashboard'],
  ['/admin/words',      'fa-book-open',      'Words'],
  ['/admin/proverbs',   'fa-scroll',         'Proverbs'],
  ['/admin/articles',   'fa-newspaper',      'Articles'],
  ['/admin/moderation', 'fa-shield-alt',     'Moderation'],
  ['---content---', '', ''],
  ['/admin/users',      'fa-users',          'Users'],
  ['/admin/ads',        'fa-ad',             'Advertising'],
  ['/admin/donations',  'fa-hand-holding-heart','Donations'],
  ['/admin/newsletter', 'fa-envelope-open-text','Newsletter'],
  ['---system---', '', ''],
  ['/admin/settings',   'fa-cog',            'Settings'],
  ['/admin/analytics',  'fa-chart-bar',      'Analytics'],
];
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ?? 'Admin') ?> — TENIKO</title>
  <meta name="csrf-token" content="<?= e($csrfToken) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/assets/css/main.css">
  <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-body">

<!-- ── Sidebar ──────────────────────────────────────────── -->
<aside class="admin-sidebar" id="admin-sidebar" role="navigation" aria-label="Admin navigation">
  <a href="/admin" class="admin-sidebar__brand">
    <span>TENIKO</span>
    <span class="badge-admin">Admin</span>
  </a>
  <nav class="admin-nav">
    <?php $section = ''; foreach ($navItems as [$href, $icon, $label]):
      if (str_starts_with($href, '---')) { $section = str_replace('---','', $href); continue; }
    ?>
    <?php if ($section): ?>
      <div class="admin-nav__section"><?= ucfirst($section) ?></div>
      <?php $section = ''; ?>
    <?php endif; ?>
    <a href="<?= $href ?>" class="admin-nav__item <?= str_starts_with($currentPath, $href) && $href !== '/admin' || $currentPath === $href ? 'active' : '' ?>">
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

  <!-- Flash messages -->
  <?php if (!empty($flash_success) || !empty($flash_error) || !empty($flash_info)): ?>
  <div style="padding:1rem 2rem 0">
    <?php if (!empty($flash_success)): ?><div class="alert alert-success" data-auto-dismiss><i class="fa fa-check-circle"></i> <?= e($flash_success) ?></div><?php endif; ?>
    <?php if (!empty($flash_error)):   ?><div class="alert alert-error"   data-auto-dismiss><i class="fa fa-times-circle"></i> <?= e($flash_error) ?></div><?php endif; ?>
    <?php if (!empty($flash_info)):    ?><div class="alert alert-info"    data-auto-dismiss><i class="fa fa-info-circle"></i> <?= e($flash_info) ?></div><?php endif; ?>
  </div>
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
