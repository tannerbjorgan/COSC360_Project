document.addEventListener('DOMContentLoaded', function() {
    // Load initial data
    loadUserStats();
    loadRecentPosts();
    loadFollowing();
    loadAnalytics();

    // Store the initial dashboard content
    const dashboardContent = document.getElementById('dashboardContent');
    const initialContent = dashboardContent.innerHTML;

    // Tab Switching Functionality
    const sidebarLinks = document.querySelectorAll('.sidebar-nav a[data-content]');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all links
            document.querySelectorAll('.sidebar-nav li').forEach(item => item.classList.remove('active'));
            this.parentElement.classList.add('active');
            
            const contentId = this.getAttribute('data-content');
            
            // Handle different content sections
            switch(contentId) {
                case 'dashboard':
                    dashboardContent.innerHTML = initialContent;
                    // Reload dashboard data
                    loadUserStats();
                    loadRecentPosts();
                    loadFollowing();
                    loadAnalytics();
                    break;
                case 'my-posts':
                    loadMyPosts();
                    break;
                case 'analytics':
                    loadAnalytics();
                    break;
                case 'bookmarks':
                    loadBookmarks();
                    break;
                case 'following':
                    loadFollowing();
                    break;
            }
        });
    });

    // User Menu Dropdown
    const userMenuBtn = document.querySelector('.user-menu-btn');
    const userDropdown = document.querySelector('.user-dropdown');
    if (userMenuBtn && userDropdown) {
        userMenuBtn.addEventListener('click', function() {
            userDropdown.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.remove('active');
            }
        });
    }

    // Search Functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                const keyword = this.value.trim();
                if (keyword) {
                    loadSearchResults(keyword);
                }
                this.value = '';
            }
        });
    }

    // Add event listener for the Following tab
    document.querySelector('a[data-content="following"]').addEventListener('click', function(e) {
        e.preventDefault();
        const dashboardContent = document.getElementById('dashboardContent');
        dashboardContent.innerHTML = `
            <div class="content-section">
                <div class="section-header">
                    <h2>People You Follow</h2>
                </div>
                <div class="following-grid" id="followingUsers">
                    <!-- Following users will be loaded dynamically -->
                </div>
            </div>
        `;
        loadFollowing();
    });
});

// Function to load user stats
function loadUserStats() {
    fetch('../frontend/php/get_user_stats.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('totalPosts').textContent = data.stats.total_posts;
                document.getElementById('totalLikes').textContent = data.stats.total_likes;
                document.getElementById('totalComments').textContent = data.stats.total_comments;
                document.getElementById('totalFollowers').textContent = data.stats.total_followers;
            }
        })
        .catch(error => console.error('Error loading user stats:', error));
}

// Function to load recent posts
function loadRecentPosts() {
    fetch('../frontend/php/get_recent_posts.php')
        .then(response => response.json())
        .then(data => {
            const recentPostsContainer = document.getElementById('recentPosts');
            if (!recentPostsContainer) return;

            if (data.success) {
                const postsHtml = data.posts.map(post => createPostCard(post)).join('');
                recentPostsContainer.innerHTML = postsHtml || '<p>No recent posts yet.</p>';
            }
        })
        .catch(error => console.error('Error loading recent posts:', error));
}

// Function to load my posts
function loadMyPosts() {
    fetch('../frontend/php/get_my_posts.php')
        .then(response => response.json())
        .then(data => {
            const content = `
                <div class="content-section">
                    <div class="section-header">
                        <h2>My Posts</h2>
                        <button class="btn btn-primary" onclick="window.location.href='../frontend/create-post.html'">
                            <i class="fas fa-plus"></i> Create New Post
                        </button>
                    </div>
                    <div class="posts-grid">
                        ${data.success ? data.posts.map(post => createPostCard(post)).join('') : '<p>No posts found.</p>'}
                    </div>
                </div>
            `;
            document.getElementById('dashboardContent').innerHTML = content;
        })
        .catch(error => {
            console.error('Error loading posts:', error);
            document.getElementById('dashboardContent').innerHTML = `
                <div class="content-section">
                    <div class="error-message">
                        <p>Failed to load posts. Please try again later.</p>
                    </div>
                </div>
            `;
        });
}

// Function to load analytics
async function loadAnalytics() {
    try {
        const response = await fetch('../frontend/php/get_user_analytics.php');
        const data = await response.json();

        if (data.success) {
            // Update stats
            document.getElementById('totalPosts').textContent = data.analytics.posts_count;
            document.getElementById('totalLikes').textContent = data.analytics.likes_count;
            document.getElementById('totalFollowers').textContent = data.analytics.followers_count;

            // Display recent followers
            const followersContainer = document.getElementById('recentFollowers');
            followersContainer.innerHTML = '';

            if (data.analytics.recent_followers.length === 0) {
                followersContainer.innerHTML = '<p class="no-content">No followers yet.</p>';
                return;
            }

            data.analytics.recent_followers.forEach(user => {
                const userCard = document.createElement('div');
                userCard.className = 'user-card';
                userCard.innerHTML = `
                    <div class="profile-image">
                        <img src="${user.profile_image || '../frontend/images/placeholder-profile.png'}" alt="${user.name}">
                    </div>
                    <div class="user-name">${user.name}</div>
                    <div class="username">@${user.username}</div>
                    <button class="follow-btn" onclick="toggleFollow(${user.id}, this)">
                        Follow Back
                    </button>
                `;
                followersContainer.appendChild(userCard);
            });
        } else {
            console.error('Failed to load analytics:', data.error);
        }
    } catch (error) {
        console.error('Error loading analytics:', error);
    }
}

// Function to load bookmarks
function loadBookmarks() {
    dashboardContent.innerHTML = `
        <div class="content-section">
            <h2>Bookmarks</h2>
            <p>Bookmarks feature coming soon...</p>
        </div>
    `;
}

// Function to load following
async function loadFollowing() {
    try {
        const response = await fetch('../frontend/php/get_following.php');
        const data = await response.json();

        if (data.success) {
            const followingContainer = document.getElementById('followingUsers');
            followingContainer.innerHTML = '';

            if (data.following.length === 0) {
                followingContainer.innerHTML = '<p class="no-content">You are not following anyone yet.</p>';
                return;
            }

            data.following.forEach(user => {
                const userCard = document.createElement('div');
                userCard.className = 'user-card';
                userCard.innerHTML = `
                    <div class="profile-image">
                        <img src="${user.profile_image || '../frontend/images/placeholder-profile.png'}" alt="${user.name}">
                    </div>
                    <div class="user-name">${user.name}</div>
                    <div class="username">@${user.username}</div>
                    <button class="follow-btn following" onclick="toggleFollow(${user.id}, this)">
                        Following
                    </button>
                `;
                followingContainer.appendChild(userCard);
            });
        } else {
            console.error('Failed to load following users:', data.error);
        }
    } catch (error) {
        console.error('Error loading following users:', error);
    }
}

// Function to create a post card
function createPostCard(post) {
    return `
        <div class="post-card" data-post-id="${post.id}">
            <div class="post-content" onclick="window.location.href='../frontend/post.php?id=${post.id}'">
                <h3>${escapeHtml(post.title)}</h3>
                <p>${escapeHtml(post.content)}</p>
                <div class="post-meta">
                    <span>Posted on: ${new Date(post.created_at).toLocaleDateString()}</span>
                    <span><i class="fas fa-heart"></i> ${post.likes_count || 0}</span>
                    <span><i class="fas fa-comment"></i> ${post.comments_count || 0}</span>
                </div>
                <div class="post-actions" onclick="event.stopPropagation()">
                    <button class="btn btn-link" onclick="editPost(${post.id})"><i class="fas fa-edit"></i> Edit</button>
                    <button class="btn btn-link text-danger" onclick="deletePost(${post.id})"><i class="fas fa-trash"></i> Delete</button>
                </div>
            </div>
        </div>
    `;
}

// Function to load search results
function loadSearchResults(keyword) {
    fetch(`../frontend/php/search_posts.php?keyword=${encodeURIComponent(keyword)}`)
        .then(response => response.json())
        .then(data => {
            const content = `
                <div class="content-section">
                    <div class="section-header">
                        <h2>Search Results for "${escapeHtml(keyword)}"</h2>
                    </div>
                    <div class="posts-grid">
                        ${data.success && data.posts.length > 0 ? 
                            data.posts.map(post => createPostCard(post)).join('') : 
                            '<p>No posts found.</p>'}
                    </div>
                </div>
            `;
            document.getElementById('dashboardContent').innerHTML = content;
        })
        .catch(error => {
            console.error('Error searching posts:', error);
            showNotification('Failed to search posts', 'error');
        });
}

// Function to edit post
function editPost(postId) {
    window.location.href = `../frontend/edit-post.php?id=${postId}`;
}

// Function to delete post
function deletePost(postId) {
    if (confirm('Are you sure you want to delete this post?')) {
        fetch('../frontend/php/delete_post.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ post_id: postId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const postCard = document.querySelector(`.post-card[data-post-id="${postId}"]`);
                if (postCard) {
                    postCard.remove();
                }
                showNotification('Post deleted successfully', 'success');
            } else {
                showNotification(data.error || 'Failed to delete post', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while deleting the post', 'error');
        });
    }
}

// Function to toggle follow status
async function toggleFollow(userId, button) {
    try {
        const response = await fetch('../frontend/php/toggle_follow.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ user_id: userId })
        });

        const data = await response.json();

        if (data.success) {
            const isFollowing = button.classList.contains('following');
            button.classList.toggle('following');
            button.textContent = isFollowing ? 'Follow' : 'Following';

            // Update followers count
            loadAnalytics();
        } else {
            console.error('Failed to toggle follow:', data.error);
        }
    } catch (error) {
        console.error('Error toggling follow:', error);
    }
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}
