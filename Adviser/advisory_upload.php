<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../Login/db.php';
require_once __DIR__ . '/../includes/advisory_excel_importer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo json_encode(['success'=>false,'error'=>'Invalid request method']);
    exit;
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK){
    echo json_encode(['success'=>false,'error'=>'No file uploaded or upload error']);
    exit;
}

$uploaded = $_FILES['file'];
$originalName = $uploaded['name'];
$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
if (!in_array($ext, ['xls','xlsx'])){
    echo json_encode(['success'=>false,'error'=>'Only Excel files are allowed']);
    exit;
}

// grade_section from form
$grade_section = isset($_POST['grade_section']) ? trim($_POST['grade_section']) : '';
// prefer session user as adviser name if available
$adviser_name = '';
if (!empty($_SESSION['name'])){
    $adviser_name = trim($_SESSION['name']);
} elseif (isset($_POST['teacher_name'])){
    $adviser_name = trim($_POST['teacher_name']);
}

// try to parse grade_section from filename if not provided
if (empty($grade_section) && $originalName){
    $base = preg_replace('/\.[^.]+$/','',$originalName);
    $parts = explode('-', $base);
    if (count($parts) >= 2){
        $grade_section = trim($parts[1]);
    }
}

// Save file into uploads/advisory_files
$uploadsDir = __DIR__ . '/../uploads/advisory_files';
if (!is_dir($uploadsDir)){
    if (!mkdir($uploadsDir, 0755, true)){
        echo json_encode(['success'=>false,'error'=>'Failed to create upload directory']);
        exit;
    }
}

$safeName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($originalName));
$targetName = $safeName;
$targetPath = $uploadsDir . '/' . $targetName;

if (!move_uploaded_file($uploaded['tmp_name'], $targetPath)){
    echo json_encode(['success'=>false,'error'=>'Failed to move uploaded file']);
    exit;
}

$relativePath = 'uploads/advisory_files/' . $targetName;

// run importer to create grade_section table and insert rows
try{
    // verify adviser is authorized to upload for this grade_section
    if (empty($adviser_name)){
        echo json_encode(['success'=>false,'error'=>'Adviser identity not found in session or form']);
        exit;
    }

    $userStmt = $pdo->prepare('SELECT role, advisory FROM users WHERE name = :name LIMIT 1');
    $userStmt->execute([':name' => $adviser_name]);
    $userRow = $userStmt->fetch(PDO::FETCH_ASSOC);
    if (!$userRow){
        echo json_encode(['success'=>false,'error'=>'Adviser not found in users table']);
        exit;
    }
    if (strtolower($userRow['role']) !== 'adviser'){
        echo json_encode(['success'=>false,'error'=>'Only users with role adviser can upload advisory files']);
        exit;
    }

    // normalize strings for comparison: remove non-alphanum and lowercase
    $normalize = function($s){ return strtolower(preg_replace('/[^A-Za-z0-9]/','', (string)$s)); };
    $expected = $normalize($userRow['advisory']);
    $provided = $normalize($grade_section);
    if (empty($expected)){
        echo json_encode(['success'=>false,'error'=>'Adviser has no advisory assigned in users table']);
        exit;
    }
    if ($expected !== $provided){
        echo json_encode(['success'=>false,'error'=>'Grade/Section does not match adviser\'s assigned advisory','assigned'=>$userRow['advisory'],'provided'=>$grade_section]);
        exit;
    }

    $importResult = import_advisory_excel($relativePath, $pdo, $grade_section, $adviser_name);
    // remove uploaded file after import so advisory uploads are not persisted with teacher files
    if (file_exists($targetPath)) {
        @unlink($targetPath);
    }
    if ($importResult['success']){
        echo json_encode(['success'=>true,'message'=>$importResult['message'],'table'=>$importResult['table']]);
    } else {
        echo json_encode(['success'=>false,'error'=>$importResult['message']]);
    }
} catch (Exception $e){
    echo json_encode(['success'=>false,'error'=>'Import error: ' . $e->getMessage()]);
}

?>