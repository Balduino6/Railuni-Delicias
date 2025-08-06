// assets/js/app.js

// Exemplo: Toggle de sidebar em telas menores
const sidebar = document.querySelector('.sidebar');
const toggleBtn = document.createElement('button');
toggleBtn.textContent = '☰';
toggleBtn.classList.add('sidebar-toggle');

const topbar = document.querySelector('.topbar');
topbar.appendChild(toggleBtn);

toggleBtn.addEventListener('click', () => {
  sidebar.classList.toggle('collapsed');
});

// Dark Mode (experimental)
const darkModeBtn = document.createElement('button');
darkModeBtn.textContent = '🌓';
darkModeBtn.classList.add('dark-mode-toggle');
topbar.appendChild(darkModeBtn);

darkModeBtn.addEventListener('click', () => {
  document.body.classList.toggle('dark-mode');
});