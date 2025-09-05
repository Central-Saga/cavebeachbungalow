// Theme Manager for Puco Rooftop Coworking
class ThemeManager {
    constructor() {
        this.theme = localStorage.getItem("theme") || "system";
        this.isInitialized = false;
        this.init();
    }

    init() {
        // Wait for Alpine.js to initialize first
        console.log("Theme Manager initialized with theme:", this.theme);
        this.setupEventListeners();
        this.setupSystemThemeListener();

        // Mark as initialized
        this.isInitialized = true;
    }

    getSystemTheme() {
        return window.matchMedia("(prefers-color-scheme: dark)").matches
            ? "dark"
            : "light";
    }

    applyTheme(theme) {
        console.log("Theme Manager: Applying theme:", theme);

        let actualTheme = theme;

        if (theme === "system") {
            actualTheme = this.getSystemTheme();
        }

        // Remove existing theme classes
        document.documentElement.classList.remove("light", "dark");

        // Add new theme class
        document.documentElement.classList.add(actualTheme);

        // Update localStorage
        localStorage.setItem("theme", theme);

        // Set data attribute
        document.documentElement.setAttribute("data-theme", theme);

        // Dispatch custom event
        window.dispatchEvent(
            new CustomEvent("theme-changed", {
                detail: { theme: actualTheme, preference: theme },
            })
        );

        // Update meta theme-color if exists
        this.updateMetaThemeColor(actualTheme);

        console.log("Theme Manager: Theme applied successfully:", actualTheme);
    }

    updateMetaThemeColor(theme) {
        let metaThemeColor = document.querySelector('meta[name="theme-color"]');
        if (!metaThemeColor) {
            metaThemeColor = document.createElement("meta");
            metaThemeColor.name = "theme-color";
            document.head.appendChild(metaThemeColor);
        }

        if (theme === "dark") {
            metaThemeColor.content = "#111827"; // dark gray
        } else {
            metaThemeColor.content = "#ffffff"; // white
        }
    }

    setupEventListeners() {
        // Listen for theme changes from other components
        window.addEventListener("flux:theme-changed", (e) => {
            console.log("Theme Manager: Flux theme change event:", e.detail);
            this.theme = e.detail.theme;
            this.applyTheme(this.theme);
        });

        // Listen for custom theme change events
        window.addEventListener("theme-changed", (e) => {
            console.log(
                "Theme Manager: Received theme-changed event:",
                e.detail
            );
            // Don't reapply if it's the same theme to avoid loops
            if (e.detail.theme !== this.theme) {
                this.theme = e.detail.theme;
                this.applyTheme(this.theme);
            }
        });

        // Listen for storage changes (when theme is changed in another tab)
        window.addEventListener("storage", (e) => {
            if (e.key === "theme") {
                console.log("Theme Manager: Storage event:", e.newValue);
                this.theme = e.newValue || "system";
                this.applyTheme(this.theme);
            }
        });

        // Check for theme changes less frequently and only if needed
        let themeCheckInterval = setInterval(() => {
            const currentTheme = localStorage.getItem("theme") || "system";
            if (currentTheme !== this.theme) {
                console.log(
                    "Theme Manager: Detected theme change:",
                    currentTheme
                );
                this.theme = currentTheme;
                this.applyTheme(this.theme);
            }
        }, 10000); // Check every 10 seconds instead of 5
    }

    setupSystemThemeListener() {
        const mediaQuery = window.matchMedia("(prefers-color-scheme: dark)");

        mediaQuery.addEventListener("change", (e) => {
            console.log("Theme Manager: System preference changed:", e.matches);
            // Only apply system theme change if user hasn't explicitly chosen a theme
            if (this.theme === "system") {
                this.applyTheme("system");
            } else {
                console.log(
                    "Theme Manager: User has explicit theme choice, ignoring system change"
                );
            }
        });
    }

    // Public method to change theme
    changeTheme(newTheme) {
        console.log("Theme Manager: changeTheme called:", newTheme);
        this.theme = newTheme;
        this.applyTheme(newTheme);
    }

    // Get current theme preference
    getCurrentTheme() {
        return this.theme;
    }

    // Get actual applied theme
    getAppliedTheme() {
        if (this.theme === "system") {
            return this.getSystemTheme();
        }
        return this.theme;
    }
}

// Don't apply theme immediately when script loads - let Alpine.js handle it
console.log(
    "Theme Manager script loaded, waiting for Alpine.js initialization"
);

// Initialize theme manager when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    // Wait a bit longer for Alpine.js to fully initialize
    setTimeout(() => {
        window.themeManager = new ThemeManager();
        console.log("Theme Manager initialized on DOM ready");
    }, 200);
});

// Export for use in other scripts
if (typeof module !== "undefined" && module.exports) {
    module.exports = ThemeManager;
}
