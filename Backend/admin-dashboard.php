<?php
session_start();

// Check if the user is logged in and is an admin.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../frontend/index.php");
    exit;
}

require_once('config.php'); // Sets up $pdo as a PDO connection

try {
    // Query total number of users.
    $stmt = $pdo->query("SELECT COUNT(*) AS total_users FROM users");
    $totalUsersData = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalUsers = $totalUsersData ? $totalUsersData['total_users'] : 0;

    // Query total number of posts.
    $stmt = $pdo->query("SELECT COUNT(*) AS total_posts FROM posts");
    $totalPostsData = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalPosts = $totalPostsData ? $totalPostsData['total_posts'] : 0;

    // Query current admin details.
    $stmt = $pdo->prepare("SELECT name, username, profile_image FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $adminData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Query user management list (all users)
    $stmt = $pdo->query("SELECT id, username, email, created_at, is_admin FROM users ORDER BY created_at DESC");
    $userList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("DB Error in admin dashboard: " . $e->getMessage());
    // Set defaults on error.
    $totalUsers = 0;
    $totalPosts = 0;
    $adminData = ['name' => 'Admin User', 'username' => 'admin', 'profile_image' => 'images/placeholder-profile.png'];
    $userList = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Blogging Platform</title>
    <!-- Adjust the paths as needed for your project structure -->
    <link rel="stylesheet" href="../frontend/styles/common.css">
    <link rel="stylesheet" href="../frontend/styles/dashboard.css">
    <link rel="stylesheet" href="../frontend/styles/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Basic inline styles in case your external CSS needs quick adjustments */
        .stat-card p {
            font-size: 1.5em;
            margin: 0;
        }
        .users-table {
            width: 100%;
            border-collapse: collapse;
        }
        .users-table th, .users-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <a href="../frontend/index.php" class="logo">
                    <i class="fas fa-pen-fancy"></i>
                    <span>Admin</span>
                </a>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <ul>
                        <li class="active">
                            <a href="#" id="userAccountsTab" data-content="accounts">
                                <i class="fas fa-users"></i>
                                <span>User Accounts</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" data-content="analytics">
                                <i class="fas fa-chart-bar"></i>
                                <span>Analytics</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" data-content="reports">
                                <i class="fas fa-flag"></i>
                                <span>Reports</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="sidebar-footer">
                <a href="../frontend/admin-profile.php" class="profile-section">
                    <div class="profile-image">
                        <img src="<?php echo htmlspecialchars(!empty($adminData['profile_image']) ? $adminData['profile_image'] : 'images/placeholder-profile.png'); ?>" alt="Admin Profile">
                    </div>
                    <div class="profile-info">
                        <span class="username"><?php echo htmlspecialchars($adminData['name']); ?></span>
                        <span class="role">Administrator</span>
                    </div>
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Stats Overview -->
            <div class="overview-section">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Users</h3>
                            <p id="totalUsers"><?php echo number_format($totalUsers); ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Posts</h3>
                            <p id="totalPosts"><?php echo number_format($totalPosts); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Section: User Management List -->
            <div class="content-section" id="accountsSection">
                <div class="section-header">
                    <h2>User Management</h2>
                </div>
                <div class="table-container">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Join Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($userList)): ?>
                                <?php foreach ($userList as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                                        <td><?php echo $user['is_admin'] ? 'Admin' : 'User'; ?></td>
                                        <td>
                                            <button class="delete-btn" onclick="deleteUser(<?php echo $user['id']; ?>)">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">No user data available.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Delete user with given userId.
        async function deleteUser(userId) {
            if (!confirm("Are you sure you want to delete this user? This action cannot be undone.")) {
                return;
            }
            try {
                const response = await fetch('../frontend/php/delete_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ user_id: userId })
                });
                const data = await response.json();
                if (data.success) {
                    // Remove the corresponding row from the table.
                    const row = document.getElementById('userRow-' + userId);
                    if (row) {
                        row.remove();
                    }
                    alert("User deleted successfully.");
                } else {
                    throw new Error(data.error || "Failed to delete user.");
                }
            } catch (error) {
                console.error("Error deleting user:", error);
                alert(error.message || "Failed to delete user. Please try again.");
            }
        }
        
    </script>
</body>
</html>
