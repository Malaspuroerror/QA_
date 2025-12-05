<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teacher | Students</title>
  <link rel="stylesheet" href="teacher_style.css">
</head>
<body>

  <!-- ===== HEADER ===== -->
  <header class="header">
    <img src="OIP.png" alt="Logo">
    <h1>Teacher</h1>
  </header>

  <!-- ===== DASHBOARD CONTAINER ===== -->
  <div class="dashboard-container">
    
    <aside class="sidebar">
      <div class="menu">
        <a href="teacher_dashboard.php"><img src="dashboard.png" alt="">Dashboard</a>
        <a href="teacher_students.php" class="active"><img src="User.png" alt="">Students</a>
        <a href="teacher_files.php"><img src="google-docs.png" alt="">Files</a>
      </div>

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

    <main class="main-content">
      <section class="content-box active" id="students">
        <div class="section-header">
          <div>
            <h2>Students</h2>
            <p style="color: #666;">Overview of Students</p>
          </div>
        </div>

        <div style="background-color: #d9e8ef; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
          <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 20px;">
            <div>
              <label>Subject Code:</label>
              <span style="font-weight: 600;" id="subjectCode">ESP-5</span>
            </div>
            <div>
              <label>Grade Level:</label>
              <span style="font-weight: 600;" id="gradeLevel">Grade 5</span>
            </div>
            <div>
              <label>Section:</label>
              <select id="section" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #ccc;">
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Students Table -->
        <div>
          <table class="data-table">
            <thead>
              <tr>
                <th style="width: 60px;">No.</th>
                <th>Fullname</th>
                <th style="width: 120px;">Grade</th>
                <th style="width: 150px;">Remarks</th>
              </tr>
            </thead>
            <tbody id="studentTableBody">
              <tr>
                <td colspan="4" style="text-align: center; color: #888;">No students available</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div> <!-- end dashboard container -->

  <!-- ===== JAVASCRIPT FILE LINK ===== -->
  <script src="scriptTeacher.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const signoutBtn = document.getElementById('signoutBtn');
      if (signoutBtn) {
        signoutBtn.addEventListener('click', function() {
          window.location.href = '../Login/logout.php';
        });
      }
    });
  </script>
</body>
</html>