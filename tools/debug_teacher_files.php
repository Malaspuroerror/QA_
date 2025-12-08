<?php
// Simple debug endpoint to list recent teacher_files rows as JSON
require_once __DIR__ . '/../Login/db.php';
header('Content-Type: application/json');
try{
    $stmt = $pdo->query("SELECT id, teacher_name, subject, grade_section, file_name, file_path, status, submitted_date, approve_date, created_at FROM teacher_files ORDER BY submitted_date DESC LIMIT 50");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'rows' => $rows]);
} catch (Exception $e){
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

?>
