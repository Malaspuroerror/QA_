<?php
// Form to request password reset code
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Forgot Password</title>
</head>
<body>
  <h2>Forgot Password</h2>
  <?php if (!empty($_GET['error'])) { echo '<p style="color:red">'.htmlspecialchars($_GET['error']).'</p>'; } ?>
  <?php if (!empty($_GET['success'])) { echo '<p style="color:green">'.htmlspecialchars($_GET['success']).'</p>'; } ?>
  <form method="post" action="send_reset.php">
    <label>Your email</label>
    <input type="email" name="email" required>
    <button type="submit">Send verification code</button>
  </form>
</body>
</html>
