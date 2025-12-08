<?php
// Returns HTML table rows for admin users list
require_once __DIR__ . '/../Login/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $limit = 100; // Limit initial load to first 100 users
    $stmt = $pdo->query("SELECT id, name, role, email FROM users ORDER BY id ASC LIMIT {$limit}");
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

if (count($rows) >= $limit) {
    echo '<tr><td colspan="4" style="text-align: center; padding: 10px; color: #999; font-size: 0.9em;">Showing first ' . $limit . ' users. Use search to find more.</td></tr>';
}

?>
