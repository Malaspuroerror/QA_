<?php
// Search users by name, role, or email
require_once __DIR__ . '/../Login/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$limit = 100; // Limit results to first 100 for faster loading

try {
    if (empty($query)) {
        // If no search query, return limited users
        $stmt = $pdo->query("SELECT id, name, role, email FROM users ORDER BY id ASC LIMIT {$limit}");
    } else {
        // Search by name, role, or email with LIMIT
        $searchTerm = '%' . $query . '%';
        $stmt = $pdo->prepare("SELECT id, name, role, email FROM users WHERE name LIKE ? OR role LIKE ? OR email LIKE ? ORDER BY id ASC LIMIT {$limit}");
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

if (count($rows) >= $limit) {
    echo '<tr><td colspan="4" style="text-align: center; padding: 10px; color: #999; font-size: 0.9em;">Showing first ' . $limit . ' results. Refine search to see more.</td></tr>';
}

?>
