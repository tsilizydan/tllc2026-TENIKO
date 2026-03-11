<?php // Admin: Articles list — $paged ?>
<div class="flex-between" style="margin-bottom:1.5rem">
  <h1 style="font-size:1.5rem;font-family:var(--font-heading)">Articles</h1>
  <a href="/admin/articles/create" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> New Article</a>
</div>
<div class="table-wrap">
  <table class="table">
    <thead><tr><th>Title</th><th>Type</th><th>Status</th><th>Views</th><th>Published</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach (($paged['items'] ?? []) as $a): ?>
    <tr>
      <td style="max-width:280px"><a href="/article/<?= e($a['slug']) ?>" target="_blank" style="color:var(--clr-primary);font-weight:600"><?= e(truncate($a['title'], 55)) ?></a></td>
      <td><span class="article-card__type"><?= e($a['type'] ?? '—') ?></span></td>
      <td><span class="badge <?= $a['status'] === 'published' ? 'badge-green' : 'badge-gray' ?>"><?= e($a['status']) ?></span></td>
      <td><?= number_format($a['view_count'] ?? 0) ?></td>
      <td style="color:var(--clr-text-muted);font-size:.8rem"><?= $a['published_at'] ? formatDate($a['published_at']) : '—' ?></td>
      <td><a href="/admin/articles/<?= $a['id'] ?>/edit" class="btn btn-ghost btn-sm"><i class="fa fa-edit"></i></a></td>
    </tr>
    <?php endforeach; ?>
    <?php if (empty($paged['items'])): ?><tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--clr-text-muted)">No articles yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php $last = $paged['last_page'] ?? 1; $cur = $paged['current_page'] ?? 1; if ($last > 1): ?>
<nav class="pagination" style="justify-content:flex-start;margin-top:1rem">
  <?php if ($cur > 1): ?><a class="pagination__item" href="/admin/articles?page=<?= $cur - 1 ?>">‹</a><?php endif; ?>
  <?php for ($i = max(1,$cur-2); $i <= min($last,$cur+2); $i++): ?><a class="pagination__item <?= $i===$cur?'active':''?>" href="/admin/articles?page=<?= $i ?>"><?= $i ?></a><?php endfor; ?>
  <?php if ($cur < $last): ?><a class="pagination__item" href="/admin/articles?page=<?= $cur + 1 ?>">›</a><?php endif; ?>
</nav>
<?php endif; ?>
