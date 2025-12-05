<?php
// ensure session is started before any output so session cookie and vars are available
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Principal Dashboard</title>
  <link rel="stylesheet" href="stylePrincipalDashboard.css?v=<?php echo time(); ?>">
</head>
<body>
  <!-- HEADER BAR -->
  <header class="header">
    <img src="OIP.png" alt="DepEd Logo">
    <h1>Principal</h1>
  </header>
  <div class="dashboard-container">
    <!-- SIDEBAR -->
    <aside class="sidebar">
      <nav class="menu">
        <a href="principal_dashboard.php" class="active">
          <img src="dashboard.png" alt="Dashboard Icon">
          Dashboard
        </a>
        <a href="principal_files.php">
          <img src="google-docs.png" alt="Files Icon">
          Files
        </a>
      </nav>
      <!-- SIDEBAR FOOTER -->
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
          <img src="out.png" alt="Logout Icon">
          Sign Out
        </button>
      </div>
    </aside>
    <!-- MAIN CONTENT -->
    <main class="main-content">
      <div class="topbar">
        <h3>Dashboard</h3>
        <p>(Overview of Students)</p>
      </div>
      <!-- STATS CARDS -->
      <div class="stats">
        <div class="card">
          <p>Total Enrolled Students</p>
          <div class="blue-line"></div>
        </div>
        <div class="card">
          <p>Total Pending</p>
          <div class="blue-line"></div>
        </div>
        <div class="card">
          <p>Total Files</p>
          <div class="blue-line"></div>
        </div>
      </div>
      <!-- FILES TABLE -->
      <div class="files-section">
        <h3>Files</h3>
        <table>
          <thead>
            <tr>
              <th>Filename</th>
              <th>Teacher</th>
              <th>Submitted Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="4" class="no-data">No data available</td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </div>
  <!-- JavaScript -->
  <script src="scriptPrincipal.js"></script>
</body>
</html>


