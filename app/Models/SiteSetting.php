<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Model;

class SiteSetting extends Model
{
    protected string $table = 'site_settings';
    protected bool $softDelete = false;

    private array $cache = [];

    /** Get a single setting value by key. */
    public function get(string $key, mixed $default = null): mixed
    {
        if (isset($this->cache[$key])) return $this->cache[$key];
        $row   = $this->db->fetch("SELECT value FROM site_settings WHERE `key` = ?", [$key]);
        $value = $row ? $row['value'] : $default;
        $this->cache[$key] = $value;
        return $value;
    }

    /** Upsert a single setting. */
    public function set(string $key, mixed $value): void
    {
        $exists = $this->db->fetch("SELECT id FROM site_settings WHERE `key` = ?", [$key]);
        if ($exists) {
            $this->db->update(
                'site_settings',
                ['value' => (string)$value, 'updated_at' => date('Y-m-d H:i:s')],
                '`key` = :k',
                ['k' => $key]
            );
        } else {
            $this->db->insert('site_settings', ['key' => $key, 'value' => (string)$value]);
        }
        $this->cache[$key] = (string)$value;
    }

    /**
     * Return ALL settings as a flat key→value array.
     * Used by the settings view: $settings['site_name']
     */
    public function allFlat(): array
    {
        $rows = $this->db->fetchAll("SELECT `key`, value FROM site_settings ORDER BY `key`");
        $flat = [];
        foreach ($rows as $row) {
            $flat[$row['key']] = $row['value'];
        }
        return $flat;
    }

    /**
     * Return settings grouped by their `group` column.
     * Gracefully handles missing `group` column.
     */
    public function allGrouped(): array
    {
        try {
            $rows = $this->db->fetchAll("SELECT * FROM site_settings ORDER BY `group`, `key`");
        } catch (\Throwable) {
            $rows = $this->db->fetchAll("SELECT * FROM site_settings ORDER BY `key`");
        }
        $grouped = [];
        foreach ($rows as $row) {
            $g = $row['group'] ?? 'general';
            $grouped[$g][] = $row;
        }
        return $grouped;
    }
}
