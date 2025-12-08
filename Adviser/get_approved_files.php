<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../Login/db.php';

$teacherOnly = isset($_GET['teacher']) && $_GET['teacher'] == '1';

try{
    if ($teacherOnly && !empty($_SESSION['name'])){
        $stmt = $pdo->prepare("SELECT id, teacher_name, subject, grade_section, file_name, file_path, status, submitted_date, approve_date FROM teacher_files WHERE teacher_name = :tname AND status = 'approved' ORDER BY approve_date DESC");
        $stmt->execute([':tname' => $_SESSION['name']]);
    } else {
        $stmt = $pdo->prepare("SELECT id, teacher_name, subject, grade_section, file_name, file_path, status, submitted_date, approve_date FROM teacher_files WHERE status = 'approved' ORDER BY approve_date DESC");
        $stmt->execute();
    }
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'files' => $rows]);
} catch (Exception $e){
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

?>
