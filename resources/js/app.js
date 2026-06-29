import './bootstrap';
import { bootTheme } from './theme.js';
import { initThemeSwitcher } from './theme-switcher.js';

bootTheme();

document.addEventListener('DOMContentLoaded', () => {
    initThemeSwitcher();
});