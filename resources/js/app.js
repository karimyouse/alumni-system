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

function setupLanguageLinks() {
  document.querySelectorAll("[data-lang-link]").forEach((link) => {
    link.addEventListener("click", (event) => {
      const href = link.getAttribute("href");
      if (!href) return;

      event.preventDefault();

      const url = new URL(href, window.location.origin);
      const currentLocation = `${window.location.pathname}${window.location.search}${window.location.hash}`;

      url.searchParams.set("redirect_to", currentLocation);

      window.location.assign(url.toString());
    });
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

  function isDesktop() {
    return window.innerWidth >= 768;
  }

  function getHiddenClass() {
    return document.documentElement.getAttribute("dir") === "rtl"
      ? "translate-x-full"
      : "-translate-x-full";
  }

  function getStoredCollapsedState() {
    return localStorage.getItem(desktopKey) === "1";
  }

  function syncDesktopToggleState() {
    if (!desktopToggle) return;
    const collapsed = body.classList.contains("dashboard-sidebar-collapsed");
    desktopToggle.setAttribute("aria-pressed", collapsed ? "true" : "false");
  }

  function applyDesktopState() {
    if (!isDesktop()) return;

    const collapsed = getStoredCollapsedState();
    body.classList.toggle("dashboard-sidebar-collapsed", collapsed);
    overlay?.classList.add("hidden");
    sidebar.classList.remove("translate-x-0", "-translate-x-full", "translate-x-full");
    syncDesktopToggleState();
  }

  function openMobileSidebar() {
    if (isDesktop()) return;
    sidebar.classList.remove("-translate-x-full", "translate-x-full");
    sidebar.classList.add("translate-x-0");
    overlay?.classList.remove("hidden");
  }

  function closeMobileSidebar() {
    if (isDesktop()) return;
    sidebar.classList.remove("translate-x-0", "-translate-x-full", "translate-x-full");
    sidebar.classList.add(getHiddenClass());
    overlay?.classList.add("hidden");
  }

  desktopToggle?.addEventListener("click", () => {
    if (!isDesktop()) return;

    const collapsed = !body.classList.contains("dashboard-sidebar-collapsed");
    body.classList.toggle("dashboard-sidebar-collapsed", collapsed);
    localStorage.setItem(desktopKey, collapsed ? "1" : "0");
    syncDesktopToggleState();
  });

  mobileOpen?.addEventListener("click", openMobileSidebar);
  mobileClose?.addEventListener("click", closeMobileSidebar);
  overlay?.addEventListener("click", closeMobileSidebar);

  const syncSidebarState = () => {
    if (isDesktop()) {
      applyDesktopState();
    } else {
      body.classList.remove("dashboard-sidebar-collapsed");
      syncDesktopToggleState();
      closeMobileSidebar();
    }
  };

  window.addEventListener("resize", syncSidebarState);
  window.addEventListener("pageshow", syncSidebarState);

  syncSidebarState();
}


function setupArabicDomTranslations() {
  const locale = window.__APP_LOCALE__;
  const translations = window.__AR_TRANSLATIONS__ || {};

  if (locale !== "ar" || !translations || typeof translations !== "object") {
    return;
  }

  const skipTags = new Set(["SCRIPT", "STYLE", "NOSCRIPT", "TEMPLATE", "CODE", "PRE"]);

  function translateText(text) {
    if (typeof text !== "string") return text;

    const direct = translations[text];
    if (typeof direct === "string" && direct.trim() !== "") {
      return direct;
    }

    const trimmed = text.trim();
    if (!trimmed) return text;

    const translatedTrimmed = translations[trimmed];
    if (typeof translatedTrimmed === "string" && translatedTrimmed.trim() !== "") {
      const leading = text.match(/^\s*/u)?.[0] || "";
      const trailing = text.match(/\s*$/u)?.[0] || "";
      return `${leading}${translatedTrimmed}${trailing}`;
    }

    return text;
  }

  function shouldSkip(node) {
    if (!node) return true;

    let current = node.nodeType === Node.ELEMENT_NODE ? node : node.parentElement;
    while (current) {
      if (current.nodeType === Node.ELEMENT_NODE) {
        if (skipTags.has(current.tagName)) return true;
        if ((current.getAttribute("data-translate") || "").toLowerCase() === "off") return true;
      }
      current = current.parentElement;
    }

    return false;
  }

  function translateAttributes(element) {
    if (!(element instanceof Element) || shouldSkip(element)) return;

    ["placeholder", "title", "aria-label", "aria-placeholder"].forEach((attribute) => {
      if (element.hasAttribute(attribute)) {
        const current = element.getAttribute(attribute);
        const translated = translateText(current);
        if (translated !== current) {
          element.setAttribute(attribute, translated);
        }
      }
    });

    if (
      element instanceof HTMLInputElement &&
      ["submit", "button", "reset"].includes((element.type || "").toLowerCase())
    ) {
      const translated = translateText(element.value);
      if (translated !== element.value) {
        element.value = translated;
      }
    }
  }

  function translateNode(node) {
    if (!node || shouldSkip(node)) return;

    if (node.nodeType === Node.TEXT_NODE) {
      const translated = translateText(node.nodeValue || "");
      if (translated !== node.nodeValue) {
        node.nodeValue = translated;
      }
      return;
    }

    if (node.nodeType !== Node.ELEMENT_NODE) return;

    translateAttributes(node);

    node.childNodes.forEach((child) => {
      translateNode(child);
    });
  }

  translateNode(document.body);

  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.type === "characterData" && mutation.target) {
        translateNode(mutation.target);
        return;
      }

      if (mutation.type === "attributes" && mutation.target instanceof Element) {
        translateAttributes(mutation.target);
        return;
      }

      mutation.addedNodes.forEach((node) => {
        translateNode(node);
      });
    });
  });

  observer.observe(document.body, {
    subtree: true,
    childList: true,
    characterData: true,
    attributes: true,
    attributeFilter: ["placeholder", "title", "aria-label", "aria-placeholder", "value"],
  });
}

function setupScrollReveal() {
  const sections = document.querySelectorAll(".reveal-section");
  if (!sections.length) return;

  const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  sections.forEach((section) => {
    section.classList.add("reveal-ready");
  });

  if (prefersReducedMotion || !("IntersectionObserver" in window)) {
    sections.forEach((section) => {
      section.classList.add("is-visible");
      section.querySelectorAll(".reveal-item").forEach((item) => item.classList.add("is-visible"));
    });
    return;
  }

  const revealObserver = new IntersectionObserver(
    (entries, observer) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;

        entry.target.classList.add("is-visible");
        entry.target.querySelectorAll(".reveal-item").forEach((item) => item.classList.add("is-visible"));
        observer.unobserve(entry.target);
      });
    },
    {
      threshold: 0.16,
      rootMargin: "0px 0px -8% 0px",
    },
  );

  sections.forEach((section) => revealObserver.observe(section));
}

document.addEventListener("DOMContentLoaded", () => {
  renderIcons();
  applyTheme(getInitialTheme());
  setupLanguageLinks();
  setupDashboardSidebar();
  setupArabicDomTranslations();
  setupScrollReveal();

  document.querySelectorAll("[data-theme-toggle]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const isDarkNow = document.documentElement.classList.contains("dark");
      applyTheme(isDarkNow ? "light" : "dark");
    });
  });
});
