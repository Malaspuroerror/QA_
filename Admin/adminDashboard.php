<?php
// ensure session is started before any output so session cookie and vars are available
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin</title>
  <link rel="stylesheet" href="styleAdmin.css">
</head>
<body>
  <!-- ===== HEADER ===== -->
  <header class="header">
    <img src="../assets/OIP.png" alt="Logo">
    <h1>Admin Dashboard</h1>
  </header>

  <!-- ===== DASHBOARD CONTAINER ===== -->
  <div class="dashboard-container">
    <!-- SIDEBAR -->
    <aside class="sidebar">
      <div class="menu">
        <a href="adminDashboard.php" id="userTab" class="active">
          <img src="../assets/dashboard.png" alt="Dashboard">
          Users
        </a>
        <a href="adminLogs.php" id="logsTab">
          <img src="../assets/User.png" alt="Logs">
          Logs
        </a>
      </div>

      <div class="sidebar-footer">
        <div class="user-info">
          <?php
            // display name stored in session by login.php
            if (!empty($_SESSION['name'])) {
                echo '<p class="user-name">' . htmlspecialchars($_SESSION['name']) . '</p>';
            } else {
                echo '<p class="user-name">Not logged in</p>';
            }
          ?>
        </div>
        <button class="signout" id="signoutBtn">
          <img src="../assets/out.png" alt="Sign Out">
          Logout
        </button>
      </div>
    </aside>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="main-content">

      <!-- ===== USERS SECTION ===== -->
      <section id="userSection" class="content-box active">
        <div class="section-header">

          <!-- SEARCH BAR -->
          <div class="search-container">
            <input type="text" id="userSearchInput" placeholder="Search users...">
          </div>
        </div>

        <!-- USERS TABLE -->
        <table class="data-table">
          <thead>
            <tr>
              <th>No.</th>
              <th>Name</th>
              <th>Role</th>
              <th>Email</th>
            </tr>
          </thead>
          <tbody id="usersTableBody">
            <?php
              // Include server-side users rows so the Users table shows even without JS
              require_once __DIR__ . '/list_users.php';
            ?>
          </tbody>
        </table>
      </section>

    </main>
  </div>

  <script src="scriptAdmin.js"></script>
</body>
</html>
