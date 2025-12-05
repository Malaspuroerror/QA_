<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teacher | Files</title>
  <!-- Core and Page-Specific CSS -->
  <link rel="stylesheet" href="teacher_style.css">
  <link rel="stylesheet" href="teacher_files.css">
</head>
<body>

  <!-- ===== HEADER ===== -->
  <header class="header">
    <img src="OIP.png" alt="Logo">
    <h1>Teacher</h1>
  </header>

  <!-- ===== DASHBOARD CONTAINER ===== -->
  <div class="dashboard-container">
    
    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar">
      <div class="menu">
        <a href="teacher_dashboard.php"><img src="dashboard.png" alt="">Dashboard</a>
        <a href="teacher_students.php"><img src="User.png" alt="">Students</a>
        <a href="teacher_files.php" class="active"><img src="google-docs.png" alt="">Files</a>
      </div>

      <!-- ===== SIDEBAR FOOTER ===== -->
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

    <!-- ===== MAIN CONTENT ===== -->
    <main class="main-content">
      <section class="content-box active" id="files">
        <div class="section-header">
          <div>
            <h2>Files</h2>
            <p style="color: #666;">Overview of Students</p>
          </div>
          <button class="new-file-btn" id="openUploadModal">+ New</button>
        </div>

        <!-- ===== FILES TABLE ===== -->
        <div>
          <table class="data-table">
            <thead>
              <tr>
                <th>Filename</th>
                <th>Submitted Date</th>
                <th>Status</th>
                <th>Approve Date</th>
              </tr>
            </thead>
            <tbody>
              <!-- Data from database will load here -->
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>

  <!-- ===== FILE VIEWER MODAL (OVERLAY) ===== -->
  <div class="file-viewer-modal" id="fileViewerModal">
    <div class="file-viewer-content">
      <button class="close-viewer" id="closeViewer">✖</button>
      <div id="fileViewerContainer" style="width:100%;height:100%;padding:40px;overflow:auto;">
        <!-- Preview will be loaded here -->
      </div>
    </div>
  </div>

  <!-- ===== UPLOAD MODAL ===== -->
  <div class="upload-modal" id="uploadModal">
    <div class="upload-content">
      <button class="close-modal" id="closeUploadModal">✖</button>
      <h3>Upload Files</h3>
      <div class="upload-box" id="dropZone">
        <p id="dropText">Drag and drop Excel files here</p>
        <span>- OR -</span>
        <button class="browse-btn" id="browseBtn">Browse Files</button>
        <input type="file" id="fileInput" accept=".xls,.xlsx" style="display:none">
      </div>

      <div class="file-preview" id="filePreview" style="display:none;margin-top:12px;">
        <p><strong>Filename:</strong> <span id="previewName"></span></p>
        <p><strong>Subject:</strong> <span id="previewSubject"></span></p>
        <p><strong>Grade & Section:</strong> <span id="previewGrade"></span></p>
        <p><strong>Teacher:</strong> <span id="previewTeacher"><?php echo !empty($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'Unknown'; ?></span></p>
        <div style="margin-top:8px;"><button id="uploadFileBtn">Upload</button> <button id="cancelFileBtn">Cancel</button></div>
        <p id="previewError" style="color:#cc0000;display:none;margin-top:8px;"></p>
      </div>
    </div>
  </div>

  <script>
    const openModal = document.getElementById("openUploadModal");
    const closeModal = document.getElementById("closeUploadModal");
    const modal = document.getElementById("uploadModal");

    openModal.addEventListener("click", () => modal.style.display = "flex");
    closeModal.addEventListener("click", () => closeUploadModalHandler());
    window.onclick = (e) => { if (e.target === modal) closeUploadModalHandler(); };

    function closeUploadModalHandler(){
      modal.style.display = "none";
      resetPreview();
    }

    // Drag/drop and file selection handling
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const browseBtn = document.getElementById('browseBtn');
    const filePreview = document.getElementById('filePreview');
    const previewName = document.getElementById('previewName');
    const previewSubject = document.getElementById('previewSubject');
    const previewGrade = document.getElementById('previewGrade');
    const previewTeacher = document.getElementById('previewTeacher');
    const uploadFileBtn = document.getElementById('uploadFileBtn');
    const cancelFileBtn = document.getElementById('cancelFileBtn');
    const previewError = document.getElementById('previewError');

    let selectedFile = null;

    browseBtn.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', (e) => handleFiles(e.target.files));

    ['dragenter','dragover'].forEach(evt => {
      dropZone.addEventListener(evt, (e) => { e.preventDefault(); e.stopPropagation(); dropZone.classList.add('drag-over'); });
    });
    ['dragleave','drop'].forEach(evt => {
      dropZone.addEventListener(evt, (e) => { e.preventDefault(); e.stopPropagation(); dropZone.classList.remove('drag-over'); });
    });

    dropZone.addEventListener('drop', (e) => {
      const dt = e.dataTransfer;
      if (dt && dt.files && dt.files.length) handleFiles(dt.files);
    });

    function handleFiles(files){
      previewError.style.display = 'none';
      const f = files[0];
      if (!f) return;
      const name = f.name;
      const ext = name.split('.').pop().toLowerCase();
      if (!['xls','xlsx'].includes(ext)){
        showPreviewError('Only Excel files (.xls, .xlsx) are allowed.');
        return;
      }
      // parse filename SUBJECTNAME-GRADELEVEL-SECTION
      const base = name.replace(/\.[^.]+$/, '');
      const parts = base.split('-');
      if (parts.length < 3){
        showPreviewError('Filename must be in format SUBJECTNAME-GRADELEVEL-SECTION');
        return;
      }
      const subject = parts[0].trim();
      const grade = parts[1].trim();
      const section = parts.slice(2).join('-').trim();

      selectedFile = f;
      previewName.textContent = name;
      previewSubject.textContent = subject;
      previewGrade.textContent = grade + ' - ' + section;
      filePreview.style.display = 'block';
    }

    function showPreviewError(msg){
      previewError.textContent = msg;
      previewError.style.display = 'block';
    }

    function resetPreview(){
      selectedFile = null;
      previewName.textContent = '';
      previewSubject.textContent = '';
      previewGrade.textContent = '';
      previewError.style.display = 'none';
      filePreview.style.display = 'none';
      fileInput.value = '';
    }

    cancelFileBtn.addEventListener('click', () => resetPreview());

    uploadFileBtn.addEventListener('click', async () => {
      if (!selectedFile){ showPreviewError('No file selected.'); return; }

      // re-parse filename
      const name = selectedFile.name;
      const base = name.replace(/\.[^.]+$/, '');
      const parts = base.split('-');
      const subject = parts[0].trim();
      const grade = parts[1].trim();
      const section = parts.slice(2).join('-').trim();

      const teacher = previewTeacher.textContent || '';

      const form = new FormData();
      form.append('file', selectedFile);
      form.append('subject', subject);
      form.append('grade_section', grade + ' - ' + section);
      form.append('teacher_name', teacher);

      uploadFileBtn.disabled = true;
      uploadFileBtn.textContent = 'Uploading...';

      try{
        // post to the shared upload handler in Adviser
        const res = await fetch('../Adviser/upload.php', { method: 'POST', body: form });
        const data = await res.json();
        if (data.success){
          alert('Upload successful');
          closeUploadModalHandler();
          loadFiles();
        } else {
          showPreviewError(data.error || 'Upload failed');
        }
      }catch(err){
        showPreviewError('Upload error: ' + err.message);
      }finally{
        uploadFileBtn.disabled = false;
        uploadFileBtn.textContent = 'Upload';
      }
    });

    // Load files table via AJAX and inject rows (use Adviser endpoint)
    async function loadFiles(){
      try{
        const res = await fetch('../Adviser/list_files.php?teacher=1');
        const html = await res.text();
        const tbody = document.querySelector('.data-table tbody');
        if (tbody) tbody.innerHTML = html;
      }catch(err){
        console.error('Failed to load files:', err);
      }
    }

    // initial load
    loadFiles();

    // File Viewer Modal
    const fileViewerModal = document.getElementById('fileViewerModal');
    const closeViewer = document.getElementById('closeViewer');
    const fileViewerContainer = document.getElementById('fileViewerContainer');

    closeViewer.addEventListener('click', () => fileViewerModal.style.display = 'none');
    fileViewerModal.addEventListener('click', (e) => {
      if (e.target === fileViewerModal) fileViewerModal.style.display = 'none';
    });

    // Delegate click on filename to open viewer
    document.querySelector('.data-table tbody').addEventListener('click', (e) => {
      if (e.target.classList.contains('file-link')) {
        e.preventDefault();
        const filePath = e.target.getAttribute('data-file-path');
        const fileName = e.target.getAttribute('data-file-name');
        openFileViewer(filePath, fileName);
      }
    });

    function openFileViewer(filePath, fileName) {
      fileViewerContainer.innerHTML = '<p style="text-align:center;margin-top:20px;">Loading file...</p>';
      fileViewerModal.style.display = 'flex';
      
      // Use iframe to display the file
      const iframe = document.createElement('iframe');
      iframe.src = filePath;
      iframe.style.width = '100%';
      iframe.style.height = '100%';
      iframe.style.border = 'none';
      
      fileViewerContainer.innerHTML = '';
      fileViewerContainer.appendChild(iframe);
    }
  </script>

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
