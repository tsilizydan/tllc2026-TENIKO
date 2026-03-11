<?php // About page ?>
<div style="background:linear-gradient(135deg,var(--clr-primary),var(--clr-primary-dk));padding:4rem 0;text-align:center;color:white">
  <div class="container">
    <h1 style="font-family:var(--font-heading);font-size:2.75rem;margin-bottom:1rem">About TENIKO</h1>
    <p style="font-size:1.15rem;max-width:600px;margin:0 auto;opacity:.9">The Living Archive of Malagasy Language & Culture</p>
  </div>
</div>

<section class="section">
  <div class="container container-narrow">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:3rem;margin-bottom:3rem">
      <div>
        <h2 style="font-family:var(--font-heading);font-size:1.5rem;color:var(--clr-primary);margin-bottom:1rem">Our Mission</h2>
        <p style="line-height:1.9;color:var(--clr-text)">
          TENIKO is a modern reconstruction and expansion of the Malagasy linguistic web. Our mission is to <strong>preserve, modernize, and expand</strong> Malagasy linguistic and cultural knowledge through a collaborative, community-driven digital platform.
        </p>
        <p style="line-height:1.9;color:var(--clr-text);margin-top:1rem">
          We aim to become the world's largest open digital archive of the Malagasy language — making it accessible to speakers, learners, researchers, and descendants of Madagascar worldwide.
        </p>
      </div>
      <div>
        <h2 style="font-family:var(--font-heading);font-size:1.5rem;color:var(--clr-primary);margin-bottom:1rem">What We Offer</h2>
        <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:.75rem">
          <?php foreach ([
            ['fa-book', 'A comprehensive Malagasy dictionary with 20,000+ entries'],
            ['fa-scroll', 'The largest collection of Malagasy proverbs (ohabolana)'],
            ['fa-landmark', 'Cultural knowledge base on traditions, history, and folklore'],
            ['fa-map', 'Detailed documentation of 18+ regional dialects'],
            ['fa-microphone', 'Audio pronunciations from native speakers'],
            ['fa-users', 'Community forums for linguistic discussion'],
            ['fa-plus-circle', 'Open contribution system for community members'],
          ] as [$icon, $text]): ?>
          <li style="display:flex;gap:.75rem;align-items:flex-start">
            <i class="fa <?= $icon ?>" style="color:var(--clr-primary);margin-top:.15rem;width:16px;flex-shrink:0"></i>
            <span style="font-size:.95rem;line-height:1.5"><?= $text ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <div style="background:var(--clr-beige);border-radius:var(--radius-xl);padding:2.5rem;text-align:center;margin-bottom:2rem">
      <h2 style="font-family:var(--font-heading);font-size:1.5rem;margin-bottom:1rem">The Name "TENIKO"</h2>
      <p style="line-height:1.9;max-width:600px;margin:0 auto">
        <em>"Teniko"</em> is a Malagasy word meaning <strong>"my words"</strong> or <strong>"my language"</strong> — a deeply personal declaration of connection to one's mother tongue. The name reflects our belief that language is not just communication, but identity.
      </p>
    </div>

    <div style="text-align:center">
      <a href="/contribute" class="btn btn-primary btn-lg btn-rounded"><i class="fa fa-heart"></i> Join the Archive</a>
      <a href="/contact" class="btn btn-outline btn-lg btn-rounded" style="margin-left:.75rem"><i class="fa fa-envelope"></i> Contact Us</a>
    </div>
  </div>
</section>
