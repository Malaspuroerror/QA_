<?php
// Returns HTML table rows for admin file logs
require_once __DIR__ . '/../Login/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $limit = 100; // Limit initial load to first 100 records
    $stmt = $pdo->query("SELECT id, teacher_name, subject, grade_section, file_name, file_path, status, submitted_date, approve_date FROM teacher_files ORDER BY created_at DESC LIMIT {$limit}");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    http_response_code(500);
    echo '<tr><td colspan="6">Error loading logs</td></tr>';
    exit;
}

if (!$rows) {
    echo '<tr><td colspan="6" class="no-data">No logs found</td></tr>';
    exit;
}

$idx = 1;
foreach ($rows as $r) {
    $fileName = htmlspecialchars($r['file_name']);
    $teacher = htmlspecialchars($r['teacher_name']);
    $status = htmlspecialchars($r['status']);
    $submitted = $r['submitted_date'] ? htmlspecialchars(date('Y-m-d H:i', strtotime($r['submitted_date']))) : '-';
    $approve = $r['approve_date'] ? htmlspecialchars(date('Y-m-d H:i', strtotime($r['approve_date']))) : '-';

    echo "<tr>";
    echo "<td>{$idx}</td>";
    echo "<td>{$fileName}</td>";
    echo "<td>" . ucfirst($status) . "</td>";
    echo "<td>{$submitted}</td>";
    echo "<td>{$teacher}</td>";
    echo "<td>{$approve}</td>";
    echo "</tr>";

    $idx++;
}

if (count($rows) >= $limit) {
    echo '<tr><td colspan="6" style="text-align: center; padding: 10px; color: #999; font-size: 0.9em;">Showing first ' . $limit . ' results. Use search to find more.</td></tr>';
}

?>