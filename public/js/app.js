// app.js — GLPI-like header & drawer (FINAL, bersih)
document.addEventListener("DOMContentLoaded", () => {
    /* ==== UTIL ==== */
    const $ = (s, r = document) => r.querySelector(s);
    const $$ = (s, r = document) => [...r.querySelectorAll(s)];
    const body = document.body;

    /* ==== Selectors (sesuai Blade yang kita pakai) ==== */
    const btnMobileNav = $("#btnMobileNav");
    const btnCloseDrawer = $("#btnCloseDrawer");
    const mobileNav = $("#mobileNav");
    const backdrop = $("#navBackdrop");

    // (opsional) dropdown user jika ada
    const userToggle = $("#userToggle");
    const userWrap = $("#userDropdown");

    /* ==== Helpers ==== */
    const lockScroll = () => body.classList.add("no-scroll");
    const unlockScroll = () => body.classList.remove("no-scroll");

    const openDrawer = () => {
        if (!mobileNav || !backdrop) return;
        mobileNav.dataset.open = "true";
        backdrop.dataset.show = "true";
        backdrop.hidden = false;
        btnMobileNav?.setAttribute("data-open", "true");
        btnMobileNav?.setAttribute("aria-expanded", "true");
        mobileNav.setAttribute("aria-hidden", "false");
        lockScroll();
        mobileNav.focus({ preventScroll: true });
    };

    const closeDrawer = () => {
        if (!mobileNav || !backdrop) return;
        mobileNav.dataset.open = "false";
        backdrop.dataset.show = "false";
        btnMobileNav?.setAttribute("data-open", "false");
        btnMobileNav?.setAttribute("aria-expanded", "false");
        mobileNav.setAttribute("aria-hidden", "true");
        unlockScroll();
        setTimeout(() => {
            backdrop.hidden = true;
        }, 200);
    };

    /* ==== Bindings Drawer ==== */
    btnMobileNav?.addEventListener("click", () => {
        mobileNav?.dataset.open === "true" ? closeDrawer() : openDrawer();
    });
    btnCloseDrawer?.addEventListener("click", closeDrawer);
    backdrop?.addEventListener("click", closeDrawer);

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeDrawer();
    });

    // Tutup saat klik item menu (dukung dua kelas: .mobile-link & .glpi-item)
    [...$$(".mobile-link"), ...$$(".glpi-item")].forEach((a) => {
        // jika <button> di dalam form logout, tetap close
        a.addEventListener("click", closeDrawer);
    });

    // Auto-close saat masuk desktop
    window.addEventListener("resize", () => {
        if (window.innerWidth >= 992) closeDrawer();
    });

    /* ==== (Opsional) User dropdown jika disertakan di topbar ==== */
    const openUserMenu = () => {
        if (!userWrap || !userToggle) return;
        userWrap.dataset.open = "true";
        userToggle.setAttribute("aria-expanded", "true");
    };
    const closeUserMenu = () => {
        if (!userWrap || !userToggle) return;
        userWrap.dataset.open = "false";
        userToggle.setAttribute("aria-expanded", "false");
    };
    userToggle?.addEventListener("click", (e) => {
        e.stopPropagation();
        userWrap?.dataset.open === "true" ? closeUserMenu() : openUserMenu();
    });
    document.addEventListener("click", (e) => {
        if (!userWrap) return;
        if (!userWrap.contains(e.target)) closeUserMenu();
    });

    /* ==== (Opsional) Flash auto-dismiss ==== */
    const flash = $("#flashAlert");
    if (flash) {
        setTimeout(() => {
            flash.style.transition = "opacity .4s ease";
            flash.style.opacity = "0";
            setTimeout(() => flash.remove(), 400);
        }, 3500);
    }
});
