<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Principal - Files</title>
  <link rel="stylesheet" href="stylePrincipalDashboard.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="principal_files.css?v=<?php echo time(); ?>">
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
        <a href="principalDashboard.php">
          <img src="dashboard.png" alt="Dashboard Icon">
          Dashboard
        </a>
        <a href="principal_files.php" class="active">
          <img src="google-docs.png" alt="Files Icon">
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
          <img src="out.png" alt="Logout Icon">
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
      fetch('principal_update_file.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=' + action + '&file_id=' + fileId
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            // Reload files to reflect changes
            loadFiles();
          } else {
            alert('Error: ' + (data.error || 'Unknown error'));
          }
        })
        .catch(err => {
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
