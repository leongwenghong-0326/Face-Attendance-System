<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Subject extends Model
{
    protected static string $table = 'subjects';

    public static function allWithClassCount(): array
    {
        $sql = "SELECT sub.*, COUNT(cs.class_id) AS class_count
                FROM subjects sub
                LEFT JOIN class_subjects cs ON cs.subject_id = sub.id
                GROUP BY sub.id
                ORDER BY sub.subject_code ASC";
        return Database::fetchAll($sql);
    }
}
