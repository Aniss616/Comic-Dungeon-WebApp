export const THEMES = {
  'street-level': {
    '--sl-black':     '#0D0D0D',
    '--sl-surface':   '#141418',
    '--sl-raised':    '#1C1C22',
    '--sl-border':    'rgba(255,255,255,0.06)',
    '--sl-border-md': 'rgba(255,255,255,0.11)',
    '--sl-red':       '#c9011d',
    '--sl-red-dim':   'rgba(192,57,43,0.12)',
    '--sl-amber':     '#D4832A',
    '--sl-amber-dim': 'rgba(212,131,42,0.10)',
    '--sl-text':      '#E8E4DC',
    '--sl-muted':     'rgba(232,228,220,0.45)',
    '--sl-faint':     'rgba(232,228,220,0.18)',
  },
  'cosmic': {
    '--sl-black':     '#080D0F',
    '--sl-surface':   '#0D1520',
    '--sl-raised':    '#111E2E',
    '--sl-border':    'rgba(255,255,255,0.06)',
    '--sl-border-md': 'rgba(255,255,255,0.11)',
    '--sl-red':       '#1A8A3C',
    '--sl-red-dim':   'rgba(26,138,60,0.12)',
    '--sl-amber':     '#2E7FD4',
    '--sl-amber-dim': 'rgba(46,127,212,0.10)',
    '--sl-text':      '#DDE8F0',
    '--sl-muted':     'rgba(221,232,240,0.45)',
    '--sl-faint':     'rgba(221,232,240,0.18)',
  },
};

export function applyTheme(name, animate = false) {
  const vars = THEMES[name];
  if (!vars) return;

  const root = document.documentElement;

  if (animate) {
    let overlay = document.getElementById('theme-transition-overlay');
    if (!overlay) {
      overlay = document.createElement('div');
      overlay.id = 'theme-transition-overlay';
      overlay.style.cssText = [
        'position:fixed', 'inset:0', 'z-index:99999',
        'background:#000', 'opacity:0', 'pointer-events:none',
        'transition:opacity 0.25s ease',
      ].join(';');
      document.body.appendChild(overlay);
    }

    // Force reflow so the transition actually fires from 0
    overlay.getBoundingClientRect();
    overlay.style.opacity = '0.6';

    setTimeout(() => {
      root.setAttribute('data-theme', name);
      for (const [prop, val] of Object.entries(vars)) {
        root.style.setProperty(prop, val);
      }
      swapLogo(name);
      overlay.style.opacity = '0';
      // Notify home page to swap its canvas
      window.dispatchEvent(new CustomEvent('cd-theme-changed', { detail: { theme: name } }));
      setTimeout(() => overlay.remove(), 300);
    }, 270);

  } else {
    root.setAttribute('data-theme', name);
    for (const [prop, val] of Object.entries(vars)) {
      root.style.setProperty(prop, val);
    }
    swapLogo(name);
  }

  localStorage.setItem('cd-theme', name);
}

function swapLogo(name) {
  const img = document.querySelector('.navbar-logo-img');
  if (!img) return;
  const src = name === 'cosmic'
    ? img.dataset.cosmicSrc
    : img.dataset.slSrc;
  if (src && img.src !== src) img.src = src;
}

export function getStoredTheme() {
  return localStorage.getItem('cd-theme') || 'street-level';
}

export function bootTheme() {
  const name = getStoredTheme();
  applyTheme(name, false);
}