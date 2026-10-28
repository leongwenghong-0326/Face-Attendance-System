<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class AuditLog extends Model
{
    protected static string $table = 'audit_logs';

    public static function log(
        string $action,
        ?int $userId = null,
        ?int $studentId = null,
        ?float $confidence = null,
        ?int $version = null,
        ?array $metadata = null,
        ?string $ip = null,
        ?string $userAgent = null
    ): void {
        $ip = $ip ?? ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
        $userAgent = $userAgent ?? ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown');

        self::insert([
            'user_id' => $userId,
            'student_id' => $studentId,
            'action' => $action,
            'confidence_score' => $confidence,
            'registration_version' => $version,
            'ip_address' => $ip,
            'user_agent' => substr($userAgent, 0, 500),
            'metadata' => $metadata ? json_encode($metadata) : null,
        ]);
    }

    public static function allWithDetails(int $limit = 200): array
    {
        $sql = "SELECT al.*, u.username, u.role AS user_role, s.full_name AS student_name, s.student_code
                FROM audit_logs al
                LEFT JOIN users u ON al.user_id = u.id
                LEFT JOIN students s ON al.student_id = s.id
                ORDER BY al.id DESC
                LIMIT {$limit}";
        return Database::fetchAll($sql);
    }
}
