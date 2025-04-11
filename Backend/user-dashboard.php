<?php
session_start();


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: login.html");
    exit;
}

require_once 'config.php';

$userId = $_SESSION['user_id'];

try {
    // Get user information
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        session_destroy();
        header("Location: login.html");
        exit;
    }

    // Get user stats
    $statsQuery = "SELECT 
        (SELECT COUNT(*) FROM posts WHERE user_id = ?) as total_posts,
        (SELECT COUNT(*) FROM likes WHERE post_id IN (SELECT id FROM posts WHERE user_id = ?)) as total_likes,
        (SELECT COUNT(*) FROM comments WHERE post_id IN (SELECT id FROM posts WHERE user_id = ?)) as total_comments,
        (SELECT COUNT(*) FROM followers WHERE following_id = ?) as total_followers";

    $stmt = $pdo->prepare($statsQuery);
    $stmt->execute([$userId, $userId, $userId, $userId]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("A database error occurred. Please try again later.");
} catch (Exception $e) {
    die("An error occurred. Please try again later.");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard - Blogging Platform</title>

    <link rel="stylesheet" href="../frontend/styles/common.css">
    <link rel="stylesheet" href="../frontend/styles/dashboard.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">

                <a href="../frontend/index.php" class="logo">

                    <i class="fas fa-pen-fancy"></i>
                    <span>Blogging</span>
                </a>
            </div>


            <button class="btn btn-primary btn-create-post" onclick="window.location.href='../frontend/create-post.html'">

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
                       
                    </ul>
                </div>

                <div class="nav-section">
                    <h4>DISCOVER</h4>
                    <ul>
                        <li>

                            <a href="../frontend/discover.php">


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

                <a href="../frontend/user-profile.php" class="profile-section">
                    <div class="profile-image">
                        <img src="<?php echo htmlspecialchars($user['profile_image'] ? '../frontend/' . $user['profile_image'] : '../frontend/images/placeholder-profile.png'); ?>" alt="Profile">
                    </div>
                    <div class="profile-info">
                        <span class="username"><?php echo htmlspecialchars($user['username']); ?></span>

                        <span class="role">Content Creator</span>
                    </div>
                </a>
            </div>
        </div>

        <div class="main-content">

            <div class="top-bar">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" placeholder="Search posts, topics, or users..." class="search-input" id="searchInput">
                </div>
                <div class="top-bar-actions">
                    <a href="../frontend/create-post.html" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        <span>Create Post</span>
                    </a>
                    <a href="../frontend/user-profile.php" class="btn btn-link">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </a>
                </div>
            </div>

            <div class="dashboard-content" id="dashboardContent">

                <div class="overview-section">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">

                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Total Posts</h3>
                                <p id="totalPosts"><?php echo $stats['total_posts']; ?></p>

                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Total Likes</h3>

                                <p id="totalLikes"><?php echo $stats['total_likes']; ?></p>

                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-comment"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Comments</h3>

                                <p id="totalComments"><?php echo $stats['total_comments']; ?></p>

                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Followers</h3>

                                <p id="totalFollowers"><?php echo $stats['total_followers']; ?></p>

                            </div>
                        </div>
                    </div>
                </div>


                <div class="content-section">
                    <div class="section-header">
                        <h2>Recent Posts</h2>
                        <a href="#" class="btn btn-link" data-content="my-posts">View All <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="posts-grid" id="recentPosts">
                        <!-- Posts will be loaded dynamically -->
                    </div>
                </div>

                <div class="content-section">
                    <div class="section-header">
                        <h2>Following</h2>
                        <a href="#" class="btn btn-link" data-content="following">View All <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="following-grid" id="followingUsers">
                        <!-- Following users will be loaded dynamically -->
                    </div>
                </div>

                <div class="content-section">
                    <div class="section-header">
                        <h2>Recent Followers</h2>
                    </div>
                    <div class="followers-grid" id="recentFollowers">
                        <!-- Recent followers will be loaded dynamically -->
                    </div>
                </div>

            </div>
        </div>
    </div>


    <script>
        const initialDashboardContent = document.getElementById('dashboardContent').innerHTML;
    </script>
    <script src="../frontend/scripts/dashboard.js"></script>

</body>
</html>
