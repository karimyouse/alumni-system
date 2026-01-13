import "./bootstrap";
import "../css/app.css";

import { createIcons, icons } from "lucide";

function renderIcons() {
  createIcons({ icons });
}

function getInitialTheme() {
  const saved = localStorage.getItem("theme");
  if (saved === "dark" || saved === "light") return saved;

  return window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches
    ? "dark"
    : "light";
}


function updateThemeToggleIcons(theme) {
  const icon = theme === "dark" ? "sun" : "moon";

  document.querySelectorAll("[data-theme-toggle]").forEach((btn) => {

    btn.innerHTML = `<i data-lucide="${icon}" class="h-4 w-4"></i>`;
  });
}

function applyTheme(theme) {
  const isDark = theme === "dark";
  document.documentElement.classList.toggle("dark", isDark);
  localStorage.setItem("theme", theme);

  updateThemeToggleIcons(theme);
  renderIcons();
}

document.addEventListener("DOMContentLoaded", () => {

  renderIcons();


  applyTheme(getInitialTheme());


  document.querySelectorAll("[data-theme-toggle]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const isDarkNow = document.documentElement.classList.contains("dark");
      applyTheme(isDarkNow ? "light" : "dark");
    });
  });

  
  const toggle = document.querySelector("[data-lang-toggle]");
  const menu = document.querySelector("[data-lang-menu]");

  if (toggle && menu) {
    toggle.addEventListener("click", (e) => {
      e.preventDefault();
      menu.classList.toggle("hidden");
    });

    document.addEventListener("click", (e) => {
      const t = e.target;
      if (!(t instanceof Node)) return;
      if (!menu.contains(t) && !toggle.contains(t)) menu.classList.add("hidden");
    });
  }
});
