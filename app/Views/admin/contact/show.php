<?php // Admin: Contact message detail — $msg, $csrfToken
$csrfToken = $csrfToken ?? \App\Core\CSRF::generate();
?>
<div class="flex-between" style="margin-bottom:1.5rem">
  <h1 style="font-size:1.5rem;font-family:var(--font-heading)">Message from <?= e($msg['name']) ?></h1>
  <a href="/admin/contact" class="btn btn-ghost btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;align-items:start">
  <div>
    <!-- Message body -->
    <div class="card" style="margin-bottom:1.5rem">
      <div class="card__body">
        <div style="display:flex;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem">
          <div>
            <div style="font-weight:700;font-size:1rem"><?= e($msg['name']) ?></div>
            <div style="font-size:.85rem;color:var(--clr-text-muted)"><?= e($msg['email']) ?></div>
          </div>
          <div style="font-size:.8rem;color:var(--clr-text-muted)"><?= formatDate($msg['created_at']) ?></div>
        </div>
        <?php if ($msg['subject']): ?>
          <div style="font-weight:600;margin-bottom:.75rem;padding:.5rem .75rem;background:var(--clr-bg-surface);border-radius:var(--radius-md)">
            <?= e($msg['subject']) ?>
          </div>
        <?php endif; ?>
        <div style="line-height:1.7;white-space:pre-wrap"><?= e($msg['message']) ?></div>
      </div>
    </div>

    <!-- Reply form -->
    <div class="card">
      <div class="card__body">
        <h3 style="font-size:1rem;margin-bottom:1rem"><i class="fa fa-reply"></i> Reply</h3>
        <?php if ($msg['reply']): ?>
          <div class="alert alert-success" style="margin-bottom:1rem">
            <strong>Previous reply sent:</strong><br>
            <span style="white-space:pre-wrap"><?= e($msg['reply']) ?></span>
          </div>
        <?php endif; ?>
        <form action="/admin/contact/<?= $msg['id'] ?>/reply" method="POST">
          <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
          <div class="form-group">
            <label class="form-label">Reply Message</label>
            <textarea name="reply" class="form-control" rows="6" placeholder="Type your reply here…"><?= e($msg['reply'] ?? '') ?></textarea>
            <span class="form-hint">Note: manually send email to <?= e($msg['email']) ?> if SMTP is not configured.</span>
          </div>
          <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"></i> Save Reply</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Actions sidebar -->
  <div>
    <div class="card">
      <div class="card__body">
        <h3 style="font-size:.9rem;margin-bottom:1rem;color:var(--clr-text-muted)">Actions</h3>
        <?php
        $cls = ['unread'=>'badge-red','read'=>'badge-beige','replied'=>'badge-green','archived'=>'badge-gray'][$msg['status']] ?? 'badge-gray';
        ?>
        <div style="margin-bottom:1rem">
          <span class="badge <?= $cls ?>"><?= ucfirst($msg['status']) ?></span>
        </div>
        <form action="/admin/contact/<?= $msg['id'] ?>/archive" method="POST" style="margin-bottom:.5rem">
          <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
          <button class="btn btn-ghost btn-sm w-full"><i class="fa fa-archive"></i> Archive</button>
        </form>
        <form action="/admin/contact/<?= $msg['id'] ?>/delete" method="POST" onsubmit="return confirm('Delete this message?')">
          <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
          <button class="btn btn-ghost btn-sm w-full" style="color:var(--clr-accent)"><i class="fa fa-trash"></i> Delete</button>
        </form>
        <div style="margin-top:1.25rem;font-size:.75rem;color:var(--clr-text-muted)">
          <div>IP: <?= e($msg['ip'] ?? '—') ?></div>
          <div>Received: <?= formatDate($msg['created_at']) ?></div>
        </div>
      </div>
    </div>
  </div>
</div>
