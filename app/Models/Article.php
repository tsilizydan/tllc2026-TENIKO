<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Model;

class Article extends Model
{
    protected string $table = 'articles';

    public function getBySlug(string $slug): ?array
    {
        $article = $this->db->fetch(
            "SELECT a.*, c.name AS category_name, u.display_name AS author_name
             FROM articles a
             LEFT JOIN categories c ON c.id = a.category_id
             LEFT JOIN users u ON u.id = a.author_id
             WHERE a.slug = ? AND a.status = 'published' AND a.deleted_at IS NULL", [$slug]
        );
        if (!$article) return null;
        $this->db->query("UPDATE articles SET view_count = view_count + 1 WHERE id = ?", [$article['id']]);
        return $article;
    }

    public function featured(int $limit = 4): array
    {
        return $this->db->fetchAll(
            "SELECT a.id, a.title, a.slug, a.excerpt, a.cover_image, a.type, a.created_at,
                    c.name AS category_name
             FROM articles a LEFT JOIN categories c ON c.id = a.category_id
             WHERE a.status = 'published' AND a.deleted_at IS NULL AND a.featured = 1
             ORDER BY a.published_at DESC LIMIT ?", [$limit]
        );
    }

    public function latest(int $limit = 8): array
    {
        return $this->db->fetchAll(
            "SELECT a.id, a.title, a.slug, a.excerpt, a.cover_image, a.type, a.created_at,
                    c.name AS category_name
             FROM articles a LEFT JOIN categories c ON c.id = a.category_id
             WHERE a.status = 'published' AND a.deleted_at IS NULL
             ORDER BY a.published_at DESC LIMIT ?", [$limit]
        );
    }

    public function byCategory(int $categoryId, int $page = 1, int $perPage = 12): array
    {
        return $this->paginate($page, $perPage, "status='published' AND category_id=:cid", ['cid' => $categoryId], 'published_at DESC');
    }
}
