<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discover - Blogging Platform</title>
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/discover.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header class="modern-header">
            <div class="logo">
                <a href="index.php">
                    <i class="fas fa-pen-fancy"></i>
                    <span>Blogging</span>
                </a>
            </div>
            <nav>
                <ul>
                    <li><a href="discover.php" class="active">Discover</a></li>
                    <li><a href="index.php#features">Features</a></li>
                    <li><a href="index.php#about">About</a></li>
                    <?php if ($isLoggedIn): ?>
                        <li><a href="../Backend/user-dashboard.php">Dashboard</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <div class="auth-buttons">
                <?php if ($isLoggedIn): ?>
                    <a href="../Backend/logout.php" class="btn btn-primary">Logout</a>
                <?php else: ?>
                    <a href="../Backend/login.php" class="btn btn-link">Log In</a>
                    <a href="../Backend/signup.html" class="btn btn-primary">Get Started</a>
                <?php endif; ?>
            </div>
        </header>

        <main class="discover-main">
            <aside class="discover-sidebar">
                <div class="search-container">
                    <input type="text" id="searchInput" placeholder="Search posts..." class="search-input">
                    <i class="fas fa-search search-icon"></i>
                </div>
                
                <div class="sidebar-tabs">
                    <button class="tab-btn active" data-tab="trending">
                        <i class="fas fa-fire"></i>
                        Trending
                    </button>
                    <button class="tab-btn" data-tab="filters">
                        <i class="fas fa-filter"></i>
                        Filters
                    </button>
                </div>

                <div class="tab-content">
                    <div class="tab-pane active" id="trending-tab">
                        <div class="trending-posts" id="trendingPosts">
                            <!-- Trending posts will be loaded dynamically -->
                        </div>
                    </div>

                    <div class="tab-pane" id="filters-tab">
                        <div class="filters-content">
                            <div class="filter-section">
                                <h3>Categories</h3>
                                <div class="filter-options" id="categoryFilters">
                                    <!-- Categories will be loaded dynamically -->
                                </div>
                            </div>

                            <button class="btn btn-primary apply-filters" id="applyFilters">Apply Filters</button>
                        </div>
                    </div>
                </div>

                <div class="sidebar-footer">
                    <?php if (!$isLoggedIn): ?>
                    <a href="../Backend/login.php" class="sidebar-login">
                        <div class="login-content">
                            <i class="fas fa-user-circle"></i>
                            <span>Log in to create and share your stories</span>
                        </div>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </aside>

            <section class="discover-content">
                <div class="discover-header">
                    <h2>Trending Posts</h2>
                    <div class="view-options">
                        <button class="view-btn active" data-view="grid">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button class="view-btn" data-view="list">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>

                <div class="posts-grid" id="postsContainer">
                    <!-- Posts will be loaded dynamically -->
                </div>

                <div class="load-more">
                    <button class="btn btn-secondary" id="loadMoreBtn">Load More Posts</button>
                </div>
            </section>
        </main>
    </div>

    <script>
    let currentOffset = 0;
    const postsPerPage = 10;
    let selectedCategory = null;
    let currentSearch = '';
    const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

    // Function to format the date
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    }

    // Function to create a post card
    function createPostCard(post) {
        const isOwnPost = post.author.id === <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
        return `
            <article class="post-card">
                <div class="post-content">
                    <div class="author-info">
                        <img src="images/placeholder-profile.png" alt="${post.author.name}" class="author-avatar">
                        <div class="author-details">
                            <span class="author-name">${post.author.name}</span>
                            ${!isOwnPost && isLoggedIn ? `
                                <button class="follow-btn" onclick="toggleFollow(event, ${post.author.id})" data-user-id="${post.author.id}">
                                    <i class="fas fa-user-plus"></i> Follow
                                </button>
                            ` : ''}
                        </div>
                    </div>
                    <a href="post.php?id=${post.id}" class="post-link">
                        <h3>${post.title}</h3>
                        <p>${post.content}</p>
                        <div class="post-meta">
                            <span><i class="fas fa-heart"></i> ${post.likes_count}</span>
                            <span><i class="fas fa-comment"></i> ${post.comments_count}</span>
                            <span class="read-time">${post.read_time} min read</span>
                            <span class="post-date">${formatDate(post.created_at)}</span>
                        </div>
                    </a>
                </div>
            </article>
        `;
    }

    // Function to load posts
    function loadPosts() {
        const postsContainer = document.getElementById('postsContainer');
        postsContainer.innerHTML = '<div class="loading">Loading posts...</div>';

        fetch('php/get_all_posts.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.posts.length === 0) {
                        postsContainer.innerHTML = '<div class="no-posts">No posts found</div>';
                    } else {
                        const postsHtml = data.posts.map(post => createPostCard(post)).join('');
                        postsContainer.innerHTML = postsHtml;
                    }
                } else {
                    throw new Error(data.error || 'Failed to load posts');
                }
            })
            .catch(error => {
                console.error('Error loading posts:', error);
                postsContainer.innerHTML = `
                    <div class="error-message">
                        <p>Failed to load posts. Please try again.</p>
                        <button onclick="loadPosts()" class="btn btn-primary">Retry</button>
                    </div>
                `;
            });
    }

    // Function to load categories
    function loadCategories() {
        fetch('php/get_categories.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const categoriesHtml = data.categories.map(category => `
                        <label class="filter-option">
                            <input type="checkbox" name="category" value="${category}">
                            <span class="filter-label">${category}</span>
                        </label>
                    `).join('');
                    document.getElementById('categoryFilters').innerHTML = categoriesHtml;
                }
            })
            .catch(error => console.error('Error loading categories:', error));
    }

    // Event Listeners
    document.getElementById('loadMoreBtn').addEventListener('click', () => {
        currentOffset += postsPerPage;
        loadPosts();
    });

    document.getElementById('searchInput').addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const posts = document.querySelectorAll('.post-card');
        
        posts.forEach(post => {
            const title = post.querySelector('h3').textContent.toLowerCase();
            const content = post.querySelector('p').textContent.toLowerCase();
            const author = post.querySelector('.author span').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || content.includes(searchTerm) || author.includes(searchTerm)) {
                post.closest('.post-link').style.display = '';
            } else {
                post.closest('.post-link').style.display = 'none';
            }
        });
    });

    document.getElementById('applyFilters').addEventListener('click', () => {
        const selectedCategories = Array.from(document.querySelectorAll('input[name="category"]:checked'))
            .map(input => input.value);
        selectedCategory = selectedCategories[0] || null;
        currentOffset = 0;
        loadPosts();
    });

    // Add follow functionality
    async function toggleFollow(event, userId) {
        event.preventDefault();
        event.stopPropagation();
        
        if (!isLoggedIn) {
            window.location.href = '../Backend/login.php';
            return;
        }

        try {
            const response = await fetch('php/toggle_follow.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ user_id: userId })
            });
            const data = await response.json();
            
            if (data.success) {
                const button = event.target.closest('.follow-btn');
                if (data.is_following) {
                    button.innerHTML = '<i class="fas fa-user-check"></i> Following';
                    button.classList.add('following');
                } else {
                    button.innerHTML = '<i class="fas fa-user-plus"></i> Follow';
                    button.classList.remove('following');
                }
            }
        } catch (error) {
            console.error('Error toggling follow:', error);
            alert('Failed to update follow status');
        }
    }

    // Load initial data
    loadCategories();
    loadPosts();
    </script>
</body>
</html> 