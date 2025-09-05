// Theme Initializer - Ensures theme is applied correctly on all pages
(function () {
    "use strict";

    // Function to get the actual theme to apply
    function getActualTheme() {
        const savedTheme = localStorage.getItem("theme");

        if (!savedTheme || savedTheme === "system") {
            // Only follow browser preference if user hasn't made a choice
            return window.matchMedia("(prefers-color-scheme: dark)").matches
                ? "dark"
                : "light";
        }

        // User has explicitly chosen a theme, use it
        return savedTheme;
    }

    // Function to apply theme
    function applyTheme(theme) {
        console.log("Theme Initializer: Applying theme:", theme);

        // Remove existing theme classes
        document.documentElement.classList.remove("light", "dark");

        // Add the chosen theme
        document.documentElement.classList.add(theme);

        // Set data attribute for additional styling
        document.documentElement.setAttribute("data-theme", theme);

        console.log("Theme Initializer: Theme applied:", theme);
    }

    // Function to initialize theme
    function initTheme() {
        const actualTheme = getActualTheme();
        console.log("Theme Initializer: Initializing with theme:", actualTheme);
        applyTheme(actualTheme);
    }

    // Don't apply theme immediately - wait for Alpine.js to initialize first
    console.log("Theme Initializer loaded, waiting for Alpine.js");

    // Apply theme after a longer delay to let Alpine.js initialize
    setTimeout(() => {
        // Check if Alpine.js has already applied a theme
        const hasAlpineTheme =
            document.documentElement.classList.contains("light") ||
            document.documentElement.classList.contains("dark");

        if (!hasAlpineTheme) {
            console.log(
                "Theme Initializer: No Alpine.js theme detected, applying initial theme"
            );
            initTheme();
        } else {
            console.log(
                "Theme Initializer: Alpine.js theme already applied, skipping initial theme"
            );
        }
    }, 300); // Increased delay to 300ms

    // Listen for theme changes from other sources
    window.addEventListener("storage", function (e) {
        if (e.key === "theme") {
            console.log("Theme Initializer: Storage event:", e.newValue);
            const newTheme = e.newValue || "system";
            const actualTheme =
                newTheme === "system"
                    ? window.matchMedia("(prefers-color-scheme: dark)").matches
                        ? "dark"
                        : "light"
                    : newTheme;
            applyTheme(actualTheme);
        }
    });

    // Listen for custom theme change events
    window.addEventListener("theme-changed", function (e) {
        console.log("Theme Initializer: theme-changed event:", e.detail);
        const theme = e.detail.theme;
        if (theme) {
            applyTheme(theme);
        }
    });

    // Override system theme listener to respect user choice
    const mediaQuery = window.matchMedia("(prefers-color-scheme: dark)");
    mediaQuery.addEventListener("change", function (e) {
        const savedTheme = localStorage.getItem("theme");
        console.log(
            "Theme Initializer: System preference changed, saved theme:",
            savedTheme
        );

        // Only change theme if user hasn't explicitly chosen one
        if (!savedTheme || savedTheme === "system") {
            const newTheme = e.matches ? "dark" : "light";
            console.log(
                "Theme Initializer: Applying system preference change:",
                newTheme
            );
            applyTheme(newTheme);
        } else {
            console.log(
                "Theme Initializer: User has explicit theme choice, ignoring system change"
            );
        }
    });

    // Expose functions globally for debugging
    window.themeInitializer = {
        getActualTheme: getActualTheme,
        applyTheme: applyTheme,
        initTheme: initTheme,
    };

    console.log(
        "🔧 Theme Initializer loaded. Use window.themeInitializer.initTheme() to apply theme manually."
    );
})();
