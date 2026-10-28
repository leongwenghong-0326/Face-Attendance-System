<?php
namespace App\Services;

class ExportService
{
    public static function exportAttendanceCsv(array $records, string $filename = 'attendance_report.csv'): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        // UTF-8 BOM for Microsoft Excel
        fputs($out, "\xEF\xBB\xBF");

        // Headers
        fputcsv($out, [
            'Attendance ID',
            'Date',
            'Student Code',
            'Student Name',
            'Class',
            'Check In',
            'Check In Confidence',
            'Check Out',
            'Check Out Confidence',
            'Status',
            'Notes'
        ]);

        foreach ($records as $r) {
            fputcsv($out, [
                $r['id'],
                $r['attendance_date'],
                $r['student_code'] ?? '',
                $r['student_name'] ?? '',
                $r['class_name'] ?? 'Unassigned',
                $r['check_in_time'] ?: '-',
                $r['check_in_confidence'] ? round($r['check_in_confidence'] * 100, 1) . '%' : '-',
                $r['check_out_time'] ?: '-',
                $r['check_out_confidence'] ? round($r['check_out_confidence'] * 100, 1) . '%' : '-',
                strtoupper(str_replace('_', ' ', $r['status'])),
                $r['notes'] ?? '',
            ]);
        }

        fclose($out);
        exit;
    }

    public static function exportStudentsCsv(array $students, string $filename = 'students_export.csv'): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        // UTF-8 BOM
        fputs($out, "\xEF\xBB\xBF");

        // Headers
        fputcsv($out, [
            'ID',
            'Student Code',
            'Username',
            'Full Name',
            'Email',
            'Phone',
            'Gender',
            'Date of Birth',
            'Class',
            'Face Registered',
            'Face Version',
            'Account Status',
            'Created Date'
        ]);

        foreach ($students as $s) {
            fputcsv($out, [
                $s['id'],
                $s['student_code'],
                $s['username'] ?? '',
                $s['full_name'],
                $s['email'],
                $s['phone'] ?? '-',
                ucfirst($s['gender'] ?? 'other'),
                $s['date_of_birth'] ?? '-',
                $s['class_name'] ?? 'Unassigned',
                $s['is_face_registered'] ? 'Yes' : 'No',
                $s['face_version'],
                ucfirst($s['status']),
                $s['created_at'],
            ]);
        }

        fclose($out);
        exit;
    }
}
