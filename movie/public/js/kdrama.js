class KDramaWatchlist {
  constructor() {
    this.addButtonsSelector = '.watchlist-btn';
    this.category = 'KDrama';
  }

  addToWatchlist = (item) => {
    const watchlist = JSON.parse(localStorage.getItem('watchlist')) || {};

    if (!watchlist[this.category]) {
      watchlist[this.category] = [];
    }

    const alreadyAdded = watchlist[this.category].some(entry => entry.title === item.title);

    if (!alreadyAdded) {
      watchlist[this.category].push(item);
      localStorage.setItem('watchlist', JSON.stringify(watchlist));
      alert(`"${item.title}" added to your ${this.category} Watchlist!`);
    } else {
      alert(`"${item.title}" is already in your ${this.category} Watchlist.`);
    }
  };

  handleAddButtonClick = (e) => {
    e.preventDefault();
    const btn = e.currentTarget;
    const card = btn.closest('.card');
    if (!card) return;

    const title = card.querySelector('h3')?.innerText || '';
    const image = card.querySelector('img')?.getAttribute('src') || '';
    const quote = card.querySelector('p')?.innerText || '';

    const item = { title, image, quote };
    this.addToWatchlist(item);
  };

  handleTrailerClick = (e) => {
    const target = e.target;
    if (target.classList.contains('trailer-btn')) {
      e.preventDefault();
      const trailerUrl = target.getAttribute('data-trailer');
      if (trailerUrl) {
        window.open(trailerUrl, '_blank', 'noopener,noreferrer');
      } else {
        alert('Trailer link not available.');
      }
    }
  };

  init = () => {
    document.addEventListener('DOMContentLoaded', () => {
      const addButtons = document.querySelectorAll(this.addButtonsSelector);
      addButtons.forEach(btn => btn.addEventListener('click', this.handleAddButtonClick));
    });

    document.addEventListener('click', this.handleTrailerClick);
  };
}


const kdramaWatchlist = new KDramaWatchlist();
kdramaWatchlist.init();
