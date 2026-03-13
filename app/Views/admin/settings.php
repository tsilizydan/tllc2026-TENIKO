<?php
// Admin: Site Settings — $settings (flat key→value array from allFlat())
/** @var array<string,string> $settings */
$csrfToken = \App\Core\CSRF::generate();

/**
 * Local helper — renders a single settings form field.
 * Using a closure variable (not a global function) to avoid re-declaration
 * if the view is ever included more than once, and to fix static-analysis warnings.
 *
 * @var \Closure(string,string,string,string,string):void $sf
 */
$sf = static function (string $key, string $label, string $type, string $value, string $hint = ''): void {
    echo '<div class="form-group">';
    echo '<label class="form-label">' . htmlspecialchars($label, ENT_QUOTES) . '</label>';
    if ($type === 'select-onoff') {
        echo '<select name="settings[' . htmlspecialchars($key, ENT_QUOTES) . ']" class="form-control">';
        echo '<option value="0"' . ($value === '0' || $value === '' ? ' selected' : '') . '>Disabled</option>';
        echo '<option value="1"' . ($value === '1' ? ' selected' : '') . '>Enabled</option>';
        echo '</select>';
    } elseif ($type === 'textarea') {
        echo '<textarea name="settings[' . htmlspecialchars($key, ENT_QUOTES) . ']" class="form-control" rows="3">'
            . htmlspecialchars($value, ENT_QUOTES) . '</textarea>';
    } else {
        echo '<input type="' . htmlspecialchars($type, ENT_QUOTES) . '" '
            . 'name="settings[' . htmlspecialchars($key, ENT_QUOTES) . ']" '
            . 'class="form-control" '
            . 'value="' . htmlspecialchars($value, ENT_QUOTES) . '">';
    }
    if ($hint !== '') {
        echo '<span class="form-hint">' . htmlspecialchars($hint, ENT_QUOTES) . '</span>';
    }
    echo '</div>';
};
?>
<div style="margin-bottom:1.5rem">
  <h1 style="font-size:1.5rem;font-family:var(--font-heading)">Site Settings</h1>
  <p style="color:var(--clr-text-muted);font-size:.9rem">All settings are stored in the database and reflect immediately on the public site.</p>
</div>
<form action="/admin/settings" method="POST">
  <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;align-items:start">
    <div>

      <!-- ── General ──────────────────────────────────── -->
      <div class="card" style="margin-bottom:1.5rem">
        <div class="card__body">
          <h2 style="font-size:1rem;margin-bottom:1.25rem"><i class="fa fa-globe"></i> General</h2>
          <?php
          $sf('site_name',    'Site Name',     'text',  $settings['site_name']    ?? 'TENIKO');
          $sf('site_tagline', 'Tagline',       'text',  $settings['site_tagline'] ?? 'The Malagasy Language & Culture Encyclopedia');
          $sf('site_email',   'Contact Email', 'email', $settings['site_email']   ?? '');
          $sf('site_url',     'Site URL',      'url',   $settings['site_url']     ?? '');
          ?>
          <div class="form-group">
            <label class="form-label">Maintenance Mode</label>
            <select name="settings[maintenance_mode]" class="form-control">
              <option value="0" <?= ($settings['maintenance_mode'] ?? '0') === '0' ? 'selected' : '' ?>>Off — Site is live</option>
              <option value="1" <?= ($settings['maintenance_mode'] ?? '0') === '1' ? 'selected' : '' ?>>On — Show maintenance page</option>
            </select>
          </div>
        </div>
      </div>

      <!-- ── Homepage Content ──────────────────────────── -->
      <div class="card" style="margin-bottom:1.5rem">
        <div class="card__body">
          <h2 style="font-size:1rem;margin-bottom:1.25rem"><i class="fa fa-home"></i> Homepage Content</h2>
          <?php
          $sf('homepage_hero_title',    'Hero Title',          'text',     $settings['homepage_hero_title']    ?? 'Discover the Malagasy Language');
          $sf('homepage_hero_subtitle', 'Hero Subtitle',       'text',     $settings['homepage_hero_subtitle'] ?? 'Teny — The Living Archive of Malagasy Language & Culture');
          $sf('homepage_featured_title','Featured Section Title', 'text',  $settings['homepage_featured_title'] ?? 'Cultural Knowledge');
          $sf('homepage_words_title',   'Latest Words Title',  'text',     $settings['homepage_words_title']   ?? 'Latest Words Added');
          $sf('homepage_cta_title',     'CTA Heading',         'text',     $settings['homepage_cta_title']     ?? 'Help Build the Archive');
          $sf('homepage_cta_text',      'CTA Body Text',       'textarea', $settings['homepage_cta_text']      ?? 'TENIKO grows through community contributions.');
          ?>
        </div>
      </div>

      <!-- ── Announcement Banner ───────────────────────── -->
      <div class="card" style="margin-bottom:1.5rem">
        <div class="card__body">
          <h2 style="font-size:1rem;margin-bottom:1.25rem"><i class="fa fa-bullhorn"></i> Announcement Banner</h2>
          <?php $sf('announcement_text', 'Message (leave blank to hide)', 'text', $settings['announcement_text'] ?? ''); ?>
          <div class="form-group">
            <label class="form-label">Type</label>
            <select name="settings[announcement_type]" class="form-control">
              <?php foreach (['info','success','warning'] as $t): ?>
              <option value="<?= $t ?>" <?= ($settings['announcement_type'] ?? 'info') === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>

    </div>
    <div>

      <!-- ── Feature Toggles ──────────────────────────── -->
      <div class="card" style="margin-bottom:1.5rem">
        <div class="card__body">
          <h2 style="font-size:1rem;margin-bottom:1.25rem"><i class="fa fa-toggle-on"></i> Feature Toggles</h2>
          <?php
          $features = [
            'features_forums'        => 'Forums',
            'features_comments'      => 'Comments',
            'features_registration'  => 'User Registration',
            'features_contributions' => 'Content Contributions',
            'features_ads'           => 'Advertisements',
          ];
          foreach ($features as $key => $label) {
              $sf($key, $label, 'select-onoff', $settings[$key] ?? '1');
          }
          ?>
        </div>
      </div>

      <!-- ── Social Media ─────────────────────────────── -->
      <div class="card" style="margin-bottom:1.5rem">
        <div class="card__body">
          <h2 style="font-size:1rem;margin-bottom:1.25rem"><i class="fa fa-share-alt"></i> Social Media Links</h2>
          <?php
          $sf('social_facebook',  'Facebook URL',  'url', $settings['social_facebook']  ?? '');
          $sf('social_twitter',   'Twitter / X URL','url',$settings['social_twitter']   ?? '');
          $sf('social_instagram', 'Instagram URL', 'url', $settings['social_instagram'] ?? '');
          $sf('social_youtube',   'YouTube URL',   'url', $settings['social_youtube']   ?? '');
          ?>
        </div>
      </div>

      <!-- ── Footer ───────────────────────────────────── -->
      <div class="card" style="margin-bottom:1.5rem">
        <div class="card__body">
          <h2 style="font-size:1rem;margin-bottom:1.25rem"><i class="fa fa-align-center"></i> Footer</h2>
          <?php $sf('footer_copyright', 'Copyright Text', 'text', $settings['footer_copyright'] ?? '© 2025 TENIKO'); ?>
        </div>
      </div>

      <!-- ── Donation Widget ─────────────────────────── -->
      <div class="card" style="margin-bottom:1.5rem">
        <div class="card__body">
          <h2 style="font-size:1rem;margin-bottom:1.25rem"><i class="fa fa-heart" style="color:var(--clr-accent)"></i> Floating Donation Widget</h2>
          <?php
          $sf('donate_float_enabled', 'Show Widget', 'select-onoff', $settings['donate_float_enabled'] ?? '0');
          $sf('donate_float_message', 'Widget Message', 'text', $settings['donate_float_message'] ?? 'Help us preserve Malagasy language & culture!');
          ?>
        </div>
      </div>

      <!-- ── SEO ──────────────────────────────────────── -->
      <div class="card" style="margin-bottom:1.5rem">
        <div class="card__body">
          <h2 style="font-size:1rem;margin-bottom:1.25rem"><i class="fa fa-search"></i> SEO & Analytics</h2>
          <?php
          $sf('meta_description', 'Default Meta Description', 'textarea', $settings['meta_description'] ?? '', 'Used on pages without specific SEO metadata.');
          $sf('google_analytics', 'Google Analytics ID', 'text', $settings['google_analytics'] ?? '', 'e.g. G-XXXXXXXXXX');
          ?>
        </div>
      </div>

    </div>
  </div>
  <div style="margin-top:.5rem">
    <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> Save All Settings</button>
  </div>
</form>
