
/* ======== Topbar Override JS (drop-in, safe) ========
   Load AFTER your current app.js (or at least after the DOM is ready).
   Adds: click-outside to close user menu, sync ESC & backdrop, aria states.
======================================================*/
(function(){
  const body = document.body;
  const sel = (s, r=document) => r.querySelector(s);
  const $$ = (s, r=document) => Array.from(r.querySelectorAll(s));

  // Ensure user dropdown is accessible and closes on outside click
  const dropdown = sel('.user-dropdown');
  const chip = sel('.user-chip');
  const menu = dropdown ? dropdown.querySelector('.user-menu') : null;

  function closeMenu(){
    if (!dropdown) return;
    dropdown.classList.remove('open');
    chip?.setAttribute('aria-expanded','false');
  }
  function openMenu(){
    dropdown.classList.add('open');
    chip?.setAttribute('aria-expanded','true');
  }

  chip?.addEventListener('click', (e)=>{
    e.stopPropagation();
    const isOpen = dropdown.classList.contains('open');
    (isOpen ? closeMenu : openMenu)();
  });

  document.addEventListener('click', (e)=>{
    if (!dropdown) return;
    if (!dropdown.contains(e.target)) closeMenu();
  });
  document.addEventListener('keydown', (e)=>{
    if (e.key === 'Escape') closeMenu();
  });

  // Toggle mobile sidebar (uses existing markup: [data-toggle-mobile-nav])
  const togglers = $$('[data-toggle-mobile-nav]');
  const backdropId = 'nav-backdrop';
  function ensureBackdrop(){
    let el = document.getElementById(backdropId);
    if (!el){
      el = document.createElement('div');
      el.id = backdropId;
      el.className = 'nav-backdrop';
      document.body.appendChild(el);
    }
    return el;
  }
  const backdrop = ensureBackdrop();

  function toggleNav(force){
    const willOpen = typeof force === 'boolean' ? force : !body.classList.contains('nav-open');
    body.classList.toggle('nav-open', willOpen);
    backdrop.style.opacity = willOpen ? '1' : '0';
    backdrop.style.visibility = willOpen ? 'visible' : 'hidden';
  }
  togglers.forEach(btn => btn.addEventListener('click', ()=> toggleNav()));
  backdrop.addEventListener('click', ()=> toggleNav(false));
})();
