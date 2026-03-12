<?php // Public: Donate page — $goal, $raised, $progress, $message
$csrfToken = \App\Core\CSRF::generate();
?>
<section class="section" style="padding:3rem 0">
  <div class="container" style="max-width:900px">
    <div style="text-align:center;margin-bottom:3rem">
      <span style="display:inline-flex;align-items:center;justify-content:center;width:80px;height:80px;background:rgba(46,125,50,.12);border-radius:50%;margin-bottom:1.5rem">
        <i class="fa fa-heart" style="font-size:2rem;color:var(--clr-primary)"></i>
      </span>
      <h1 style="font-family:var(--font-heading);font-size:2.5rem;margin-bottom:.75rem">Support TENIKO</h1>
      <p style="color:var(--clr-text-muted);font-size:1.1rem;max-width:600px;margin:0 auto;line-height:1.7">
        <?= $message ? e($message) : 'Help us preserve and celebrate the Malagasy language and culture for generations to come. Every contribution makes a difference.' ?>
      </p>
    </div>

    <?php if ($goal > 0): ?>
    <!-- Progress -->
    <div class="card" style="margin-bottom:2rem">
      <div class="card__body">
        <div class="flex-between" style="margin-bottom:.75rem">
          <span style="font-weight:700;font-size:1.5rem;color:var(--clr-primary)">€<?= number_format($raised, 0) ?> raised</span>
          <span style="color:var(--clr-text-muted)">of €<?= number_format($goal, 0) ?> goal</span>
        </div>
        <div class="progress-bar" style="height:12px;border-radius:100px;overflow:hidden;background:var(--clr-gray-100)">
          <div class="progress-bar__fill" style="width:<?= $progress ?>%;height:100%;background:linear-gradient(90deg,var(--clr-primary),#43a047);transition:width 1s ease;border-radius:100px"></div>
        </div>
        <p style="margin-top:.5rem;font-size:.875rem;color:var(--clr-text-muted)"><?= $progress ?>% of our goal reached</p>
      </div>
    </div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 320px;gap:2rem">
      <!-- Donation form -->
      <div>
        <div class="card">
          <div class="card__body">
            <h2 style="font-size:1.25rem;margin-bottom:1.5rem">Make a Donation</h2>
            <form action="/donate" method="POST" novalidate>
              <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">

              <!-- Amount picker -->
              <div class="form-group">
                <label class="form-label">Choose Amount</label>
                <div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:.75rem">
                  <?php foreach ([5, 10, 25, 50, 100] as $preset): ?>
                  <button type="button" class="btn btn-outline btn-sm amount-preset" data-amount="<?= $preset ?>" onclick="document.getElementById('amount').value='<?= $preset ?>';setActive(this)">€<?= $preset ?></button>
                  <?php endforeach; ?>
                </div>
                <div style="display:flex;align-items:center;gap:.5rem">
                  <span style="font-size:1.25rem;color:var(--clr-text-muted);">€</span>
                  <input type="number" name="amount" id="amount" class="form-control" min="1" step="0.01" placeholder="Other amount" required style="font-size:1.25rem;font-weight:600">
                </div>
              </div>

              <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div class="form-group">
                  <label class="form-label">Name</label>
                  <input type="text" name="name" class="form-control" placeholder="Optional" value="<?= e(\App\Core\Auth::user()['display_name'] ?? '') ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">Email</label>
                  <input type="email" name="email" class="form-control" placeholder="For receipt" value="<?= e(\App\Core\Auth::user()['email'] ?? '') ?>">
                </div>
              </div>

              <button type="submit" class="btn btn-primary btn-lg w-full" style="margin-top:.5rem">
                <i class="fa fa-heart"></i> Donate Now
              </button>
              <p style="font-size:.75rem;color:var(--clr-text-muted);text-align:center;margin-top:.75rem"><i class="fa fa-lock"></i> Secure payment processing. No card details stored on our servers.</p>
            </form>
          </div>
        </div>
      </div>

      <!-- Why donate -->
      <aside>
        <div class="card" style="margin-bottom:1rem">
          <div class="card__body">
            <h3 style="font-size:1rem;margin-bottom:1rem">Your donation helps us:</h3>
            <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:.75rem">
              <?php foreach ([
                ['fa-server',  'Maintain our servers and infrastructure'],
                ['fa-book',    'Digitize rare Malagasy texts and manuscripts'],
                ['fa-users',   'Build community features and collaboration tools'],
                ['fa-language','Expand our dictionary to 100,000+ words'],
                ['fa-graduation-cap','Partner with Malagasy universities'],
              ] as [$icon, $text]): ?>
              <li style="display:flex;align-items:flex-start;gap:.75rem">
                <i class="fa <?= $icon ?>" style="color:var(--clr-primary);margin-top:.2rem;flex-shrink:0"></i>
                <span style="font-size:.875rem;line-height:1.5"><?= $text ?></span>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
        <div class="card" style="background:var(--clr-beige);border:none">
          <div class="card__body" style="text-align:center">
            <i class="fa fa-shield-alt" style="font-size:1.5rem;color:var(--clr-primary);display:block;margin-bottom:.5rem"></i>
            <p style="font-size:.8rem;color:var(--clr-text-muted);line-height:1.6">TENIKO is a non-profit cultural preservation project. All funds go directly toward platform development and content expansion.</p>
          </div>
        </div>
      </aside>
    </div>
  </div>
</section>

<script>
function setActive(btn) {
  document.querySelectorAll('.amount-preset').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}
</script>
