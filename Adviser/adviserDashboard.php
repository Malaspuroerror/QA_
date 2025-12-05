<?php
// ensure session is started before any output so session cookie and vars are available
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title>Adviser Dashboard</title>
  <link rel="stylesheet" href="styleAdviserDashboard.css">
</head>
<body>
  <!-- ===== HEADER ===== -->
  <header class="header">
    <img src="OIP.png" alt="Logo">
    <h1>Adviser</h1>
  </header>

  <!-- ===== DASHBOARD CONTAINER ===== -->
  <div class="dashboard-container">
    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar">
      <div class="menu">
        <a href="adviserDashboard.php" class="active">
          <img src="dashboard.png" alt="Dashboard Icon">
          Dashboard
        </a>
        <a href="student_list.php">
          <img src="User.png" alt="Student Icon">
          Student
        </a>
        <a href="advisory.php">
          <img src="class.png" alt="Advisory Icon">
          Advisory
        </a>
        <a href="files.php">
          <img src="google-docs.png" alt="Files Icon">
          Files
        </a>
      </div>

      <!-- ===== SIDEBAR FOOTER ===== -->
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
          <img src="out.png" alt="Logout Icon"> Sign Out
        </button>
      </div>
    </aside>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="main-content">
      <section class="content-box active" id="dashboard">
        <div class="section-header">
          <h2>Dashboard</h2>
          <span>(Overview of Advisory & Files)</span>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards-container">
          <div class="summary-card">
            <h3>Total Students</h3>
            <p id="studentCount">—</p>
          </div>
          <div class="summary-card">
            <h3>With Honors</h3>
            <p id="honorstudent">—</p>
          </div>
          <div class="summary-card">
            <h3>Total Files</h3>
            <p id="fileCount">—</p>
          </div>
        </div>

        <!-- File Table -->
        <div class="file-section">
          <h3>Recent Files</h3>
          <table class="data-table">
            <thead>
              <tr>
                <th>Filename</th>
                <th>Submitted Date</th>
                <th>Status</th>
                <th>Approved Date</th>
              </tr>
            </thead>
            <tbody id="fileTableBody">
              <tr>
                <td colspan="4" class="no-data">No data available</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>

  <!-- ===== JAVASCRIPT ===== -->
  <script src="scriptAdviser.js"></script>
</body>
</html>
