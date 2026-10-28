<?php
namespace App\Core;

use PDO;

abstract class Model
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';

    public static function all(string $orderBy = 'id DESC'): array
    {
        $table = static::$table;
        return Database::fetchAll("SELECT * FROM `{$table}` ORDER BY {$orderBy}");
    }

    public static function find(int $id): ?array
    {
        $table = static::$table;
        $pk = static::$primaryKey;
        return Database::fetchOne("SELECT * FROM `{$table}` WHERE `{$pk}` = :id LIMIT 1", ['id' => $id]);
    }

    public static function findBy(string $column, mixed $value): ?array
    {
        $table = static::$table;
        return Database::fetchOne("SELECT * FROM `{$table}` WHERE `{$column}` = :val LIMIT 1", ['val' => $value]);
    }

    public static function where(string $column, mixed $value, string $orderBy = 'id DESC'): array
    {
        $table = static::$table;
        return Database::fetchAll("SELECT * FROM `{$table}` WHERE `{$column}` = :val ORDER BY {$orderBy}", ['val' => $value]);
    }

    public static function insert(array $data): int
    {
        $table = static::$table;
        $columns = array_keys($data);
        $fields = implode('`, `', $columns);
        $placeholders = ':' . implode(', :', $columns);

        $sql = "INSERT INTO `{$table}` (`{$fields}`) VALUES ({$placeholders})";
        Database::query($sql, $data);
        return (int) Database::lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $table = static::$table;
        $pk = static::$primaryKey;
        $setClauses = [];
        $params = [':pk' => $id];

        foreach ($data as $key => $value) {
            $setClauses[] = "`{$key}` = :{$key}";
            $params[":{$key}"] = $value;
        }

        $sql = "UPDATE `{$table}` SET " . implode(', ', $setClauses) . " WHERE `{$pk}` = :pk";
        Database::query($sql, $params);
        return true;
    }

    public static function delete(int $id): bool
    {
        $table = static::$table;
        $pk = static::$primaryKey;
        Database::query("DELETE FROM `{$table}` WHERE `{$pk}` = :id", ['id' => $id]);
        return true;
    }

    public static function count(string $where = '', array $params = []): int
    {
        $table = static::$table;
        $sql = "SELECT COUNT(*) AS total FROM `{$table}`";
        if ($where !== '') {
            $sql .= " WHERE {$where}";
        }
        $row = Database::fetchOne($sql, $params);
        return (int) ($row['total'] ?? 0);
    }
}
