<?php
require_once 'db.php';

$redirects = [
    'adviser'   => '../Adviser/adviserDashboard.php',
    'teacher'   => '../Teacher/teacherDashboard.php',
    'principal' => '../Principal/principalDashboard.php',
    'admin'     => '../Admin/adminDashboard.php'
];

if (isset($_POST['btnSignIn'])) {
    $email = trim($_POST['txtEmail']);
    $password = $_POST['txtPassword'];

    if (empty($email) || empty($password)) {
        header("Location: index.php?error=" . urlencode("Email and password are required!"));
        exit();
    }

    $stmt = $pdo->prepare("SELECT password_hash, role, name FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        session_start();
        $_SESSION['email'] = $email;
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
    
        if (isset($redirects[$user['role']])) {
            header("Location: " . $redirects[$user['role']]);
            exit();
        } else {
            header("Location: index.php?error=" . urlencode("Invalid role! Contact administrator."));
            exit();
        }
    } else {
        header("Location: index.php?error=" . urlencode("Invalid email or password!"));
        exit();
    }
} else {
    // If someone tries to access login.php directly
    header("Location: index.php");
    exit();
}
?>
