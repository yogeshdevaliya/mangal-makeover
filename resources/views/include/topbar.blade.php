<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
  <div class="text-center navbar-brand-wrapper d-flex align-items-top justify-content-center">
    <a href="{{ url('/') }}" class="navbar-brand brand-logo logo">Makeover Studio</a>
    {{-- <a class="navbar-brand brand-logo" href="index.html">
      <img src="images/logo.svg" alt="logo" />Logo
    </a>
    <a class="navbar-brand brand-logo-mini" href="index.html">
      <img src="images/logo-mini.svg" alt="logo" />
    </a> --}}
  </div>
  <div class="navbar-menu-wrapper d-flex align-items-center">
    <h4 class="m0">Welcome to Makeover Studio</h4>
    <ul class="navbar-nav navbar-nav-right">
      <li class="nav-item dropdown d-none d-xl-inline-block sn-top-dropdown">
        <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
          <i class="mdi mdi-account-outline"></i><span class="profile-text">Hello, {{ Auth::user()->name }}!</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
          <a href="{{ url('admin/change/password') }}" class="dropdown-item csr-ptr">
            Change Password
          </a>
          <a class="dropdown-item csr-ptr" onclick="event.preventDefault();
            document.getElementById('nav-logout-form').submit();">
            Sign Out
            <form id="nav-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
          </a>
        </div>
      </li>
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
    <span class="mdi mdi-menu"></span>
    </button>
  </div>
</nav>
