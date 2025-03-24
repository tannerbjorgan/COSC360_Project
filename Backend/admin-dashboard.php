<?php
session_start();

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Blogging Platform</title>
    <link rel="stylesheet" href="/frontend/styles/common.css">
    <link rel="stylesheet" href="/frontend/styles/dashboard.css">
    <link rel="stylesheet" href="/frontend/styles/admin.css">
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
                <a href="admin-profile.html" class="profile-section">
                    <div class="profile-image">
                        <img src="placeholder-profile.png" alt="Admin Profile">
                    </div>
                    <div class="profile-info">
                        <span class="username">Admin User</span>
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

                    <!-- Logout link -->
                    <a href="logout.php" class="btn btn-secondary">Logout</a>

                    <div class="user-menu">
                        <button class="user-menu-btn">
                            <img src="placeholder-profile.png" alt="Profile">
                            <a href="admin-profile.html" class="username-link">Admin User</a>
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
                            <p>1,234</p>
                            <span class="trend positive">+12% <i class="fas fa-arrow-up"></i></span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Posts</h3>
                            <p>5,678</p>
                            <span class="trend positive">+8% <i class="fas fa-arrow-up"></i></span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="stat-info">
                            <h3>New Users</h3>
                            <p>145</p>
                            <span class="trend positive">+23% <i class="fas fa-arrow-up"></i></span>
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
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/frontend/scripts/script.js"></script>
</body>
</html>
