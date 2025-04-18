<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogging Platform - Landing Page</title>
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/landing.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header class="modern-header">
            <div class="logo">
                <i class="fas fa-pen-fancy"></i>
                <span>Blogging</span>
            </div>
            <nav>
                <ul>
                    <li><a href="discover.php">Discover</a></li>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#about">About</a></li>
                    <?php if ($isLoggedIn): ?>
                        <li><a href="../Backend/user-dashboard.php">Dashboard</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <div class="auth-buttons">
                <?php if ($isLoggedIn): ?>
                    <a href="../Backend/user-dashboard.php" class="btn btn-link">Dashboard</a>
                    <a href="../frontend/user-profile.php" class="btn btn-primary">Profile</a>
                <?php else: ?>
                    <a href="../Backend/login.php" class="btn btn-link">Log In</a>
                    <a href="../Backend/signup.html" class="btn btn-primary">Get Started</a>
                <?php endif; ?>
            </div>
        </header>

        <main>
            <section class="hero-section">
                <div class="hero-content">
                    <h1>Share Your Story With<br><span class="highlight">The World</span></h1>
                    <p class="hero-subtitle">Create, share, and discover amazing stories from writers around the globe.</p>
                    <div class="hero-cta">
                        <?php if ($isLoggedIn): ?>
                            <a href="../Backend/user-dashboard.php" class="btn btn-action">Start Writing <i class="fas fa-arrow-right"></i></a>
                        <?php else: ?>
                            <a href="../Backend/signup.html" class="btn btn-action">Start Writing <i class="fas fa-arrow-right"></i></a>
                        <?php endif; ?>
                        <a href="discover.php" class="btn btn-secondary">Explore Posts</a>
                    </div>
                </div>
                <div class="hero-image">
                    <div class="floating-cards">
                        <div class="card card-1">
                            <i class="fas fa-edit"></i>
                            <span>Easy Writing</span>
                        </div>
                        <div class="card card-2">
                            <i class="fas fa-users"></i>
                            <span>Community</span>
                        </div>
                        <div class="card card-3">
                            <i class="fas fa-chart-line"></i>
                            <span>Analytics</span>
                        </div>
                    </div>
                </div>
            </section>

            <section id="features" class="features-section">
                <h2>Why Choose Us</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <i class="fas fa-feather-alt"></i>
                        <h3>Intuitive Editor</h3>
                        <p>Write and format your stories with our easy-to-use editor</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-globe"></i>
                        <h3>Global Reach</h3>
                        <p>Share your content with readers worldwide</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-bolt"></i>
                        <h3>Fast Performance</h3>
                        <p>Lightning-fast loading and seamless experience</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-mobile-alt"></i>
                        <h3>Mobile Ready</h3>
                        <p>Perfect experience on any device</p>
                    </div>
                </div>
            </section>

            <section class="cta-section">
                <div class="cta-content">
                    <h2>Ready to Start Your Journey?</h2>
                    <p>Join thousands of writers who have already found their voice</p>
                    <?php if ($isLoggedIn): ?>
                        <a href="../Backend/user-dashboard.php" class="btn btn-action">Go to Dashboard</a>
                    <?php else: ?>
                        <a href="../Backend/signup.html" class="btn btn-action">Create Your Account</a>
                    <?php endif; ?>
                </div>
            </section>
        </main>

        <footer class="modern-footer">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>Platform</h4>
                    <ul>
                        <li><a href="#features">Features</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Blogging Platform. All rights reserved.</p>
            </div>
        </footer>
    </div>

</body>
</html>