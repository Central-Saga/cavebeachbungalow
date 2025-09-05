// Theme Debug - Helps debug theme issues
(function () {
    "use strict";

    // Function to log theme information
    function logThemeInfo() {
        const savedTheme = localStorage.getItem("theme");
        const currentClasses = document.documentElement.classList.toString();
        const dataTheme = document.documentElement.getAttribute("data-theme");
        const systemPrefersDark = window.matchMedia(
            "(prefers-color-scheme: dark)"
        ).matches;

        console.group("🔍 Theme Debug Information");
        console.log("Saved theme in localStorage:", savedTheme);
        console.log("Current HTML classes:", currentClasses);
        console.log("data-theme attribute:", dataTheme);
        console.log("System prefers dark:", systemPrefersDark);
        console.log("Document element:", document.documentElement);
        console.log("Theme manager available:", !!window.themeManager);
        console.log("Theme initializer available:", !!window.themeInitializer);
        console.log("Alpine.js available:", !!window.Alpine);
        console.groupEnd();
    }

    // Function to force apply theme
    function forceApplyTheme(theme) {
        console.log("🔄 Force applying theme:", theme);

        // Remove existing classes
        document.documentElement.classList.remove("light", "dark");

        // Add new theme
        document.documentElement.classList.add(theme);
        document.documentElement.setAttribute("data-theme", theme);

        // Update localStorage
        localStorage.setItem("theme", theme);

        console.log("✅ Theme forced to:", theme);
        logThemeInfo();
    }

    // Function to reset to system theme
    function resetToSystem() {
        console.log("🔄 Resetting to system theme");
        localStorage.removeItem("theme");

        const systemTheme = window.matchMedia("(prefers-color-scheme: dark)")
            .matches
            ? "dark"
            : "light";
        forceApplyTheme(systemTheme);
    }

    // Function to check theme consistency
    function checkThemeConsistency() {
        const savedTheme = localStorage.getItem("theme");
        const hasLightClass =
            document.documentElement.classList.contains("light");
        const hasDarkClass =
            document.documentElement.classList.contains("dark");
        const dataTheme = document.documentElement.getAttribute("data-theme");

        console.group("🔍 Theme Consistency Check");
        console.log("localStorage theme:", savedTheme);
        console.log("Has light class:", hasLightClass);
        console.log("Has dark class:", hasDarkClass);
        console.log("data-theme attribute:", dataTheme);

        if (savedTheme === "light" && !hasLightClass) {
            console.warn(
                "⚠️ Inconsistency: localStorage says light but no light class"
            );
        } else if (savedTheme === "dark" && !hasDarkClass) {
            console.warn(
                "⚠️ Inconsistency: localStorage says dark but no dark class"
            );
        } else if (savedTheme && savedTheme !== "system") {
            console.log("✅ Theme consistency: OK");
        }

        console.groupEnd();
    }

    // Expose debug functions globally
    window.themeDebug = {
        logInfo: logThemeInfo,
        forceTheme: forceApplyTheme,
        resetSystem: resetToSystem,
        checkConsistency: checkThemeConsistency,
    };

    // Log theme info when page loads
    document.addEventListener("DOMContentLoaded", function () {
        setTimeout(logThemeInfo, 2000); // Wait a bit for other scripts to run
        setTimeout(checkThemeConsistency, 3000); // Check consistency after everything loads
    });

    // Log theme info when theme changes
    window.addEventListener("theme-changed", function (e) {
        console.log("🎨 Theme changed event:", e.detail);
        setTimeout(logThemeInfo, 100);
        setTimeout(checkThemeConsistency, 200);
    });

    // Add debug info to console
    console.log(
        "🔧 Theme Debug loaded. Use window.themeDebug.logInfo() to see theme information."
    );
    console.log("Available commands:");
    console.log("- window.themeDebug.logInfo() - Show theme info");
    console.log("- window.themeDebug.forceTheme('light') - Force light theme");
    console.log("- window.themeDebug.forceTheme('dark') - Force dark theme");
    console.log("- window.themeDebug.resetSystem() - Reset to system theme");
    console.log(
        "- window.themeDebug.checkConsistency() - Check theme consistency"
    );
})();
