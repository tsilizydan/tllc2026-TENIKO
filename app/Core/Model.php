<?php
declare(strict_types=1);
namespace App\Core;

abstract class Model
{
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected bool $softDelete = true;
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function find(int $id): ?array
    {
        $soft = $this->softDelete ? " AND deleted_at IS NULL" : "";
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ? {$soft}",
            [$id]
        );
    }

    public function findBy(string $column, mixed $value): ?array
    {
        $soft = $this->softDelete ? " AND deleted_at IS NULL" : "";
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `{$column}` = ? {$soft}",
            [$value]
        );
    }

    public function all(string $orderBy = 'id DESC', int $limit = 100): array
    {
        $soft = $this->softDelete ? "WHERE deleted_at IS NULL" : "";
        return $this->db->fetchAll(
            "SELECT * FROM `{$this->table}` {$soft} ORDER BY {$orderBy} LIMIT {$limit}"
        );
    }

    public function paginate(int $page = 1, int $perPage = 20, string $where = '1', array $params = [], string $orderBy = 'id DESC'): array
    {
        $soft = $this->softDelete ? " AND deleted_at IS NULL" : "";
        $offset = ($page - 1) * $perPage;
        $total = $this->db->count(
            "SELECT COUNT(*) FROM `{$this->table}` WHERE {$where} {$soft}",
            $params
        );
        $items = $this->db->fetchAll(
            "SELECT * FROM `{$this->table}` WHERE {$where} {$soft} ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        return [
            'items'       => $items,
            'total'       => $total,
            'per_page'    => $perPage,
            'current_page'=> $page,
            'last_page'   => (int)ceil($total / $perPage),
        ];
    }

    public function create(array $data): int|string
    {
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        return $this->db->insert($this->table, $data);
    }

    public function update(int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update($this->table, $data, "`{$this->primaryKey}` = :pk", ['pk' => $id]);
    }

    public function delete(int $id): int
    {
        if ($this->softDelete) {
            return $this->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
        }
        return $this->db->delete($this->table, "`{$this->primaryKey}` = ?", [$id]);
    }

    public function count(string $where = '1', array $params = []): int
    {
        $soft = $this->softDelete ? " AND deleted_at IS NULL" : "";
        return $this->db->count(
            "SELECT COUNT(*) FROM `{$this->table}` WHERE {$where} {$soft}",
            $params
        );
    }
}
