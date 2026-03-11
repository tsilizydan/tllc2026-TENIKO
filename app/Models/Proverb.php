<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Model;

class Proverb extends Model
{
    protected string $table = 'proverbs';

    public function getFullEntry(int $id): ?array
    {
        $proverb = $this->db->fetch(
            "SELECT p.*, d.name AS dialect_name, u.display_name AS author_name
             FROM proverbs p
             LEFT JOIN dialects d ON d.id = p.dialect_id
             LEFT JOIN users u ON u.id = p.created_by
             WHERE p.id = ? AND p.status = 'published' AND p.deleted_at IS NULL", [$id]
        );
        if (!$proverb) return null;
        $proverb['audio'] = $this->db->fetchAll(
            "SELECT * FROM audio_files WHERE entity_type='proverb' AND entity_id=? AND status='published'",
            [$id]
        );
        $this->db->query("UPDATE proverbs SET view_count = view_count + 1 WHERE id = ?", [$id]);
        return $proverb;
    }

    public function proverbOfDay(): ?array
    {
        $proverb = $this->db->fetch(
            "SELECT * FROM proverbs WHERE proverb_of_day_date = CURDATE() AND status = 'published' LIMIT 1"
        );
        if (!$proverb) {
            $proverb = $this->db->fetch(
                "SELECT * FROM proverbs WHERE status = 'published' AND deleted_at IS NULL ORDER BY RAND() LIMIT 1"
            );
        }
        return $proverb;
    }

    public function paginated(int $page = 1, int $perPage = 20, ?int $dialectId = null): array
    {
        $where  = "status = 'published' AND deleted_at IS NULL";
        $params = [];
        if ($dialectId) {
            $where  .= " AND dialect_id = :dialect_id";
            $params['dialect_id'] = $dialectId;
        }
        return $this->paginate($page, $perPage, $where, $params, 'created_at DESC');
    }

    public function latest(int $limit = 6): array
    {
        return $this->db->fetchAll(
            "SELECT id, text, slug, translation_fr FROM proverbs
             WHERE status='published' AND deleted_at IS NULL ORDER BY created_at DESC LIMIT ?", [$limit]
        );
    }
}
