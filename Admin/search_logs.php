<?php
// Search logs by filename, teacher name, or status
require_once __DIR__ . '/../Login/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

try {
    if (empty($query)) {
        // If no search query, return all logs
        $stmt = $pdo->query("SELECT id, teacher_name, subject, grade_section, file_name, file_path, status, submitted_date, approve_date FROM teacher_files ORDER BY created_at DESC");
    } else {
        // Search by filename, teacher name, or status
        $searchTerm = '%' . $query . '%';
        $stmt = $pdo->prepare("SELECT id, teacher_name, subject, grade_section, file_name, file_path, status, submitted_date, approve_date FROM teacher_files WHERE file_name LIKE ? OR teacher_name LIKE ? OR status LIKE ? ORDER BY created_at DESC");
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    }
    
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    http_response_code(500);
    echo '<tr><td colspan="6">Error searching logs</td></tr>';
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

?>
