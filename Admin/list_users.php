<?php
// Returns HTML table rows for admin users list
require_once __DIR__ . '/../Login/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $stmt = $pdo->query("SELECT id, name, role, email FROM users ORDER BY id ASC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    http_response_code(500);
    echo '<tr><td colspan="4">Error loading users</td></tr>';
    exit;
}

if (!$rows) {
    echo '<tr><td colspan="4" class="no-data">No users found</td></tr>';
    exit;
}

$idx = 1;
foreach ($rows as $r) {
    $name = htmlspecialchars($r['name']);
    $role = htmlspecialchars($r['role']);
    $email = htmlspecialchars($r['email']);

    echo "<tr>";
    echo "<td>{$idx}</td>";
    echo "<td>{$name}</td>";
    echo "<td>" . ucfirst($role) . "</td>";
    echo "<td>{$email}</td>";
    echo "</tr>";

    $idx++;
}

?>
