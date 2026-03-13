<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Model;

class Announcement extends Model
{
    protected string $table = 'announcements';
    protected bool $softDelete = false;

    public function active(int $limit = 5): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM announcements WHERE is_active = 1
             AND (expires_at IS NULL OR expires_at > NOW())
             ORDER BY created_at DESC LIMIT ?",
            [$limit]
        );
    }
}
