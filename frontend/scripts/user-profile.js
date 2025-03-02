document.addEventListener('DOMContentLoaded', function() {
    let user = {
      id: 'testuser',
      email: 'user@example.com',
      password: 'password'
    };
  
    document.getElementById('userId').textContent = user.id;
    document.getElementById('emailDisplay').textContent = user.email;
    document.getElementById('passwordDisplay').textContent = '********';
  
    document.getElementById('backBtn').addEventListener('click', function() {
      window.location.href = 'user-dashboard.html';
    });
  
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    let passwordVisible = false;
    togglePasswordBtn.addEventListener('click', function() {
      if (!passwordVisible) {
        document.getElementById('passwordDisplay').textContent = user.password;
        togglePasswordBtn.textContent = 'Hide';
      } else {
        document.getElementById('passwordDisplay').textContent = '********';
        togglePasswordBtn.textContent = 'Show';
      }
      passwordVisible = !passwordVisible;
    });
  
    const emailModal = document.getElementById('emailModal');
    document.getElementById('openEmailModalBtn').addEventListener('click', function() {
      emailModal.classList.add('active');
      document.getElementById('newEmail').value = user.email;
    });
    document.getElementById('cancelEmailBtn').addEventListener('click', function() {
      emailModal.classList.remove('active');
    });
    document.getElementById('saveEmailBtn').addEventListener('click', function() {
      let newEmail = document.getElementById('newEmail').value;
      if (newEmail && newEmail.includes('@')) {
        user.email = newEmail;
        document.getElementById('emailDisplay').textContent = user.email;
        emailModal.classList.remove('active');
        alert("Email updated successfully.");
      } else {
        alert("Invalid email address.");
      }
    });
  
    const passwordModal = document.getElementById('passwordModal');
    document.getElementById('openPasswordModalBtn').addEventListener('click', function() {
      passwordModal.classList.add('active');
      document.getElementById('oldPassword').value = "";
      document.getElementById('newPassword').value = "";
    });
    document.getElementById('cancelPasswordBtn').addEventListener('click', function() {
      passwordModal.classList.remove('active');
    });
    document.getElementById('savePasswordBtn').addEventListener('click', function() {
      let oldPass = document.getElementById('oldPassword').value;
      let newPass = document.getElementById('newPassword').value;
      if (oldPass === user.password) {
        if (newPass) {
          user.password = newPass;
          document.getElementById('passwordDisplay').textContent = '********';
          togglePasswordBtn.textContent = 'Show';
          passwordVisible = false;
          passwordModal.classList.remove('active');
          alert("Password updated successfully.");
        } else {
          alert("New password cannot be empty.");
        }
      } else {
        alert("Old password is incorrect.");
      }
    });
  
    document.getElementById('showFollowersBtn').addEventListener('click', function() {
      document.getElementById('followersCount').textContent = "123";
    });
    document.getElementById('hideFollowersBtn').addEventListener('click', function() {
      document.getElementById('followersCount').textContent = "";
    });
  
    document.getElementById('logoutBtn').addEventListener('click', function() {
      if (confirm("Are you sure you want to log out?")) {
        alert("Logged out successfully.");
        window.location.href = "index.html";
      }
    });
  
    document.getElementById('deleteBtn').addEventListener('click', function() {
      if (confirm("Are you sure you want to delete your account? This action cannot be undone.")) {
        alert("Account deleted.");
        window.location.href = "index.html";
      }
    });
  });
  