<?php // Admin: Moderation queue — $pending (words), $corrections
$csrfToken = \App\Core\CSRF::generate(); ?>
<div style="margin-bottom:1.5rem">
  <h1 style="font-size:1.5rem;font-family:var(--font-heading)">Moderation Queue</h1>
</div>

<h2 style="font-size:1rem;font-weight:700;margin-bottom:1rem;color:var(--clr-text-muted)">Pending Word Submissions (<?= count($pending) ?>)</h2>
<?php if (empty($pending)): ?>
  <div class="alert alert-success" style="margin-bottom:1.5rem"><i class="fa fa-check-circle"></i> No pending word submissions.</div>
<?php else: ?>
<div class="table-wrap" style="margin-bottom:2rem">
  <table class="table">
    <thead><tr><th>Word</th><th>Submitted by</th><th>Date</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($pending as $w): ?>
    <tr>
      <td><strong><?= e($w['word']) ?></strong><br><small style="color:var(--clr-text-muted)"><?= e(truncate($w['definition_mg'] ?? '', 60)) ?></small></td>
      <td style="font-size:.875rem"><?= e($w['display_name'] ?? $w['author_name'] ?? '—') ?></td>
      <td style="font-size:.8rem;color:var(--clr-text-muted)"><?= timeAgo($w['created_at']) ?></td>
      <td>
        <form action="/admin/moderation/approve" method="POST" style="display:inline">
          <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
          <input type="hidden" name="type" value="word">
          <input type="hidden" name="id" value="<?= $w['id'] ?>">
          <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check"></i> Approve</button>
        </form>
        <form action="/admin/moderation/reject" method="POST" style="display:inline;margin-left:.25rem">
          <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
          <input type="hidden" name="type" value="word">
          <input type="hidden" name="id" value="<?= $w['id'] ?>">
          <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--clr-accent)"><i class="fa fa-times"></i> Reject</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<h2 style="font-size:1rem;font-weight:700;margin-bottom:1rem;color:var(--clr-text-muted)">Pending Corrections (<?= count($corrections ?? []) ?>)</h2>
<?php if (empty($corrections)): ?>
  <div class="alert alert-success"><i class="fa fa-check-circle"></i> No pending corrections.</div>
<?php else: ?>
<div class="table-wrap">
  <table class="table">
    <thead><tr><th>Entry</th><th>Type</th><th>Description</th><th>By</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($corrections as $c): ?>
    <tr>
      <td><?= e($c['entry'] ?? '—') ?></td>
      <td><span class="badge badge-beige"><?= e($c['type'] ?? '—') ?></span></td>
      <td style="font-size:.875rem;max-width:250px"><?= e(truncate($c['description'] ?? '', 80)) ?></td>
      <td style="font-size:.8rem"><?= e($c['display_name'] ?? '—') ?></td>
      <td>
        <form action="/admin/moderation/approve" method="POST" style="display:inline">
          <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
          <input type="hidden" name="type" value="correction">
          <input type="hidden" name="id" value="<?= $c['id'] ?>">
          <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check"></i></button>
        </form>
        <form action="/admin/moderation/reject" method="POST" style="display:inline;margin-left:.25rem">
          <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
          <input type="hidden" name="type" value="correction">
          <input type="hidden" name="id" value="<?= $c['id'] ?>">
          <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--clr-accent)"><i class="fa fa-times"></i></button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>
