<?php
require_once 'db.php';
require_once __DIR__ . '/../includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $code = trim($_POST['code']);
    $newpw = $_POST['new_password'];

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        header('Location: verify_reset.php?error=' . urlencode('Invalid email'));
        exit();
    }

    $pr = $pdo->prepare('SELECT id, code_hash, expires_at FROM password_resets WHERE user_id = ? ORDER BY id DESC LIMIT 1');
    $pr->execute([$user['id']]);
    $row = $pr->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        header('Location: verify_reset.php?error=' . urlencode('No reset request found'));
        exit();
    }

    if (new DateTime() > new DateTime($row['expires_at'])) {
        header('Location: verify_reset.php?error=' . urlencode('Code expired'));
        exit();
    }

    if (!password_verify($code, $row['code_hash'])) {
        header('Location: verify_reset.php?error=' . urlencode('Invalid code'));
        exit();
    }

    // update password
    $pw_hash = password_hash($newpw, PASSWORD_DEFAULT);
    $upd = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
    $upd->execute([$pw_hash, $user['id']]);

    // remove used reset tokens
    $del = $pdo->prepare('DELETE FROM password_resets WHERE user_id = ?');
    $del->execute([$user['id']]);

    header('Location: loginpage.php?success=' . urlencode('Password updated. You can now login.'));
    exit();
}

$email = $_GET['email'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Verify Reset Code</title>
  <style>label{display:block;margin-top:8px}</style>
</head>
<body>
  <h2>Enter verification code</h2>
  <?php if (!empty($_GET['error'])) { echo '<p style="color:red">'.htmlspecialchars($_GET['error']).'</p>'; } ?>
  <?php if (!empty($_GET['success'])) { echo '<p style="color:green">'.htmlspecialchars($_GET['success']).'</p>'; } ?>
  <form method="post">
    <label>Email</label>
    <input type="email" name="email" required value="<?= htmlspecialchars($email) ?>">
    <label>Verification code</label>
    <input type="text" name="code" required>
    <label>New password</label>
    <input type="password" name="new_password" required>
    <button type="submit">Reset password</button>
  </form>
</body>
</html>
