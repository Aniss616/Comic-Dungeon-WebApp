/**
 * Comic Dungeon — Theme Toggle Button
 * Wires up #theme-toggle-btn to switch between street-level and cosmic.
 * Import this in app.js.
 */

import { applyTheme, getStoredTheme } from './theme.js';

export function initThemeSwitcher() {
  const btn = document.getElementById('theme-toggle-btn');
  if (!btn) return;

  function updateBtn(name) {
    if (name === 'cosmic') {
      btn.title = 'Switch to Street Level';
      btn.setAttribute('aria-label', 'Switch to Street Level theme');
      btn.innerHTML = `
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M13 8A5 5 0 1 1 8 3c-1.1 1.1-1.5 2.7-1 4.1.5 1.4 1.8 2.4 3.3 2.6A5 5 0 0 1 13 8z"
                stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
        </svg>
        <span>Street Level</span>`;
    } else {
      btn.title = 'Switch to Cosmic theme';
      btn.setAttribute('aria-label', 'Switch to Cosmic theme');
      btn.innerHTML = `
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <circle cx="8" cy="8" r="5.5" stroke="currentColor" stroke-width="1.4"/>
          <circle cx="8" cy="8" r="2" fill="currentColor"/>
          <line x1="8" y1="1"  x2="8"  y2="3"  stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
          <line x1="8" y1="13" x2="8"  y2="15" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
          <line x1="1" y1="8"  x2="3"  y2="8"  stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
          <line x1="13" y1="8" x2="15" y2="8"  stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
        </svg>
        <span>Cosmic</span>`;
    }
  }

  updateBtn(getStoredTheme());

  btn.addEventListener('click', () => {
    const next = getStoredTheme() === 'cosmic' ? 'street-level' : 'cosmic';
    applyTheme(next, true);
    updateBtn(next);
  });
}