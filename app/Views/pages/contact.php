<?php // Contact page — variables: none needed
$csrfToken = \App\Core\CSRF::generate();
?>
<div style="background:var(--clr-bg-surface);border-bottom:1px solid var(--clr-border);padding:2rem 0">
  <div class="container">
    <h1 class="section-title">Contact Us</h1>
    <p class="text-muted" style="margin-top:.5rem">We'd love to hear from you. Whether it's feedback, a partnership inquiry, or a question about TENIKO.</p>
  </div>
</div>

<section class="section" style="padding-top:2rem">
  <div class="container" style="display:grid;grid-template-columns:1fr 320px;gap:3rem;align-items:start">
    <div>
      <form action="/contact" method="POST" novalidate>
        <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
          <div class="form-group">
            <label class="form-label" for="contact-name">Your Name <span style="color:red">*</span></label>
            <input type="text" id="contact-name" name="name" class="form-control" required placeholder="Rakoto Razafy">
          </div>
          <div class="form-group">
            <label class="form-label" for="contact-email">Email Address <span style="color:red">*</span></label>
            <input type="email" id="contact-email" name="email" class="form-control" required placeholder="you@example.com">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="contact-subject">Subject</label>
          <select id="contact-subject" name="subject" class="form-control">
            <option value="General Inquiry">General Inquiry</option>
            <option value="Content Issue">Content Issue</option>
            <option value="Partnership / Collaboration">Partnership / Collaboration</option>
            <option value="Media / Press">Media / Press</option>
            <option value="Technical Issue">Technical Issue</option>
            <option value="Data Donation">Data Donation</option>
            <option value="Other">Other</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" for="contact-message">Message <span style="color:red">*</span></label>
          <textarea id="contact-message" name="message" class="form-control" rows="7" required minlength="20" placeholder="Tell us how we can help…"></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-paper-plane"></i> Send Message</button>
      </form>
    </div>

    <aside>
      <div class="card" style="margin-bottom:1rem">
        <div class="card__body">
          <h3 style="font-size:1rem;margin-bottom:1rem">Other Ways to Reach Us</h3>
          <div style="display:flex;flex-direction:column;gap:.75rem;font-size:.875rem">
            <a href="mailto:hello@teniko.mg" style="display:flex;gap:.75rem;align-items:center;text-decoration:none;color:inherit" onmouseover="this.style.color='var(--clr-primary)'" onmouseout="this.style.color=''">
              <i class="fa fa-envelope" style="color:var(--clr-primary);width:16px"></i> hello@teniko.mg
            </a>
            <div style="display:flex;gap:.75rem;align-items:center">
              <i class="fa fa-clock" style="color:var(--clr-primary);width:16px"></i> We respond within 48 hours
            </div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card__body">
          <h3 style="font-size:1rem;margin-bottom:.75rem">Contribute Instead?</h3>
          <p style="font-size:.875rem;color:var(--clr-text-muted);margin-bottom:1rem">Have linguistic knowledge to share? Contribute words, proverbs, or corrections directly.</p>
          <a href="/contribute" class="btn btn-primary btn-sm w-full">Start Contributing</a>
        </div>
      </div>
    </aside>
  </div>
</section>
