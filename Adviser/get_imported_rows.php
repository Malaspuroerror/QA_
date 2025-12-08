<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../Login/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET'){
    echo json_encode(['success'=>false,'error'=>'Invalid request method']);
    exit;
}

$table = isset($_GET['table']) ? $_GET['table'] : '';
if (empty($table) || !preg_match('/^[A-Za-z0-9_]+$/', $table)){
    echo json_encode(['success'=>false,'error'=>'Invalid table name']);
    exit;
}

try{
    $stmt = $pdo->prepare("SELECT student_name, gender FROM `" . $table . "` ORDER BY id ASC");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success'=>true,'rows'=>$rows]);
} catch (Exception $e){
    echo json_encode(['success'=>false,'error'=>'DB error: ' . $e->getMessage()]);
}

?>