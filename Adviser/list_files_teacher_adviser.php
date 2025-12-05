<?php
// Returns HTML table rows for teacher_files - for Teacher and Adviser roles
require_once __DIR__ . '/../Login/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If teacher=1 is provided, show only files for the logged-in teacher/adviser
$teacherOnly = isset($_GET['teacher']) && $_GET['teacher'] == '1';
$isDashboard = isset($_GET['dashboard']) && $_GET['dashboard'] == '1';

try{
    if ($teacherOnly && !empty($_SESSION['name'])){
        $stmt = $pdo->prepare("SELECT id, teacher_name, subject, grade_section, file_name, file_path, status, submitted_date, approve_date FROM teacher_files WHERE teacher_name = :tname ORDER BY created_at DESC");
        $stmt->execute([':tname' => $_SESSION['name']]);
    } else {
        $stmt = $pdo->query("SELECT id, teacher_name, subject, grade_section, file_name, file_path, status, submitted_date, approve_date FROM teacher_files ORDER BY created_at DESC");
    }
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e){
    http_response_code(500);
    echo '<tr><td colspan="4">Error loading files</td></tr>';
    exit;
}

if (!$rows){
    echo '<tr><td colspan="4" class="no-data">No files found</td></tr>';
    exit;
}

foreach($rows as $r){
    $fileName = htmlspecialchars($r['file_name']);
    $subject = htmlspecialchars($r['subject']);
    $grade = htmlspecialchars($r['grade_section']);
    $status = htmlspecialchars($r['status']);
    $submitted = $r['submitted_date'] ? htmlspecialchars(date('Y-m-d H:i', strtotime($r['submitted_date']))) : '-';
    $approve = $r['approve_date'] ? htmlspecialchars(date('Y-m-d H:i', strtotime($r['approve_date']))) : '-';
    $filePath = htmlspecialchars($r['file_path']);
    $fileId = intval($r['id']);

    // link to file (pages are in subfolders so prefix with ../)
    $href = '../' . $filePath;

    echo "<tr>";
    // Show plain text filename on dashboard, link on files page
    if ($isDashboard) {
        echo "<td>{$fileName}</td>";
    } else {
        echo "<td><a href=\"{$href}\" download>{$fileName}</a></td>";
    }
    echo "<td>{$submitted}</td>";
    echo "<td>" . ucfirst($status) . "</td>";
    echo "<td>{$approve}</td>";
    echo "</tr>";
}

?>
