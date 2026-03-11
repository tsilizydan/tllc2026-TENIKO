<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Model;

class Word extends Model
{
    protected string $table = 'words';

    /** Full-text + LIKE search */
    public function search(string $query, int $limit = 20): array
    {
        $q = '%' . $query . '%';
        return $this->db->fetchAll(
            "SELECT w.*, 
                    GROUP_CONCAT(DISTINCT CONCAT(t.lang,':', t.translation) ORDER BY t.sort_order SEPARATOR '|') AS translations
             FROM words w
             LEFT JOIN translations t ON t.word_id = w.id
             WHERE w.status = 'published' AND w.deleted_at IS NULL
               AND (w.word LIKE ? OR MATCH(w.word) AGAINST(? IN BOOLEAN MODE))
             GROUP BY w.id
             ORDER BY w.word LIKE ? DESC, w.word ASC
             LIMIT ?",
            [$q, $query . '*', $q, $limit]
        );
    }

    /** Autocomplete suggestions */
    public function autocomplete(string $prefix, int $limit = 8): array
    {
        return $this->db->fetchAll(
            "SELECT id, word, slug, part_of_speech FROM words
             WHERE status = 'published' AND deleted_at IS NULL AND word LIKE ?
             ORDER BY view_count DESC, word ASC LIMIT ?",
            [$prefix . '%', $limit]
        );
    }

    /** Get word with all definitions, translations, dialect variants, related words */
    public function getFullEntry(string $slug): ?array
    {
        $word = $this->db->fetch(
            "SELECT w.*, u.display_name AS author_name
             FROM words w LEFT JOIN users u ON u.id = w.created_by
             WHERE w.slug = ? AND w.status = 'published' AND w.deleted_at IS NULL",
            [$slug]
        );
        if (!$word) return null;

        $word['definitions'] = $this->db->fetchAll(
            "SELECT * FROM definitions WHERE word_id = ? ORDER BY lang, sort_order", [$word['id']]);
        $word['translations'] = $this->db->fetchAll(
            "SELECT * FROM translations WHERE word_id = ? ORDER BY lang, sort_order", [$word['id']]);
        $word['dialect_variants'] = $this->db->fetchAll(
            "SELECT wdv.*, d.name AS dialect_name, d.region FROM word_dialect_variants wdv
             JOIN dialects d ON d.id = wdv.dialect_id WHERE wdv.word_id = ?", [$word['id']]);
        $word['audio_files'] = $this->db->fetchAll(
            "SELECT * FROM audio_files WHERE entity_type='word' AND entity_id=? AND status='published'",
            [$word['id']]);
        $word['related'] = $this->db->fetchAll(
            "SELECT w2.id, w2.word, w2.slug, wr.type FROM word_relations wr
             JOIN words w2 ON w2.id = wr.related_id
             WHERE wr.word_id = ? AND w2.deleted_at IS NULL", [$word['id']]);

        // Increment view count
        $this->db->query("UPDATE words SET view_count = view_count + 1 WHERE id = ?", [$word['id']]);

        return $word;
    }

    /** Get Word of the Day */
    public function wordOfDay(): ?array
    {
        // Try today's date first
        $word = $this->db->fetch(
            "SELECT w.*, 
                    (SELECT translation FROM translations WHERE word_id=w.id AND lang='fr' LIMIT 1) AS trans_fr,
                    (SELECT translation FROM translations WHERE word_id=w.id AND lang='en' LIMIT 1) AS trans_en
             FROM words w WHERE w.word_of_day_date = CURDATE() AND w.status='published' LIMIT 1"
        );
        if (!$word) {
            // Fall back to a random featured word
            $word = $this->db->fetch(
                "SELECT w.*,
                        (SELECT translation FROM translations WHERE word_id=w.id AND lang='fr' LIMIT 1) AS trans_fr,
                        (SELECT translation FROM translations WHERE word_id=w.id AND lang='en' LIMIT 1) AS trans_en
                 FROM words w WHERE w.status='published' AND w.deleted_at IS NULL
                 ORDER BY RAND() LIMIT 1"
            );
        }
        return $word;
    }

    /** Most viewed words */
    public function mostViewed(int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT id, word, slug, part_of_speech, view_count FROM words
             WHERE status='published' AND deleted_at IS NULL
             ORDER BY view_count DESC LIMIT ?", [$limit]
        );
    }

    /** Latest published words */
    public function latest(int $limit = 8): array
    {
        return $this->db->fetchAll(
            "SELECT w.id, w.word, w.slug, w.part_of_speech, w.created_at,
                    (SELECT translation FROM translations WHERE word_id=w.id AND lang='fr' LIMIT 1) AS trans_fr
             FROM words w WHERE w.status='published' AND w.deleted_at IS NULL
             ORDER BY w.created_at DESC LIMIT ?", [$limit]
        );
    }
}
