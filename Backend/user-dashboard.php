<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard - Blogging Platform</title>
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <a href="index.html" class="logo">
                    <i class="fas fa-pen-fancy"></i>
                    <span>Blogging</span>
                </a>
            </div>

            <button class="btn btn-primary btn-create-post" onclick="window.location.href='create-post.html'">
                <i class="fas fa-plus"></i>
                <span>Create New Post</span>
            </button>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <ul>
                        <li class="active">
                            <a href="#" data-content="dashboard">
                                <i class="fas fa-home"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" data-content="my-posts">
                                <i class="fas fa-file-alt"></i>
                                <span>My Posts</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" data-content="drafts">
                                <i class="fas fa-edit"></i>
                                <span>Drafts</span>
                                <span class="badge">3</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" data-content="analytics">
                                <i class="fas fa-chart-line"></i>
                                <span>Analytics</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="nav-section">
                    <h4>DISCOVER</h4>
                    <ul>
                        <li>
                            <a href="#" data-content="explore">
                                <i class="fas fa-compass"></i>
                                <span>Explore</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" data-content="bookmarks">
                                <i class="fas fa-bookmark"></i>
                                <span>Bookmarks</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" data-content="following">
                                <i class="fas fa-users"></i>
                                <span>Following</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="sidebar-footer">
                <a href="user-profile.html" class="profile-section">
                    <div class="profile-image">
                        <img src="placeholder-profile.png" alt="Profile">
                    </div>
                    <div class="profile-info">
                        <span class="username">John Doe</span>
                        <span class="role">Content Creator</span>
                    </div>
                </a>
            </div>
        </div>

        <div class="main-content">

            <div class="top-bar">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" placeholder="Search posts, topics, or users..." class="search-input">
                </div>
                <div class="top-bar-actions">
                    <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">5</span>
                    </button>
                    
                    <a href="logout.php" class="btn btn-secondary">Logout</a>

                    <div class="user-menu">
                        <button class="user-menu-btn">
                            <img src="placeholder-profile.png" alt="Profile">
                            <a href="user-profile.html" class="username-link">John Doe</a>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="dashboard-content">


                <div class="overview-section">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Total Views</h3>
                                <p id="totalViews">0</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Total Likes</h3>
                                <p id="totalLikes">0</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-comment"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Comments</h3>
                                <p id="totalComments">0</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Followers</h3>
                                <p id="totalFollowers">0</p>
                            </div>
                        </div>                        
                    </div>
                </div>
                <div class="content-section">
                    <div class="section-header">
                        <h2>Recent Posts</h2>
                        <a href="#" id="recentViewAll" class="btn btn-link">View All <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="posts-grid" id="recentPosts">
                        <!-- Recent posts will be injected here via JavaScript -->
                    </div>
                </div>

                <div class="content-grid">
                    <div class="content-section">
                        <div class="section-header">
                            <h2>Recent Activity</h2>
                            <a href="#" class="btn btn-link">View All</a>
                        </div>
                        <div class="activity-list">
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <div class="activity-content">
                                    <p><strong>Sarah Parker</strong> liked your post "Getting Started with Web Development"</p>
                                    <span class="activity-time">2 hours ago</span>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-comment"></i>
                                </div>
                                <div class="activity-content">
                                    <p><strong>Mike Chen</strong> commented on your post "UI Design Trends"</p>
                                    <span class="activity-time">5 hours ago</span>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <div class="activity-content">
                                    <p><strong>Emma Wilson</strong> started following you</p>
                                    <span class="activity-time">1 day ago</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="content-section">
                        <div class="section-header">
                            <h2>Top Followers</h2>
                            <a href="#" class="btn btn-link">View All</a>
                        </div>
                        <div class="followers-list">
                            <div class="follower-item">
                                <img src="https://via.placeholder.com/40" alt="Follower" class="follower-avatar">
                                <div class="follower-info">
                                    <h4>Sarah Parker</h4>
                                    <span>UX Designer</span>
                                </div>
                                <button class="btn btn-outline">Following</button>
                            </div>
                            <div class="follower-item">
                                <img src="https://via.placeholder.com/40" alt="Follower" class="follower-avatar">
                                <div class="follower-info">
                                    <h4>Mike Chen</h4>
                                    <span>Developer</span>
                                </div>
                                <button class="btn btn-outline">Following</button>
                            </div>
                            <div class="follower-item">
                                <img src="https://via.placeholder.com/40" alt="Follower" class="follower-avatar">
                                <div class="follower-info">
                                    <h4>Emma Wilson</h4>
                                    <span>Content Creator</span>
                                </div>
                                <button class="btn btn-outline">Following</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script src="scripts/dashboard.js"></script>

</body>
</html>
