<?php $csrfToken = \App\Core\CSRF::generate(); ?>
<div class="auth-card">
  <div class="auth-logo">
    <h1>Reset Password</h1>
    <p>Enter your new password below</p>
  </div>
  <form action="/reset-password" method="POST" novalidate>
    <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
    <input type="hidden" name="token" value="<?= e($token ?? '') ?>">
    <div class="form-group">
      <label class="form-label" for="password">New Password</label>
      <div class="pwd-field">
        <input type="password" id="password" name="password" class="form-control" placeholder="At least 8 characters" required minlength="8" autocomplete="new-password" autofocus>
        <button type="button" class="pwd-toggle" data-target="password" aria-label="Show password"><i class="fa fa-eye"></i></button>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label" for="password_confirm">Confirm New Password</label>
      <div class="pwd-field">
        <input type="password" id="password_confirm" name="password_confirm" class="form-control" placeholder="Repeat your new password" required minlength="8" autocomplete="new-password">
        <button type="button" class="pwd-toggle" data-target="password_confirm" aria-label="Show password"><i class="fa fa-eye"></i></button>
      </div>
    </div>
    <button type="submit" class="btn btn-primary btn-lg w-full">Set New Password <i class="fa fa-check"></i></button>
  </form>
</div>
