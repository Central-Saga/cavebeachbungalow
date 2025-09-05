// Theme Enforcer - Memastikan tema yang dipilih user tetap konsisten
(function () {
    "use strict";

    let userTheme = null;
    let isEnforcing = false;

    // Fungsi untuk mendapatkan tema yang seharusnya diterapkan
    function getEnforcedTheme() {
        const savedTheme = localStorage.getItem("theme");

        if (savedTheme && savedTheme !== "system") {
            // User telah memilih tema secara eksplisit
            return savedTheme;
        }

        // User memilih system, ikuti preferensi browser
        return window.matchMedia("(prefers-color-scheme: dark)").matches
            ? "dark"
            : "light";
    }

    // Fungsi untuk memaksa tema
    function enforceTheme(theme) {
        if (isEnforcing) return;

        isEnforcing = true;
        console.log("Theme Enforcer: Memaksa tema:", theme);

        // Hapus semua kelas tema yang ada
        document.documentElement.classList.remove("light", "dark");

        // Tambahkan kelas tema yang dipilih
        document.documentElement.classList.add(theme);

        // Set data attribute
        document.documentElement.setAttribute("data-theme", theme);

        // Set color-scheme meta tag
        const metaColorScheme = document.querySelector(
            'meta[name="color-scheme"]'
        );
        if (metaColorScheme) {
            metaColorScheme.content = theme;
        } else {
            const newMeta = document.createElement("meta");
            newMeta.name = "color-scheme";
            newMeta.content = theme;
            document.head.appendChild(newMeta);
        }

        // Override CSS variables jika ada
        if (theme === "light") {
            document.documentElement.style.setProperty("--tw-bg-opacity", "1");
            document.documentElement.style.setProperty(
                "--tw-text-opacity",
                "1"
            );
        } else {
            document.documentElement.style.setProperty("--tw-bg-opacity", "1");
            document.documentElement.style.setProperty(
                "--tw-text-opacity",
                "1"
            );
        }

        isEnforcing = false;
        console.log("Theme Enforcer: Tema berhasil dipaksa:", theme);
    }

    // Fungsi untuk memantau perubahan tema
    function watchThemeChanges() {
        // Observer untuk memantau perubahan pada documentElement
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (
                    mutation.type === "attributes" &&
                    mutation.attributeName === "class"
                ) {
                    const enforcedTheme = getEnforcedTheme();
                    const currentClasses = document.documentElement.classList;

                    // Jika ada kelas tema yang tidak sesuai dengan pilihan user
                    if (
                        enforcedTheme === "light" &&
                        currentClasses.contains("dark")
                    ) {
                        console.log(
                            "Theme Enforcer: Mencegah pemaksaan dark mode"
                        );
                        enforceTheme("light");
                    } else if (
                        enforcedTheme === "dark" &&
                        currentClasses.contains("light")
                    ) {
                        console.log(
                            "Theme Enforcer: Mencegah pemaksaan light mode"
                        );
                        enforceTheme("dark");
                    }
                }
            });
        });

        // Mulai observasi
        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ["class"],
        });

        console.log("Theme Enforcer: Observer tema aktif");
    }

    // Fungsi untuk memantau perubahan localStorage
    function watchLocalStorage() {
        window.addEventListener("storage", function (e) {
            if (e.key === "theme") {
                console.log(
                    "Theme Enforcer: Perubahan localStorage terdeteksi:",
                    e.newValue
                );
                userTheme = e.newValue;
                const enforcedTheme = getEnforcedTheme();
                enforceTheme(enforcedTheme);
            }
        });
    }

    // Fungsi untuk memantau perubahan preferensi sistem
    function watchSystemPreference() {
        const mediaQuery = window.matchMedia("(prefers-color-scheme: dark)");

        mediaQuery.addEventListener("change", function (e) {
            const savedTheme = localStorage.getItem("theme");

            // Hanya ikuti perubahan sistem jika user memilih 'system'
            if (!savedTheme || savedTheme === "system") {
                console.log(
                    "Theme Enforcer: Preferensi sistem berubah:",
                    e.matches ? "dark" : "light"
                );
                const enforcedTheme = getEnforcedTheme();
                enforceTheme(enforcedTheme);
            } else {
                console.log(
                    "Theme Enforcer: User telah memilih tema eksplisit, mengabaikan perubahan sistem"
                );
            }
        });
    }

    // Fungsi untuk memantau perubahan tema dari komponen lain
    function watchThemeEvents() {
        window.addEventListener("theme-changed", function (e) {
            console.log(
                "Theme Enforcer: Event perubahan tema diterima:",
                e.detail
            );
            const enforcedTheme = getEnforcedTheme();
            enforceTheme(enforcedTheme);
        });
    }

    // Fungsi untuk memeriksa dan memperbaiki tema secara berkala
    function periodicThemeCheck() {
        setInterval(function () {
            const enforcedTheme = getEnforcedTheme();
            const currentClasses = document.documentElement.classList;

            // Jika tema tidak sesuai, paksa kembali
            if (
                enforcedTheme === "light" &&
                !currentClasses.contains("light")
            ) {
                console.log(
                    "Theme Enforcer: Memperbaiki tema yang tidak sesuai"
                );
                enforceTheme("light");
            } else if (
                enforcedTheme === "dark" &&
                !currentClasses.contains("dark")
            ) {
                console.log(
                    "Theme Enforcer: Memperbaiki tema yang tidak sesuai"
                );
                enforceTheme("dark");
            }
        }, 2000); // Periksa setiap 2 detik
    }

    // Fungsi inisialisasi
    function init() {
        console.log("Theme Enforcer: Memulai inisialisasi");

        // Tunggu DOM siap
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", init);
            return;
        }

        // Dapatkan tema yang seharusnya diterapkan
        userTheme = getEnforcedTheme();

        // Terapkan tema
        enforceTheme(userTheme);

        // Mulai semua pemantauan
        watchThemeChanges();
        watchLocalStorage();
        watchSystemPreference();
        watchThemeEvents();
        periodicThemeCheck();

        console.log(
            "Theme Enforcer: Inisialisasi selesai, tema aktif:",
            userTheme
        );
    }

    // Mulai Theme Enforcer
    init();

    // Expose untuk debugging
    window.themeEnforcer = {
        getEnforcedTheme: getEnforcedTheme,
        enforceTheme: enforceTheme,
        userTheme: function () {
            return userTheme;
        },
    };
})();
