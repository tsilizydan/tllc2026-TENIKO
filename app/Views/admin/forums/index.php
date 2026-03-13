<?php // Admin: Forums — $forums, shows channel list + quick-create form
$csrfToken = \App\Core\CSRF::generate();
?>
<div class="flex-between" style="margin-bottom:1.5rem">
  <h1 style="font-size:1.5rem;font-family:var(--font-heading)">Forums Admin</h1>
  <a href="/admin/forums/topics" class="btn btn-ghost btn-sm"><i class="fa fa-list"></i> Manage Topics</a>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;align-items:start">
  <!-- Channel list -->
  <div class="card">
    <div class="card__body">
      <h2 style="font-size:1rem;margin-bottom:1rem">Forum Channels</h2>
      <?php if (empty($forums)): ?>
        <p style="color:var(--clr-text-muted);font-size:.9rem">No forum channels yet.</p>
      <?php else: ?>
        <table class="table" style="margin:0">
          <thead><tr><th>Name</th><th>Topics</th><th>Order</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($forums as $f): ?>
          <tr>
            <td>
              <div style="font-weight:600"><?= e($f['name']) ?></div>
              <?php if ($f['description']): ?>
                <div style="font-size:.75rem;color:var(--clr-text-muted)"><?= e(truncate($f['description'], 60)) ?></div>
              <?php endif; ?>
            </td>
            <td style="text-align:center"><?= $f['topic_count_live'] ?></td>
            <td><?= $f['sort_order'] ?></td>
            <td>
              <form action="/admin/forums/<?= $f['id'] ?>/delete" method="POST"
                    onsubmit="return confirm('Delete channel and all its topics?')">
                <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
                <button class="btn btn-sm btn-ghost" style="color:var(--clr-accent)"><i class="fa fa-trash"></i></button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

  <!-- Create new channel -->
  <div class="card">
    <div class="card__body">
      <h2 style="font-size:1rem;margin-bottom:1rem">Add New Channel</h2>
      <form action="/admin/forums/channel" method="POST">
        <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
        <div class="form-group">
          <label class="form-label">Channel Name *</label>
          <input type="text" name="name" class="form-control" required
                 placeholder="e.g. Linguistics, Grammar, Vocabulary">
        </div>
        <div class="form-group">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="2"
                    placeholder="Short description of this forum…"></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" class="form-control" value="0" min="0">
        </div>
        <button type="submit" class="btn btn-primary w-full"><i class="fa fa-plus"></i> Create Channel</button>
      </form>
    </div>
  </div>
</div>
