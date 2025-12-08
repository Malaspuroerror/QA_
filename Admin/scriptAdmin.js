// Admin dashboard and logs page script - OPTIMIZED
let userSearchTimeout;
let logsSearchTimeout;
let lastUserQuery = '';
let lastLogsQuery = '';

document.addEventListener('DOMContentLoaded', function() {
  // User search with live filtering on input (debounced - reduced to 150ms for faster response)
  const userSearchInput = document.getElementById('userSearchInput');
  if (userSearchInput) {
    userSearchInput.addEventListener('input', function(e) {
      const v = e.target.value;
      console.log('user input event:', v);
      clearTimeout(userSearchTimeout);
      userSearchTimeout = setTimeout(searchUsers, 150);
    });
  }

  // Logs search with live filtering on input (debounced - reduced to 150ms for faster response)
  const logsSearchInput = document.getElementById('logsSearchInput');
  if (logsSearchInput) {
    logsSearchInput.addEventListener('input', function(e) {
      const v = e.target.value;
      console.log('logs input event:', v);
      clearTimeout(logsSearchTimeout);
      logsSearchTimeout = setTimeout(searchLogs, 150);
    });
  }

  // Sign out button
  const signoutBtn = document.getElementById('signoutBtn');
  if (signoutBtn) {
    signoutBtn.addEventListener('click', function() {
      if (confirm('Are you sure you want to log out?')) {
        window.location.href = '../Login/logout.php';
      }
    });
  }
});

// Search users with live input - OPTIMIZED with faster network and caching
async function searchUsers() {
  const searchInput = document.getElementById('userSearchInput');
  const query = searchInput ? searchInput.value.trim() : '';
  const userTableBody = document.getElementById('usersTableBody');
  
  if (!userTableBody) return;
  
  // Skip if query hasn't changed
  if (query === lastUserQuery) return;
  lastUserQuery = query;

  try {
    const urlBase = 'search_users.php';
    const url = urlBase + (query ? '?q=' + encodeURIComponent(query) : '') + (query ? '&' : '?') + 't=' + Date.now();
    console.log('Searching users with query:', query);
    
    // Show loading indicator
    userTableBody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 15px;">Loading...</td></tr>';
    
    const res = await fetch(url, { cache: 'no-store' });
    if (!res.ok) {
      console.error('Search users HTTP error', res.status);
      userTableBody.innerHTML = '<tr><td colspan="4" class="no-data">Server error</td></tr>';
      return;
    }
    const html = await res.text();
    console.log('Search response length:', html.length);
    if (!html || html.trim().length === 0) {
      userTableBody.innerHTML = '<tr><td colspan="4" class="no-data">No users found</td></tr>';
    } else {
      userTableBody.innerHTML = html;
    }
  } catch (err) {
    console.error('Failed to search users:', err);
    userTableBody.innerHTML = '<tr><td colspan="4" class="no-data">Error searching users</td></tr>';
  }
}

// Search logs with live input - OPTIMIZED with faster network and caching
async function searchLogs() {
  const searchInput = document.getElementById('logsSearchInput');
  const query = searchInput ? searchInput.value.trim() : '';
  const logsTableBody = document.getElementById('logsTableBody');
  
  if (!logsTableBody) return;
  
  // Skip if query hasn't changed
  if (query === lastLogsQuery) return;
  lastLogsQuery = query;

  try {
    const urlBase = 'search_logs.php';
    const url = urlBase + (query ? '?q=' + encodeURIComponent(query) : '') + (query ? '&' : '?') + 't=' + Date.now();
    console.log('Searching logs with query:', query);
    
    // Show loading indicator
    logsTableBody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 15px;">Loading...</td></tr>';
    
    const res = await fetch(url, { cache: 'no-store' });
    if (!res.ok) {
      console.error('Search logs HTTP error', res.status);
      logsTableBody.innerHTML = '<tr><td colspan="6" class="no-data">Server error</td></tr>';
      return;
    }
    const html = await res.text();
    console.log('Search response length:', html.length);
    if (!html || html.trim().length === 0) {
      logsTableBody.innerHTML = '<tr><td colspan="6" class="no-data">No logs found</td></tr>';
    } else {
      logsTableBody.innerHTML = html;
    }
  } catch (err) {
    console.error('Failed to search logs:', err);
    logsTableBody.innerHTML = '<tr><td colspan="6" class="no-data">Error searching logs</td></tr>';
  }
}




