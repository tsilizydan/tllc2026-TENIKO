<?php $csrfToken = \App\Core\CSRF::generate(); ?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ?? 'TENIKO') ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/assets/css/main.css">
  <meta name="csrf-token" content="<?= e($csrfToken) ?>">
  <style>
    body { display: flex; min-height: 100vh; align-items: center; justify-content: center; background: var(--clr-bg-surface); }
    .auth-wrap { width: min(440px, 96vw); }
    .auth-card { background: var(--clr-bg-card); border: 1px solid var(--clr-border); border-radius: var(--radius-xl); padding: 2.5rem; box-shadow: var(--shadow-lg); }
    .auth-logo { text-align: center; margin-bottom: 2rem; }
    .auth-logo h1 { font-family: var(--font-heading); font-size: 2rem; color: var(--clr-primary); }
    .auth-logo p  { color: var(--clr-text-muted); font-size: var(--fs-sm); }
  </style>
</head>
<body>
<main class="auth-wrap">
  <?php if (!empty($flash_success) || !empty($flash_error) || !empty($flash_info)): ?>
    <?php if (!empty($flash_success)): ?><div class="alert alert-success"><i class="fa fa-check-circle"></i> <?= e($flash_success) ?></div><?php endif; ?>
    <?php if (!empty($flash_error)):   ?><div class="alert alert-error"><i class="fa fa-times-circle"></i> <?= e($flash_error) ?></div><?php endif; ?>
    <?php if (!empty($flash_info)):    ?><div class="alert alert-info"><i class="fa fa-info-circle"></i> <?= e($flash_info) ?></div><?php endif; ?>
  <?php endif; ?>
  <?= $content ?>
</main>
<script src="/assets/js/app.js"></script>
</body>
</html>
