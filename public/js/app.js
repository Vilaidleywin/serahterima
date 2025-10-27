// app.js — SerahTerimaDokumen (clean)

document.addEventListener("DOMContentLoaded", () => {
    /* ======================== UTIL ======================== */
    const $ = (sel, root = document) => root.querySelector(sel);
    const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];
    const body = document.body;

    /* ================== 1) Form confirm =================== */
    document.addEventListener("submit", (e) => {
        const form = e.target.closest("form[data-confirm]");
        if (!form) return;
        const msg = form.getAttribute("data-confirm") || "Yakin lanjut?";
        if (!confirm(msg)) e.preventDefault();
    });

    /* ================= 2) Flash auto-dismiss =============== */
    const flash = $("#flashAlert");
    if (flash) {
        setTimeout(() => {
            flash.style.transition = "opacity .4s ease";
            flash.style.opacity = "0";
            setTimeout(() => flash.remove(), 400);
        }, 3500);
    }

    /* ============== 3) Autofocus field search ============== */
    const search = $('input[name="search"]');
    if (search) setTimeout(() => search.focus(), 150);

    /* ============== 4) Format Rupiah (live) ================ */
    $$(".idr-input").forEach((inp) => {
        const targetName = inp.dataset.rawFor;
        const hidden = targetName ? $(`input[name="${targetName}"]`) : null;

        const toRupiah = (raw) =>
            "Rp " +
            (raw || "0")
                .replace(/\D/g, "")
                .replace(/^0+/, "")
                .replace(/\B(?=(\d{3})+(?!\d))/g, ".");

        const rawVal = () => inp.value.replace(/\D/g, "") || "0";
        const sync = () => hidden && (hidden.value = rawVal());
        const handle = () => {
            inp.value = toRupiah(rawVal());
            sync();
        };

        inp.addEventListener("input", handle);
        inp.addEventListener("blur", handle);
        handle(); // init
    });

    /* ============== 5) User dropdown (profil) ============== */
    const userToggle = $("#userToggle");
    const userDropdown = userToggle
        ? userToggle.closest(".user-dropdown")
        : null;

    if (userToggle && userDropdown) {
        const menu = $(".user-menu", userDropdown);

        const closeDD = () => {
            userDropdown.classList.remove("open");
            userToggle.setAttribute("aria-expanded", "false");
        };
        const openDD = () => {
            userDropdown.classList.add("open");
            userToggle.setAttribute("aria-expanded", "true");
        };

        userToggle.addEventListener("click", (e) => {
            e.stopPropagation();
            userDropdown.classList.contains("open") ? closeDD() : openDD();
        });

        document.addEventListener("click", (e) => {
            if (!userDropdown.contains(e.target)) closeDD();
        });

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") closeDD();
        });
    }

    /* ======= 6) Mobile drawer (GLPI-style full sheet) ======= */
    // selector sesuai topbar & layout
    const mobileToggleBtn = $("[data-toggle-mobile-nav]"); // tombol hamburger (topbar)
    const mobileNav = $("#mobileNav"); // container drawer
    const mobileCloseBtn = $("[data-close-mobile-nav]"); // tombol X di header drawer

    const openDrawer = () => {
        body.classList.add("nav-open");
        mobileNav?.setAttribute("aria-hidden", "false");
    };
    const closeDrawer = () => {
        body.classList.remove("nav-open");
        mobileNav?.setAttribute("aria-hidden", "true");
    };
    const toggleDrawer = () => {
        body.classList.toggle("nav-open");
        mobileNav?.setAttribute(
            "aria-hidden",
            body.classList.contains("nav-open") ? "false" : "true"
        );
    };

    mobileToggleBtn?.addEventListener("click", (e) => {
        e.stopPropagation();
        toggleDrawer();
    });

    mobileCloseBtn?.addEventListener("click", closeDrawer);

    // tutup saat klik area kosong drawer (bukan header/menu)
    mobileNav?.addEventListener("click", (e) => {
        if (e.target === mobileNav) closeDrawer();
    });

    // tutup saat ESC
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeDrawer();
    });

    // tutup saat klik link menu (biar UX enak)
    $$(".mobile-menu .mobile-link", mobileNav || document).forEach((a) => {
        a.addEventListener("click", closeDrawer);
    });

    // harden: kalau resize ke desktop, pastikan drawer tertutup
    const mq = window.matchMedia("(min-width: 993px)");
    const handleResize = () => {
        if (mq.matches) closeDrawer();
    };
    mq.addEventListener
        ? mq.addEventListener("change", handleResize)
        : window.addEventListener("resize", handleResize);

    /* ======= 7) (Opsional) Sidebar desktop link close on mobile ======= */
    // Kalau kamu masih menampilkan .sidebar di mobile tertentu, ini menjaga close:
    $$(".sidebar a").forEach((a) => {
        a.addEventListener("click", () => {
            if (window.matchMedia("(max-width: 992px)").matches) closeDrawer();
        });
    });
});
// === Responsive Sidebar ala GLPI ===
document.addEventListener("DOMContentLoaded", () => {
    const body = document.body;
    const toggleBtn = document.querySelector("[data-toggle-sidebar]");
    const sidebar = document.querySelector(".sidebar");

    // buat overlay (sekali)
    let backdrop = document.querySelector(".nav-backdrop");
    if (!backdrop) {
        backdrop = document.createElement("div");
        backdrop.className = "nav-backdrop";
        document.body.appendChild(backdrop);
    }

    const openSidebar = () => body.classList.add("nav-open");
    const closeSidebar = () => body.classList.remove("nav-open");
    const toggleSidebar = () => body.classList.toggle("nav-open");

    toggleBtn?.addEventListener("click", (e) => {
        e.stopPropagation();
        toggleSidebar();
    });
    backdrop.addEventListener("click", closeSidebar);
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeSidebar();
    });

    // === ubah icon hamburger ke X ===
    const bars = toggleBtn?.querySelectorAll("span");
    const updateIcon = () => {
        const isOpen = body.classList.contains("nav-open");
        if (!bars) return;
        bars[0].style.transform = isOpen
            ? "translateY(6px) rotate(45deg)"
            : "translateY(0)";
        bars[1].style.opacity = isOpen ? "0" : "1";
        bars[2].style.transform = isOpen
            ? "translateY(-6px) rotate(-45deg)"
            : "translateY(0)";
    };
    toggleBtn?.addEventListener("click", updateIcon);
    backdrop.addEventListener("click", updateIcon);
    document.addEventListener(
        "keydown",
        (e) => e.key === "Escape" && updateIcon()
    );
});
