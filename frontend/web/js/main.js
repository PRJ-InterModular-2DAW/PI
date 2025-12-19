document.addEventListener("DOMContentLoaded", function () {
    const hamburgerBtn = document.getElementById("hamburgerBtn");

    // Select all nav items that are hidden by default on mobile
    // Plus the new mobile-extra items
    const navItems = [
        document.getElementById("nav-ropa"),
        document.getElementById("nav-calzado"),
        document.getElementById("nav-accesorios"),
        document.getElementById("nav-relojes"),
        ...document.querySelectorAll(".mobile-extra")
    ];

    if (hamburgerBtn) {
        hamburgerBtn.addEventListener("click", function () {
            navItems.forEach((item) => {
                if (item) {
                    // Toggle logic
                    // If element has style.display set to block, we hide it.
                    // Otherwise we show it.
                    // Note: we use regular style.display here assuming CSS !important was removed
                    // OR we force it. Since mediaQuery had !important removed, this should work.
                    // If not, we fall back to setProperty.

                    if (item.style.display === "block") {
                        item.style.display = "none";
                    } else {
                        item.style.display = "block";
                    }
                }
            });
        });
    }

    // Window Resize Listener to fix Desktop View issues
    // If user opens menu in mobile (display: block inline) -> then resizes to desktop,
    // we want to REMOVE that inline style so desktop CSS takes over (display: block or none as appropriate).
    window.addEventListener("resize", function () {
        if (window.innerWidth > 768) {
            navItems.forEach(item => {
                if (item) {
                    item.style.display = ""; // Clears inline style
                }
            });
        }
    });
});
