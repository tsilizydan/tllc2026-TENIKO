<?php // Admin: Contact inbox — $messages, $unread, $status, $page, $last_page ?>
<div class="flex-between" style="margin-bottom:1.5rem">
  <div>
    <h1 style="font-size:1.5rem;font-family:var(--font-heading)">
      Contact Inbox
      <?php if ($unread > 0): ?>
        <span class="admin-nav__badge" style="position:static;margin-left:.5rem"><?= $unread ?> unread</span>
      <?php endif; ?>
    </h1>
  </div>
  <div style="display:flex;gap:.5rem">
    <?php foreach ([''=>'All','unread'=>'Unread','read'=>'Read','replied'=>'Replied','archived'=>'Archived'] as $v => $l): ?>
      <a href="/admin/contact<?= $v ? '?status='.$v : '' ?>"
         class="btn btn-sm <?= $status === $v ? 'btn-primary' : 'btn-ghost' ?>"><?= $l ?></a>
    <?php endforeach; ?>
  </div>
</div>

<?php if (empty($messages)): ?>
  <div class="alert alert-info"><i class="fa fa-inbox"></i> No messages found.</div>
<?php else: ?>
<div class="table-wrap">
  <table class="table">
    <thead><tr>
      <th>From</th><th>Subject</th><th>Status</th><th>Date</th><th>Actions</th>
    </tr></thead>
    <tbody>
    <?php foreach ($messages as $m): ?>
    <tr style="<?= $m['status']==='unread' ? 'font-weight:600' : '' ?>">
      <td>
        <div style="font-weight:<?= $m['status']==='unread' ? '700' : '500' ?>"><?= e($m['name']) ?></div>
        <div style="font-size:.75rem;color:var(--clr-text-muted)"><?= e($m['email']) ?></div>
      </td>
      <td><?= e(truncate($m['subject'] ?? '(no subject)', 50)) ?></td>
      <td>
        <?php $cls = ['unread'=>'badge-red','read'=>'badge-beige','replied'=>'badge-green','archived'=>'badge-gray'][$m['status']] ?? 'badge-gray'; ?>
        <span class="badge <?= $cls ?>"><?= ucfirst($m['status']) ?></span>
      </td>
      <td style="font-size:.8rem;white-space:nowrap;color:var(--clr-text-muted)"><?= timeAgo($m['created_at']) ?></td>
      <td>
        <a href="/admin/contact/<?= $m['id'] ?>" class="btn btn-sm btn-ghost"><i class="fa fa-eye"></i></a>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php if ($last_page > 1): ?>
<nav class="pagination" style="margin-top:1rem">
  <?php for ($i = 1; $i <= $last_page; $i++): ?>
    <a class="pagination__item <?= $i===$page?'active':'' ?>" href="?status=<?= $status ?>&page=<?= $i ?>"><?= $i ?></a>
  <?php endfor; ?>
</nav>
<?php endif; ?>
<?php endif; ?>
