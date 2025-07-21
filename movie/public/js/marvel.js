class MarvelWatchlist {
  constructor() {
    this.addButtonsSelector = '.watchlist-btn';
    this.trailerButtonsSelector = '.trailer-btn';
    this.category = 'Marvel';
  }

  addToWatchlist = (movie) => {
    const watchlist = JSON.parse(localStorage.getItem('watchlist')) || {};

    if (!watchlist[this.category]) {
      watchlist[this.category] = [];
    }

    const alreadyAdded = watchlist[this.category].some(item => item.title === movie.title);

    if (!alreadyAdded) {
      watchlist[this.category].push(movie);
      localStorage.setItem('watchlist', JSON.stringify(watchlist));
      alert(`"${movie.title}" added to ${this.category} Watchlist!`);
    } else {
      alert(`"${movie.title}" is already in your ${this.category} Watchlist.`);
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

    const marvelMovie = { title, image, quote };
    this.addToWatchlist(marvelMovie);
  };

  handleTrailerClick = (e) => {
    e.preventDefault();
    const btn = e.currentTarget;
    const trailerId = btn.dataset.trailer;
    if (trailerId) {
      const youtubeUrl = `https://www.youtube.com/watch?v=${trailerId}`;
      window.open(youtubeUrl, '_blank', 'noopener,noreferrer');
    } else {
      alert('Trailer link not available.');
    }
  };

  init = () => {
    document.addEventListener('DOMContentLoaded', () => {
      const addButtons = document.querySelectorAll(this.addButtonsSelector);
      addButtons.forEach(btn => btn.addEventListener('click', this.handleAddButtonClick));

      const trailerButtons = document.querySelectorAll(this.trailerButtonsSelector);
      trailerButtons.forEach(btn => btn.addEventListener('click', this.handleTrailerClick));
    });
  };
}


const marvelWatchlist = new MarvelWatchlist();
marvelWatchlist.init();
