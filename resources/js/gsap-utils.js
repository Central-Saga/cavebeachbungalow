// GSAP Utilities - Global helper functions for animations
// This file provides reusable GSAP animation utilities across the application

class GSAPUtils {
    constructor() {
        this.isInitialized = false;
        this.init();
    }

    init() {
        // Wait for GSAP to be available
        if (typeof gsap !== "undefined") {
            this.isInitialized = true;
            console.log(
                "GSAP Utils initialized with GSAP version:",
                gsap.version
            );
        } else {
            // Check again after a short delay
            setTimeout(() => this.init(), 100);
        }
    }

    // Smooth scrolling utility
    smoothScrollTo(targetId, offset = 100) {
        if (!this.isInitialized) {
            console.warn("GSAP Utils not initialized yet");
            return;
        }

        const targetSection = document.querySelector(targetId);
        if (targetSection) {
            const offsetTop = targetSection.offsetTop - offset;
            gsap.to(window, {
                duration: 1,
                scrollTo: offsetTop,
                ease: "power2.out",
            });
        }
    }

    // Navbar animation utility
    animateNavbar(navbar, isScrolled) {
        if (!this.isInitialized || !navbar) return;

        if (isScrolled) {
            gsap.to(navbar, {
                duration: 0.8,
                ease: "power3.out",
                css: {
                    top: "0px",
                    left: "0px",
                    transform: "translateX(0%)",
                    width: "100%",
                    maxWidth: "none",
                    borderRadius: "0px",
                    margin: "0px",
                    backgroundColor: "rgba(255, 255, 255, 0.95)",
                },
            });
        } else {
            gsap.to(navbar, {
                duration: 0.8,
                ease: "power3.out",
                css: {
                    top: "1rem",
                    left: "50%",
                    transform: "translateX(-50%)",
                    width: "calc(100% - 2rem)",
                    maxWidth: "1200px",
                    borderRadius: "9999px",
                    margin: "0 1rem",
                    backgroundColor: "rgba(255, 255, 255, 0.9)",
                },
            });
        }
    }

    // Throttled scroll utility
    throttleScroll(callback, delay = 16) {
        let ticking = false;
        return function () {
            if (!ticking) {
                requestAnimationFrame(() => {
                    callback();
                    ticking = false;
                });
                ticking = true;
            }
        };
    }

    // Fade in animation utility
    fadeIn(element, duration = 0.5, delay = 0) {
        if (!this.isInitialized || !element) return;

        gsap.fromTo(
            element,
            { opacity: 0, y: 20 },
            {
                opacity: 1,
                y: 0,
                duration: duration,
                delay: delay,
                ease: "power2.out",
            }
        );
    }

    // Slide in from left utility
    slideInLeft(element, duration = 0.6, delay = 0) {
        if (!this.isInitialized || !element) return;

        gsap.fromTo(
            element,
            { x: -100, opacity: 0 },
            {
                x: 0,
                opacity: 1,
                duration: duration,
                delay: delay,
                ease: "power3.out",
            }
        );
    }

    // Slide in from right utility
    slideInRight(element, duration = 0.6, delay = 0) {
        if (!this.isInitialized || !element) return;

        gsap.fromTo(
            element,
            { x: 100, opacity: 0 },
            {
                x: 0,
                opacity: 1,
                duration: duration,
                delay: delay,
                ease: "power3.out",
            }
        );
    }

    // Scale in animation utility
    scaleIn(element, duration = 0.5, delay = 0) {
        if (!this.isInitialized || !element) return;

        gsap.fromTo(
            element,
            { scale: 0, opacity: 0 },
            {
                scale: 1,
                opacity: 1,
                duration: duration,
                delay: delay,
                ease: "back.out(1.7)",
            }
        );
    }

    // Stagger animation for multiple elements
    staggerAnimation(elements, animationType = "fadeIn", staggerDelay = 0.1) {
        if (!this.isInitialized || !elements || elements.length === 0) return;

        const elementArray = Array.from(elements);

        switch (animationType) {
            case "fadeIn":
                gsap.fromTo(
                    elementArray,
                    { opacity: 0, y: 30 },
                    {
                        opacity: 1,
                        y: 0,
                        duration: 0.6,
                        stagger: staggerDelay,
                        ease: "power2.out",
                    }
                );
                break;
            case "slideInLeft":
                gsap.fromTo(
                    elementArray,
                    { x: -50, opacity: 0 },
                    {
                        x: 0,
                        opacity: 1,
                        duration: 0.6,
                        stagger: staggerDelay,
                        ease: "power3.out",
                    }
                );
                break;
            case "scaleIn":
                gsap.fromTo(
                    elementArray,
                    { scale: 0, opacity: 0 },
                    {
                        scale: 1,
                        opacity: 1,
                        duration: 0.5,
                        stagger: staggerDelay,
                        ease: "back.out(1.7)",
                    }
                );
                break;
        }
    }

    // Parallax effect utility
    parallax(element, speed = 0.5) {
        if (!this.isInitialized || !element) return;

        gsap.to(element, {
            yPercent: -50 * speed,
            ease: "none",
            scrollTrigger: {
                trigger: element,
                start: "top bottom",
                end: "bottom top",
                scrub: true,
            },
        });
    }

    // Text reveal animation
    textReveal(element, duration = 1, delay = 0) {
        if (!this.isInitialized || !element) return;

        // Split text into characters if it's a text element
        if (
            element.tagName === "H1" ||
            element.tagName === "H2" ||
            element.tagName === "H3" ||
            element.tagName === "H4" ||
            element.tagName === "H5" ||
            element.tagName === "H6" ||
            element.tagName === "P" ||
            element.tagName === "SPAN"
        ) {
            const text = element.textContent;
            element.innerHTML = "";

            text.split("").forEach((char, index) => {
                const span = document.createElement("span");
                span.textContent = char === " " ? "\u00A0" : char;
                span.style.display = "inline-block";
                element.appendChild(span);
            });

            const chars = element.querySelectorAll("span");
            gsap.fromTo(
                chars,
                { opacity: 0, y: 20 },
                {
                    opacity: 1,
                    y: 0,
                    duration: duration,
                    delay: delay,
                    stagger: 0.02,
                    ease: "power2.out",
                }
            );
        }
    }
}

// Initialize GSAP Utils when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
    // Create global instance
    window.gsapUtils = new GSAPUtils();

    // Also keep the old global functions for backward compatibility
    window.GSAPUtils = {
        smoothScrollTo: (targetId, offset) =>
            window.gsapUtils.smoothScrollTo(targetId, offset),
        animateNavbar: (navbar, isScrolled) =>
            window.gsapUtils.animateNavbar(navbar, isScrolled),
        throttleScroll: (callback, delay) =>
            window.gsapUtils.throttleScroll(callback, delay),
        fadeIn: (element, duration, delay) =>
            window.gsapUtils.fadeIn(element, duration, delay),
        slideInLeft: (element, duration, delay) =>
            window.gsapUtils.slideInLeft(element, duration, delay),
        slideInRight: (element, duration, delay) =>
            window.gsapUtils.slideInRight(element, duration, delay),
        scaleIn: (element, duration, delay) =>
            window.gsapUtils.scaleIn(element, duration, delay),
        staggerAnimation: (elements, animationType, staggerDelay) =>
            window.gsapUtils.staggerAnimation(
                elements,
                animationType,
                staggerDelay
            ),
        parallax: (element, speed) => window.gsapUtils.parallax(element, speed),
        textReveal: (element, duration, delay) =>
            window.gsapUtils.textReveal(element, duration, delay),
    };
});

// Export for module systems
if (typeof module !== "undefined" && module.exports) {
    module.exports = GSAPUtils;
}
