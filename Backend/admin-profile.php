<?php
session_start();
require_once 'config.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Ensure user is admin
if ($_SESSION['is_admin'] != 1) {
    header("Location: user-dashboard.php");
    exit;
}

// Get admin user data
try {
    $stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ? AND is_admin = 1");
    $stmt->execute([$_SESSION['user_id']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$admin) {
        header("Location: login.html");
        exit;
    }
} catch (PDOException $e) {
    error_log("Error fetching admin data: " . $e->getMessage());
    header("Location: admin-dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Profile - Blogging Platform</title>
  <link rel="stylesheet" href="../frontend/styles/profile.css" />
  <link rel="stylesheet" href="../frontend/styles/common.css" />
</head>
<body>
  <div class="profile-container">
    <div class="back-btn-container">
      <a href="admin-dashboard.php" class="btn back-btn">←</a>
    </div>
    
    <div class="profile-row user-id-row">
      <span id="userId" class="user-id"><?php echo htmlspecialchars($admin['username']); ?></span>
    </div>
    <hr />

    <div class="profile-row">
      <span class="profile-label">Email Address:</span>
      <div class="profile-right">
        <span id="emailDisplay" class="profile-value"><?php echo htmlspecialchars($admin['email']); ?></span>
        <button id="openEmailModalBtn" class="btn btn-primary">Change</button>
      </div>
    </div>
    <hr />

    <div class="profile-row">
      <span class="profile-label">Password:</span>
      <div class="profile-right">
        <span id="passwordDisplay" class="profile-value">********</span>
        <button id="openPasswordModalBtn" class="btn btn-primary">Change</button>
      </div>
    </div>
    <hr />

    <div class="profile-row">
      <div class="profile-right full-width">
        <a href="logout.php" class="btn red-btn" style="text-decoration: none; display: inline-block; width: 100%; text-align: center;">Log out</a>
      </div>
    </div>
  </div>

  <div id="emailModal" class="modal">
    <div class="modal-overlay"></div>
    <div class="modal-content">
      <h2>Update Email Address</h2>
      <form id="emailForm" action="update_admin_email.php" method="POST">
        <input type="email" id="newEmail" name="newEmail" placeholder="Enter new email" required />
        <div class="modal-buttons">
          <button type="button" id="cancelEmailBtn" class="btn">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>

  <div id="passwordModal" class="modal">
    <div class="modal-overlay"></div>
    <div class="modal-content">
      <h2>Update Password</h2>
      <form id="passwordForm" action="update_admin_password.php" method="POST">
        <div class="form-group">
          <label for="oldPassword">Current Password:</label>
          <input type="password" id="oldPassword" name="oldPassword" placeholder="Enter current password" required />
        </div>
        <div class="form-group">
          <label for="newPassword">New Password:</label>
          <input type="password" id="newPassword" name="newPassword" placeholder="Enter new password" required />
        </div>
        <div class="form-group">
          <label for="confirmPassword">Confirm New Password:</label>
          <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm new password" required />
        </div>
        <div class="modal-buttons">
          <button type="button" id="cancelPasswordBtn" class="btn">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Email modal functionality
        const emailModal = document.getElementById('emailModal');
        const openEmailModalBtn = document.getElementById('openEmailModalBtn');
        const cancelEmailBtn = document.getElementById('cancelEmailBtn');
        const emailForm = document.getElementById('emailForm');

        openEmailModalBtn.addEventListener('click', () => {
            emailModal.style.display = 'block';
        });

        cancelEmailBtn.addEventListener('click', () => {
            emailModal.style.display = 'none';
            emailForm.reset();
        });

        // Password modal functionality
        const passwordModal = document.getElementById('passwordModal');
        const openPasswordModalBtn = document.getElementById('openPasswordModalBtn');
        const cancelPasswordBtn = document.getElementById('cancelPasswordBtn');
        const passwordForm = document.getElementById('passwordForm');

        openPasswordModalBtn.addEventListener('click', () => {
            passwordModal.style.display = 'block';
        });

        cancelPasswordBtn.addEventListener('click', () => {
            passwordModal.style.display = 'none';
            passwordForm.reset();
        });

        // Close modals when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-overlay')) {
                emailModal.style.display = 'none';
                passwordModal.style.display = 'none';
                emailForm.reset();
                passwordForm.reset();
            }
        });

        // Password form validation
        passwordForm.addEventListener('submit', function(e) {
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('New passwords do not match!');
            }
        });
    });
  </script>
</body>
</html> 