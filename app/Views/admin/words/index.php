<?php // Admin: Words list — $paged ?>
<div class="flex-between" style="margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
  <h1 style="font-size:1.5rem;font-family:var(--font-heading)">Words</h1>
  <a href="/admin/words/create" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add Word</a>
</div>
<div class="kpi-grid" style="margin-bottom:1.5rem">
  <div class="kpi-card"><span class="kpi-value"><?= number_format($paged['total'] ?? 0) ?></span><span class="kpi-label">Total Words</span></div>
</div>
<div class="table-wrap">
  <table class="table">
    <thead><tr><th>Word</th><th>Part of Speech</th><th>Status</th><th>Views</th><th>Created</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach (($paged['items'] ?? []) as $w): ?>
    <tr>
      <td><a href="/word/<?= e($w['slug']) ?>" target="_blank" style="color:var(--clr-primary);font-weight:600"><?= e($w['word']) ?></a></td>
      <td><span class="pos-pill"><?= e($w['part_of_speech'] ?? '—') ?></span></td>
      <td><span class="badge <?= $w['status'] === 'published' ? 'badge-green' : 'badge-gray' ?>"><?= e($w['status']) ?></span></td>
      <td><?= number_format($w['view_count'] ?? 0) ?></td>
      <td style="color:var(--clr-text-muted);font-size:.8rem"><?= formatDate($w['created_at']) ?></td>
      <td>
        <a href="/admin/words/<?= $w['id'] ?>/edit" class="btn btn-ghost btn-sm"><i class="fa fa-edit"></i></a>
        <form action="/admin/words/<?= $w['id'] ?>/delete" method="POST" style="display:inline" onsubmit="return confirm('Delete this word?')">
          <input type="hidden" name="_csrf_token" value="<?= e(\App\Core\CSRF::generate()) ?>">
          <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--clr-accent)"><i class="fa fa-trash"></i></button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php if (empty($paged['items'])): ?><tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--clr-text-muted)">No words yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php $last = $paged['last_page'] ?? 1; $cur = $paged['current_page'] ?? 1; if ($last > 1): ?>
<nav class="pagination" style="justify-content:flex-start;margin-top:1rem">
  <?php if ($cur > 1): ?><a class="pagination__item" href="/admin/words?page=<?= $cur - 1 ?>">‹</a><?php endif; ?>
  <?php for ($i = max(1,$cur-2); $i <= min($last,$cur+2); $i++): ?><a class="pagination__item <?= $i===$cur?'active':''?>" href="/admin/words?page=<?= $i ?>"><?= $i ?></a><?php endfor; ?>
  <?php if ($cur < $last): ?><a class="pagination__item" href="/admin/words?page=<?= $cur + 1 ?>">›</a><?php endif; ?>
</nav>
<?php endif; ?>
