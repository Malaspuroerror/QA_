<?php
require_once 'db.php';
session_start();

// Ensure helper tables exist
$pdo->exec("CREATE TABLE IF NOT EXISTS user_tokens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id DOUBLE,
  selector VARCHAR(32) NOT NULL,
  token_hash VARCHAR(255) NOT NULL,
  expires_at DATETIME NOT NULL,
  type VARCHAR(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id DOUBLE,
  code_hash VARCHAR(255) NOT NULL,
  expires_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

// Auto-login using remember cookie
if (empty($_SESSION['email']) && !empty($_COOKIE['remember'])) {
  list($selector, $token) = explode(':', $_COOKIE['remember']);
  $stmt = $pdo->prepare('SELECT user_id, token_hash, expires_at FROM user_tokens WHERE selector = ? AND type = "remember" LIMIT 1');
  $stmt->execute([$selector]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    if (new DateTime() < new DateTime($row['expires_at']) && hash_equals($row['token_hash'], hash('sha256', $token))) {
      // valid token
      $u = $pdo->prepare('SELECT id, email, role, name FROM users WHERE id = ? LIMIT 1');
      $u->execute([$row['user_id']]);
      $user = $u->fetch(PDO::FETCH_ASSOC);
      if ($user) {
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        // redirect based on role
        $redirects = [
          'adviser' => '../Adviser/adviserDashboard.php',
          'teacher' => '../Teacher/teacherDashboard.php',
          'principal' => '../Principal/principalDashboard.php',
          'admin' => '../Admin/adminDashboard.php'
        ];
        if (isset($redirects[$user['role']])) {
          header('Location: ' . $redirects[$user['role']]);
          exit();
        }
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Telabastagan Integrated School - Login</title>
  <link rel="stylesheet" href="styleKKMLogin.css">
</head>
<body>
  <div class="login-container">
    <!-- Left Side (Form Section) -->
    <div class="login-left">
      <img src="../assets/OIP.png" class="school-logo" alt="School Logo">

      <h2>Welcome Back</h2>
      <p class="subtitle">Welcome back! Please enter your details</p>

        <?php
        if (!empty($_GET['error'])) {
          $msg = htmlspecialchars($_GET['error']);
          echo "<p style='color:red; font-size:13px;'>$msg</p>";
        }
        if (!empty($_GET['success'])) {
          $msg = htmlspecialchars($_GET['success']);
          echo "<p style='color:green; font-size:13px;'>$msg</p>";
        }
        ?>

      <form method="POST" action="login.php">
        <label>Email</label>
        <input type="email" name="txtEmail" placeholder="Enter your email" required>

        <label>Password</label>
        <input type="password" name="txtPassword" placeholder="Enter your password" required>

        <div class="options">
          <label><input type="checkbox" name="chkRemember"> Remember me</label>
          <a href="forgot_password.php">Forgot password?</a>
        </div>

        <button type="submit" name="btnSignIn" class="signin-btn">Sign In</button>

        <button type="button" class="google-btn" onclick="window.location.href='google_login.php'">
          <img src="https://www.svgrepo.com/show/355037/google.svg" width="18" alt="">
          Sign in with Google
        </button>
      </form>
    </div>

    <!-- Right Side (Image Section) -->
    <div class="login-right" style="background-color: #001f3f;"></div>
  </div>
</body>
</html>
