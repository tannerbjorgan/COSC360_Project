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

// Get statistics from database
try {
    // Total users count
    $stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
    $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

    // Total posts count
    $stmt = $pdo->query("SELECT COUNT(*) as total_posts FROM posts");
    $totalPosts = $stmt->fetch(PDO::FETCH_ASSOC)['total_posts'];

    // New users in last 30 days
    $stmt = $pdo->query("SELECT COUNT(*) as new_users FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $newUsers = $stmt->fetch(PDO::FETCH_ASSOC)['new_users'];

    // Get all users for the table
    $stmt = $pdo->query("SELECT id, username, email, created_at, is_admin FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    $totalUsers = 0;
    $totalPosts = 0;
    $newUsers = 0;
    $users = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Blogging Platform</title>

    <link rel="stylesheet" href="../frontend/styles/common.css">
    <link rel="stylesheet" href="../frontend/styles/dashboard.css">
    <link rel="stylesheet" href="../frontend/styles/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <a href="index.html" class="logo">
                    <i class="fas fa-pen-fancy"></i>
                    <span>Admin</span>
                </a>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <h4>MANAGEMENT</h4>
                    <ul>
                        <li class="active">
                            <a href="#" data-content="accounts">
                                <i class="fas fa-users"></i>
                                <span>User Accounts</span>
                            </a>
                        </li>
                        
                    </ul>
                </div>
            </nav>

            <div class="sidebar-footer">
                <a href="admin-profile.php" class="profile-section">
                    <div class="profile-image">
                        <img src="placeholder-profile.png" alt="Admin Profile">
                    </div>
                    <div class="profile-info">
                        <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <span class="role">Administrator</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="top-bar">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" placeholder="Search users..." class="search-input">
                </div>
                <div class="top-bar-actions">
                    <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>

                    <a href="logout.php" class="btn btn-secondary">Logout</a>

                    <div class="user-menu">
                        <button class="user-menu-btn">
                            <img src="placeholder-profile.png" alt="Profile">
                            <a href="admin-profile.html" class="username-link"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Stats Overview -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Users</h3>
                            <p><?php echo number_format($totalUsers); ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Posts</h3>
                            <p><?php echo number_format($totalPosts); ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="stat-info">
                            <h3>New Users (30 days)</h3>
                            <p><?php echo number_format($newUsers); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="content-section">
                    <div class="section-header">
                        <h2>User Management</h2>
                        <button class="btn btn-outline">
                            <i class="fas fa-download"></i>
                            Export Data
                        </button>
                    </div>
                    <div class="table-container">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Join Date</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                                    <td><?php echo $user['is_admin'] ? 'Admin' : 'User'; ?></td>
                                    <td>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <button class="btn btn-danger btn-sm delete-user" data-userid="<?php echo $user['id']; ?>">
                                            Delete
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle user deletion
        const deleteButtons = document.querySelectorAll('.delete-user');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-userid');
                if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                    // Send delete request to server
                    fetch(`delete_user.php?id=${userId}`, {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the row from the table
                            this.closest('tr').remove();
                        } else {
                            alert('Failed to delete user: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while deleting the user.');
                    });
                }
            });
        });

        // Handle search functionality
        const searchInput = document.querySelector('.search-input');
        const userRows = document.querySelectorAll('.users-table tbody tr');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            userRows.forEach(row => {
                const username = row.querySelector('td:first-child').textContent.toLowerCase();
                const email = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                
                if (username.includes(searchTerm) || email.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
    </script>
</body>
</html>
