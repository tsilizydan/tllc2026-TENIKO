<?php $csrfToken = \App\Core\CSRF::generate(); ?>
<div class="auth-card">
  <div class="auth-logo">
    <h1>Forgot Password</h1>
    <p>Enter your email and we'll send a reset link</p>
  </div>
  <form action="/forgot-password" method="POST" novalidate>
    <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
    <div class="form-group">
      <label class="form-label" for="email">Email Address</label>
      <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com" required autocomplete="email" autofocus>
    </div>
    <button type="submit" class="btn btn-primary btn-lg w-full">Send Reset Link <i class="fa fa-paper-plane"></i></button>
  </form>
  <p style="text-align:center;margin-top:1.5rem;font-size:.9rem;color:var(--clr-text-muted)">
    <a href="/login" style="color:var(--clr-primary);font-weight:600">← Back to Login</a>
  </p>
</div>
