<?php
/**
 * TENIKO — Main Public Layout
 * Variables available: $pageTitle, $metaDesc, $authUser, $content, $flash_*
 */
$settings = function(string $key, mixed $default = '') {
    static $cache = [];
    if (!isset($cache[$key])) {
        try {
            $db = \App\Core\Database::getInstance();
            $row = $db->fetch("SELECT value FROM site_settings WHERE `key`=?", [$key]);
            $cache[$key] = $row ? $row['value'] : $default;
        } catch (\Throwable) { $cache[$key] = $default; }
    }
    return $cache[$key];
};
$siteName = $settings('site_name', 'TENIKO');
$csrfToken = \App\Core\CSRF::generate();
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$isLoggedIn  = \App\Core\Auth::check();
$authUser    = \App\Core\Auth::user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= e($pageTitle ?? $siteName) ?></title>
  <script>/* Prevent FOUC */
  (function(){var t=localStorage.getItem('teniko-theme')||(window.matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light');document.documentElement.setAttribute('data-theme',t);})();
  </script>
  <meta name="description" content="<?= e($metaDesc ?? 'The Living Archive of Malagasy Language & Culture.') ?>">
  <meta name="csrf-token" content="<?= e($csrfToken) ?>">

  <!-- Open Graph -->
  <meta property="og:title"       content="<?= e($pageTitle ?? $siteName) ?>">
  <meta property="og:description" content="<?= e($metaDesc ?? '') ?>">
  <meta property="og:type"        content="website">
  <meta property="og:site_name"   content="TENIKO">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <!-- Animate.css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="/assets/css/main.css">
  <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
</head>
<body <?= $isLoggedIn ? 'data-logged-in="1"' : '' ?>>

<!-- Announcement Banner -->
<?php if (!empty($announcements)) foreach (array_slice($announcements, 0, 1) as $ann): ?>
<div class="announcement announcement--<?= e($ann['type']) ?>">
  <div class="container flex-between" style="gap:1rem">
    <span><i class="fa fa-bullhorn"></i> <strong><?= e($ann['title']) ?>:</strong> <?= e($ann['body']) ?></span>
    <button onclick="this.closest('.announcement').remove()" style="opacity:.6;font-size:1.2rem;background:none;border:none;cursor:pointer">&times;</button>
  </div>
</div>
<?php endforeach; ?>

<!-- ── Navigation ──────────────────────────────────────── -->
<nav class="nav" role="navigation" aria-label="Main navigation">
  <div class="container nav__inner">

    <a href="/" class="nav__logo" aria-label="TENIKO Home">
      <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="32" height="32" rx="8" fill="#2E7D32"/>
        <path d="M16 4 C12 4 8 8 8 13 C8 18 12 20 16 22 C20 20 24 18 24 13 C24 8 20 4 16 4Z" fill="white" opacity="0.9"/>
        <rect x="14" y="22" width="4" height="6" rx="2" fill="white" opacity="0.9"/>
        <path d="M10 28 Q16 26 22 28" stroke="white" stroke-width="1.5" fill="none" stroke-linecap="round" opacity="0.9"/>
      </svg>
      <span><?= e($siteName) ?></span>
    </a>

    <div class="nav__links" role="menubar">
      <a href="/"           class="nav__link <?= $currentPath === '/'           ? 'active' : '' ?>" role="menuitem">Home</a>
      <a href="/dictionary" class="nav__link <?= str_starts_with($currentPath, '/dictionary') || str_starts_with($currentPath, '/word') ? 'active' : '' ?>" role="menuitem">Dictionary</a>
      <a href="/proverbs"   class="nav__link <?= str_starts_with($currentPath, '/proverb') ? 'active' : '' ?>" role="menuitem">Proverbs</a>
      <a href="/culture"    class="nav__link <?= str_starts_with($currentPath, '/culture') || str_starts_with($currentPath, '/article') ? 'active' : '' ?>" role="menuitem">Culture</a>
      <a href="/dialects"   class="nav__link <?= str_starts_with($currentPath, '/dialect') ? 'active' : '' ?>" role="menuitem">Dialects</a>
      <a href="/forums"     class="nav__link <?= str_starts_with($currentPath, '/forum') || str_starts_with($currentPath, '/topic') ? 'active' : '' ?>" role="menuitem">Forums</a>
      <a href="/contribute" class="nav__link <?= str_starts_with($currentPath, '/contribute') ? 'active' : '' ?>" role="menuitem">Contribute</a>
    </div>

    <div class="nav__actions">
      <!-- Theme toggle -->
      <button class="btn-icon btn-ghost" onclick="toggleTheme()" aria-label="Toggle dark mode" title="Toggle theme">
        <i class="fa fa-moon theme-icon"></i>
      </button>

      <?php if ($isLoggedIn): ?>
        <!-- Notifications -->
        <button class="btn-icon btn-ghost" id="notif-btn" aria-label="Notifications" style="position:relative">
          <i class="fa fa-bell"></i>
          <span class="notif-badge" style="position:absolute;top:6px;right:6px;width:8px;height:8px;background:var(--clr-accent);border-radius:50%"></span>
        </button>
        <!-- User menu -->
        <div style="position:relative">
          <a href="/profile/<?= e($authUser['username']) ?>" class="btn btn-ghost btn-sm flex" style="gap:.5rem">
            <span style="width:30px;height:30px;background:var(--clr-primary);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700">
              <?= strtoupper(substr($authUser['display_name'] ?? $authUser['username'], 0, 1)) ?>
            </span>
            <span><?= e($authUser['display_name'] ?? $authUser['username']) ?></span>
          </a>
        </div>
        <?php if ($authUser['role'] === 'admin'): ?>
          <a href="/admin" class="btn btn-primary btn-sm">Admin</a>
        <?php endif; ?>
        <a href="/logout" class="btn btn-ghost btn-sm">Logout</a>
      <?php else: ?>
        <a href="/login"    class="btn btn-ghost btn-sm">Login</a>
        <a href="/register" class="btn btn-primary btn-sm btn-rounded">Join TENIKO</a>
      <?php endif; ?>

      <!-- Mobile hamburger -->
      <button class="nav__burger" aria-label="Open menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</nav>

<!-- ── Mobile Drawer ─────────────────────────────────────── -->
<div class="nav__drawer" role="dialog" aria-modal="true" aria-label="Navigation">
  <div class="nav__drawer__backdrop"></div>
  <div class="nav__drawer__panel">
    <div style="padding:1.5rem 1.5rem 1rem;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--clr-border)">
      <span style="font-family:var(--font-heading);font-weight:700;font-size:1.2rem;color:var(--clr-primary)"><?= e($siteName) ?></span>
      <button class="nav__drawer__close btn-icon btn-ghost"><i class="fa fa-times"></i></button>
    </div>
    <nav style="padding:1rem 0">
      <?php foreach ([
        ['/', 'fa-home', 'Home'],
        ['/dictionary', 'fa-book', 'Dictionary'],
        ['/proverbs', 'fa-scroll', 'Proverbs'],
        ['/culture', 'fa-landmark', 'Culture'],
        ['/dialects', 'fa-map', 'Dialects'],
        ['/forums', 'fa-comments', 'Forums'],
        ['/contribute', 'fa-plus-circle', 'Contribute'],
        ['/about', 'fa-info-circle', 'About'],
        ['/contact', 'fa-envelope', 'Contact'],
      ] as [$href, $icon, $label]): ?>
        <a href="<?= $href ?>" style="display:flex;align-items:center;gap:.75rem;padding:.75rem 1.5rem;color:var(--clr-text);font-size:.95rem;transition:background .15s ease" onmouseover="this.style.background='rgba(46,125,50,.06)'" onmouseout="this.style.background=''">
          <i class="fa <?= $icon ?>" style="width:20px;color:var(--clr-primary)"></i><?= $label ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div style="padding:1rem 1.5rem;border-top:1px solid var(--clr-border);margin-top:auto">
      <?php if ($isLoggedIn): ?>
        <a href="/logout" class="btn btn-outline w-full">Logout</a>
      <?php else: ?>
        <a href="/login" class="btn btn-primary w-full" style="margin-bottom:.5rem">Login</a>
        <a href="/register" class="btn btn-outline w-full">Register</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ── Flash Messages ─────────────────────────────────────── -->
<?php if (!empty($flash_success) || !empty($flash_error) || !empty($flash_info)): ?>
<div class="container" style="padding-top:1rem">
  <?php if (!empty($flash_success)): ?><div class="alert alert-success" data-auto-dismiss><i class="fa fa-check-circle"></i> <?= e($flash_success) ?></div><?php endif; ?>
  <?php if (!empty($flash_error)):   ?><div class="alert alert-error"   data-auto-dismiss><i class="fa fa-times-circle"></i> <?= e($flash_error) ?></div><?php endif; ?>
  <?php if (!empty($flash_info)):    ?><div class="alert alert-info"    data-auto-dismiss><i class="fa fa-info-circle"></i> <?= e($flash_info) ?></div><?php endif; ?>
</div>
<?php endif; ?>

<!-- ── Page Content ──────────────────────────────────────── -->
<main id="main-content" tabindex="-1">
  <?= $content ?>
</main>

<!-- ── Footer ────────────────────────────────────────────── -->
<footer class="footer" role="contentinfo">
  <div class="container">
    <div class="footer__grid">
      <div>
        <div class="footer__logo">TENIKO</div>
        <p class="footer__desc">The Living Archive of Malagasy Language & Culture. Preserving and celebrating Madagascar's linguistic heritage for generations to come.</p>
        <div class="flex gap-4" style="margin-top:1.5rem">
          <a href="#" class="btn-icon btn-ghost" style="color:rgba(255,255,255,.6)" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
          <a href="#" class="btn-icon btn-ghost" style="color:rgba(255,255,255,.6)" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
          <a href="#" class="btn-icon btn-ghost" style="color:rgba(255,255,255,.6)" aria-label="GitHub"><i class="fab fa-github"></i></a>
        </div>
      </div>
      <div>
        <div class="footer__heading">Explore</div>
        <a href="/dictionary" class="footer__link">Dictionary</a>
        <a href="/proverbs"   class="footer__link">Proverbs</a>
        <a href="/culture"    class="footer__link">Culture</a>
        <a href="/dialects"   class="footer__link">Dialects</a>
      </div>
      <div>
        <div class="footer__heading">Community</div>
        <a href="/forums"     class="footer__link">Forums</a>
        <a href="/contribute" class="footer__link">Contribute</a>
        <a href="/register"   class="footer__link">Join TENIKO</a>
      </div>
      <div>
        <div class="footer__heading">About</div>
        <a href="/about"      class="footer__link">About Us</a>
        <a href="/contact"    class="footer__link">Contact</a>
        <a href="/sitemap.xml" class="footer__link">Sitemap</a>
        <!-- Newsletter mini-form -->
        <div style="margin-top:1rem">
          <form action="/newsletter/subscribe" method="POST" style="display:flex;gap:.5rem;margin-top:.5rem">
            <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
            <input type="email" name="email" placeholder="Your email" required aria-label="Subscribe to newsletter"
              style="flex:1;padding:.5rem .75rem;border-radius:var(--radius-md);border:1px solid rgba(255,255,255,.2);background:rgba(255,255,255,.08);color:white;font-size:.8rem">
            <button type="submit" class="btn btn-primary btn-sm">Subscribe</button>
          </form>
        </div>
      </div>
    </div>
    <div class="footer__bottom">
      <span><?= e($settings('footer_text', '© 2026 TENIKO. All rights reserved.')) ?></span>
      <span>Built with ❤️ for Malagasy Culture</span>
    </div>
  </div>
</footer>

<!-- ── Scripts ────────────────────────────────────────────── -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="/assets/js/app.js"></script>
<script src="/assets/js/search.js"></script>
</body>
</html>
