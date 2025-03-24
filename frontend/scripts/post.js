document.addEventListener('DOMContentLoaded', function() {
    function getQueryParam(name) {
      const params = new URLSearchParams(window.location.search);
      return params.get(name);
    }
  
    document.getElementById('backBtn').addEventListener('click', function() {
      window.history.back();
    });
  
    document.getElementById('commentsIcon').addEventListener('click', function() {
      document.getElementById('commentsSection').scrollIntoView({ behavior: 'smooth' });
    });
  
    const postId = getQueryParam('post_id');
    if (!postId) {
      alert("No post specified.");
      return;
    }
  
    fetch('php/get_post.php?post_id=' + encodeURIComponent(postId))
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          alert(data.error);
          return;
        }
        document.getElementById('postTitle').textContent = data.title || "No Title";
        document.getElementById('postSubtitle').textContent = data.subtitle || "";
        document.getElementById('posterId').textContent = data.username || "Unknown";
        document.getElementById('postDate').textContent = new Date(data.created_at).toLocaleDateString();
        document.getElementById('likeCount').textContent = data.like_count;
        document.getElementById('commentCount').textContent = data.comment_count;
        document.getElementById('postContent').innerHTML = data.content ? data.content.replace(/\n/g, "<br>") : "";
        
        const imageContainer = document.getElementById('postImageContainer');
        if (data.post_image) {
          imageContainer.innerHTML = `<img src="${data.post_image}" alt="Post Image">`;
        } else {
          imageContainer.innerHTML = `<img src="https://via.placeholder.com/600x300" alt="Default Post Image">`;
        }
        
        const heartIcon = document.querySelector('.likes i');
        if (data.user_liked && heartIcon) {
          heartIcon.classList.add('liked');
        }
      })
      .catch(error => console.error("Error fetching post details:", error));
  
    const heartIcon = document.querySelector('.likes i');
    const likeCountElem = document.getElementById('likeCount');
    if (heartIcon && postId) {
      heartIcon.addEventListener('click', function() {
        if (heartIcon.classList.contains('liked')) {
          return;
        }
        
        fetch('php/like_post.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: "post_id=" + encodeURIComponent(postId)
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            likeCountElem.textContent = data.like_count;
            heartIcon.classList.add('liked'); 
          } else {
            console.error("Failed to update likes:", data.message);
          }
        })
        .catch(error => console.error("Error updating likes:", error));
      });
    }
  });
  