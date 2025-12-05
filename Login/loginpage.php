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
      <img src="OIP.png" class="school-logo" alt="School Logo">

      <h2>Welcome Back</h2>
      <p class="subtitle">Welcome back! Please enter your details</p>

        <?php 
        if (!empty($errorMessage)) {
            echo "<p style='color:red; font-size:13px;'>$errorMessage</p>";
        }
        ?>

      <form method="POST" action="login.php">
        <label>Email</label>
        <input type="email" name="txtEmail" placeholder="Enter your email" required>

        <label>Password</label>
        <input type="password" name="txtPassword" placeholder="Enter your password" required>

        <div class="options">
          <label><input type="checkbox" name="chkRemember"> Remember me</label>
          <a href="#">Forgot password?</a>
        </div>

        <button type="submit" name="btnSignIn" class="signin-btn">Sign In</button>

        <button type="button" class="google-btn">
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