<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Article;

class CultureController extends Controller
{
    public function index(Request $request): void
    {
        $model      = new Article();
        $categories = $this->db()->fetchAll("SELECT * FROM categories WHERE type IN ('article','cultural') ORDER BY sort_order");
        $featured   = $model->featured(6);
        $latest     = $model->latest(12);

        $this->render('culture/index', [
            'categories' => $categories,
            'featured'   => $featured,
            'latest'     => $latest,
            'pageTitle'  => 'Culture & Knowledge — TENIKO',
            'metaDesc'   => 'Explore Malagasy culture, traditions, folklore, history, and linguistics.',
        ]);
    }

    public function article(Request $request): void
    {
        $slug    = $request->param('slug');
        $model   = new Article();
        $article = $model->getBySlug($slug);

        if (!$article) { $this->abort(404, 'Article not found.'); return; }

        $related = $this->db()->fetchAll(
            "SELECT id, title, slug, excerpt, cover_image FROM articles
             WHERE status='published' AND category_id=? AND deleted_at IS NULL AND id != ?
             ORDER BY published_at DESC LIMIT 4",
            [$article['category_id'], $article['id']]
        );
        $comments = $this->db()->fetchAll(
            "SELECT c.*, u.username, u.display_name, u.avatar FROM comments c
             JOIN users u ON u.id = c.user_id
             WHERE c.entity_type='article' AND c.entity_id=? AND c.status='published' AND c.deleted_at IS NULL
             ORDER BY c.created_at ASC",
            [$article['id']]
        );

        $this->render('culture/article', [
            'article'   => $article,
            'related'   => $related,
            'comments'  => $comments,
            'pageTitle' => e($article['title']) . ' | TENIKO',
            'metaDesc'  => $article['excerpt'] ? truncate($article['excerpt'], 160) : '',
        ]);
    }
}
