<header class="navbar navbar-expand-md navbar-light d-print-none">
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <a>
                <img src="https://cdn.shopify.com/s/files/1/0550/4060/6257/files/21_Spirit_Premium_Logo.png?v=1672484757" width="110" height="32" alt="{{ env('APP_NAME') }} Premium" class="navbar-brand-image">
            </a>
        </h1>

        <div class="collapse navbar-collapse" id="navbar-menu">
            <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ \Illuminate\Support\Facades\URL::tokenRoute('home') }}" >
                         <span class="nav-link-title">Home</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ \Illuminate\Support\Facades\URL::tokenRoute('shopify.index') }}" >
                          <span class="nav-link-title">Users</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ \Illuminate\Support\Facades\URL::tokenRoute('settings.index') }}" >
                           <span class="nav-link-title">Settings</span>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </div>
</header>
