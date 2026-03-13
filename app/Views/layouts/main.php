<?php
/**
 * TENIKO — Main Public Layout (Production-Ready)
 * Variables: $pageTitle, $metaDesc, $authUser, $content, $flash_*
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
$siteName    = $settings('site_name', 'TENIKO');
$csrfToken   = \App\Core\CSRF::generate();
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$isLoggedIn  = \App\Core\Auth::check();
$authUser    = \App\Core\Auth::user();

// Helper: active nav class (PHP 7.4 compatible, no fn() arrow function)
$navActive = function() use ($currentPath) {
    $prefixes = func_get_args();
    foreach ($prefixes as $p) {
        if ($p === '/' ? $currentPath === '/' : strpos($currentPath, $p) === 0) return 'active';
    }
    return '';
};

// Announcements
$announcements = [];
try {
    $db2 = \App\Core\Database::getInstance();
    $announcements = $db2->fetchAll(
        "SELECT * FROM announcements WHERE is_active=1 AND (expires_at IS NULL OR expires_at > NOW()) ORDER BY created_at DESC LIMIT 1"
    );
} catch (\Throwable) {}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= e($pageTitle ?? $siteName) ?></title>
  <?php if (!empty($metaDesc)): ?>
  <meta name="description" content="<?= e($metaDesc) ?>">
  <?php endif; ?>
  <meta name="theme-color" content="#2E7D32">
  <meta property="og:site_name" content="<?= e($siteName) ?>">
  <meta property="og:title" content="<?= e($pageTitle ?? $siteName) ?>">
  <?php if (!empty($metaDesc)): ?>
  <meta property="og:description" content="<?= e($metaDesc) ?>">
  <?php endif; ?>

  <script>
    /* Prevent FOUC — apply theme before render */
    (function(){
      var t = localStorage.getItem('teniko-theme') ||
              (window.matchMedia('(prefers-color-scheme:dark)').matches ? 'dark' : 'light');
      document.documentElement.setAttribute('data-theme', t);
    })();
  </script>

  <!-- Favicons -->
  <link rel="icon" type="image/png" href="/assets/imgs/favicon/favicon-96x96.png" sizes="96x96">
  <link rel="icon" type="image/svg+xml" href="/assets/imgs/favicon/favicon.svg">
  <link rel="shortcut icon" href="/assets/imgs/favicon/favicon.ico">
  <link rel="apple-touch-icon" sizes="180x180" href="/assets/imgs/favicon/apple-touch-icon.png">
  <meta name="apple-mobile-web-app-title" content="TENIKO">
  <link rel="manifest" href="/assets/imgs/favicon/site.webmanifest">

  <!-- Font Awesome & Fonts -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&display=swap">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="/assets/css/main.css">
  <link rel="stylesheet" href="/assets/css/supplement.css">
</head>
<body <?= $isLoggedIn ? 'data-logged-in="1"' : '' ?>>

<!-- ── Announcement Banner ──────────────────────────────── -->
<?php if (!empty($announcements)): $ann = $announcements[0]; ?>
<div class="announcement announcement--<?= e($ann['type'] ?? 'info') ?>" style="background:var(--clr-primary);color:white;padding:.625rem 1rem;font-size:.85rem;text-align:center;position:relative">
  <div class="container" style="display:flex;align-items:center;justify-content:center;gap:1rem">
    <span><i class="fa fa-bullhorn"></i> <strong><?= e($ann['title']) ?>:</strong> <?= e($ann['body']) ?></span>
    <button onclick="this.closest('.announcement').remove()" style="position:absolute;right:1rem;top:50%;transform:translateY(-50%);opacity:.7;font-size:1.1rem;background:none;border:none;cursor:pointer;color:white" aria-label="Dismiss">&times;</button>
  </div>
</div>
<?php endif; ?>

<!-- ── Navigation ──────────────────────────────────────── -->
<nav class="nav" role="navigation" aria-label="Main navigation" id="main-nav">
  <div class="container nav__inner">

    <!-- Logo -->
    <a href="/" class="nav__logo" aria-label="TENIKO Home">
      <img src="/assets/imgs/teniko2.png" alt="TENIKO logo" width="38" height="38" style="object-fit:contain;border-radius:4px;flex-shrink:0;display:block;">
      <span class="nav__logo-text"><?= e($siteName) ?></span>
    </a>

    <!-- Desktop nav links -->
    <div class="nav__links" role="menubar" aria-label="Main menu">
      <a href="/"           class="nav__link <?= $navActive('/')          ?>" role="menuitem">Home</a>
      <a href="/dictionary" class="nav__link <?= $navActive('/dictionary', '/word') ?>" role="menuitem">Dictionary</a>
      <a href="/proverbs"   class="nav__link <?= $navActive('/proverb')   ?>" role="menuitem">Proverbs</a>
      <a href="/culture"    class="nav__link <?= $navActive('/culture', '/article') ?>" role="menuitem">Culture</a>
      <a href="/dialects"   class="nav__link <?= $navActive('/dialect')   ?>" role="menuitem">Dialects</a>
      <a href="/forums"     class="nav__link <?= $navActive('/forum', '/topic') ?>" role="menuitem">Forums</a>
      <a href="/contribute" class="nav__link <?= $navActive('/contribute') ?>" role="menuitem">Contribute</a>
    </div>

    <!-- Actions: theme + auth + hamburger -->
    <div class="nav__actions">
      <!-- Dark/Light toggle -->
      <button class="btn-icon btn-ghost nav__theme-btn" onclick="toggleTheme()" aria-label="Toggle dark/light mode" title="Toggle theme" id="theme-btn">
        <i class="fa fa-moon theme-icon" id="theme-icon"></i>
      </button>

      <?php if ($isLoggedIn && $authUser): ?>
        <!-- Notifications (logged in) -->
        <button class="btn-icon btn-ghost nav__notif-btn" id="notif-btn" aria-label="Notifications" style="position:relative">
          <i class="fa fa-bell"></i>
          <span class="notif-badge" aria-hidden="true"></span>
        </button>
        <!-- User avatar dropdown -->
        <div class="nav__user-wrap" style="position:relative" id="user-menu-wrap">
          <button class="nav__user-btn btn btn-ghost btn-sm" id="user-menu-btn" aria-haspopup="true" aria-expanded="false" onclick="toggleUserMenu()">
            <span class="nav__avatar" aria-hidden="true"><?= strtoupper(substr($authUser['display_name'] ?? $authUser['username'] ?? 'U', 0, 1)) ?></span>
            <span class="nav__username"><?= e($authUser['display_name'] ?? $authUser['username'] ?? '') ?></span>
            <i class="fa fa-chevron-down" style="font-size:.65rem;opacity:.6"></i>
          </button>
          <div class="nav__dropdown" id="user-dropdown" role="menu" aria-labelledby="user-menu-btn" hidden>
            <a href="/profile/<?= e($authUser['username'] ?? '') ?>" class="nav__dropdown-item" role="menuitem"><i class="fa fa-user"></i> My Profile</a>
            <a href="/settings" class="nav__dropdown-item" role="menuitem"><i class="fa fa-cog"></i> Settings</a>
            <?php if (($authUser['role'] ?? '') === 'admin'): ?>
            <hr class="nav__dropdown-sep">
            <a href="/admin" class="nav__dropdown-item" role="menuitem"><i class="fa fa-shield-alt"></i> Admin Panel</a>
            <?php endif; ?>
            <hr class="nav__dropdown-sep">
            <a href="/logout" class="nav__dropdown-item nav__dropdown-item--danger" role="menuitem"><i class="fa fa-sign-out-alt"></i> Sign Out</a>
          </div>
        </div>
      <?php else: ?>
        <a href="/login"    class="btn btn-ghost  btn-sm nav__login-btn">Login</a>
        <a href="/register" class="btn btn-primary btn-sm btn-rounded nav__register-btn">Join TENIKO</a>
      <?php endif; ?>

      <!-- Hamburger (mobile only) -->
      <button class="nav__burger" id="nav-burger" aria-label="Open menu" aria-expanded="false" aria-controls="mobile-drawer">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</nav>

<!-- ── Mobile Drawer ─────────────────────────────────────── -->
<div class="nav__drawer" id="mobile-drawer" role="dialog" aria-modal="true" aria-label="Navigation menu" hidden>
  <div class="nav__drawer__backdrop" id="drawer-backdrop"></div>
  <div class="nav__drawer__panel">
    <!-- Drawer header -->
    <div class="nav__drawer__header">
      <a href="/" class="nav__logo" style="text-decoration:none" aria-label="TENIKO Home">
        <img src="/assets/imgs/teniko2.png" alt="TENIKO" width="32" height="32" style="object-fit:contain;border-radius:4px;flex-shrink:0;">
        <span class="nav__drawer__title"><?= e($siteName) ?></span>
      </a>
      <button class="nav__drawer__close btn-icon btn-ghost" id="drawer-close" aria-label="Close menu">
        <i class="fa fa-times"></i>
      </button>
    </div>

    <!-- User info in drawer (logged in) -->
    <?php if ($isLoggedIn && $authUser): ?>
    <div class="nav__drawer__user">
      <span class="nav__avatar nav__avatar--lg"><?= strtoupper(substr($authUser['display_name'] ?? $authUser['username'] ?? 'U', 0, 1)) ?></span>
      <div>
        <div style="font-weight:700;font-size:.95rem"><?= e($authUser['display_name'] ?? $authUser['username'] ?? '') ?></div>
        <div style="font-size:.75rem;color:var(--clr-text-muted)"><?= e($authUser['email'] ?? '') ?></div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Nav links -->
    <nav class="nav__drawer__links" aria-label="Mobile navigation">
      <?php
      $drawerLinks = [
        ['/', 'fa-home', 'Home'],
        ['/dictionary', 'fa-book', 'Dictionary'],
        ['/proverbs', 'fa-scroll', 'Proverbs'],
        ['/culture', 'fa-landmark', 'Culture'],
        ['/dialects', 'fa-map-marked-alt', 'Dialects'],
        ['/forums', 'fa-comments', 'Forums'],
        ['/contribute', 'fa-plus-circle', 'Contribute'],
        ['/about', 'fa-info-circle', 'About'],
        ['/contact', 'fa-envelope', 'Contact'],
        ['/donate', 'fa-heart', 'Donate ❤️'],
      ];
      foreach ($drawerLinks as [$href, $icon, $label]):
        $isActive = ($href === '/' ? $currentPath === '/' : strpos($currentPath, $href) === 0);
      ?>
      <a href="<?= $href ?>" class="nav__drawer__link <?= $isActive ? 'nav__drawer__link--active' : '' ?>">
        <i class="fa <?= $icon ?>" aria-hidden="true"></i>
        <span><?= $label ?></span>
        <?php if ($isActive): ?><i class="fa fa-chevron-right nav__drawer__link-caret" aria-hidden="true"></i><?php endif; ?>
      </a>
      <?php endforeach; ?>
    </nav>

    <!-- Drawer footer auth -->
    <div class="nav__drawer__footer">
      <?php if ($isLoggedIn): ?>
        <?php if (($authUser['role'] ?? '') === 'admin'): ?>
        <a href="/admin" class="btn btn-outline w-full" style="margin-bottom:.5rem"><i class="fa fa-shield-alt"></i> Admin Panel</a>
        <?php endif; ?>
        <a href="/logout" class="btn btn-ghost w-full"><i class="fa fa-sign-out-alt"></i> Sign Out</a>
      <?php else: ?>
        <a href="/login"    class="btn btn-primary w-full" style="margin-bottom:.5rem"><i class="fa fa-sign-in-alt"></i> Sign In</a>
        <a href="/register" class="btn btn-outline w-full">Create Free Account</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ── Flash Messages ─────────────────────────────────────── -->
<?php if (!empty($flash_success) || !empty($flash_error) || !empty($flash_info) || !empty($flash_warning)): ?>
<div class="flash-bar container" role="status" aria-live="polite">
  <?php if (!empty($flash_success)): ?><div class="alert alert-success" data-auto-dismiss><i class="fa fa-check-circle"></i> <?= e($flash_success) ?></div><?php endif; ?>
  <?php if (!empty($flash_error)):   ?><div class="alert alert-error"   data-auto-dismiss><i class="fa fa-times-circle"></i> <?= e($flash_error) ?></div><?php endif; ?>
  <?php if (!empty($flash_info)):    ?><div class="alert alert-info"    data-auto-dismiss><i class="fa fa-info-circle"></i> <?= e($flash_info) ?></div><?php endif; ?>
  <?php if (!empty($flash_warning)): ?><div class="alert alert-warning" data-auto-dismiss><i class="fa fa-exclamation-triangle"></i> <?= e($flash_warning) ?></div><?php endif; ?>
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
      <div class="footer__brand">
        <a href="/" style="display:flex;align-items:center;gap:.75rem;margin-bottom:.875rem;text-decoration:none">
          <img src="/assets/imgs/teniko2.png" alt="TENIKO" width="44" height="44" style="object-fit:contain;border-radius:6px;filter:brightness(0) invert(1) opacity(.9);flex-shrink:0;">
          <div class="footer__logo"><?= e($siteName) ?></div>
        </a>
        <p class="footer__desc">The Living Archive of Malagasy Language &amp; Culture. Preserving and celebrating Madagascar's linguistic heritage for generations to come.</p>
        <div style="display:flex;gap:.75rem;margin-top:1.25rem">
          <a href="#" class="btn-icon" style="color:rgba(255,255,255,.5);font-size:1.1rem" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
          <a href="#" class="btn-icon" style="color:rgba(255,255,255,.5);font-size:1.1rem" aria-label="Twitter/X"><i class="fab fa-twitter"></i></a>
          <a href="#" class="btn-icon" style="color:rgba(255,255,255,.5);font-size:1.1rem" aria-label="GitHub"><i class="fab fa-github"></i></a>
          <a href="#" class="btn-icon" style="color:rgba(255,255,255,.5);font-size:1.1rem" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
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
        <a href="/donate"     class="footer__link"><i class="fa fa-heart" style="color:#e53935;font-size:.75rem"></i> Donate</a>
      </div>
      <div>
        <div class="footer__heading">About</div>
        <a href="/about"      class="footer__link">About Us</a>
        <a href="/contact"    class="footer__link">Contact</a>
        <a href="/sitemap.xml" class="footer__link">Sitemap</a>
        <!-- Newsletter mini-form -->
        <div style="margin-top:1.25rem">
          <p style="font-size:.78rem;color:rgba(255,255,255,.55);margin-bottom:.5rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px">Newsletter</p>
          <form id="footer-newsletter-form" style="display:flex;gap:.375rem">
            <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
            <input type="email" name="email" placeholder="Your email" required aria-label="Subscribe to newsletter"
              style="flex:1;padding:.5rem .75rem;border-radius:var(--radius-md);border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.07);color:white;font-size:.8rem;min-width:0;transition:border-color .2s"
              onfocus="this.style.borderColor='rgba(255,255,255,.4)'" onblur="this.style.borderColor='rgba(255,255,255,.15)'">
            <button type="submit" class="btn btn-primary btn-sm">Go</button>
          </form>
        </div>
      </div>
    </div>
    <div class="footer__bottom">
      <span><?= e($settings('footer_text', '© 2026 TENIKO. All rights reserved.')) ?></span>
      <span style="opacity:.5">Built with ❤️ for Malagasy Culture</span>
    </div>
  </div>
</footer>

<!-- ── Donation Floating Widget ──────────────────────────── -->
<?php
$donateFloat = false;
try {
    $fDb = \App\Core\Database::getInstance();
    $fSetting   = $fDb->fetch("SELECT value FROM site_settings WHERE `key`='donate_float_enabled'");
    $donateFloat = ($fSetting['value'] ?? '0') === '1';
    if ($donateFloat) {
        $donateMsg     = $fDb->fetch("SELECT value FROM site_settings WHERE `key`='donate_float_message'")['value'] ?? 'Help us preserve Malagasy language & culture!';
        $donateGoal    = (float)($fDb->fetch("SELECT value FROM site_settings WHERE `key`='donation_goal'")['value'] ?? 5000);
        $donateRaised  = (float)($fDb->fetch("SELECT COALESCE(SUM(amount),0) AS v FROM donations WHERE status='completed'")['v'] ?? 0);
        $donateProgress= $donateGoal > 0 ? min(100, round(($donateRaised / $donateGoal) * 100)) : 0;
    }
} catch (\Throwable) {}
?>
<?php if ($donateFloat): ?>
<div id="donate-float" class="donate-float" role="complementary" aria-label="Donation widget">
  <button class="donate-float__close" onclick="closeDonateFloat()" aria-label="Close donation box"><i class="fa fa-times"></i></button>
  <div class="donate-float__icon"><i class="fa fa-heart"></i></div>
  <h3 class="donate-float__title">Support TENIKO</h3>
  <p class="donate-float__msg"><?= e($donateMsg ?? '') ?></p>
  <?php if (($donateGoal ?? 0) > 0): ?>
  <div class="donate-float__progress">
    <div class="donate-float__bar"><div style="width:<?= $donateProgress ?? 0 ?>%"></div></div>
    <span>€<?= number_format($donateRaised ?? 0, 0) ?> of €<?= number_format($donateGoal ?? 0, 0) ?></span>
  </div>
  <?php endif; ?>
  <a href="/donate" class="btn btn-primary btn-sm w-full" style="margin-top:.75rem"><i class="fa fa-heart"></i> Donate Now</a>
</div>
<script>
function closeDonateFloat() {
  var el = document.getElementById('donate-float');
  if (el) el.style.display = 'none';
  sessionStorage.setItem('teniko-donate-dismissed', '1');
}
if (sessionStorage.getItem('teniko-donate-dismissed')) {
  var el = document.getElementById('donate-float');
  if (el) el.style.display = 'none';
}
</script>
<?php endif; ?>

<!-- ── Newsletter AJAX ──────────────────────────────────── -->
<script>
(function(){
  var f = document.getElementById('footer-newsletter-form');
  if (!f) return;
  f.addEventListener('submit', async function(e) {
    e.preventDefault();
    var fd = new FormData(f);
    var btn = f.querySelector('button[type="submit"]');
    if (btn) { btn.disabled = true; btn.textContent = '…'; }
    try {
      var r = await fetch('/newsletter/subscribe', {method:'POST', body: fd});
      var ct = r.headers.get('content-type') || '';
      if (ct.includes('application/json')) {
        var d = await r.json();
        window.showToast && window.showToast(d.error ? d.error : 'Subscribed! Thank you.', d.error ? 'error' : 'success');
      } else {
        f.reset();
        window.showToast && window.showToast('Subscribed! Thank you.', 'success');
      }
    } catch(err) {
      window.showToast && window.showToast('Error. Please try again.', 'error');
    }
    if (btn) { btn.disabled = false; btn.textContent = 'Go'; }
  });
})();
</script>

<!-- ── Scripts ────────────────────────────────────────────── -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="/assets/js/app.js"></script>
<script src="/assets/js/search.js"></script>

<!-- ── Inline: Nav Drawer + Theme + User Menu ──────────── -->
<script>
// ─ Theme ─────────────────────────────────
function toggleTheme() {
  var html = document.documentElement;
  var next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
  html.setAttribute('data-theme', next);
  localStorage.setItem('teniko-theme', next);
  updateThemeIcon(next);
}
function updateThemeIcon(theme) {
  var icon = document.getElementById('theme-icon');
  if (!icon) return;
  icon.className = theme === 'dark' ? 'fa fa-sun' : 'fa fa-moon';
}
// Apply on load
(function() {
  var t = localStorage.getItem('teniko-theme') ||
          (window.matchMedia('(prefers-color-scheme:dark)').matches ? 'dark' : 'light');
  updateThemeIcon(t);
})();

// ─ Mobile Drawer ─────────────────────────
var drawer    = document.getElementById('mobile-drawer');
var burger    = document.getElementById('nav-burger');
var backdrop  = document.getElementById('drawer-backdrop');
var closeBtn  = document.getElementById('drawer-close');

function openDrawer() {
  if (!drawer) return;
  drawer.hidden = false;
  burger && burger.setAttribute('aria-expanded', 'true');
  document.body.style.overflow = 'hidden';
  setTimeout(function(){ drawer.classList.add('is-open'); }, 10);
}
function closeDrawer() {
  if (!drawer) return;
  drawer.classList.remove('is-open');
  burger && burger.setAttribute('aria-expanded', 'false');
  document.body.style.overflow = '';
  setTimeout(function(){ drawer.hidden = true; }, 300);
}
burger   && burger.addEventListener('click', openDrawer);
closeBtn && closeBtn.addEventListener('click', closeDrawer);
backdrop && backdrop.addEventListener('click', closeDrawer);
document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeDrawer(); });

// ─ User Dropdown ─────────────────────────
function toggleUserMenu() {
  var dd = document.getElementById('user-dropdown');
  if (!dd) return;
  var isHidden = dd.hidden;
  dd.hidden = !isHidden;
  var btn = document.getElementById('user-menu-btn');
  btn && btn.setAttribute('aria-expanded', String(!isHidden));
}
document.addEventListener('click', function(e) {
  var wrap = document.getElementById('user-menu-wrap');
  var dd   = document.getElementById('user-dropdown');
  if (wrap && dd && !wrap.contains(e.target)) { dd.hidden = true; }
});

// ─ Auto-dismiss flash messages ───────────
document.querySelectorAll('[data-auto-dismiss]').forEach(function(el) {
  setTimeout(function(){ el.style.opacity='0'; el.style.transform='translateY(-8px)'; el.style.transition='opacity .4s,transform .4s'; setTimeout(function(){ el.remove(); }, 400); }, 5000);
});
</script>

</body>
</html>
