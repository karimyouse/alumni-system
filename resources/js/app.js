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

function setupLanguageMenus() {
  const dropdowns = Array.from(document.querySelectorAll("[data-lang-dropdown]"));
  if (!dropdowns.length) return;

  const syncRedirectInputs = () => {
    dropdowns.forEach((wrap) => {
      wrap.querySelectorAll("[data-lang-redirect]").forEach((input) => {
        if (!(input instanceof HTMLInputElement)) return;
        try {
          const url = new URL(window.location.href);
          url.hash = "";
          input.value = url.toString();
        } catch (error) {
          input.value = window.location.pathname + window.location.search;
        }
      });

      wrap.querySelectorAll("[data-lang-fragment]").forEach((input) => {
        if (!(input instanceof HTMLInputElement)) return;
        input.value = window.location.hash ? window.location.hash.replace(/^#/, "") : "";
      });
    });
  };

  const closeAll = (exceptMenu = null) => {
    dropdowns.forEach((wrap) => {
      const menu = wrap.querySelector("[data-lang-menu]");
      if (!(menu instanceof HTMLElement)) return;
      if (exceptMenu && menu === exceptMenu) return;
      menu.classList.add("hidden");
    });

    dropdowns.forEach((wrap) => {
      const toggle = wrap.querySelector("[data-lang-toggle]");
      if (toggle instanceof HTMLElement) {
        toggle.setAttribute("aria-expanded", "false");
      }
    });
  };

  syncRedirectInputs();

  dropdowns.forEach((wrap) => {
    const toggle = wrap.querySelector("[data-lang-toggle]");
    const menu = wrap.querySelector("[data-lang-menu]");

    if (!(toggle instanceof HTMLElement) || !(menu instanceof HTMLElement)) return;

    toggle.addEventListener("click", (event) => {
      event.preventDefault();
      event.stopPropagation();

      const willOpen = menu.classList.contains("hidden");
      closeAll(willOpen ? menu : null);
      menu.classList.toggle("hidden", !willOpen);
      toggle.setAttribute("aria-expanded", willOpen ? "true" : "false");
      syncRedirectInputs();
    });
  });

  document.addEventListener("click", (event) => {
    const target = event.target;
    if (!(target instanceof Node)) return;

    dropdowns.forEach((wrap) => {
      if (!wrap.contains(target)) {
        const menu = wrap.querySelector("[data-lang-menu]");
        const toggle = wrap.querySelector("[data-lang-toggle]");
        if (menu instanceof HTMLElement) menu.classList.add("hidden");
        if (toggle instanceof HTMLElement) toggle.setAttribute("aria-expanded", "false");
      }
    });
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      closeAll();
    }
  });

  window.addEventListener("hashchange", syncRedirectInputs);
}



function setupClientSideArabicTranslations() {
  if (document.documentElement.lang !== "ar" && window.__APP_LOCALE__ !== "ar") return;

  const translations = window.__AR_TRANSLATIONS__ || {};
  if (!translations || typeof translations !== "object" || !Object.keys(translations).length) return;

  const skippedTags = new Set(["SCRIPT", "STYLE", "NOSCRIPT", "TEMPLATE", "CODE", "PRE"]);
  const attributeNames = ["placeholder", "title", "aria-label", "aria-placeholder"];

  const translateText = (value) => {
    if (typeof value !== "string" || !value.trim()) return value;

    if (Object.prototype.hasOwnProperty.call(translations, value)) {
      return translations[value];
    }

    const trimmed = value.trim();
    if (Object.prototype.hasOwnProperty.call(translations, trimmed)) {
      const leading = value.match(/^\s+/u)?.[0] || "";
      const trailing = value.match(/\s+$/u)?.[0] || "";
      return `${leading}${translations[trimmed]}${trailing}`;
    }

    return value;
  };

  const shouldSkipElement = (element) => {
    if (!(element instanceof Element)) return true;
    if (skippedTags.has(element.tagName)) return true;
    if (element.closest("[data-translate='off']")) return true;
    return false;
  };

  const translateElementAttributes = (element) => {
    if (!(element instanceof Element) || shouldSkipElement(element)) return;

    attributeNames.forEach((attribute) => {
      if (element.hasAttribute(attribute)) {
        const current = element.getAttribute(attribute);
        const translated = translateText(current);
        if (translated !== current) {
          element.setAttribute(attribute, translated);
        }
      }
    });

    if (
      element.tagName === "INPUT" &&
      ["submit", "button", "reset"].includes((element.getAttribute("type") || "").toLowerCase()) &&
      element.hasAttribute("value")
    ) {
      const current = element.getAttribute("value");
      const translated = translateText(current);
      if (translated !== current) {
        element.setAttribute("value", translated);
      }
    }
  };

  const translateTextNodes = (rootNode = document.body) => {
    if (!(rootNode instanceof Node)) return;

    const walker = document.createTreeWalker(rootNode, NodeFilter.SHOW_TEXT, {
      acceptNode(node) {
        if (!(node.parentElement instanceof Element)) return NodeFilter.FILTER_REJECT;
        if (shouldSkipElement(node.parentElement)) return NodeFilter.FILTER_REJECT;
        if (!node.nodeValue || !node.nodeValue.trim()) return NodeFilter.FILTER_REJECT;
        return NodeFilter.FILTER_ACCEPT;
      }
    });

    const textNodes = [];
    while (walker.nextNode()) {
      textNodes.push(walker.currentNode);
    }

    textNodes.forEach((node) => {
      const current = node.nodeValue;
      const translated = translateText(current);
      if (translated !== current) {
        node.nodeValue = translated;
      }
    });
  };

  const translateSubtree = (rootElement = document.body) => {
    if (!(rootElement instanceof Element) && rootElement !== document.body) return;
    const scope = rootElement instanceof Element ? rootElement : document.body;

    translateElementAttributes(scope);
    scope.querySelectorAll("*").forEach((element) => translateElementAttributes(element));
    translateTextNodes(scope);
  };

  translateSubtree(document.body);

  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.type === "childList") {
        mutation.addedNodes.forEach((node) => {
          if (node instanceof Element) {
            translateSubtree(node);
          } else if (node instanceof Text && node.parentElement instanceof Element) {
            const current = node.nodeValue;
            const translated = translateText(current);
            if (translated !== current) {
              node.nodeValue = translated;
            }
          }
        });
      }

      if (mutation.type === "attributes" && mutation.target instanceof Element) {
        translateElementAttributes(mutation.target);
      }
    });
  });

  observer.observe(document.body, {
    childList: true,
    subtree: true,
    attributes: true,
    attributeFilter: attributeNames
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

  const isRtl = document.documentElement.getAttribute("dir") === "rtl";
  const desktopKey = "dashboard_sidebar_collapsed";
  const hiddenClass = isRtl ? "translate-x-full" : "-translate-x-full";
  const openClass = "translate-x-0";

  function applyDesktopState() {
    if (window.innerWidth < 768) return;
    const collapsed = localStorage.getItem(desktopKey) === "1";
    body.classList.toggle("dashboard-sidebar-collapsed", collapsed);
  }

  function openMobileSidebar() {
    if (window.innerWidth >= 768) return;
    sidebar.classList.remove(hiddenClass);
    sidebar.classList.add(openClass);
    overlay?.classList.remove("hidden");
  }

  function closeMobileSidebar() {
    if (window.innerWidth >= 768) return;
    sidebar.classList.remove(openClass);
    sidebar.classList.add(hiddenClass);
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
      sidebar.classList.remove(hiddenClass);
      sidebar.classList.remove(openClass);
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

  setupLanguageMenus();
  setupClientSideArabicTranslations();
  setupDashboardSidebar();
});
