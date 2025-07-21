class WatchlistManager {
  constructor() {
    this.categories = {
      Marvel: 'marvel-watchlist',
      KDrama: 'kdrama-watchlist',
      Movies: 'movies-watchlist',
      Series: 'series-watchlist',
    };
  }

  loadWatchlist = () => {
    const watchlist = JSON.parse(localStorage.getItem('watchlist')) || {};

    Object.entries(this.categories).forEach(([category, containerId]) => {
      const container = document.getElementById(containerId);
      if (!container) return;

      const items = watchlist[category] || [];
      container.innerHTML = '';

      if (items.length === 0) {
        container.innerHTML = `<p class="empty-msg">No ${category} entries in your watchlist yet.</p>`;
        return;
      }

      items.forEach(item => {
        const card = document.createElement('article');
        card.classList.add('watchlist-item');

        card.innerHTML = `
          <button class="delete-btn" title="Verwijderen">&times;</button>
          <img src="${item.image}" alt="${item.title}" />
          <div class="item-info">
            <h3>${item.title}</h3>
            <p>${item.quote}</p>
          </div>
        `;

        card.querySelector('.delete-btn').addEventListener('click', () => {
          card.classList.add('fade-out');
          setTimeout(() => {
            watchlist[category] = watchlist[category].filter(e => e.title !== item.title);
            localStorage.setItem('watchlist', JSON.stringify(watchlist));
            this.loadWatchlist();
          }, 300);
        });

        container.appendChild(card);
      });
    });
  };

  init = () => {
    window.addEventListener('DOMContentLoaded', this.loadWatchlist);
  };
}


const watchlistManager = new WatchlistManager();
watchlistManager.init();
