<?php $csrfToken = \App\Core\CSRF::generate(); ?>
<div class="auth-card">
  <div class="auth-logo">
    <h1>Join TENIKO</h1>
    <p>Create a free account and help build the archive</p>
  </div>
  <form action="/register" method="POST" novalidate>
    <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
    <div class="form-group">
      <label class="form-label" for="username">Username</label>
      <input type="text" id="username" name="username" class="form-control" placeholder="e.g. rakoto_malagasy" required minlength="3" maxlength="60" autocomplete="username" autofocus>
      <span class="form-hint">Shown publicly on your contributions.</span>
    </div>
    <div class="form-group">
      <label class="form-label" for="email">Email Address</label>
      <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com" required maxlength="191" autocomplete="email">
    </div>
    <div class="form-group">
      <label class="form-label" for="password">Password</label>
      <div class="pwd-field">
        <input type="password" id="password" name="password" class="form-control" placeholder="At least 8 characters" required minlength="8" autocomplete="new-password">
        <button type="button" class="pwd-toggle" data-target="password" aria-label="Show password"><i class="fa fa-eye"></i></button>
      </div>
      <span class="form-hint">Minimum 8 characters.</span>
    </div>
    <p style="font-size:.8rem;color:var(--clr-text-muted);margin-bottom:1.25rem;line-height:1.6">
      By registering, you agree to our <a href="/terms" style="color:var(--clr-primary)">Terms of Service</a> and <a href="/privacy" style="color:var(--clr-primary)">Privacy Policy</a>.
    </p>
    <button type="submit" class="btn btn-primary btn-lg w-full">Create Account <i class="fa fa-arrow-right"></i></button>
  </form>
  <p style="text-align:center;margin-top:1.5rem;font-size:.9rem;color:var(--clr-text-muted)">
    Already have an account? <a href="/login" style="color:var(--clr-primary);font-weight:600">Sign In</a>
  </p>
</div>
