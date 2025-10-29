/**
 * ================================
 * app.js — drawer, user menu, flash, confirm delete (Final)
 * ================================
 */

import Swal from "sweetalert2";
window.Swal = Swal;

document.addEventListener("DOMContentLoaded", () => {
    /* ==== UTIL ==== */
    const $ = (s, r = document) => r.querySelector(s);
    const $$ = (s, r = document) => [...r.querySelectorAll(s)];
    const body = document.body;

    /* ==== Selectors ==== */
    const btnMobileNav = $("#btnMobileNav");
    const btnCloseDrawer = $("#btnCloseDrawer");
    const mobileNav = $("#mobileNav");
    const backdrop = $("#navBackdrop");
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

    /* ==== Drawer bindings ==== */
    btnMobileNav?.addEventListener("click", () => {
        mobileNav?.dataset.open === "true" ? closeDrawer() : openDrawer();
    });
    btnCloseDrawer?.addEventListener("click", closeDrawer);
    backdrop?.addEventListener("click", closeDrawer);
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeDrawer();
    });
    [...$$(".mobile-link"), ...$$(".glpi-item")].forEach((a) => {
        a.addEventListener("click", closeDrawer);
    });
    window.addEventListener("resize", () => {
        if (window.innerWidth >= 992) closeDrawer();
    });

    /* ==== User dropdown ==== */
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

    /* ==== Flash auto-dismiss ==== */
    const flash = $("#flashAlert");
    if (flash) {
        setTimeout(() => {
            flash.style.transition = "opacity .4s ease";
            flash.style.opacity = "0";
            setTimeout(() => flash.remove(), 400);
        }, 3500);
    }

    /* ==== SweetAlert flash success (set via Laravel session) ==== */
    const flashSuccess = document.querySelector('meta[name="flash-success"]');
    if (flashSuccess && flashSuccess.content) {
        Swal.fire({
            title: "Berhasil!",
            text: flashSuccess.content,
            icon: "success",
            confirmButtonColor: "#0d6efd",
            confirmButtonText: "OK",
        });
    }
});

/* ==== Global confirmDelete() ==== */
window.confirmDelete = function (id) {
    Swal.fire({
        title: "Hapus dokumen ini?",
        text: "Data akan hilang secara permanen!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    });
};
