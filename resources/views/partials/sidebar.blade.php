<aside id="drawer" class="drawer" aria-hidden="true">
  <div class="drawer-header">
    <button id="btn-close-drawer" class="appbar-btn" aria-label="Close"><i class="ti ti-x"></i></button>
    <div class="drawer-brand">GLPI-style Menu</div>
  </div>

  <nav>
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard')?'active':'' }}">
      <i class="ti ti-home"></i> <span>Home</span>
    </a>
    <a href="{{ route('tickets.create') }}" class="{{ request()->routeIs('tickets.create')?'active':'' }}">
      <i class="ti ti-plus"></i> <span>Create a Ticket</span>
    </a>
    <a href="{{ route('tickets.index') }}" class="{{ request()->routeIs('tickets.*')?'active':'' }}">
      <i class="ti ti-ticket"></i> <span>Tickets</span>
    </a>
    <a href="#" >
      <i class="ti ti-calendar"></i> <span>Reservations</span>
    </a>
    <a href="{{ route('faq') }}">
      <i class="ti ti-help-circle"></i> <span>FAQ</span>
    </a>
  </nav>
</aside>
