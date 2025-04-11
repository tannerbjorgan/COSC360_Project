<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);

if (!isset($_GET['id'])) {
    header('Location: discover.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Blog Post - Blogging Platform</title>
    <link rel="stylesheet" href="styles/common.css" />
    <link rel="stylesheet" href="styles/post.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="post-container">
        <div class="top-bar">
            <button class="back-btn" id="backBtn">←</button>
            <h1 class="page-label">Blog Post</h1>
            <?php if ($isLoggedIn): ?>
                <div class="post-actions">
                    <button class="share-btn"><i class="fas fa-share"></i></button>
                    <button class="bookmark-btn"><i class="fas fa-bookmark"></i></button>
                </div>
            <?php endif; ?>
        </div>

        <div id="postContent">
            <!-- Post content will be loaded here -->
            <div class="loading">Loading...</div>
        </div>

        <hr class="divider" />

        <div class="comments-section" id="commentsSection">
            <h3>Comments</h3>
            <?php if ($isLoggedIn): ?>
                <div class="new-comment">
                    <textarea id="commentText" placeholder="Leave a comment..."></textarea>
                    <button class="comment-btn" id="submitComment">Post Comment</button>
                </div>
            <?php else: ?>
                <div class="login-prompt">
                    <p>Please <a href="../Backend/login.html">log in</a> to leave a comment.</p>
                </div>
            <?php endif; ?>
            
            <div id="commentsList">
                <!-- Comments will be loaded here -->
            </div>
        </div>
    </div>

    <script>
    const postId = <?php echo (int)$_GET['id']; ?>;
    const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;

    // Helper function to format date strings.
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Load post data from the backend.
    async function loadPost() {
        try {
            const response = await fetch(`php/get_post.php?id=${postId}`);
            const data = await response.json();
            
            if (data.success) {
                const post = data.post;
                document.title = `${post.title} - Blogging Platform`;
                
                const postHtml = `
                    <h2 class="post-title">${post.title}</h2>
                    
                    <div class="meta-row">
                        <div class="user-info">
                            <img src="${post.author.profile_image ? post.author.profile_image : 'images/placeholder-profile.png'}" alt="${post.author.name}" class="author-avatar"style="width:75px; height:75px; object-fit:cover;">
                            <div class="author-details">
                                <span class="author-name">${post.author.name}</span>
                                <span class="post-date">${formatDate(post.created_at)}</span>
                            </div>
                        </div>

                        <div class="post-stats">
                            <button class="like-btn ${post.user_liked ? 'liked' : ''}" onclick="toggleLike()">
                                <i class="fas fa-heart"></i>
                                <span id="likeCount">${post.likes_count}</span>
                            </button>
                            <span class="comments-count">
                                <i class="fas fa-comment"></i>
                                <span id="commentCount">${post.comments_count}</span>
                            </span>
                        </div>
                    </div>

                    <div class="post-content">
                        ${post.content}
                    </div>
                `;
                
                document.getElementById('postContent').innerHTML = postHtml;
                
                const commentsHtml = post.comments.map(comment => `
                    <div class="comment">
                        <div class="comment-header">
                            <img src="${comment.author.profile_image ? comment.author.profile_image : 'images/placeholder-profile.png'}" alt="${comment.author.name}" class="comment-avatar"style="width:30px; height:30px; object-fit:cover;">
                            <div class="comment-meta">
                                <span class="comment-author">${comment.author.name}</span>
                                <span class="comment-time">${formatDate(comment.created_at)}</span>
                            </div>
                        </div>
                        <p class="comment-text">${escapeHtml(comment.content)}</p>
                    </div>
                `).join('');
                
                document.getElementById('commentsList').innerHTML = commentsHtml || '<p>No comments yet.</p>';
            } else {
                throw new Error(data.error || 'Failed to load post');
            }
        } catch (error) {
            console.error('Error loading post:', error);
            document.getElementById('postContent').innerHTML = `
                <div class="error-message">
                    <p>Failed to load post. Please try again later.</p>
                    <button onclick="loadPost()">Retry</button>
                </div>
            `;
        }
    }

    // Toggle like functionality.
    async function toggleLike() {
        if (!isLoggedIn) {
            window.location.href = '../Backend/login.html';
            return;
        }

        try {
            const response = await fetch('php/toggle_like.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ post_id: postId })
            });
            const data = await response.json();
            
            if (data.success) {
                const likeBtn = document.querySelector('.like-btn');
                const likeCount = document.getElementById('likeCount');
                likeBtn.classList.toggle('liked');
                likeCount.textContent = data.likes_count;
            }
        } catch (error) {
            console.error('Error toggling like:', error);
        }
    }

    // Submit new comment functionality.
    document.getElementById('submitComment')?.addEventListener('click', async () => {
        const commentText = document.getElementById('commentText').value.trim();
        const submitButton = document.getElementById('submitComment');
        const commentTextarea = document.getElementById('commentText');

        if (!commentText) {
            alert('Please enter a comment before submitting.');
            return;
        }

        try {
            submitButton.disabled = true;
            submitButton.textContent = 'Posting...';

            const response = await fetch('php/add_comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    post_id: postId,
                    content: commentText
                })
            });
            const data = await response.json();
            
            if (data.success) {
                commentTextarea.value = '';
                await loadPost();  // Reload the post and comments.
                
                // Optionally update comment count.
                const commentCount = document.getElementById('commentCount');
                commentCount.textContent = parseInt(commentCount.textContent) + 1;
                
                // Scroll to the new comment.
                const commentsList = document.getElementById('commentsList');
                commentsList.lastElementChild?.scrollIntoView({ behavior: 'smooth' });
            } else {
                throw new Error(data.error || 'Failed to add comment');
            }
        } catch (error) {
            console.error('Error adding comment:', error);
            alert(error.message || 'Failed to add comment. Please try again.');
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Post Comment';
        }
    });

    // Back button handler.
    document.getElementById('backBtn').addEventListener('click', () => {
        window.history.back();
    });

    // Load the post when the page loads.
    loadPost();

    // Helper function to escape HTML.
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
    </script>
</body>
</html>
