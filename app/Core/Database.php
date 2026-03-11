<?php
declare(strict_types=1);
namespace App\Core;

use PDO;
use PDOStatement;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $config = require CONFIG_PATH . '/database.php';
        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=%s',
            $config['driver'],
            $config['host'],
            $config['port'],
            $config['name'],
            $config['charset']
        );
        try {
            $this->pdo = new PDO($dsn, $config['user'], $config['password'], $config['options']);
        } catch (PDOException $e) {
            if ($_ENV['APP_DEBUG'] ?? false) {
                die('Database connection failed: ' . $e->getMessage());
            }
            die('A database error occurred. Please try again later.');
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetch(PDO::FETCH_ASSOC);
        return $result === false ? null : $result;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function insert(string $table, array $data): int|string
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn($k) => ':' . $k, array_keys($data)));
        $sql = "INSERT INTO `{$table}` ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, $data);
        return $this->pdo->lastInsertId();
    }

    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $set = implode(', ', array_map(fn($k) => "`{$k}` = :{$k}", array_keys($data)));
        $sql = "UPDATE `{$table}` SET {$set} WHERE {$where}";
        $stmt = $this->query($sql, array_merge($data, $whereParams));
        return $stmt->rowCount();
    }

    public function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM `{$table}` WHERE {$where}";
        return $this->query($sql, $params)->rowCount();
    }

    public function count(string $sql, array $params = []): int
    {
        return (int) $this->query($sql, $params)->fetchColumn();
    }

    public function beginTransaction(): void { $this->pdo->beginTransaction(); }
    public function commit(): void           { $this->pdo->commit(); }
    public function rollback(): void         { $this->pdo->rollBack(); }

    public function pdo(): PDO { return $this->pdo; }
}
