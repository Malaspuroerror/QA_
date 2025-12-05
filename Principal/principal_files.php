<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Principal - Files</title>
  <link rel="stylesheet" href="stylePrincipalDashboard.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="principal_files.css?v=<?php echo time(); ?>">
  <style>
    /* Loading Animation Styles */
    .loading-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 1000;
      justify-content: center;
      align-items: center;
    }

    .loading-overlay.show {
      display: flex;
    }

    .loading-spinner {
      background-color: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .spinner {
      border: 4px solid #f3f3f3;
      border-top: 4px solid #3498db;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      animation: spin 1s linear infinite;
      margin: 0 auto 15px;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .loading-text {
      color: #333;
      font-size: 14px;
      font-weight: 500;
    }
  </style>
</head>
<body>

  <!-- HEADER BAR -->
  <header class="header">
    <img src="../assets/OIP.png" alt="DepEd Logo">
    <h1>Principal</h1>
  </header>

  <div class="dashboard-container">

    <!-- SIDEBAR -->
    <aside class="sidebar">
      <nav class="menu">
        <a href="principalDashboard.php">
          <img src="../assets/dashboard.png" alt="Dashboard Icon">
          Dashboard
        </a>
        <a href="principal_files.php" class="active">
          <img src="../assets/google-docs.png" alt="Files Icon">
          Files
        </a>
      </nav>

      <!-- SIDEBAR FOOTER -->
      <div class="sidebar-footer">
        <div class="user-info">
          <?php
            if (!empty($_SESSION['name'])) {
              echo '<p class="user-name">' . htmlspecialchars($_SESSION['name']) . '</p>';
            } else {
              echo '<p class="user-name">Not logged in</p>';
            }
          ?>
        </div>
        <button class="signout" id="signoutBtn">
          <img src="../assets/out.png" alt="Logout Icon">
          Sign Out
        </button>
      </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
      <div class="topbar">
        <h3>Files</h3>
        <p>Manage and review submitted files</p>
      </div>

      <!-- FILES TABLE -->
      <div class="files-section">
        <h3>FILES</h3>
        <table class="files-table">
          <thead>
            <tr>
              <th>Filename</th>
              <th>Teacher</th>
              <th>Submitted Date</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="filesTableBody">
            <tr><td colspan="5" class="no-data">Loading files...</td></tr>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <!-- LOADING OVERLAY -->
  <div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner">
      <div class="spinner"></div>
      <p class="loading-text">Processing file...</p>
    </div>
  </div>

  <script>
    // Load files on page load
    function loadFiles() {
      fetch('principal_list_files.php')
        .then(res => res.text())
        .then(html => {
          document.getElementById('filesTableBody').innerHTML = html;
          attachApproveRejectHandlers();
        })
        .catch(err => {
          document.getElementById('filesTableBody').innerHTML = '<tr><td colspan="5" class="no-data" style="color:red;">Error loading files</td></tr>';
        });
    }

    // Attach event listeners to approve/reject buttons
    function attachApproveRejectHandlers() {
      document.querySelectorAll('.approve-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          const fileId = this.getAttribute('data-file-id');
          if (confirm('Are you sure you want to approve this file?')) {
            updateFileStatus(fileId, 'approve');
          }
        });
      });

      document.querySelectorAll('.reject-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          const fileId = this.getAttribute('data-file-id');
          if (confirm('Are you sure you want to reject this file?')) {
            updateFileStatus(fileId, 'reject');
          }
        });
      });
    }

    // Update file status via AJAX
    function updateFileStatus(fileId, action) {
      // Show loading overlay
      const loadingOverlay = document.getElementById('loadingOverlay');
      if (loadingOverlay) {
        loadingOverlay.classList.add('show');
      }

      fetch('principal_update_file.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=' + action + '&file_id=' + fileId
      })
        .then(res => res.json())
        .then(data => {
          // Hide loading overlay
          if (loadingOverlay) {
            loadingOverlay.classList.remove('show');
          }

          if (data.success) {
            // Reload files to reflect changes
            loadFiles();
          } else {
            alert('Error: ' + (data.error || 'Unknown error'));
          }
        })
        .catch(err => {
          // Hide loading overlay
          if (loadingOverlay) {
            loadingOverlay.classList.remove('show');
          }

          alert('Failed to update file: ' + err.message);
        });
    }

    // Sign out functionality
    document.addEventListener('DOMContentLoaded', function() {
      loadFiles();

      const signoutBtn = document.getElementById('signoutBtn');
      if (signoutBtn) {
        signoutBtn.addEventListener('click', function() {
          if (confirm('Are you sure you want to sign out?')) {
            window.location.href = '../Login/logout.php';
          }
        });
      }
    });
  </script>

</body>
</html>
