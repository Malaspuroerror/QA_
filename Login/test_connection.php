<?php
include('db.php');

try {
    $query = $pdo->query("SELECT NOW()");
    $result = $query->fetch();
    echo " Connection successful! Server time: " . $result[0];
} catch (PDOException $e) {
    echo " Connection failed: " . $e->getMessage();
}
?>
