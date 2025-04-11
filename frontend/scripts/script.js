document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all buttons and panes
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));

            // Add active class to clicked button and corresponding pane
            button.classList.add('active');
            const tabId = button.getAttribute('data-tab');
            document.getElementById(`${tabId}-tab`).classList.add('active');
        });
    });

    // View switching functionality
    const viewButtons = document.querySelectorAll('.view-btn');
    const postsGrid = document.querySelector('.posts-grid');

    viewButtons.forEach(button => {
        button.addEventListener('click', () => {
            viewButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            const viewType = button.getAttribute('data-view');
            postsGrid.className = `posts-grid view-${viewType}`;
        });
    });

    // Category and type item click handling
    const subcategoryItems = document.querySelectorAll('.subcategory-item');
    subcategoryItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all category items
            subcategoryItems.forEach(cat => cat.classList.remove('active'));
            // Add active class to clicked item
            this.classList.add('active');
        });
    });

    const typeItems = document.querySelectorAll('.type-item');
    typeItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all type items
            typeItems.forEach(type => type.classList.remove('active'));
            // Add active class to clicked item
            this.classList.add('active');
        });
    });

    // Trending post click handling
    const trendingPosts = document.querySelectorAll('.trending-post');
    trendingPosts.forEach(post => {
        post.addEventListener('click', function() {
            // Handle trending post click (e.g., navigate to post)
            console.log('Trending post clicked:', this.querySelector('h4').textContent);
        });
    });

    // Search functionality
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                // Handle search
                console.log('Search for:', this.value);
                this.value = '';
            }
        });
    }

    // Navigation between sidebar tabs in dashboards
    const sidebarLinks = document.querySelectorAll('.sidebar-nav a[data-content]');
    if (sidebarLinks.length > 0) {
        // Store the initial dashboard content
        const dashboardContent = document.querySelector('.dashboard-content').innerHTML;

        sidebarLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Get the content section to show
                const contentId = this.getAttribute('data-content');
                
                // Update active state in sidebar
                document.querySelectorAll('.sidebar-nav li').forEach(item => {
                    item.classList.remove('active');
                });
                this.parentElement.classList.add('active');
                
                // Handle dashboard content
                const mainContent = document.querySelector('.main-content');
                if (contentId === 'dashboard') {
                    // Restore the dashboard content
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
                            ${dashboardContent}
                        </div>
                    `;
                } else {
                    // Show placeholder content for other sections
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
    }
    
    // Added placeholder profile images
    const profileImgs = document.querySelectorAll('.profile-image img, .auth-icon img');
    profileImgs.forEach(img => {
        if (!img.src || img.src.includes('placeholder')) {
            img.src = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23cccccc"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>';
        }
    });
    
    // Category selection in discover page
    const categoryItems = document.querySelectorAll('.category-item');
    if (categoryItems.length > 0) {
        categoryItems.forEach(item => {
            item.addEventListener('click', function() {
                categoryItems.forEach(cat => cat.classList.remove('active'));
                this.classList.add('active');
                
                //Here the content will eventually actually change
                const discoverTitle = document.querySelector('.discover-grid h2');
                if (discoverTitle) {
                    discoverTitle.textContent = this.textContent;
                }
            });
        });
    }
    
    // Login/signup navigation
    const authButtons = document.querySelectorAll('.auth-buttons a, .auth-link');
    authButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Only intercept clicks for "Log In" button
            if (this.textContent.includes('Log In')) {
                e.preventDefault(); // Prevent default navigation
                window.location.href = '../Backend/login.php'; // Redirect to login
            }
            // Allow "Get Started" button to use its default href
        });
    });
    
    // Sample data for admin dashboard
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
        
        // Add event listeners to delete buttons
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this user?')) {
                    this.closest('tr').remove();
                }
            });
        });
    }
    
    // Search functionality
    const searchInputs = document.querySelectorAll('.search-input');
    searchInputs.forEach(input => {
        input.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                alert(`Search functionality would filter results for: "${this.value}"`);
                this.value = '';
            }
        });
    });
    
    // Create blog button
    const createBlogBtn = document.querySelector('.btn-create');
    if (createBlogBtn) {
        createBlogBtn.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Creating a new blog post would open an editor interface.');
        });
    }
    //Function to display error messages 
    function displayError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
    errorElement.textContent = message;
        }
    }

// Login form submission
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Get form values
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        displayError('usernameError', '');
        displayError('passwordError', '');

        // Validation
        let hasErrors = false;
        if (!username) {
            displayError('usernameError', 'Please enter your username.');
            hasErrors = true;
        }
        if (!password) {
            displayError('passwordError', 'Please enter your password.');
            hasErrors = true;
        }

        if (hasErrors) {
            return;
        }

    // Test login- with remove after backend implementation 
    if (username === 'testuser' && password === 'password') {
        alert('Login Successful!');
        window.location.href = '../Backend/user-dashboard.php';
    } else {
        displayError('passwordError', 'Invalid username or password.');
    }
    });
    }
        // Signup form submission
        const signupForm = document.getElementById('signupForm');
        if (signupForm) {
        signupForm.addEventListener('submit', function(e) {
            e.preventDefault(); 
        // Get form values
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

        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email && !emailRegex.test(email)) {
            displayError('emailError', 'Please enter a valid email address.');
            hasErrors = true;
        }

        if (hasErrors) {
            return;
        }

        // Simulate signup (will change later)
        alert('Signup Successful!\n\nName: ' + name + '\nEmail: ' + email + '\nUsername: ' + username + '\nPassword: ' + password);

        window.location.href = '../Backend/login.php';
    });
}            
});