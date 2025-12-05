 // Navigation handling
    document.addEventListener('DOMContentLoaded', function() {
      console.log('DOMContentLoaded event fired'); // Debug log
      
      // Sign out button handling
      const signoutBtn = document.getElementById('signoutBtn');
      if (signoutBtn) {
        signoutBtn.addEventListener('click', function() {
          // Navigate to logout.php which destroys the session and redirects
          window.location.href = '../Login/logout.php';
        });
      }

      // Get all sidebar links
      const sidebarLinks = document.querySelectorAll('.sidebar .menu a');
      
      // Add click event to each link
      sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
          // Remove active class from all links
          sidebarLinks.forEach(l => l.classList.remove('active'));
          
          // Add active class to clicked link
          this.classList.add('active');
          
          // Allow default navigation to proceed
        });
      });

      // Load dashboard data if on dashboard page
      const fileTableBody = document.getElementById('fileTableBody');
      if (fileTableBody) {
        console.log('Dashboard page detected, loading files...'); // Debug log
        loadDashboardFiles();
        loadStudentCount();
      }
    });

    // Load files for the logged-in teacher
    async function loadDashboardFiles() {
      try {
        const res = await fetch('../Adviser/list_files.php?teacher=1');
        const html = await res.text();
        console.log('Files response:', html); // Debug log
        const tbody = document.getElementById('fileTableBody');
        if (tbody) {
          tbody.innerHTML = html;
        }
      } catch (err) {
        console.error('Error loading files:', err);
        const tbody = document.getElementById('fileTableBody');
        if (tbody) {
          tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: #888;">Error loading files</td></tr>';
        }
      }
    }

    // Placeholder for loading student count (implement as needed)
    function loadStudentCount() {
      // This can be expanded later to fetch actual student count
      const studentCountEl = document.getElementById('studentCount');
      const fileCountEl = document.getElementById('fileCount');
      
      if (studentCountEl) studentCountEl.textContent = '—';
      if (fileCountEl) fileCountEl.textContent = '—';
    }