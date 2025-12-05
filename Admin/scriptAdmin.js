document.addEventListener('DOMContentLoaded', function() {
  // Sign out button handling
  const signoutBtn = document.getElementById('signoutBtn');
  if (signoutBtn) {
    signoutBtn.addEventListener('click', function() {
      // Navigate to logout.php which destroys the session and redirects
      window.location.href = '../Login/logout.php';
    });
  }
});

const userTab = document.getElementById("userTab");
const logsTab = document.getElementById("logsTab");
const userSection = document.getElementById("userSection");
const logsSection = document.getElementById("logsSection");

userTab.addEventListener("click", () => {
  userTab.classList.add("active");
  logsTab.classList.remove("active");
  userSection.classList.add("active");
  logsSection.classList.remove("active");
});

logsTab.addEventListener("click", () => {
  logsTab.classList.add("active");
  userTab.classList.remove("active");
  logsSection.classList.add("active");
  userSection.classList.remove("active");
});
