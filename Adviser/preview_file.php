<?php
// Returns JSON preview data for a file
session_start();
require_once __DIR__ . '/../Login/db.php';

$fileId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$fileId){
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file ID']);
    exit;
}

try{
    $stmt = $pdo->prepare("SELECT id, teacher_name, subject, grade_section, file_name, file_path, status, submitted_date, approve_date FROM teacher_files WHERE id = :id");
    $stmt->execute([':id' => $fileId]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e){
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
    exit;
}

if (!$file){
    http_response_code(404);
    echo json_encode(['error' => 'File not found']);
    exit;
}

// Prepare preview data
$preview = [
    'id' => $file['id'],
    'file_name' => htmlspecialchars($file['file_name']),
    'teacher_name' => htmlspecialchars($file['teacher_name']),
    'subject' => htmlspecialchars($file['subject']),
    'grade_section' => htmlspecialchars($file['grade_section']),
    'status' => htmlspecialchars($file['status']),
    'submitted_date' => $file['submitted_date'] ? htmlspecialchars(date('Y-m-d H:i', strtotime($file['submitted_date']))) : '-',
    'approve_date' => $file['approve_date'] ? htmlspecialchars(date('Y-m-d H:i', strtotime($file['approve_date']))) : '-',
    'file_path' => '../' . htmlspecialchars($file['file_path'])
];

header('Content-Type: application/json');
echo json_encode($preview);

?>
