(function () {
    function qs(s, el) {
        return (el || document).querySelector(s);
    }
    function qsa(s, el) {
        return (el || document).querySelectorAll(s);
    }

    const body = document.body;
    const btn = qs("#btnMobileNav");
    const mobileNav = qs("#mobileNav");
    const btnClose = qs("[data-close-mobile-nav]", mobileNav);

    function openNav() {
        body.classList.add("nav-open");
        mobileNav?.setAttribute("aria-hidden", "false");
        btn?.setAttribute("aria-expanded", "true");
    }
    function closeNav() {
        body.classList.remove("nav-open");
        mobileNav?.setAttribute("aria-hidden", "true");
        btn?.setAttribute("aria-expanded", "false");
    }

    btn &&
        btn.addEventListener("click", () => {
            if (body.classList.contains("nav-open")) closeNav();
            else openNav();
        });
    btnClose && btnClose.addEventListener("click", closeNav);
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeNav();
    });

    // User dropdown
    const userToggle = qs("#userToggle");
    const userMenu = qs("#userMenu");
    function closeUserMenu() {
        userMenu?.classList.remove("open");
        userToggle?.setAttribute("aria-expanded", "false");
    }
    function toggleUserMenu() {
        userMenu?.classList.toggle("open");
        userToggle?.setAttribute(
            "aria-expanded",
            String(userMenu?.classList.contains("open"))
        );
    }

    userToggle &&
        userToggle.addEventListener("click", (e) => {
            e.stopPropagation();
            toggleUserMenu();
        });
    document.addEventListener("click", (e) => {
        if (!userMenu) return;
        const within =
            userMenu.contains(e.target) || userToggle.contains(e.target);
        if (!within) closeUserMenu();
    });
})();
