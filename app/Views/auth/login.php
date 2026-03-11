<?php $csrfToken = \App\Core\CSRF::generate(); ?>
<div class="auth-card">
  <div class="auth-logo">
    <a href="/" style="text-decoration:none">
      <h1>TENIKO</h1>
      <p>Sign in to your account</p>
    </a>
  </div>
  <form action="/login" method="POST" novalidate>
    <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
    <div class="form-group">
      <label class="form-label" for="email">Email Address</label>
      <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com" required autocomplete="email" autofocus>
    </div>
    <div class="form-group">
      <label class="form-label" for="password">
        Password
        <a href="/forgot-password" style="float:right;font-size:.8rem;color:var(--clr-primary);font-weight:400">Forgot password?</a>
      </label>
      <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
    </div>
    <label class="flex" style="gap:.5rem;align-items:center;margin-bottom:1.5rem;cursor:pointer;font-size:.9rem">
      <input type="checkbox" name="remember"> Remember me for 30 days
    </label>
    <button type="submit" class="btn btn-primary btn-lg w-full">Sign In <i class="fa fa-arrow-right"></i></button>
  </form>
  <p style="text-align:center;margin-top:1.5rem;font-size:.9rem;color:var(--clr-text-muted)">
    Don't have an account? <a href="/register" style="color:var(--clr-primary);font-weight:600">Join TENIKO</a>
  </p>
</div>
