// Main application JavaScript file
// This file demonstrates how to use the global GSAP utilities

// Example usage of GSAP utilities
document.addEventListener("DOMContentLoaded", function () {
    console.log("App.js loaded");
    console.log("GSAP available:", !!window.gsap);
    console.log("GSAP Utils available:", !!window.gsapUtils);
    console.log("GSAPUtils (legacy) available:", !!window.GSAPUtils);

    // Wait for GSAP utilities to be available
    let attempts = 0;
    const maxAttempts = 50; // 5 seconds max wait

    function checkGSAPUtils() {
        attempts++;

        if (window.gsapUtils) {
            console.log("GSAP Utils available in app.js");

            // Example: Add fade-in animation to elements with class 'animate-fade-in'
            const fadeElements = document.querySelectorAll(".animate-fade-in");
            if (fadeElements.length > 0) {
                window.gsapUtils.staggerAnimation(fadeElements, "fadeIn", 0.2);
            }

            // Example: Add slide-in animation to elements with class 'animate-slide-left'
            const slideLeftElements = document.querySelectorAll(
                ".animate-slide-left"
            );
            slideLeftElements.forEach((element, index) => {
                window.gsapUtils.slideInLeft(element, 0.6, index * 0.1);
            });

            // Example: Add scale-in animation to elements with class 'animate-scale-in'
            const scaleElements =
                document.querySelectorAll(".animate-scale-in");
            scaleElements.forEach((element, index) => {
                window.gsapUtils.scaleIn(element, 0.5, index * 0.15);
            });

            // Example: Add text reveal animation to headings
            // DISABLED: Text reveal causes unwanted text splitting into spans
            // const textRevealHeadings =
            //     document.querySelectorAll(".text-reveal");
            // textRevealHeadings.forEach((heading, index) => {
            //     window.gsapUtils.textReveal(heading, 1, index * 0.2);
            // });

            // Example: Add parallax effect to elements with class 'parallax'
            const parallaxElements = document.querySelectorAll(".parallax");
            parallaxElements.forEach((element) => {
                window.gsapUtils.parallax(element, 0.3);
            });
        } else if (attempts < maxAttempts) {
            console.log(
                `Waiting for GSAP Utils... Attempt ${attempts}/${maxAttempts}`
            );
            setTimeout(checkGSAPUtils, 100);
        } else {
            console.error("GSAP Utils not available after maximum attempts");
        }
    }

    // Start checking for GSAP Utils
    checkGSAPUtils();
});

// Export for module systems
if (typeof module !== "undefined" && module.exports) {
    module.exports = {};
}
