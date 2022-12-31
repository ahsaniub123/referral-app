<header class="navbar navbar-expand-md navbar-light d-print-none">
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <a href="https://21spirit.com" style="display: flex; align-items: center;">
                <img src="https://cdn.shopify.com/s/files/1/0550/4060/6257/files/21_Spirit_Premium_Logo.png?v=1672484757" width="110" height="32" alt="{{ env('APP_NAME') }} Premium" class="navbar-brand-image">
            </a>
            
        </h1>
        <div class="navbar-nav flex-row order-md-last">
            <div class="nav-item dropdown d-none d-md-flex me-3">
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <span class="avatar avatar-sm" style="background-image: url(https://cdn.shopify.com/s/files/1/0550/4060/6257/files/computer-user-icon.webp?v=1672485824)"></span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ \Illuminate\Support\Facades\Auth::user()->name }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();" class="dropdown-item">Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="navbar-menu">
            <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('shopify.home') }}" >
                         <span class="nav-link-title">Home</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('shopify.referral.users') }}" >
                          <span class="nav-link-title">Referral Users</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('shopify.user.wallet') }}" >
                         <span class="nav-link-title">Discount Codes</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
