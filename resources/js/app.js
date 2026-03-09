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

function setupLanguageMenu() {
  const toggle = document.querySelector("[data-lang-toggle]");
  const menu = document.querySelector("[data-lang-menu]");

  if (!toggle || !menu) return;

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

function setupDashboardSidebar() {
  const body = document.body;
  if (!body || !body.hasAttribute("data-dashboard-layout")) return;

  const sidebar = document.getElementById("dashboardSidebar");
  const overlay = document.getElementById("sidebarOverlay");
  const desktopToggle = document.querySelector("[data-sidebar-toggle]");
  const mobileOpen = document.querySelector("[data-mobile-sidebar-open]");
  const mobileClose = document.querySelector("[data-mobile-sidebar-close]");

  if (!sidebar) return;

  const desktopKey = "dashboard_sidebar_collapsed";

  function applyDesktopState() {
    if (window.innerWidth < 768) return;
    const collapsed = localStorage.getItem(desktopKey) === "1";
    body.classList.toggle("dashboard-sidebar-collapsed", collapsed);
  }

  function openMobileSidebar() {
    if (window.innerWidth >= 768) return;
    sidebar.classList.remove("-translate-x-full");
    sidebar.classList.add("translate-x-0");
    overlay?.classList.remove("hidden");
  }

  function closeMobileSidebar() {
    if (window.innerWidth >= 768) return;
    sidebar.classList.remove("translate-x-0");
    sidebar.classList.add("-translate-x-full");
    overlay?.classList.add("hidden");
  }

  desktopToggle?.addEventListener("click", () => {
    const collapsed = body.classList.toggle("dashboard-sidebar-collapsed");
    localStorage.setItem(desktopKey, collapsed ? "1" : "0");
  });

  mobileOpen?.addEventListener("click", openMobileSidebar);
  mobileClose?.addEventListener("click", closeMobileSidebar);
  overlay?.addEventListener("click", closeMobileSidebar);

  window.addEventListener("resize", () => {
    if (window.innerWidth >= 768) {
      overlay?.classList.add("hidden");
      sidebar.classList.remove("-translate-x-full");
      sidebar.classList.remove("translate-x-0");
      applyDesktopState();
    } else {
      body.classList.remove("dashboard-sidebar-collapsed");
      closeMobileSidebar();
    }
  });

  applyDesktopState();
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

  setupLanguageMenu();
  setupDashboardSidebar();
});
