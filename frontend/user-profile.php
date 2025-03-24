<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Backend/login.html");
    exit;
}

require_once '../Backend/config.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        session_destroy();
        header("Location: ../Backend/login.html");
        exit;
    }

    // Get followers count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM followers WHERE following_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $followersCount = $stmt->fetchColumn();

} catch (PDOException $e) {
    die("A database error occurred. Please try again later.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User Profile - Blogging Platform</title>
  <link rel="stylesheet" href="styles/common.css" />
  <link rel="stylesheet" href="styles/profile.css" />
</head>
<body>
  <div class="profile-container">
    <div class="back-btn-container">
      <button id="backBtn" class="btn back-btn">←</button>
    </div>

    <div class="profile-row user-id-row">
      <div class="profile-image-container">
        <img id="profileImage" src="<?php echo htmlspecialchars($user['profile_image'] ? $user['profile_image'] : 'images/placeholder-profile.png'); ?>" alt="Profile Image">
        <button id="changeImageBtn" class="btn btn-primary">Change Image</button>
      </div>
      <span id="userId" class="user-id"><?php echo htmlspecialchars($user['username']); ?></span>
    </div>
    <hr />

    <div class="profile-row">
      <span class="profile-label">Email Address:</span>
      <div class="profile-right">
        <span id="emailDisplay" class="profile-value"><?php echo htmlspecialchars($user['email']); ?></span>
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
      <span class="profile-label">Followers:</span>
      <div class="profile-right">
        <span id="followersCount" class="profile-value"><?php echo $followersCount; ?></span>
      </div>
    </div>
    <hr />

    <div class="profile-row">
      <div class="profile-right full-width">
        <button id="logoutBtn" class="btn red-btn">Log out</button>
      </div>
    </div>
    <hr />

    <div class="profile-row">
      <div class="profile-right full-width">
        <button id="deleteBtn" class="btn red-btn">Delete Account</button>
      </div>
    </div>
  </div>

  <div id="imageModal" class="modal">
    <div class="modal-overlay"></div>
    <div class="modal-content">
      <h2>Update Profile Image</h2>
      <form id="imageUploadForm" enctype="multipart/form-data">
        <div class="form-group">
          <label for="newProfileImage">Choose a new profile image</label>
          <input type="file" id="newProfileImage" name="profile_image" accept="image/*" required>
          <div class="image-preview">
            <img id="imagePreview" src="#" alt="Preview" style="display: none; max-width: 200px;">
          </div>
        </div>
        <div class="modal-buttons">
          <button type="button" id="cancelImageBtn" class="btn">Cancel</button>
          <button type="submit" class="btn btn-primary">Upload</button>
        </div>
      </form>
    </div>
  </div>

  <div id="emailModal" class="modal">
    <div class="modal-overlay"></div>
    <div class="modal-content">
      <h2>Update Email Address</h2>
      <input type="email" id="newEmail" placeholder="Enter new email" />
      <div class="modal-buttons">
        <button id="cancelEmailBtn" class="btn">Cancel</button>
        <button id="saveEmailBtn" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>

  <div id="passwordModal" class="modal">
    <div class="modal-overlay"></div>
    <div class="modal-content">
      <h2>Update Password</h2>
      <label for="oldPassword">Old Password:</label>
      <input type="password" id="oldPassword" placeholder="Enter old password" />
      <label for="newPassword">New Password:</label>
      <input type="password" id="newPassword" placeholder="Enter new password" />
      <div class="modal-buttons">
        <button id="cancelPasswordBtn" class="btn">Cancel</button>
        <button id="savePasswordBtn" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>

  <script src="scripts/user-profile.js"></script>
</body>
</html> 