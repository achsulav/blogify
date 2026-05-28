
import '../css/toast.css';
import './toast.js';

/**
 * Blogify - Vite Powered Frontend
 */

const viteLogo = `
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128" width="24" height="24">
    <defs>
      <linearGradient id="a" x1="6" x2="235" y1="33" y2="344" gradientTransform="translate(0 .937) scale(.3122)" gradientUnits="userSpaceOnUse">
        <stop offset="0" stop-color="#41d1ff"/><stop offset="1" stop-color="#bd34fe"/>
      </linearGradient>
      <linearGradient id="b" x1="194.651" x2="236.076" y1="8.818" y2="292.989" gradientTransform="translate(0 .937) scale(.3122)" gradientUnits="userSpaceOnUse">
        <stop offset="0" stop-color="#ffea83"/><stop offset=".083" stop-color="#ffdd35"/><stop offset="1" stop-color="#ffa800"/>
      </linearGradient>
    </defs>
    <path fill="url(#a)" d="M124.766 19.52 67.324 122.238c-1.187 2.121-4.234 2.133-5.437.024L3.305 19.532c-1.313-2.302.652-5.087 3.261-4.622L64.07 25.187a3.09 3.09 0 0 0 1.11 0l56.3-10.261c2.598-.473 4.575 2.289 3.286 4.594Zm0 0"/>
    <path fill="url(#b)" d="M91.46 1.43 48.954 9.758a1.56 1.56 0 0 0-1.258 1.437l-2.617 44.168a1.563 1.563 0 0 0 1.91 1.614l11.836-2.735a1.562 1.562 0 0 1 1.88 1.836l-3.517 17.219a1.562 1.562 0 0 0 1.985 1.805l7.308-2.223c1.133-.344 2.223.652 1.985 1.812l-5.59 27.047c-.348 1.692 1.902 2.614 2.84 1.164l.625-.968 34.64-69.13c.582-1.16-.421-2.48-1.69-2.234l-12.185 2.352a1.558 1.558 0 0 1-1.793-1.965l7.95-27.562A1.56 1.56 0 0 0 91.46 1.43Zm0 0"/>
  </svg>
`;

console.log("%c Blogify %c Vite Loaded ",
  "background: #bd34fe; color: #fff; padding: 2px 4px; border-radius: 3px 0 0 3px;",
  "background: #41d1ff; color: #000; padding: 2px 4px; border-radius: 0 3px 3px 0;"
);

// Initialize any global frontend features here
document.addEventListener('DOMContentLoaded', () => {
  // Engagement: Likes
  document.querySelectorAll('.toggle-like').forEach(btn => {
      btn.addEventListener('click', async (e) => {
          e.preventDefault();
          const postId = btn.getAttribute('data-id');
          const icon = btn.querySelector('i');
          const countSpan = btn.querySelector('.likes-count');
          
          try {
              const res = await fetch('/api/engagement/like', {
                  method: 'POST',
                  headers: {'Content-Type': 'application/json'},
                  body: JSON.stringify({ post_id: postId })
              });
              const data = await res.json();
              if (data.success) {
                  if (data.action === 'liked') {
                      btn.classList.add('liked');
                      icon.classList.replace('bx-heart', 'bxs-heart');
                  } else {
                      btn.classList.remove('liked');
                      icon.classList.replace('bxs-heart', 'bx-heart');
                  }
                  countSpan.textContent = data.total_likes > 0 ? data.total_likes : '';
              }
          } catch (err) {
              console.error('Error toggling like:', err);
          }
      });
  });

  // Engagement: Bookmarks
  document.querySelectorAll('.toggle-bookmark').forEach(btn => {
      btn.addEventListener('click', async (e) => {
          e.preventDefault();
          const postId = btn.getAttribute('data-id');
          const icon = btn.querySelector('i');
          
          try {
              const res = await fetch('/api/engagement/bookmark', {
                  method: 'POST',
                  headers: {'Content-Type': 'application/json'},
                  body: JSON.stringify({ post_id: postId })
              });
              const data = await res.json();
              if (data.success) {
                  if (data.action === 'bookmarked') {
                      btn.classList.add('active');
                      icon.classList.replace('bx-bookmark', 'bxs-bookmark');
                  } else {
                      btn.classList.remove('active');
                      icon.classList.replace('bxs-bookmark', 'bx-bookmark');
                  }
              }
          } catch (err) {
              console.error('Error toggling bookmark:', err);
          }
      });
  });

  // Search Logic with Debounce
  const searchInput = document.getElementById('globalSearchInput');
  const searchResults = document.getElementById('searchResults');
  
  if (searchInput && searchResults) {
      let timeout = null;
      
      searchInput.addEventListener('input', (e) => {
          clearTimeout(timeout);
          const query = e.target.value.trim();
          
          if (query.length < 2) {
              searchResults.style.display = 'none';
              return;
          }

          timeout = setTimeout(async () => {
              try {
                  const res = await fetch(`/api/search?q=${encodeURIComponent(query)}`);
                  const data = await res.json();
                  
                  if (data.length === 0) {
                      searchResults.innerHTML = '<div style="padding: 12px; color: var(--text-muted); text-align: center; font-size: 13px;">No results found</div>';
                      searchResults.style.display = 'block';
                      return;
                  }

                  let html = '';
                  data.forEach(item => {
                      const matchBadge = item.category_match ? '<span class="match-badge">Interest Match</span>' : '';
                      html += `
                          <a href="${item.url}" class="search-result-item">
                              <div class="search-result-title">${item.title}</div>
                              <div class="search-result-meta">${item.category} ${matchBadge}</div>
                          </a>
                      `;
                  });
                  searchResults.innerHTML = html;
                  searchResults.style.display = 'block';
                  
              } catch (err) {
                  console.error("Search error", err);
              }
          }, 300); // 300ms debounce
      });

      // Close dropdown when clicking outside
      document.addEventListener('click', (e) => {
          if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
              searchResults.style.display = 'none';
          }
      });
  }
});

// Register Service Worker for Offline Support
// Disabled for local development because opening hotspot triggers offline mode in browser
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.getRegistrations().then(function(registrations) {
      for(let registration of registrations) {
        registration.unregister();
        console.log('SW unregistered to prevent local dev offline issues');
      }
    });
  });
}

