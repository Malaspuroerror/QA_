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
  <title>Teacher Dashboard</title>
  <link rel="stylesheet" href="teacher_style.css">
  <style>
    .sidebar-footer {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 15px;
      margin-top: auto;
    }
    .user-info {
      width: 100%;
      padding: 0 0 15px 0;
      text-align: center;
      border-bottom: 1px solid #eee;
      margin-bottom: 15px;
    }
    .user-name {
      color: #333;
      font-size: 16px;
      margin: 0;
      font-weight: 600;
    }
    .signout {
      width: 100%;
    }
  </style>
</head>
<body>
  <!-- ===== HEADER ===== -->
  <header class="header">
    <img src="../assets/OIP.png" alt="Logo">
    <h1>Teacher</h1>
  </header>

  <!-- ===== DASHBOARD CONTAINER ===== -->
  <div class="dashboard-container">
    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar">
      <div class="menu">
        <a href="teacher_dashboard.php" class="active">
          <img src="../assets/dashboard.png" alt="Dashboard Icon">
          Dashboard
        </a>
        <a href="teacher_students.php">
          <img src="../assets/User.png" alt="Students Icon">
          Students
        </a>
        <a href="teacher_files.php">
          <img src="../assets/google-docs.png" alt="Files Icon">
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
          <img src="../assets/out.png" alt="Logout Icon"> Sign Out
        </button>
      </div>
    </aside>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="main-content">
      <!-- Dashboard Section -->
      <section class="content-box active" id="dashboard">
        <div class="section-header">
          <h2>Dashboard</h2>
          <span style="color: #666; font-size: 14px;">(Overview of Classes)</span>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards-container">
          <div class="summary-card">
            <h3>Total Enrolled Students</h3>
            <p id="studentCount">—</p>
          </div>
          <div class="summary-card">
            <h3>Total Files</h3>
            <p id="fileCount">—</p>
          </div>
        </div>

        <!-- File Table -->
        <div>
          <h3 style="margin-bottom: 10px;">Files</h3>
          <table class="data-table">
            <thead>
              <tr>
                <th>Filename</th>
                <th>Submitted Date</th>
                <th>Status</th>
                <th>Approve Date</th>
              </tr>
            </thead>
            <tbody id="fileTableBody">
              <tr>
                <td colspan="4" style="text-align: center; color: #888;">Loading...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>

  <!-- ===== JAVASCRIPT FILE LINK ===== -->
  <script src="scriptTeacher.js"></script>
  <script>
    console.log('Dashboard inline script loaded');
    // Load files for the logged-in teacher when page loads
    window.addEventListener('load', function() {
      console.log('Page fully loaded, fetching files...');
      fetch('../Adviser/list_files_teacher_adviser.php?teacher=1&dashboard=1')
        .then(response => {
          console.log('Response received:', response.status);
          return response.text();
        })
        .then(html => {
          console.log('Files HTML:', html);
          const tbody = document.getElementById('fileTableBody');
          if (tbody) {
            tbody.innerHTML = html;
            console.log('Files loaded into table');
          } else {
            console.log('fileTableBody element not found');
          }
        })
        .catch(error => {
          console.error('Fetch error:', error);
          const tbody = document.getElementById('fileTableBody');
          if (tbody) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: #888;">Error loading files</td></tr>';
          }
        });
    });
  </script>
</body>
</html>
