<?php
// Returns HTML table rows for all teacher files (for Principal view)
require_once __DIR__ . '/../Login/db.php';
session_start();

try{
    $stmt = $pdo->query("SELECT id, teacher_name, subject, grade_section, file_name, status, submitted_date, approve_date FROM teacher_files ORDER BY submitted_date DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e){
    http_response_code(500);
    echo '<tr><td colspan="5">Error loading files</td></tr>';
    exit;
}

if (!$rows){
    echo '<tr><td colspan="5" class="no-data">No files found</td></tr>';
    exit;
}

foreach($rows as $r){
    $fileName = htmlspecialchars($r['file_name']);
    $teacher = htmlspecialchars($r['teacher_name']);
    $subject = htmlspecialchars($r['subject']);
    $grade = htmlspecialchars($r['grade_section']);
    $status = htmlspecialchars($r['status']);
    $submitted = $r['submitted_date'] ? htmlspecialchars(date('M d, Y', strtotime($r['submitted_date']))) : '-';
    $fileId = intval($r['id']);

    echo "<tr>";
    echo "<td>{$fileName}<div style=\"color:#666;font-size:12px;margin-top:4px;\">{$subject} — {$grade}</div></td>";
    echo "<td>{$teacher}</td>";
    echo "<td>{$submitted}</td>";
    
    // Status badge with color
    if ($status === 'pending') {
        echo "<td><span class=\"status pending\">Pending</span></td>";
    } elseif ($status === 'approved') {
        echo "<td><span class=\"status approve\">Approved</span></td>";
    } elseif ($status === 'rejected') {
        echo "<td><span class=\"status reject\">Rejected</span></td>";
    } else {
        echo "<td><span class=\"status\">" . ucfirst($status) . "</span></td>";
    }
    
    // Actions column
    echo "<td class=\"actions-cell\">";
    if ($status === 'pending') {
        echo "<button class=\"action-btn approve-btn\" data-file-id=\"{$fileId}\" title=\"Approve\">✓</button>";
        echo "<button class=\"action-btn reject-btn\" data-file-id=\"{$fileId}\" title=\"Reject\">✕</button>";
    } else {
        echo "<span class=\"no-action\">—</span>";
    }
    echo "</td>";
    echo "</tr>";
}

?>
