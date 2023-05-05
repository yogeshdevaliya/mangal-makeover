<nav class="sidebar dark sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    {{-- <li class="nav-item nav-profile">
      <div class="nav-link">
        <div class="user-wrapper">
          <div class="profile-image">
            Logo
          </div>
          <div class="text-wrapper">
            <p class="profile-name">Richard V.Welsh</p>
            <div>
              <small class="designation text-muted">Manager</small>
              <span class="status-indicator online"></span>
            </div>
          </div>
        </div>
      </div>
    </li> --}}
    <li class="nav-item {{ (Request::path() == 'dashboard' ? 'active' : '') }}">
      <a class="nav-link" href="{{ url('/dashboard') }}">
        <i class="menu-icon mdi mdi-television"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>
    {{-- <li class="nav-item">
      <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
        <i class="menu-icon mdi mdi-content-copy"></i>
        <span class="menu-title">Basic UI Elements</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="ui-basic">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link" href="pages/ui-features/buttons.html">Buttons</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="pages/ui-features/typography.html">Typography</a>
          </li>
        </ul>
      </div>
    </li> --}}
    <li class="nav-item {{ (Request::path() == 'admin/categories' ? 'active' : '') }}">
      <a class="nav-link" href="{{ url('/admin/categories') }}">
        <i class="menu-icon mdi mdi-cogs"></i>
        <span class="menu-title">Service</span>
      </a>
    </li>
    {{-- <li class="nav-item">
      <a class="nav-link" href="{{ url('/admin/services') }}">
      <i class="menu-icon mdi mdi-cogs"></i>
      <span class="menu-title">Services</span>
      </a>
    </li> --}}
    <li class="nav-item {{ (Request::path() == 'admin/package' ? 'active' : '') }}">
      <a class="nav-link" href="{{ url('/admin/package') }}">
        <i class="menu-icon mdi mdi-shopping"></i>
        <span class="menu-title">Package</span>
      </a>
    </li>
    <li class="nav-item {{ (Request::path() == 'admin/products' ? 'active' : '') }}">
      <a class="nav-link" href="{{ url('/admin/products') }}">
        <i class="menu-icon mdi mdi-shopping"></i>
        <span class="menu-title">Products</span>
      </a>
    </li>
    <li class="nav-item {{ (Request::path() == 'admin/clients' ? 'active' : '') }}">
      <a class="nav-link" href="{{ url('/admin/clients') }}">
        <i class="menu-icon mdi mdi-face"></i>
        <span class="menu-title">Clients</span>
      </a>
    </li>
    {{-- <li class="nav-item {{ (Request::path() == 'admin/client/services' ? 'active' : '') }}">
      <a class="nav-link" href="{{ url('/admin/client/services') }}">
        <i class="menu-icon mdi mdi-face"></i>
        <span class="menu-title">Client Services</span>
      </a>
    </li> --}}
    @role('super_admin')
      <li class="nav-item {{ (Request::path() == 'admin/employees' ? 'active' : '') }}">
        <a class="nav-link" href="{{ url('/admin/employees') }}">
          <i class="menu-icon mdi mdi-account-multiple"></i>
          <span class="menu-title">Employees</span>
        </a>
      </li>
      <li class="nav-item {{ (Request::path() == 'admin/invoices' ? 'active' : '') }}">
        <a class="nav-link" href="{{ url('/admin/invoices') }}">
          <i class="menu-icon mdi mdi-account-multiple"></i>
          <span class="menu-title">Invoices</span>
        </a>
      </li>
      <li class="nav-item {{ (Request::path() == 'admin/reports' ? 'active' : '') }}">
        <a class="nav-link" href="{{ url('/admin/reports') }}">
          <i class="menu-icon mdi mdi-file-chart"></i>
          <span class="menu-title">Reports</span>
        </a>
      </li>
      <li class="nav-item {{ (Request::path() == 'admin/expenses' ? 'active' : '') }}">
        <a class="nav-link" href="{{ url('/admin/expenses') }}">
          <i class="menu-icon mdi mdi-file-chart"></i>
          <span class="menu-title">Expenses</span>
        </a>
      </li>
    @endrole
  </ul>
</nav>