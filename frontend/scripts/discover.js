document.addEventListener('DOMContentLoaded', function() {
    let morePostsOffset = 0;
    const postsPerPage = 6; // number of posts per "Load More" request

    // ------------- Search Functionality -------------
    function loadSearchResults(keyword) {
        fetch('php/search_posts.php?keyword=' + encodeURIComponent(keyword))
          .then(response => response.json())
          .then(posts => {
              const postsGrid = document.querySelector('.discover-content .posts-grid');
              let html = '';
              if (!posts.length) {
                  html = "<p>No posts found matching your search.</p>";
              } else {
                  posts.forEach(post => {
                      const postDate = new Date(post.created_at).toLocaleDateString();
                      html += `
                          <a href="post.html?post_id=${post.id}" class="post-link">
                              <article class="post-card">
                                  <div class="post-image">
                                      <img src="${post.post_image ? post.post_image : 'https://via.placeholder.com/400x300'}" alt="Post thumbnail">
                                      <span class="category-tag">${post.category ? post.category : 'General'}</span>
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
                              </article>
                          </a>
                      `;
                  });
              }
              // Replace current posts with search results.
              const postsContainer = document.querySelector('.discover-content .posts-grid');
              if (postsContainer) {
                  postsContainer.innerHTML = html;
              }
              // Reattach the search event listener for the new search input
              attachSearchListener();
          })
          .catch(error => console.error("Error fetching search results:", error));
    }

    // ------------- Trending Posts -------------
    function loadTrendingPosts() {
        fetch('php/get_trending_posts.php')
          .then(response => response.json())
          .then(posts => {
              const trendingContainer = document.querySelector('.discover-sidebar .tab-pane#trending-tab .trending-posts');
              let html = '';
              if (!posts.length) {
                  html = "<p>No trending posts found.</p>";
              } else {
                  posts.forEach((post, index) => {
                      const postDate = new Date(post.created_at).toLocaleDateString();
                      html += `
                          <div class="trending-post">
                              <span class="trending-number">${("0" + (index + 1)).slice(-2)}</span>
                              <div class="trending-info">
                                  <h4>${post.title}</h4>
                                  <span>${post.like_count} likes</span>
                              </div>
                          </div>
                      `;
                  });
              }
              if (trendingContainer) {
                  trendingContainer.innerHTML = html;
              }
          })
          .catch(error => console.error("Error fetching trending posts:", error));
    }

    // ------------- Load More Posts (Discover Posts) -------------
    function loadMorePosts() {
        fetch('php/get_more_posts.php?offset=' + morePostsOffset + '&limit=' + postsPerPage)
          .then(response => response.json())
          .then(posts => {
              const postsGrid = document.querySelector('.discover-content .posts-grid');
              let html = '';
              if (posts && posts.length > 0) {
                  posts.forEach(post => {
                      const postDate = new Date(post.created_at).toLocaleDateString();
                      html += `
                          <a href="post.html?post_id=${post.id}" class="post-link">
                              <article class="post-card">
                                  <div class="post-image">
                                      <img src="${post.post_image ? post.post_image : 'https://via.placeholder.com/400x300'}" alt="Post thumbnail">
                                      <span class="category-tag">${post.category ? post.category : 'General'}</span>
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
                              </article>
                          </a>
                      `;
                  });
                  // Append new posts.
                  postsGrid.insertAdjacentHTML('beforeend', html);
                  morePostsOffset += posts.length;
                  // If fewer than postsPerPage were returned, hide the "Load More" button.
                  if (posts.length < postsPerPage) {
                      document.querySelector('.load-more button').style.display = 'none';
                  }
              } else {
                  // No more posts: hide the "Load More" button.
                  document.querySelector('.load-more button').style.display = 'none';
              }
          })
          .catch(error => console.error("Error fetching more posts:", error));
    }

    // ------------- Initial Load for Discover Page -------------
    function initDiscover() {
        // Attach search listener on initial load.
        attachSearchListener();
        // Load trending posts.
        loadTrendingPosts();
        // Load initial set of posts.
        morePostsOffset = 0;
        fetch('php/get_more_posts.php?offset=' + morePostsOffset + '&limit=' + postsPerPage)
          .then(response => response.json())
          .then(posts => {
              const postsGrid = document.querySelector('.discover-content .posts-grid');
              let html = '';
              if (!posts.length) {
                  html = "<p>No posts available.</p>";
              } else {
                  posts.forEach(post => {
                      const postDate = new Date(post.created_at).toLocaleDateString();
                      html += `
                          <a href="post.html?post_id=${post.id}" class="post-link">
                              <article class="post-card">
                                  <div class="post-image">
                                      <img src="${post.post_image ? post.post_image : 'https://via.placeholder.com/400x300'}" alt="Post thumbnail">
                                      <span class="category-tag">${post.category ? post.category : 'General'}</span>
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
                              </article>
                          </a>
                      `;
                  });
                  morePostsOffset = posts.length;
              }
              if (postsGrid) {
                  postsGrid.innerHTML = html;
              }
          })
          .catch(error => console.error("Error fetching initial posts:", error));
    }

    // Helper to attach search event listener to the current search input.
    function attachSearchListener() {
        const searchInput = document.querySelector('.search-container .search-input');
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
    }

    // ------------- "Load More" Button Handling -------------
    const loadMoreButton = document.querySelector('.load-more button');
    if (loadMoreButton) {
        loadMoreButton.addEventListener('click', function(e) {
            e.preventDefault();
            loadMorePosts();
        });
    }

    // ------------- Initial Discover Page Load -------------
    initDiscover();
});
