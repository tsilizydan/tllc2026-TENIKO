<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Model;

class SiteSetting extends Model
{
    protected string $table = 'site_settings';
    protected bool $softDelete = false;

    private array $cache = [];

    public function get(string $key, mixed $default = null): mixed
    {
        if (isset($this->cache[$key])) return $this->cache[$key];
        $row = $this->db->fetch("SELECT value FROM site_settings WHERE `key` = ?", [$key]);
        $value = $row ? $row['value'] : $default;
        $this->cache[$key] = $value;
        return $value;
    }

    public function set(string $key, mixed $value): void
    {
        $exists = $this->db->fetch("SELECT id FROM site_settings WHERE `key` = ?", [$key]);
        if ($exists) {
            $this->db->update('site_settings', ['value' => $value, 'updated_at' => date('Y-m-d H:i:s')], '`key` = :k', ['k' => $key]);
        } else {
            $this->db->insert('site_settings', ['key' => $key, 'value' => $value]);
        }
        $this->cache[$key] = $value;
    }

    public function allGrouped(): array
    {
        $rows = $this->db->fetchAll("SELECT * FROM site_settings ORDER BY `group`, `key`");
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['group']][] = $row;
        }
        return $grouped;
    }
}
