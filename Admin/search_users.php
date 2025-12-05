<?php
// Search users by name, role, or email
require_once __DIR__ . '/../Login/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

try {
    if (empty($query)) {
        // If no search query, return all users
        $stmt = $pdo->query("SELECT id, name, role, email FROM users ORDER BY id ASC");
    } else {
        // Search by name, role, or email
        $searchTerm = '%' . $query . '%';
        $stmt = $pdo->prepare("SELECT id, name, role, email FROM users WHERE name LIKE ? OR role LIKE ? OR email LIKE ? ORDER BY id ASC");
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    }
    
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    http_response_code(500);
    echo '<tr><td colspan="4">Error searching users</td></tr>';
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
