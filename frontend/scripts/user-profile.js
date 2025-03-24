document.addEventListener('DOMContentLoaded', function() {
    // Load user data
    loadUserData();

    // Image upload handling
    const imageModal = document.getElementById('imageModal');
    const imagePreview = document.getElementById('imagePreview');
    const imageUploadForm = document.getElementById('imageUploadForm');
    const newProfileImage = document.getElementById('newProfileImage');

    document.getElementById('changeImageBtn').addEventListener('click', function() {
        imageModal.classList.add('active');
    });

    document.getElementById('cancelImageBtn').addEventListener('click', function() {
        imageModal.classList.remove('active');
        imageUploadForm.reset();
        imagePreview.style.display = 'none';
    });

    // Show image preview
    newProfileImage.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Handle image upload
    imageUploadForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('profile_image', newProfileImage.files[0]);

        try {
            const response = await fetch('php/update_profile_image.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            
            if (data.success) {
                document.getElementById('profileImage').src = data.image_url;
                imageModal.classList.remove('active');
                imageUploadForm.reset();
                imagePreview.style.display = 'none';
                alert('Profile image updated successfully!');
            } else {
                throw new Error(data.error || 'Failed to update profile image');
            }
        } catch (error) {
            alert(error.message);
        }
    });

    // Email update handling
    const emailModal = document.getElementById('emailModal');
    document.getElementById('openEmailModalBtn').addEventListener('click', function() {
        emailModal.classList.add('active');
        document.getElementById('newEmail').value = document.getElementById('emailDisplay').textContent;
    });

    document.getElementById('cancelEmailBtn').addEventListener('click', function() {
        emailModal.classList.remove('active');
    });

    document.getElementById('saveEmailBtn').addEventListener('click', async function() {
        const newEmail = document.getElementById('newEmail').value.trim();
        if (!newEmail || !isValidEmail(newEmail)) {
            alert('Please enter a valid email address');
            return;
        }

        try {
            const response = await fetch('php/update_email.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email: newEmail })
            });
            const data = await response.json();
            
            if (data.success) {
                document.getElementById('emailDisplay').textContent = newEmail;
                emailModal.classList.remove('active');
                alert('Email updated successfully!');
            } else {
                throw new Error(data.error || 'Failed to update email');
            }
        } catch (error) {
            alert(error.message);
        }
    });

    // Password update handling
    const passwordModal = document.getElementById('passwordModal');
    document.getElementById('openPasswordModalBtn').addEventListener('click', function() {
        passwordModal.classList.add('active');
    });

    document.getElementById('cancelPasswordBtn').addEventListener('click', function() {
        passwordModal.classList.remove('active');
    });

    document.getElementById('savePasswordBtn').addEventListener('click', async function() {
        const oldPassword = document.getElementById('oldPassword').value;
        const newPassword = document.getElementById('newPassword').value;

        if (!oldPassword || !newPassword) {
            alert('Please fill in all password fields');
            return;
        }

        if (newPassword.length < 6) {
            alert('New password must be at least 6 characters long');
            return;
        }

        try {
            const response = await fetch('php/update_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    old_password: oldPassword,
                    new_password: newPassword
                })
            });
            const data = await response.json();
            
            if (data.success) {
                passwordModal.classList.remove('active');
                document.getElementById('oldPassword').value = '';
                document.getElementById('newPassword').value = '';
                alert('Password updated successfully!');
            } else {
                throw new Error(data.error || 'Failed to update password');
            }
        } catch (error) {
            alert(error.message);
        }
    });

    // Logout handling
    document.getElementById('logoutBtn').addEventListener('click', function() {
        if (confirm('Are you sure you want to log out?')) {
            window.location.href = '../Backend/logout.php';
        }
    });

    // Delete account handling
    document.getElementById('deleteBtn').addEventListener('click', async function() {
        if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
            try {
                const response = await fetch('php/delete_account.php', {
                    method: 'POST'
                });
                const data = await response.json();
                
                if (data.success) {
                    window.location.href = '../Backend/login.html';
                } else {
                    throw new Error(data.error || 'Failed to delete account');
                }
            } catch (error) {
                alert(error.message);
            }
        }
    });

    // Back button handling
    document.getElementById('backBtn').addEventListener('click', function() {
        window.location.href = '../Backend/user-dashboard.php';
    });
});

// Helper function to validate email
function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// Function to load user data
async function loadUserData() {
    try {
        const response = await fetch('php/get_user_data.php');
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('userId').textContent = data.user.username;
            document.getElementById('emailDisplay').textContent = data.user.email;
            document.getElementById('followersCount').textContent = data.user.followers_count;
            
            if (data.user.profile_image) {
                document.getElementById('profileImage').src = data.user.profile_image;
            }
        } else {
            throw new Error(data.error || 'Failed to load user data');
        }
    } catch (error) {
        console.error('Error loading user data:', error);
    }
}
  