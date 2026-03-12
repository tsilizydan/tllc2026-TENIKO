<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Model;

class Dialect extends Model
{
    protected string $table = 'dialects';
    protected bool $softDelete = false;

    public function all(string $orderBy = 'name ASC', int $limit = 100): array
    {
        return $this->db->fetchAll("SELECT * FROM dialects ORDER BY {$orderBy} LIMIT {$limit}");
    }

    public function findByCode(string $code): ?array
    {
        return $this->db->fetch("SELECT * FROM dialects WHERE code = ?", [$code]);
    }

    public function getWithWords(int $dialectId, int $limit = 30): ?array
    {
        $dialectId = (int)$dialectId; // ensure int even if called with string from PDO
        $dialect = $this->find($dialectId);
        if (!$dialect) return null;
        $dialect['word_variants'] = $this->db->fetchAll(
            "SELECT wdv.*, w.word AS standard_word, w.slug AS word_slug
             FROM word_dialect_variants wdv
             JOIN words w ON w.id = wdv.word_id
             WHERE wdv.dialect_id = ? AND w.status = 'published'
             ORDER BY w.word ASC LIMIT ?", [$dialectId, $limit]
        );
        return $dialect;
    }
}
