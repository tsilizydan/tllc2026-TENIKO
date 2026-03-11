<?php // Admin: Proverbs list — $paged, $dialects ?>
<div class="flex-between" style="margin-bottom:1.5rem">
  <h1 style="font-size:1.5rem;font-family:var(--font-heading)">Proverbs</h1>
  <a href="/admin/proverbs/create" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add Proverb</a>
</div>
<div class="table-wrap">
  <table class="table">
    <thead><tr><th>Text (excerpt)</th><th>Dialect</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach (($paged['items'] ?? []) as $p): ?>
    <tr>
      <td style="max-width:300px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-style:italic"><?= e(truncate($p['text'], 70)) ?></td>
      <td style="font-size:.8rem;color:var(--clr-text-muted)"><?= e($p['dialect_name'] ?? '—') ?></td>
      <td><span class="badge <?= $p['status'] === 'published' ? 'badge-green' : 'badge-gray' ?>"><?= e($p['status']) ?></span></td>
      <td style="color:var(--clr-text-muted);font-size:.8rem"><?= formatDate($p['created_at']) ?></td>
      <td><a href="/admin/proverbs/<?= $p['id'] ?>/edit" class="btn btn-ghost btn-sm"><i class="fa fa-edit"></i></a></td>
    </tr>
    <?php endforeach; ?>
    <?php if (empty($paged['items'])): ?><tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--clr-text-muted)">No proverbs yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php $last = $paged['last_page'] ?? 1; $cur = $paged['current_page'] ?? 1; if ($last > 1): ?>
<nav class="pagination" style="justify-content:flex-start;margin-top:1rem">
  <?php if ($cur > 1): ?><a class="pagination__item" href="/admin/proverbs?page=<?= $cur - 1 ?>">‹</a><?php endif; ?>
  <?php for ($i = max(1,$cur-2); $i <= min($last,$cur+2); $i++): ?><a class="pagination__item <?= $i===$cur?'active':''?>" href="/admin/proverbs?page=<?= $i ?>"><?= $i ?></a><?php endfor; ?>
  <?php if ($cur < $last): ?><a class="pagination__item" href="/admin/proverbs?page=<?= $cur + 1 ?>">›</a><?php endif; ?>
</nav>
<?php endif; ?>
