<script>
document.addEventListener('DOMContentLoaded', () => {
  const body = document.body;
  const hamburger = document.querySelector('.appbar-btn.hamburger');
  const backdrop = document.querySelector('.nav-backdrop');
  function toggleNav(open) {
    if (open === undefined) body.classList.toggle('nav-open');
    else body.classList.toggle('nav-open', !!open);
  }
  if (hamburger) hamburger.addEventListener('click', () => toggleNav());
  if (backdrop) backdrop.addEventListener('click', () => toggleNav(false));
});
</script>
