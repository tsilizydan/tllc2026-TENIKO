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
        $db         = $this->db();

        // Fetch categories for filter tabs
        $categories = $db->fetchAll(
            "SELECT * FROM categories WHERE type IN ('article','cultural') ORDER BY sort_order"
        );

        // Get active category filter from query string
        $categoryId = $request->get('category') ? (int)$request->get('category') : null;
        $page       = max(1, (int)$request->get('page', 1));
        $perPage    = 12;
        $offset     = ($page - 1) * $perPage;

        // Build query based on filter
        if ($categoryId) {
            $total = $db->count(
                "SELECT COUNT(*) FROM articles WHERE status='published' AND deleted_at IS NULL AND category_id=?",
                [$categoryId]
            );
            $items = $db->fetchAll(
                "SELECT a.*, c.name AS category_name FROM articles a
                 LEFT JOIN categories c ON c.id = a.category_id
                 WHERE a.status='published' AND a.deleted_at IS NULL AND a.category_id=?
                 ORDER BY a.published_at DESC LIMIT ? OFFSET ?",
                [$categoryId, $perPage, $offset]
            );
        } else {
            $total = $db->count(
                "SELECT COUNT(*) FROM articles WHERE status='published' AND deleted_at IS NULL"
            );
            $items = $db->fetchAll(
                "SELECT a.*, c.name AS category_name FROM articles a
                 LEFT JOIN categories c ON c.id = a.category_id
                 WHERE a.status='published' AND a.deleted_at IS NULL
                 ORDER BY a.published_at DESC LIMIT ? OFFSET ?",
                [$perPage, $offset]
            );
        }

        $lastPage = max(1, (int)ceil($total / $perPage));

        // Featured (top 4)
        $featured = $model->featured(4);

        $this->render('culture/index', [
            'categories'  => $categories,
            'category'    => $categoryId,
            'featured'    => $featured,
            'articles'    => [
                'items'        => $items,
                'total'        => $total,
                'current_page' => $page,
                'last_page'    => $lastPage,
            ],
            'pageTitle'   => 'Culture & Knowledge — TENIKO',
            'metaDesc'    => 'Explore Malagasy culture, traditions, folklore, history, and linguistics.',
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
