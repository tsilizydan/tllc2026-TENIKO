<?php // Admin: Forum topics — $topics, $status, $page, $last_page
$csrfToken = \App\Core\CSRF::generate();
?>
<div class="flex-between" style="margin-bottom:1.5rem">
  <h1 style="font-size:1.5rem;font-family:var(--font-heading)">Forum Topics</h1>
  <div style="display:flex;gap:.5rem">
    <a href="/admin/forums" class="btn btn-ghost btn-sm"><i class="fa fa-arrow-left"></i> Channels</a>
    <?php foreach ([''=>'All','open'=>'Open','pinned'=>'Pinned','closed'=>'Closed','archived'=>'Archived'] as $v => $l): ?>
      <a href="/admin/forums/topics?status=<?= $v ?>"
         class="btn btn-sm <?= $status === $v ? 'btn-primary' : 'btn-ghost' ?>"><?= $l ?></a>
    <?php endforeach; ?>
  </div>
</div>

<?php if (empty($topics)): ?>
  <div class="alert alert-info"><i class="fa fa-comments"></i> No topics found.</div>
<?php else: ?>
<div class="table-wrap">
  <table class="table">
    <thead><tr><th>Topic</th><th>Forum</th><th>Author</th><th>Replies</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($topics as $t): ?>
    <tr>
      <td style="max-width:300px">
        <a href="/topic/<?= $t['id'] ?>" target="_blank" style="font-weight:600;color:var(--clr-primary)">
          <?= e(truncate($t['title'], 60)) ?>
        </a>
      </td>
      <td><span class="badge badge-beige"><?= e($t['forum_name']) ?></span></td>
      <td style="font-size:.85rem">@<?= e($t['username']) ?></td>
      <td style="text-align:center"><?= $t['reply_count'] ?></td>
      <td>
        <?php $cls = ['open'=>'badge-green','pinned'=>'badge-beige','closed'=>'badge-gray','archived'=>'badge-gray'][$t['status']] ?? 'badge-gray'; ?>
        <span class="badge <?= $cls ?>"><?= ucfirst($t['status']) ?></span>
      </td>
      <td style="font-size:.8rem;white-space:nowrap;color:var(--clr-text-muted)"><?= timeAgo($t['created_at']) ?></td>
      <td>
        <form action="/admin/forums/topics/<?= $t['id'] ?>/action" method="POST"
              style="display:flex;gap:.25rem;flex-wrap:wrap">
          <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
          <?php if ($t['status'] !== 'pinned'): ?>
            <button name="action" value="pin" class="btn btn-sm btn-ghost" title="Pin"><i class="fa fa-thumbtack"></i></button>
          <?php endif; ?>
          <?php if ($t['status'] === 'open'): ?>
            <button name="action" value="close" class="btn btn-sm btn-ghost" title="Close"><i class="fa fa-lock"></i></button>
          <?php else: ?>
            <button name="action" value="open" class="btn btn-sm btn-ghost" title="Reopen"><i class="fa fa-lock-open"></i></button>
          <?php endif; ?>
          <button name="action" value="delete" class="btn btn-sm btn-ghost" style="color:var(--clr-accent)"
                  onclick="return confirm('Delete this topic?')" title="Delete"><i class="fa fa-trash"></i></button>
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
       href="/admin/forums/topics?status=<?= e($status) ?>&page=<?= $i ?>"><?= $i ?></a>
  <?php endfor; ?>
</nav>
<?php endif; ?>
<?php endif; ?>
