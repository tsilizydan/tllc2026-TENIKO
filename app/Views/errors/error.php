<?php
/**
 * Error View — 404 / 403 / 500
 * Variables: $code, $message
 */
$code    = $code ?? 500;
$message = $message ?? 'Something went wrong.';
$msgs = [
  404 => ['Page Not Found', 'The page you are looking for has been moved, deleted, or never existed.', 'fa-compass'],
  403 => ['Access Denied',  'You do not have permission to access this resource.',                     'fa-lock'],
  500 => ['Server Error',   'Something went wrong on our end. Please try again later.',                'fa-server'],
];
[$title, $desc, $icon] = $msgs[$code] ?? [$message, '', 'fa-exclamation-triangle'];
?>
<div style="min-height:60vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:4rem 1rem">
  <div>
    <div style="font-size:6rem;font-weight:900;color:var(--clr-primary);line-height:1;margin-bottom:1rem;font-family:var(--font-heading)"><?= $code ?></div>
    <i class="fa <?= $icon ?>" style="font-size:3rem;color:var(--clr-gray-300);display:block;margin-bottom:1.5rem"></i>
    <h1 style="font-size:1.75rem;margin-bottom:.75rem"><?= e($title) ?></h1>
    <p style="color:var(--clr-text-muted);max-width:440px;margin:0 auto 2rem"><?= e($desc) ?></p>
    <div class="flex flex-wrap" style="gap:1rem;justify-content:center">
      <a href="/" class="btn btn-primary"><i class="fa fa-home"></i> Go Home</a>
      <a href="/dictionary" class="btn btn-outline"><i class="fa fa-book"></i> Browse Dictionary</a>
      <?php if ($code === 500): ?>
        <button onclick="location.reload()" class="btn btn-ghost"><i class="fa fa-redo"></i> Try Again</button>
      <?php endif; ?>
    </div>
  </div>
</div>
