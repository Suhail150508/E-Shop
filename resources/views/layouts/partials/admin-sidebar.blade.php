{{-- Admin sidebar navigation. Used by layouts/admin.blade.php. --}}
<aside class="sidebar" id="sidebar" role="navigation" aria-label="{{ __('common.main_navigation') }}">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand" aria-label="{{ config('app.name') }}">
        <div class="brand-logo">
            <img src="{{ getImageOrPlaceholder(setting('app_logo'), '150x40') }}" alt="{{ config('app.name') }}" style="max-height: 40px; max-width: 100%;">
        </div>
        <div class="brand-text">
            <span>{{ setting('app_name') ?: config('app.name') }}</span>
        </div>
    </a>

    <div class="sidebar-search-wrapper">
        <div class="sidebar-search">
            <i class="fas fa-search search-icon" aria-hidden="true"></i>
            <input type="text" id="menuSearch" placeholder="{{ __('common.search_menu_placeholder') }}" class="search-input" aria-label="{{ __('common.search_menu_placeholder') }}">
        </div>
    </div>

    <nav class="sidebar-menu">
        <div class="menu-header">{{ __('Main') }}</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large" aria-hidden="true"></i>
            <span>{{ __('Dashboard') }}</span>
        </a>

        <div class="menu-header">{{ __('Product Management') }}</div>
        @php
            $isProductActive = request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.subcategories.*') || request()->routeIs('admin.brands.*') || request()->routeIs('admin.units.*') || request()->routeIs('admin.colors.*');
        @endphp
        <a class="nav-link {{ $isProductActive ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#productMenu" role="button" aria-expanded="{{ $isProductActive ? 'true' : 'false' }}" aria-controls="productMenu">
            <i class="fas fa-box" aria-hidden="true"></i>
            <span>{{ __('Product Manage') }}</span>
            <i class="fas fa-chevron-right arrow ms-auto" aria-hidden="true"></i>
        </a>
        <div class="collapse {{ $isProductActive ? 'show' : '' }}" id="productMenu">
            <div class="sub-menu">
                <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"><span>{{ __('Products') }}</span></a>
                <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"><span>{{ __('Categories') }}</span></a>
                <a href="{{ route('admin.subcategories.index') }}" class="nav-link {{ request()->routeIs('admin.subcategories.*') ? 'active' : '' }}"><span>{{ __('Sub Categories') }}</span></a>
                <a href="{{ route('admin.brands.index') }}" class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}"><span>{{ __('Brands') }}</span></a>
                <a href="{{ route('admin.units.index') }}" class="nav-link {{ request()->routeIs('admin.units.*') ? 'active' : '' }}"><span>{{ __('Units') }}</span></a>
                <a href="{{ route('admin.colors.index') }}" class="nav-link {{ request()->routeIs('admin.colors.*') ? 'active' : '' }}"><span>{{ __('Colors') }}</span></a>
            </div>
        </div>

        <div class="menu-header">{{ __('Order Management') }}</div>
        @php
            $isOrderActive = request()->routeIs('admin.orders.*') || request()->routeIs('admin.coupons.*') || request()->routeIs('admin.refund-requests.*') || request()->routeIs('admin.refund-reasons.*');
        @endphp
        <a class="nav-link {{ $isOrderActive ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#orderMenu" role="button" aria-expanded="{{ $isOrderActive ? 'true' : 'false' }}" aria-controls="orderMenu">
            <i class="fas fa-shopping-cart" aria-hidden="true"></i>
            <span>{{ __('Order Manage') }}</span>
            <i class="fas fa-chevron-right arrow ms-auto" aria-hidden="true"></i>
        </a>
        <div class="collapse {{ $isOrderActive ? 'show' : '' }}" id="orderMenu">
            <div class="sub-menu">
                <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"><span>{{ __('Orders') }}</span></a>
                <a href="{{ route('admin.refund-requests.index') }}" class="nav-link {{ request()->routeIs('admin.refund-requests.*') ? 'active' : '' }}"><span>{{ __('Refund Requests') }}</span></a>
                <a href="{{ route('admin.refund-reasons.index') }}" class="nav-link {{ request()->routeIs('admin.refund-reasons.*') ? 'active' : '' }}"><span>{{ __('Refund Reasons') }}</span></a>
                <a href="{{ route('admin.coupons.index') }}" class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}"><span>{{ __('Coupons') }}</span></a>
            </div>
        </div>

        <div class="menu-header">{{ __('Users Management') }}</div>
        @php
            $isCustomerActive = request()->routeIs('admin.customers.*') || request()->routeIs('admin.contact.*') || request()->routeIs('admin.newsletter.*');
        @endphp
        <a class="nav-link {{ $isCustomerActive ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#customerMenu" role="button" aria-expanded="{{ $isCustomerActive ? 'true' : 'false' }}" aria-controls="customerMenu">
            <i class="fas fa-users" aria-hidden="true"></i>
            <span>{{ __('Customers') }}</span>
            <i class="fas fa-chevron-right arrow ms-auto" aria-hidden="true"></i>
        </a>
        <div class="collapse {{ $isCustomerActive ? 'show' : '' }}" id="customerMenu">
            <div class="sub-menu">
                <a href="{{ route('admin.customers.index') }}" class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"><span>{{ __('Customers List') }}</span></a>
                <a href="{{ route('admin.contact.index') }}" class="nav-link {{ request()->routeIs('admin.contact.*') ? 'active' : '' }}"><span>{{ __('Contact Messages') }}</span></a>
                <a href="{{ route('admin.newsletter.index') }}" class="nav-link {{ request()->routeIs('admin.newsletter.*') ? 'active' : '' }}"><span>{{ __('Subscriber List') }}</span></a>
            </div>
        </div>

        @php
            $isSupportActive = request()->routeIs('admin.support-tickets.*') || request()->routeIs('admin.support-departments.*');
        @endphp
        <div class="menu-header">{{ __('Support Ticket') }}</div>
        <a class="nav-link {{ $isSupportActive ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#supportMenu" role="button" aria-expanded="{{ $isSupportActive ? 'true' : 'false' }}" aria-controls="supportMenu">
            <i class="fas fa-headset" aria-hidden="true"></i>
            <span>{{ __('Support Ticket') }}</span>
            <i class="fas fa-chevron-right arrow ms-auto" aria-hidden="true"></i>
        </a>
        <div class="collapse {{ $isSupportActive ? 'show' : '' }}" id="supportMenu">
            <div class="sub-menu">
                <a href="{{ route('admin.support-tickets.index') }}" class="nav-link {{ request()->routeIs('admin.support-tickets.*') ? 'active' : '' }}"><span>{{ __('All Tickets') }}</span></a>
                <a href="{{ route('admin.support-departments.index') }}" class="nav-link {{ request()->routeIs('admin.support-departments.*') ? 'active' : '' }}"><span>{{ __('Departments') }}</span></a>
            </div>
        </div>

        @if(Route::has('admin.wallet.index'))
            <div class="menu-header">{{ __('Wallet Management') }}</div>
            @php $isWalletActive = request()->routeIs('admin.wallet.*'); @endphp
            <a class="nav-link {{ $isWalletActive ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#walletMenu" role="button" aria-expanded="{{ $isWalletActive ? 'true' : 'false' }}" aria-controls="walletMenu">
                <i class="fas fa-wallet" aria-hidden="true"></i>
                <span>{{ __('Wallet Manage') }}</span>
                <i class="fas fa-chevron-right arrow ms-auto" aria-hidden="true"></i>
            </a>
            <div class="collapse {{ $isWalletActive ? 'show' : '' }}" id="walletMenu">
                <div class="sub-menu">
                    <a href="{{ route('admin.wallet.index') }}" class="nav-link {{ request()->routeIs('admin.wallet.index') ? 'active' : '' }}"><span>{{ __('Wallet Lists') }}</span></a>
                    <a href="{{ route('admin.wallet.transactions') }}" class="nav-link {{ request()->routeIs('admin.wallet.transactions') ? 'active' : '' }}"><span>{{ __('Transactions') }}</span></a>
                    <a href="{{ route('admin.wallet.settings') }}" class="nav-link {{ request()->routeIs('admin.wallet.settings') ? 'active' : '' }}"><span>{{ __('Settings') }}</span></a>
                </div>
            </div>
        @endif

        @if(Route::has('admin.livechat.index'))
            <div class="menu-header">{{ __('Live Chat') }}</div>
            <a href="{{ route('admin.livechat.index') }}" class="nav-link {{ request()->routeIs('admin.livechat.*') ? 'active' : '' }}">
                <i class="fas fa-comments" aria-hidden="true"></i>
                <span>{{ __('Live Chat') }}</span>
            </a>
        @endif

        <div class="menu-header">{{ __('Staff Management') }}</div>
        <a href="{{ route('admin.staff.index') }}" class="nav-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
            <i class="fas fa-users" aria-hidden="true"></i>
            <span>{{ __('Staffs') }}</span>
        </a>

        <div class="menu-header">{{ __('Settings') }}</div>
        @php
            $isAppearanceActive = request()->routeIs('admin.website-setup.*') || request()->routeIs('admin.menus.*');
        @endphp
        <a class="nav-link {{ $isAppearanceActive ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#appearanceMenu" role="button" aria-expanded="{{ $isAppearanceActive ? 'true' : 'false' }}" aria-controls="appearanceMenu">
            <i class="fas fa-desktop" aria-hidden="true"></i>
            <span>{{ __('Appearance') }}</span>
            <i class="fas fa-chevron-right arrow ms-auto" aria-hidden="true"></i>
        </a>
        <div class="collapse {{ $isAppearanceActive ? 'show' : '' }}" id="appearanceMenu">
            <div class="sub-menu">
                <a href="{{ route('admin.website-setup.index') }}" class="nav-link {{ request()->routeIs('admin.website-setup.*') ? 'active' : '' }}"><span>{{ __('Website Setup') }}</span></a>
                <a href="{{ route('admin.menus.index') }}" class="nav-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}"><span>{{ __('Menus') }}</span></a>
            </div>
        </div>

        <div class="menu-header">{{ __('Reviews') }}</div>
        <a href="{{ route('admin.reviews.index') }}" class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
            <i class="fas fa-star" aria-hidden="true"></i>
            <span>{{ __('Reviews') }}</span>
        </a>

        <div class="menu-header">{{ __('Page Management') }}</div>
        @php $isPagesActive = request()->routeIs('admin.pages.*'); @endphp
        <a class="nav-link {{ $isPagesActive ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#pagesMenu" role="button" aria-expanded="{{ $isPagesActive ? 'true' : 'false' }}" aria-controls="pagesMenu">
            <i class="fas fa-file-alt" aria-hidden="true"></i>
            <span>{{ __('Manage Pages') }}</span>
            <i class="fas fa-chevron-right arrow ms-auto" aria-hidden="true"></i>
        </a>
        <div class="collapse {{ $isPagesActive ? 'show' : '' }}" id="pagesMenu">
            <div class="sub-menu">
                @if(isset($adminPages))
                    @foreach($adminPages as $page)
                        <a href="{{ route('admin.pages.edit', $page->id) }}" class="nav-link {{ request()->url() == route('admin.pages.edit', $page->id) ? 'active' : '' }}"><span>{{ e($page->title) }}</span></a>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="menu-header">{{ __('International') }}</div>
        <a href="{{ route('admin.currency.index') }}" class="nav-link {{ request()->routeIs('admin.currency.*') ? 'active' : '' }}">
            <i class="fas fa-money-bill-wave" aria-hidden="true"></i>
            <span>{{ __('Multi Currency') }}</span>
        </a>
        <a href="{{ route('admin.language.index') }}" class="nav-link {{ request()->routeIs('admin.language.*') ? 'active' : '' }}">
            <i class="fas fa-globe" aria-hidden="true"></i>
            <span>{{ __('Multi Language') }}</span>
        </a>

        <div class="menu-header">{{ __('Payment Settings') }}</div>
        <a href="{{ route('admin.payment-methods.index') }}" class="nav-link {{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}">
            <i class="fas fa-credit-card" aria-hidden="true"></i>
            <span>{{ __('Payment Methods') }}</span>
        </a>

        <div class="menu-header">{{ __('System Settings') }}</div>
        @php
            $isSettingsActive = request()->routeIs('admin.email-configuration.*') || request()->routeIs('admin.settings.*');
        @endphp
        <a class="nav-link {{ $isSettingsActive ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#settingsMenu" role="button" aria-expanded="{{ $isSettingsActive ? 'true' : 'false' }}" aria-controls="settingsMenu">
            <i class="fas fa-cog" aria-hidden="true"></i>
            <span>{{ __('System Settings') }}</span>
            <i class="fas fa-chevron-right arrow ms-auto" aria-hidden="true"></i>
        </a>
        <div class="collapse {{ $isSettingsActive ? 'show' : '' }}" id="settingsMenu">
            <div class="sub-menu">
                <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"><span>{{ __('General Settings') }}</span></a>
                <a href="{{ route('admin.email-configuration.index') }}" class="nav-link {{ request()->routeIs('admin.email-configuration.index') ? 'active' : '' }}"><span>{{ __('Email Config') }}</span></a>
                <a href="{{ route('admin.email-configuration.templates') }}" class="nav-link {{ request()->routeIs('admin.email-configuration.templates*') ? 'active' : '' }}"><span>{{ __('Email Templates') }}</span></a>
            </div>
        </div>

        <div class="menu-header">{{ __('common.sign_out_section') }}</div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-link w-100 text-start bg-transparent border-0">
                <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                <span>{{ __('common.sign_out') }}</span>
            </button>
        </form>
    </nav>
</aside>
