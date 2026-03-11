<?php
// User settings — $authUser
$csrfToken = \App\Core\CSRF::generate();
$user = \App\Core\Auth::user();
?>
<section class="section" style="padding-top:2rem">
  <div class="container container-narrow">
    <h1 style="font-family:var(--font-heading);margin-bottom:2rem"><i class="fa fa-cog" style="color:var(--clr-primary)"></i> Account Settings</h1>

    <div class="card" style="margin-bottom:1.5rem">
      <div class="card__body">
        <h2 style="font-size:1.1rem;margin-bottom:1.25rem">Profile Information</h2>
        <form action="/settings" method="POST" enctype="multipart/form-data" novalidate>
          <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
          <input type="hidden" name="action" value="profile">
          <div class="form-group">
            <label class="form-label" for="display_name">Display Name</label>
            <input type="text" id="display_name" name="display_name" class="form-control" value="<?= e($user['display_name'] ?? '') ?>" maxlength="100" placeholder="Your public name">
          </div>
          <div class="form-group">
            <label class="form-label" for="bio">Bio</label>
            <textarea id="bio" name="bio" class="form-control" rows="3" maxlength="500" placeholder="Tell the community a little about yourself…"><?= e($user['bio'] ?? '') ?></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
      </div>
    </div>

    <div class="card" style="margin-bottom:1.5rem">
      <div class="card__body">
        <h2 style="font-size:1.1rem;margin-bottom:1.25rem">Change Password</h2>
        <form action="/settings" method="POST" novalidate>
          <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
          <input type="hidden" name="action" value="password">
          <div class="form-group">
            <label class="form-label" for="current_password">Current Password</label>
            <div class="pwd-field">
              <input type="password" id="current_password" name="current_password" class="form-control" required autocomplete="current-password">
              <button type="button" class="pwd-toggle" data-target="current_password" aria-label="Show password"><i class="fa fa-eye"></i></button>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="new_password">New Password</label>
            <div class="pwd-field">
              <input type="password" id="new_password" name="new_password" class="form-control" required minlength="8" autocomplete="new-password">
              <button type="button" class="pwd-toggle" data-target="new_password" aria-label="Show password"><i class="fa fa-eye"></i></button>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Update Password</button>
        </form>
      </div>
    </div>

    <div style="text-align:right">
      <a href="/profile/<?= e($user['username'] ?? '') ?>" class="btn btn-ghost"><i class="fa fa-user"></i> View Public Profile</a>
    </div>
  </div>
</section>
