document.addEventListener('DOMContentLoaded', function() {
    // ---------------------------
    // Tab Switching Functionality
    // ---------------------------
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));
            button.classList.add('active');
            const tabId = button.getAttribute('data-tab');
            document.getElementById(`${tabId}-tab`).classList.add('active');
        });
    });

    // ---------------------------
    // Function: Load User Stats
    // ---------------------------
    function loadUserStats() {
        fetch('php/get_user_stats.php')
          .then(response => response.json())
          .then(data => {
              console.log("User Stats:", data);
              if (data.totalViews !== undefined) {
                  document.getElementById('totalViews').textContent = data.totalViews;
              }
              if (data.totalLikes !== undefined) {
                  document.getElementById('totalLikes').textContent = data.totalLikes;
              }
              if (data.totalComments !== undefined) {
                  document.getElementById('totalComments').textContent = data.totalComments;
              }
              if (data.totalFollowers !== undefined) {
                  document.getElementById('totalFollowers').textContent = data.totalFollowers;
              }
          })
          .catch(error => console.error("Error fetching user stats:", error));
    }

    // ---------------------------
    // View Switching Functionality
    // ---------------------------
    const viewButtons = document.querySelectorAll('.view-btn');
    const postsGrid = document.querySelector('.posts-grid');
    viewButtons.forEach(button => {
        button.addEventListener('click', () => {
            viewButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            const viewType = button.getAttribute('data-view');
            if (postsGrid) {
                postsGrid.className = `posts-grid view-${viewType}`;
            }
        });
    });

    // ---------------------------
    // Category and Type Item Handling
    // ---------------------------
    const subcategoryItems = document.querySelectorAll('.subcategory-item');
    subcategoryItems.forEach(item => {
        item.addEventListener('click', function() {
            subcategoryItems.forEach(cat => cat.classList.remove('active'));
            this.classList.add('active');
        });
    });
    const typeItems = document.querySelectorAll('.type-item');
    typeItems.forEach(item => {
        item.addEventListener('click', function() {
            typeItems.forEach(type => type.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // ---------------------------
    // Trending Post Click Handling
    // ---------------------------
    const trendingPosts = document.querySelectorAll('.trending-post');
    trendingPosts.forEach(post => {
        post.addEventListener('click', function() {
            console.log('Trending post clicked:', this.querySelector('h4').textContent);
        });
    });

    // ---------------------------
    // Sidebar Navigation Handling
    // ---------------------------
    const sidebarLinks = document.querySelectorAll('.sidebar-nav a[data-content]');
    const dashboardContentElem = document.querySelector('.dashboard-content');
    const defaultDashboardContent = dashboardContentElem ? dashboardContentElem.innerHTML : '';

    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.sidebar-nav li').forEach(item => item.classList.remove('active'));
            this.parentElement.classList.add('active');
            const contentId = this.getAttribute('data-content');
            const mainContent = document.querySelector('.main-content');

            if (contentId === 'dashboard') {
                mainContent.innerHTML = `
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
                            <div class="user-menu">
                                <button class="user-menu-btn">
                                    <img src="placeholder-profile.png" alt="Profile">
                                    <span>John Doe</span>
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="dashboard-content">
                        ${defaultDashboardContent}
                    </div>
                `;
                loadUserStats();
                loadRecentPosts();
            } else if (contentId === 'my-posts') {
                loadMyPosts();
            } else {
                mainContent.innerHTML = `
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
                            <div class="user-menu">
                                <button class="user-menu-btn">
                                    <img src="placeholder-profile.png" alt="Profile">
                                    <span>John Doe</span>
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="content-section">
                        <h2>${contentId.charAt(0).toUpperCase() + contentId.slice(1)}</h2>
                        <p>Content for ${contentId} will be displayed here.</p>
                    </div>
                `;
            }
        });
    });

    // ---------------------------
    // Function: Load Recent Posts
    // ---------------------------
    function loadRecentPosts() {
        fetch('php/get_recent_posts.php')
            .then(response => response.json())
            .then(posts => {
                const recentPostsContainer = document.getElementById('recentPosts');
                if (!recentPostsContainer) return;
                let html = '';
                if (!posts.length) {
                    html = "<p>No recent posts yet.</p>";
                } else {
                    posts.forEach(post => {
                        const postDate = new Date(post.created_at).toLocaleDateString();
                        html += `
                            <a href="post.html?post_id=${post.id}" class="post-link">
                              <div class="post-card">
                                  <div class="post-image">
                                      <img src="https://via.placeholder.com/300x200" alt="Post thumbnail">
                                      <span class="category-tag">General</span>
                                  </div>
                                  <div class="post-content">
                                      <h3>${post.title}</h3>
                                      <p>${post.content.substring(0, 100)}...</p>
                                      <div class="post-meta">
                                          <div class="post-stats">
                                              <span><i class="fas fa-eye"></i> ${post.view_count}</span>
                                              <span><i class="fas fa-heart"></i> ${post.like_count}</span>
                                              <span><i class="fas fa-comment"></i> ${post.comment_count}</span>
                                          </div>
                                          <span class="post-date">${postDate}</span>
                                      </div>
                                  </div>
                              </div>
                            </a>
                        `;
                    });
                }
                recentPostsContainer.innerHTML = html;
                const viewAllLink = document.getElementById('recentViewAll');
                if (viewAllLink) {
                    viewAllLink.addEventListener('click', function(e) {
                        e.preventDefault();
                        const myPostsLink = document.querySelector('.sidebar-nav a[data-content="my-posts"]');
                        if (myPostsLink) {
                            myPostsLink.click();
                        }
                    });
                }
            })
            .catch(error => console.error("Error fetching recent posts:", error));
    }

    // ---------------------------
    // Function: Load My Posts
    // ---------------------------
    function loadMyPosts() {
        fetch('php/get_all_posts.php')
            .then(response => response.json())
            .then(posts => {
                const mainContent = document.querySelector('.main-content');
                let html = `
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
                            <div class="user-menu">
                                <button class="user-menu-btn">
                                    <img src="placeholder-profile.png" alt="Profile">
                                    <span>John Doe</span>
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="dashboard-content">
                        <div class="content-section">
                            <div class="section-header">
                                <h2>My Posts</h2>
                                <a href="#" id="viewAllLink" class="btn btn-link">View All <i class="fas fa-arrow-right"></i></a>
                            </div>
                            <div class="posts-grid" id="myPostsGrid">
                `;
                if (!posts.length) {
                    html += "<p>No posts yet.</p>";
                } else {
                    posts.forEach(post => {
                        const postDate = new Date(post.created_at).toLocaleDateString();
                        html += `
                            <a href="post.html?post_id=${post.id}" class="post-link">
                              <div class="post-card">
                                  <div class="post-image">
                                      <img src="https://via.placeholder.com/300x200" alt="Post thumbnail">
                                      <span class="category-tag">General</span>
                                  </div>
                                  <div class="post-content">
                                      <h3>${post.title}</h3>
                                      <p>${post.content.substring(0, 100)}...</p>
                                      <div class="post-meta">
                                          <div class="post-stats">
                                              <span><i class="fas fa-eye"></i> ${post.view_count}</span>
                                              <span><i class="fas fa-heart"></i> ${post.like_count}</span>
                                              <span><i class="fas fa-comment"></i> ${post.comment_count}</span>
                                          </div>
                                          <span class="post-date">${postDate}</span>
                                      </div>
                                      <button class="delete-btn" data-post-id="${post.id}">Delete</button>
                                  </div>
                              </div>
                            </a>
                        `;
                    });
                }
                html += `</div></div>`;
                mainContent.innerHTML = html;
                
                document.querySelectorAll('.delete-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        e.preventDefault();
                        const postId = this.getAttribute('data-post-id');
                        if (confirm("Are you sure you want to delete this post?")) {
                            fetch('php/delete_post.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: "post_id=" + encodeURIComponent(postId)
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.success) {
                                    this.closest('.post-card').remove();
                                } else {
                                    alert("Error deleting post: " + result.message);
                                }
                            })
                            .catch(error => console.error("Error deleting post:", error));
                        }
                    });
                });
                
                const viewAllLink = document.getElementById('viewAllLink');
                if (viewAllLink) {
                    viewAllLink.addEventListener('click', function(e) {
                        e.preventDefault();
                        loadMyPosts();
                    });
                }
            })
            .catch(error => console.error("Error fetching posts:", error));
    }

    // ---------------------------
    // Function: Load Search Results by Post Title
    // ---------------------------
    function loadSearchResults(keyword) {
        fetch('php/search_posts.php?keyword=' + encodeURIComponent(keyword))
          .then(response => response.json())
          .then(posts => {
              const mainContent = document.querySelector('.main-content');
              let html = `
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
                          <div class="user-menu">
                              <button class="user-menu-btn">
                                  <img src="placeholder-profile.png" alt="Profile">
                                  <span>John Doe</span>
                                  <i class="fas fa-chevron-down"></i>
                              </button>
                          </div>
                      </div>
                  </div>
                  <div class="dashboard-content">
                      <div class="content-section">
                          <div class="section-header">
                              <h2>Search Results for "${keyword}"</h2>
                          </div>
                          <div class="posts-grid" id="searchResultsGrid">
              `;
              if (!posts.length) {
                  html += "<p>No posts found matching your search.</p>";
              } else {
                  posts.forEach(post => {
                      const postDate = new Date(post.created_at).toLocaleDateString();
                      html += `
                          <a href="post.html?post_id=${post.id}" class="post-link">
                            <div class="post-card">
                                <div class="post-image">
                                    <img src="https://via.placeholder.com/300x200" alt="Post thumbnail">
                                    <span class="category-tag">General</span>
                                </div>
                                <div class="post-content">
                                    <h3>${post.title}</h3>
                                    <p>${post.content.substring(0, 100)}...</p>
                                    <div class="post-meta">
                                        <div class="post-stats">
                                            <span><i class="fas fa-eye"></i> ${post.view_count}</span>
                                            <span><i class="fas fa-heart"></i> ${post.like_count}</span>
                                            <span><i class="fas fa-comment"></i> ${post.comment_count}</span>
                                        </div>
                                        <span class="post-date">${postDate}</span>
                                    </div>
                                </div>
                            </div>
                          </a>
                      `;
                  });
              }
              html += `</div></div></div>`;
              mainContent.innerHTML = html;
              
              // Reattach search listener to the new search input.
              const newSearchInput = document.querySelector('.search-container .search-input');
              if (newSearchInput) {
                  newSearchInput.addEventListener('keyup', function(e) {
                      if (e.key === 'Enter') {
                          const newKeyword = this.value.trim();
                          if(newKeyword) {
                              loadSearchResults(newKeyword);
                          }
                          this.value = '';
                      }
                  });
              }
          })
          .catch(error => console.error("Error fetching search results:", error));
    }

    // ---------------------------
    // Search Functionality for Dashboard (Initial Binding)
    // ---------------------------
    const searchInputs = document.querySelectorAll('.search-input');
    searchInputs.forEach(input => {
        input.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                const keyword = this.value.trim();
                if(keyword) {
                    loadSearchResults(keyword);
                }
                this.value = '';
            }
        });
    });
    
    // ---------------------------
    // On Initial Dashboard Load, Load Recent Posts and User Stats
    // ---------------------------
    if (document.querySelector('.dashboard-content')) {
        loadRecentPosts();
        loadUserStats();
    }

    // ---------------------------
    // Placeholder Profile Image Handling
    // ---------------------------
    const profileImgs = document.querySelectorAll('.profile-image img, .auth-icon img');
    profileImgs.forEach(img => {
        if (!img.src || img.src.includes('placeholder')) {
            img.src = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23cccccc"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>';
        }
    });

    // ---------------------------
    // Category Selection in Discover Page
    // ---------------------------
    const categoryItems = document.querySelectorAll('.category-item');
    if (categoryItems.length > 0) {
        categoryItems.forEach(item => {
            item.addEventListener('click', function() {
                categoryItems.forEach(cat => cat.classList.remove('active'));
                this.classList.add('active');
                const discoverTitle = document.querySelector('.discover-grid h2');
                if (discoverTitle) {
                    discoverTitle.textContent = this.textContent;
                }
            });
        });
    }
    
    // ---------------------------
    // Login/Signup Navigation
    // ---------------------------
    const authButtons = document.querySelectorAll('.auth-buttons a, .auth-link');
    authButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (this.href.includes('login.html') || this.href.includes('signup.html')) {
                e.preventDefault();
                if (this.textContent.includes('Log In')) {
                    window.location.href = 'login.html';
                } else if (this.textContent.includes('Get Started')) {
                    window.location.href = 'signup.html';
                }
            }
        });
    });
    
    // ---------------------------
    // Admin Dashboard Sample Data
    // ---------------------------
    const adminTable = document.querySelector('.users-table tbody');
    if (adminTable) {
        const sampleUsers = [
            { username: 'johnsmith', email: 'john@example.com', date: '2023-01-15' },
            { username: 'sarahparker', email: 'sarah@example.com', date: '2023-02-20' },
            { username: 'michaeljohnson', email: 'michael@example.com', date: '2023-03-05' }
        ];
        sampleUsers.forEach(user => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${user.username}</td>
                <td>${user.email}</td>
                <td>${user.date}</td>
                <td><a href="#" class="delete-btn">Delete</a></td>
                <td></td>
            `;
            adminTable.appendChild(row);
        });
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this user?')) {
                    this.closest('tr').remove();
                }
            });
        });
    }
    
    // ---------------------------
    // Login Form Submission (if present)
    // ---------------------------
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            displayError('usernameError', '');
            displayError('passwordError', '');
            let hasErrors = false;
            if (!username) {
                displayError('usernameError', 'Please enter your username.');
                hasErrors = true;
            }
            if (!password) {
                displayError('passwordError', 'Please enter your password.');
                hasErrors = true;
            }
            if (hasErrors) return;
            if (username === 'testuser' && password === 'password') {
                alert('Login Successful!');
                window.location.href = 'user-dashboard.html';
            } else {
                displayError('passwordError', 'Invalid username or password.');
            }
        });
    }

    // ---------------------------
    // Signup Form Submission (if present)
    // ---------------------------
    const signupForm = document.getElementById('signupForm');
    if (signupForm) {
        signupForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            displayError('nameError', '');
            displayError('emailError', '');
            displayError('usernameError', '');
            displayError('passwordError', '');
            let hasErrors = false;
            if (!name) {
                displayError('nameError', 'Please enter your name.');
                hasErrors = true;
            }
            if (!email) {
                displayError('emailError', 'Please enter your email.');
                hasErrors = true;
            }
            if (!username) {
                displayError('usernameError', 'Please enter a username.');
                hasErrors = true;
            }
            if (!password) {
                displayError('passwordError', 'Please enter a password.');
                hasErrors = true;
            }
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email && !emailRegex.test(email)) {
                displayError('emailError', 'Please enter a valid email address.');
                hasErrors = true;
            }
            if (hasErrors) return;
            alert('Signup Successful!\n\nName: ' + name + '\nEmail: ' + email + '\nUsername: ' + username + '\nPassword: ' + password);
            window.location.href = 'login.html';
        });
    }
    
    // ---------------------------
    // Function to Display Error Messages
    // ---------------------------
    function displayError(elementId, message) {
        const errorElement = document.getElementById(elementId);
        if (errorElement) {
            errorElement.textContent = message;
        }
    }
});
