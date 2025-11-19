// pet.js - attach after your HTML (place <script src="pet.js"></script> before </body>)
// This script requires no HTML changes. It finds .card elements and manages expansion state.

(function () {
  // mapping for extra info; use the <h3> text as key when possible
  const extraInfo = {
    'Jerry': 'Vaccinated • Needs a loving family • Microchipped',
    'Tom': 'Indoor cat • Litter trained • Calm temperament',
    'Buddy': 'Energetic • Needs regular walks • Dog park friendly',
    'Luna': 'Curious • Prefers quiet homes • Great lap cat',
    'Max': 'Calm • Good with kids • Leash-trained',
    'Whiskers': 'Vocal • Loves playtime • Interactive toy fan',
    'Bruno': 'Older and relaxed • Trained • Loves naps',
    'Pixie': 'Young kitten • Very playful • Needs attention',
    'Oscar': 'Smart and obedient • Good family dog',
    'Milo': 'Gentle • Enjoys naps • Prefers soft beds',
    'Rocky': 'Protective • Loyal • Morning walk lover',
    'Bella': 'Calm • Loves sunbeams • Indoor-friendly',
    'Toby': 'Friendly • Great with kids • Energetic',
    'Neko': 'Independent • Explorer • Low maintenance',
    'Simba': 'Young and curious • Loves training',
  };

  // helper: create panel HTML
  function makePanel(text) {
    const panel = document.createElement('div');
    panel.className = 'card-panel';
    const title = document.createElement('h4');
    title.textContent = 'More info';
    const p = document.createElement('p');
    p.textContent = text || 'No additional info available.';
    panel.appendChild(title);
    panel.appendChild(p);
    return panel;
  }

  // apply to all cards
  function initCards() {
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
      // make card focusable for keyboard
      card.setAttribute('tabindex', '0');

      // create a backdrop element for subtle dim
      let backdrop = card.querySelector('.backdrop');
      if (!backdrop) {
        backdrop = document.createElement('div');
        backdrop.className = 'backdrop';
        card.appendChild(backdrop);
      }

      // create close button (will show only when expanded)
      let closeBtn = card.querySelector('.close-btn');
      if (!closeBtn) {
        closeBtn = document.createElement('button');
        closeBtn.className = 'close-btn';
        closeBtn.setAttribute('aria-label', 'Close details');
        closeBtn.innerHTML = '&#x2715;'; // simple X
        closeBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          collapseCard(card);
        });
        card.appendChild(closeBtn);
      }

      // if there's already a panel, remove it (idempotent)
      const existing = card.querySelector('.card-panel');
      if (existing) existing.remove();

      // find name in <h3> if present
      const h3 = card.querySelector('.card-body h3');
      const name = h3 ? h3.textContent.trim() : '';

      // prepare panel element
      const panel = makePanel(extraInfo[name] || extraInfo[name] === '' ? extraInfo[name] : 'Vaccinated • Neutered • Ready for adoption');
      card.appendChild(panel);

      // click toggles expansion
      card.addEventListener('click', (e) => {
        // if click on the close button, ignore here (close handled there)
        if (e.target.closest('.close-btn')) return;
        toggleCard(card);
      });

      // keyboard handling: Enter or Space toggles
      card.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          toggleCard(card);
        } else if (e.key === 'Escape') {
          collapseCard(card);
        }
      });
    });

    // click outside any expanded card collapses them
    document.addEventListener('click', (e) => {
      const expanded = document.querySelector('.card.expanded');
      if (!expanded) return;
      if (!e.target.closest('.card')) {
        collapseCard(expanded);
      }
    });
  }

  function toggleCard(card) {
    const isExpanded = card.classList.contains('expanded');
    // collapse any other expanded card first
    const other = document.querySelector('.card.expanded');
    if (other && other !== card) collapseCard(other);
    if (isExpanded) {
      collapseCard(card);
    } else {
      expandCard(card);
    }
  }

  function expandCard(card) {
    card.classList.add('expanded');
    // ensure expanded card scrolls into view nicely
    setTimeout(() => {
      card.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }, 80);
  }

  function collapseCard(card) {
    card.classList.remove('expanded');
  }

  // initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCards);
  } else {
    initCards();
  }
})();