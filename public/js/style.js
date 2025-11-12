(function () {
    const sidebar = document.querySelector(".sidebar");
    const backdrop = document.querySelector(".sidebar-backdrop");
    const toggleBtn = document.getElementById("sidebarToggle");

    // toggle for mobile
    function openSidebar(open) {
        if (!sidebar) return;
        if (open) {
            sidebar.classList.add("open");
        } else {
            sidebar.classList.remove("open");
        }
    }

    toggleBtn &&
        toggleBtn.addEventListener("click", () => {
            openSidebar(!sidebar.classList.contains("open"));
        });

    // klik backdrop tutup
    backdrop && backdrop.addEventListener("click", () => openSidebar(false));

    // esc to close
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") openSidebar(false);
    });

    // mark active link by current URL
    const links = document.querySelectorAll(".sidebar-link");
    const cur = window.location.pathname + window.location.search;
    links.forEach((a) => {
        try {
            const href = new URL(a.href).pathname;
            if (cur.startsWith(href)) a.classList.add("active");
        } catch (_) {}
    });

    // Auto close ketika resize ke desktop -> mobile -> desktop
    const mq = window.matchMedia("(min-width: 992px)");
    mq.addEventListener("change", (e) => {
        if (e.matches) openSidebar(false); // saat balik ke desktop, pastikan tertutup mode mobile
    });
})();
