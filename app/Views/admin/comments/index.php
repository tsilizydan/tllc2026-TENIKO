<?php // Admin: Comments moderation — $comments, $status, $page, $last_page
$csrfToken = \App\Core\CSRF::generate();
?>
<div class="flex-between" style="margin-bottom:1.5rem">
  <h1 style="font-size:1.5rem;font-family:var(--font-heading)">Comment Moderation</h1>
  <div style="display:flex;gap:.5rem">
    <?php foreach (['published'=>'Visible','hidden'=>'Hidden','pending'=>'Pending'] as $v => $l): ?>
      <a href="/admin/comments?status=<?= $v ?>"
         class="btn btn-sm <?= $status === $v ? 'btn-primary' : 'btn-ghost' ?>"><?= $l ?></a>
    <?php endforeach; ?>
  </div>
</div>

<?php if (empty($comments)): ?>
  <div class="alert alert-info"><i class="fa fa-comment"></i> No comments with status "<?= e($status) ?>".</div>
<?php else: ?>
<div class="table-wrap">
  <table class="table">
    <thead><tr><th>Author</th><th>Comment</th><th>On</th><th>Date</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($comments as $c): ?>
    <tr>
      <td style="white-space:nowrap">
        <div style="font-weight:600"><?= e($c['display_name'] ?? $c['username']) ?></div>
        <div style="font-size:.75rem;color:var(--clr-text-muted)">@<?= e($c['username']) ?></div>
      </td>
      <td style="font-size:.875rem;max-width:380px">
        <?= e(truncate($c['body'], 120)) ?>
      </td>
      <td style="font-size:.8rem;white-space:nowrap">
        <span class="badge badge-beige"><?= e($c['entity_type']) ?> #<?= e($c['entity_id']) ?></span>
      </td>
      <td style="font-size:.8rem;white-space:nowrap;color:var(--clr-text-muted)"><?= timeAgo($c['created_at']) ?></td>
      <td style="white-space:nowrap">
        <?php if ($c['status'] === 'hidden'): ?>
          <form action="/admin/comments/<?= $c['id'] ?>/restore" method="POST" style="display:inline">
            <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
            <button class="btn btn-sm btn-ghost" title="Restore"><i class="fa fa-eye"></i></button>
          </form>
        <?php else: ?>
          <form action="/admin/comments/<?= $c['id'] ?>/hide" method="POST" style="display:inline">
            <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
            <button class="btn btn-sm btn-ghost" title="Hide"><i class="fa fa-eye-slash"></i></button>
          </form>
        <?php endif; ?>
        <form action="/admin/comments/<?= $c['id'] ?>/delete" method="POST" style="display:inline"
              onsubmit="return confirm('Permanently delete this comment?')">
          <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
          <button class="btn btn-sm btn-ghost" style="color:var(--clr-accent)" title="Delete"><i class="fa fa-trash"></i></button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php if ($last_page > 1): ?>
<nav class="pagination" style="margin-top:1rem">
  <?php for ($i = 1; $i <= $last_page; $i++): ?>
    <a class="pagination__item <?= $i===$page?'active':'' ?>"
       href="/admin/comments?status=<?= e($status) ?>&page=<?= $i ?>"><?= $i ?></a>
  <?php endfor; ?>
</nav>
<?php endif; ?>
<?php endif; ?>
