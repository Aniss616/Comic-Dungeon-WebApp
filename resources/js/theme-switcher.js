import { applyTheme, getStoredTheme } from './theme.js';

const THEME_META = {
  'street-level': {
    label: 'Street Level',
    icon: `<svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <path d="M13 8A5 5 0 1 1 8 3c-1.1 1.1-1.5 2.7-1 4.1.5 1.4 1.8 2.4 3.3 2.6A5 5 0 0 1 13 8z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
    </svg>`,
  },
  'cosmic': {
    label: 'Cosmic',
    icon: `<svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <circle cx="8" cy="8" r="5.5" stroke="currentColor" stroke-width="1.4"/>
      <circle cx="8" cy="8" r="2" fill="currentColor"/>
      <line x1="8" y1="1" x2="8" y2="3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
      <line x1="8" y1="13" x2="8" y2="15" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
      <line x1="1" y1="8" x2="3" y2="8" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
      <line x1="13" y1="8" x2="15" y2="8" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
    </svg>`,
  },
  'supernatural': {
    label: 'Supernatural',
    icon: `<svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <path d="M8 1.5c-2.9 0-5 2.4-4.6 5.3.3 2 1.6 3.1 1.6 4.7v.5h6v-.5c0-1.6 1.3-2.7 1.6-4.7C13 3.9 10.9 1.5 8 1.5z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/>
      <path d="M6 14.5c0 .6.9 1 2 1s2-.4 2-1" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
      <circle cx="6.3" cy="7" r=".9" fill="currentColor"/>
      <circle cx="9.7" cy="7" r=".9" fill="currentColor"/>
    </svg>`,
  },
};

const ORDER = ['street-level', 'cosmic', 'supernatural'];

export function initThemeSwitcher() {
  const wrap = document.getElementById('theme-dropdown');
  const btn  = document.getElementById('theme-dropdown-btn');
  const menu = document.getElementById('theme-dropdown-menu');
  if (!wrap || !btn || !menu) return;

  function renderBtn(name) {
    const meta = THEME_META[name] || THEME_META['street-level'];
    btn.innerHTML = `${meta.icon}<span>${meta.label}</span>`;
    btn.setAttribute('aria-label', `Theme: ${meta.label}. Click to change.`);
  }

  function renderMenu(current) {
    menu.innerHTML = ORDER.map(name => {
      const meta = THEME_META[name];
      const active = name === current ? 'is-active' : '';
      return `<li role="option" data-theme-choice="${name}" class="theme-dropdown-item ${active}" aria-selected="${name === current}">
        ${meta.icon}<span>${meta.label}</span>
      </li>`;
    }).join('');
  }

  function open()   { menu.hidden = false; btn.setAttribute('aria-expanded', 'true');  wrap.classList.add('is-open'); }
  function close()  { menu.hidden = true;  btn.setAttribute('aria-expanded', 'false'); wrap.classList.remove('is-open'); }
  function toggle() { menu.hidden ? open() : close(); }

  const current = getStoredTheme();
  renderBtn(current);
  renderMenu(current);

  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    toggle();
  });

  menu.addEventListener('click', (e) => {
    const li = e.target.closest('[data-theme-choice]');
    if (!li) return;
    const name = li.dataset.themeChoice;
    applyTheme(name, true);
    renderBtn(name);
    renderMenu(name);
    close();
  });

  document.addEventListener('click', (e) => {
    if (!wrap.contains(e.target)) close();
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') close();
  });
}