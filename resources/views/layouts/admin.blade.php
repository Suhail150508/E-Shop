<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="user-id" content="{{ auth()->id() }}">
    <meta name="is-admin" content="{{ auth()->check() && auth()->user()->isAdmin() ? '1' : '0' }}">
    <title>{{ __('Admin Dashboard') }} | {{ config('app.name') }}</title>
    
    <link rel="icon" href="{{ getImageOrPlaceholder(setting('app_favicon'), '32x32') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="{{ asset('backend/css/style.css') }}" rel="stylesheet">

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    {{-- Toastr --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="{{ asset('global/toastr/toastr.main.css') }}">
    
    <!-- Vite -->
    @vite(['resources/js/app.js'])

    @stack('styles')
</head>
<body>

    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

      <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <div class="brand-logo">
                <img src="{{ getImageOrPlaceholder(setting('app_logo'), '150x40') }}" alt="{{ config('app.name') }}" style="max-height: 40px; max-width: 100%;">
            </div>
            <div class="brand-text">
                <span>{{ setting('app_name') ?: config('app.name') }}</span>
            </div>
        </a>

        <div class="sidebar-search-wrapper">
            <div class="sidebar-search">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="menuSearch" placeholder="{{ __('Search menu...') }}" class="search-input">
            </div>
        </div>

        <nav class="sidebar-menu">
                <div class="menu-header">{{ __('Main') }}</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i>
                    <span>{{ __('Dashboard') }}</span>
                </a>

                <div class="menu-header">{{ __('Product Management') }}</div>

                {{-- Product Management --}}
                @php
                    $isProductActive = request()->routeIs('admin.products.*') || 
                        request()->routeIs('admin.categories.*') || 
                        request()->routeIs('admin.subcategories.*') || 
                        request()->routeIs('admin.brands.*') ||
                        request()->routeIs('admin.units.*') ||
                        request()->routeIs('admin.colors.*');
                        
                @endphp
                <a class="nav-link {{ $isProductActive ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#productMenu" role="button" aria-expanded="{{ $isProductActive ? 'true' : 'false' }}" aria-controls="productMenu">
                    <i class="fas fa-box"></i>
                    <span>{{ __('Product Manage') }}</span>
                    <i class="fas fa-chevron-right arrow ms-auto"></i>
                </a>
                <div class="collapse {{ $isProductActive ? 'show' : '' }}" id="productMenu">
                    <div class="sub-menu">
                        <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                            <span>{{ __('Products') }}</span>
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                            <span>{{ __('Categories') }}</span>
                        </a>
                        <a href="{{ route('admin.subcategories.index') }}" class="nav-link {{ request()->routeIs('admin.subcategories.*') ? 'active' : '' }}">
                            <span>{{ __('Sub Categories') }}</span>
                        </a>
                        <a href="{{ route('admin.brands.index') }}" class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                            <span>{{ __('Brands') }}</span>
                        </a>
                        <a href="{{ route('admin.units.index') }}" class="nav-link {{ request()->routeIs('admin.units.*') ? 'active' : '' }}">
                            <span>{{ __('Units') }}</span>
                        </a>
                        <a href="{{ route('admin.colors.index') }}" class="nav-link {{ request()->routeIs('admin.colors.*') ? 'active' : '' }}">
                            <span>{{ __('Colors') }}</span>
                        </a>
                    </div>
                </div>

                <div class="menu-header">{{ __('Order Management') }}</div>
                @php
                    $isOrderActive = request()->routeIs('admin.orders.*') || request()->routeIs('admin.coupons.*') || request()->routeIs('admin.refund-requests.*') || request()->routeIs('admin.refund-reasons.*');
                @endphp
                <a class="nav-link {{ $isOrderActive ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#orderMenu" role="button" aria-expanded="{{ $isOrderActive ? 'true' : 'false' }}" aria-controls="orderMenu">
                    <i class="fas fa-shopping-cart"></i>
                    <span>{{ __('Order Manage') }}</span>
                    <i class="fas fa-chevron-right arrow ms-auto"></i>
                </a>
                <div class="collapse {{ $isOrderActive ? 'show' : '' }}" id="orderMenu">
                    <div class="sub-menu">
                        <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                            <span>{{ __('Orders') }}</span>
                        </a>
                        <a href="{{ route('admin.refund-requests.index') }}" class="nav-link {{ request()->routeIs('admin.refund-requests.*') ? 'active' : '' }}">
                            <span>{{ __('Refund Requests') }}</span>
                        </a>
                        <a href="{{ route('admin.refund-reasons.index') }}" class="nav-link {{ request()->routeIs('admin.refund-reasons.*') ? 'active' : '' }}">
                            <span>{{ __('Refund Reasons') }}</span>
                        </a>
                        <a href="{{ route('admin.coupons.index') }}" class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                            <span>{{ __('Coupons') }}</span>
                        </a>
                    </div>
                </div>

                <div class="menu-header">{{ __('Users Management') }}</div>
                @php
                    $isCustomerActive = request()->routeIs('admin.customers.*') || request()->routeIs('admin.contact.*') || request()->routeIs('admin.newsletter.*');
                @endphp
                <a class="nav-link {{ $isCustomerActive ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#customerMenu" role="button" aria-expanded="{{ $isCustomerActive ? 'true' : 'false' }}" aria-controls="customerMenu">
                    <i class="fas fa-users"></i>
                    <span>{{ __('Customers') }}</span>
                    <i class="fas fa-chevron-right arrow ms-auto"></i>
                </a>
                <div class="collapse {{ $isCustomerActive ? 'show' : '' }}" id="customerMenu">
                    <div class="sub-menu">
                        <a href="{{ route('admin.customers.index') }}" class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                            <span>{{ __('Customers List') }}</span>
                        </a>
                        <a href="{{ route('admin.contact.index') }}" class="nav-link {{ request()->routeIs('admin.contact.*') ? 'active' : '' }}">
                            <span>{{ __('Contact Messages') }}</span>
                        </a>
                        <a href="{{ route('admin.newsletter.index') }}" class="nav-link {{ request()->routeIs('admin.newsletter.*') ? 'active' : '' }}">
                            <span>{{ __('Subscriber List') }}</span>
                        </a>
                    </div>
                </div>

                @php
                    $isSupportActive = request()->routeIs('admin.support-tickets.*') || request()->routeIs('admin.support-departments.*');
                @endphp
                <div class="menu-header">{{ __('Support Ticket') }}</div>
                <a class="nav-link {{ $isSupportActive ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#supportMenu" role="button" aria-expanded="{{ $isSupportActive ? 'true' : 'false' }}" aria-controls="supportMenu">
                    <i class="fas fa-headset"></i>
                    <span>{{ __('Support Ticket') }}</span>
                    <i class="fas fa-chevron-right arrow ms-auto"></i>
                </a>
                <div class="collapse {{ $isSupportActive ? 'show' : '' }}" id="supportMenu">
                    <div class="sub-menu">
                        <a href="{{ route('admin.support-tickets.index') }}" class="nav-link {{ request()->routeIs('admin.support-tickets.*') ? 'active' : '' }}">
                            <span>{{ __('All Tickets') }}</span>
                        </a>
                        <a href="{{ route('admin.support-departments.index') }}" class="nav-link {{ request()->routeIs('admin.support-departments.*') ? 'active' : '' }}">
                            <span>{{ __('Departments') }}</span>
                        </a>
                    </div>
                </div>

                @if(Route::has('admin.wallet.index'))
                <div class="menu-header">{{ __('Wallet Management') }}</div>
                @php
                    $isWalletActive = request()->routeIs('admin.wallet.*');
                @endphp
                <a class="nav-link {{ $isWalletActive ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#walletMenu" role="button" aria-expanded="{{ $isWalletActive ? 'true' : 'false' }}" aria-controls="walletMenu">
                    <i class="fas fa-wallet"></i>
                    <span>{{ __('Wallet Manage') }}</span>
                    <i class="fas fa-chevron-right arrow ms-auto"></i>
                </a>
                <div class="collapse {{ $isWalletActive ? 'show' : '' }}" id="walletMenu">
                    <div class="sub-menu">
                        <a href="{{ route('admin.wallet.index') }}" class="nav-link {{ request()->routeIs('admin.wallet.index') ? 'active' : '' }}">
                            <span>{{ __('Wallet Lists') }}</span>
                        </a>
                        <a href="{{ route('admin.wallet.transactions') }}" class="nav-link {{ request()->routeIs('admin.wallet.transactions') ? 'active' : '' }}">
                            <span>{{ __('Transactions') }}</span>
                        </a>
                        <a href="{{ route('admin.wallet.settings') }}" class="nav-link {{ request()->routeIs('admin.wallet.settings') ? 'active' : '' }}">
                            <span>{{ __('Settings') }}</span>
                        </a>
                    </div>
                </div>
                @endif

                @if(Route::has('admin.livechat.index'))
                <div class="menu-header">{{ __('Live Chat') }}</div>
                <a href="{{ route('admin.livechat.index') }}" class="nav-link {{ request()->routeIs('admin.livechat.*') ? 'active' : '' }}">
                    <i class="fas fa-comments"></i>
                    <span>{{ __('Live Chat') }}</span>
                </a>
                @endif

                <div class="menu-header">{{ __('Staff Management') }}</div>
                <a href="{{ route('admin.staff.index') }}" class="nav-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>{{ __('Staffs') }}</span>
                </a>

                <div class="menu-header">{{ __('Settings') }}</div>
                @php
                    $isAppearanceActive = request()->routeIs('admin.website-setup.*') || request()->routeIs('admin.menus.*');
                @endphp
                <a class="nav-link {{ $isAppearanceActive ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#appearanceMenu" role="button" aria-expanded="{{ $isAppearanceActive ? 'true' : 'false' }}" aria-controls="appearanceMenu">
                    <i class="fas fa-desktop"></i>
                    <span>{{ __('Appearance') }}</span>
                    <i class="fas fa-chevron-right arrow ms-auto"></i>
                </a>
                <div class="collapse {{ $isAppearanceActive ? 'show' : '' }}" id="appearanceMenu">
                    <div class="sub-menu">
                        <a href="{{ route('admin.website-setup.index') }}" class="nav-link {{ request()->routeIs('admin.website-setup.*') ? 'active' : '' }}">
                            <span>{{ __('Website Setup') }}</span>
                        </a>
                        <a href="{{ route('admin.menus.index') }}" class="nav-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                            <span>{{ __('Menus') }}</span>
                        </a>
                    </div>
                </div>

                <div class="menu-header">{{ __('Reviews') }}</div>
                <a href="{{ route('admin.reviews.index') }}" class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <i class="fas fa-star"></i>
                    <span>{{ __('Reviews') }}</span>
                </a>

                <div class="menu-header">{{ __('Page Management') }}</div>
                @php
                    $isPagesActive = request()->routeIs('admin.pages.*');
                @endphp
                <a class="nav-link {{ $isPagesActive ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#pagesMenu" role="button" aria-expanded="{{ $isPagesActive ? 'true' : 'false' }}" aria-controls="pagesMenu">
                    <i class="fas fa-file-alt"></i>
                    <span>{{ __('Manage Pages') }}</span>
                    <i class="fas fa-chevron-right arrow ms-auto"></i>
                </a>
                <div class="collapse {{ $isPagesActive ? 'show' : '' }}" id="pagesMenu">
                    <div class="sub-menu">
                        <a href="{{ route('admin.pages.index') }}" class="nav-link {{ request()->routeIs('admin.pages.index') ? 'active' : '' }}">
                            <span>{{ __('All Pages') }}</span>
                        </a>
                        @if(isset($adminPages))
                            @foreach($adminPages as $page)
                                <a href="{{ route('admin.pages.edit', $page->id) }}" class="nav-link {{ request()->url() == route('admin.pages.edit', $page->id) ? 'active' : '' }}">
                                    <span>{{ $page->title }}</span>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="menu-header">{{ __('International') }}</div>
                <a href="{{ route('admin.currency.index') }}" class="nav-link {{ request()->routeIs('admin.currency.*') ? 'active' : '' }}">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>{{ __('Multi Currency') }}</span>
                </a>
                <a href="{{ route('admin.language.index') }}" class="nav-link {{ request()->routeIs('admin.language.*') ? 'active' : '' }}">
                    <i class="fas fa-globe"></i>
                    <span>{{ __('Multi Language') }}</span>
                </a>

                <div class="menu-header">{{ __('Payment Settings') }}</div>
                <a href="{{ route('admin.payment-methods.index') }}" class="nav-link {{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}">
                    <i class="fas fa-credit-card"></i>
                    <span>{{ __('Payment Methods') }}</span>
                </a>

                <div class="menu-header">{{ __('System Settings') }}</div>
                @php
                    $isSettingsActive = request()->routeIs('admin.email-configuration.*') || request()->routeIs('admin.settings.*');
                @endphp
                <a class="nav-link {{ $isSettingsActive ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#settingsMenu" role="button" aria-expanded="{{ $isSettingsActive ? 'true' : 'false' }}" aria-controls="settingsMenu">
                    <i class="fas fa-cog"></i>
                    <span>{{ __('System Settings') }}</span>
                    <i class="fas fa-chevron-right arrow ms-auto"></i>
                </a>
                <div class="collapse {{ $isSettingsActive ? 'show' : '' }}" id="settingsMenu">
                    <div class="sub-menu">
                        <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                            <span>{{ __('General Settings') }}</span>
                        </a>
                        <a href="{{ route('admin.email-configuration.index') }}" class="nav-link {{ request()->routeIs('admin.email-configuration.index') ? 'active' : '' }}">
                            <span>{{ __('Email Config') }}</span>
                        </a>
                        <a href="{{ route('admin.email-configuration.templates') }}" class="nav-link {{ request()->routeIs('admin.email-configuration.templates*') ? 'active' : '' }}">
                            <span>{{ __('Email Templates') }}</span>
                        </a>
                    </div>
                </div>
                <div class="menu-header">{{ __('Sign Out') }}</div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-link w-100 text-start bg-transparent border-0">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>{{ __('Sign out') }}</span>
                    </button>
                </form>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-wrapper">
        <!-- Top Header -->
        <header class="top-header">
            <div class="d-flex align-items-center">
                <div class="toggle-sidebar" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </div>
                <div class="d-none d-md-block">
                    <h5 class="mb-0 fw-semibold">@yield('page_title', __('Dashboard'))</h5>
                </div>
            </div>

            <div class="header-right">
                <a href="{{ route('home') }}" target="_blank" class="btn btn-light btn-sm d-none d-md-flex align-items-center gap-2">
                    <i class="fas fa-external-link-alt"></i> {{ __('Visit Site') }}
                </a>

                <div class="dropdown notification-dropdown">
                    <div class="notification-btn" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell"></i>
                        <span class="notification-badge d-none" id="notification-count">0</span>
                    </div>
                    <div class="dropdown-menu dropdown-menu-end p-0 shadow-lg border-0" style="width: 320px; max-height: 400px; overflow-y: auto;">
                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                            <h6 class="m-0 fw-bold">{{ __('Notifications') }}</h6>
                            <button type="button" class="btn btn-sm btn-link text-decoration-none p-0" onclick="markAllRead()">{{ __('Mark all read') }}</button>
                        </div>
                        <div id="notification-list">
                            <div class="text-center p-4 text-muted" id="no-notifications">
                                <i class="bi bi-bell-slash fs-4 mb-2 d-block"></i>
                                {{ __('No new notifications') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dropdown user-dropdown">
                    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=4f46e5&color=fff" alt="{{ __('Admin') }}" class="user-avatar">
                        <div class="d-none d-md-block text-start">
                            <div class="small fw-bold">{{ auth()->user()->name ?? __('Admin') }}</div>
                            <div class="small text-muted" style="font-size: 0.7rem; line-height: 1;">{{ __('Administrator') }}</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                        <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}"><i class="fas fa-cog me-2"></i> {{ __('Settings') }}</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item text-danger w-100 text-start" type="submit"><i class="fas fa-sign-out-alt me-2"></i> {{ __('Logout') }}</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="content-body">
            @yield('content')
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('global/toastr/toastr.main.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainWrapper = document.querySelector('.main-wrapper');
            const sidebarToggle = document.getElementById('sidebarToggle'); // Mobile/Header toggle
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const menuSearch = document.getElementById('menuSearch');
            
            function toggleSidebar() {
                if (window.innerWidth >= 992) {
                    // Desktop behavior
                    sidebar.classList.toggle('collapsed');
                    mainWrapper.classList.toggle('collapsed');
                } else {
                    // Mobile behavior
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                }
            }
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                });
            }

            // Menu Search Functionality
            if (menuSearch) {
                menuSearch.addEventListener('keyup', function() {
                    const filter = this.value.toLowerCase();
                    const allLinks = document.querySelectorAll('.sidebar-menu .nav-link');
                    const headers = document.querySelectorAll('.sidebar-menu .menu-header');
                    const collapses = document.querySelectorAll('.sidebar-menu .collapse');

                    if (filter === '') {
                        // Reset
                        allLinks.forEach(link => link.style.display = '');
                        headers.forEach(header => header.style.display = '');
                        collapses.forEach(c => {
                            if (c.querySelector('.nav-link.active')) {
                                c.classList.add('show');
                                const toggle = document.querySelector(`[href="#${c.id}"]`);
                                if (toggle) {
                                    toggle.classList.remove('collapsed');
                                    toggle.setAttribute('aria-expanded', 'true');
                                }
                            } else {
                                c.classList.remove('show');
                                const toggle = document.querySelector(`[href="#${c.id}"]`);
                                if (toggle) {
                                    toggle.classList.add('collapsed');
                                    toggle.setAttribute('aria-expanded', 'false');
                                }
                            }
                        });
                        return;
                    }

                    // Search Mode
                    headers.forEach(h => h.style.display = 'none'); // Hide headers during search
                    
                    allLinks.forEach(link => {
                        const text = link.textContent.toLowerCase();
                        const isMatch = text.includes(filter);
                        
                        if (isMatch) {
                            link.style.display = '';
                            
                            // If it's a parent toggle, expand it and show children
                            if (link.hasAttribute('data-bs-toggle')) {
                                const targetId = link.getAttribute('href').substring(1);
                                const targetCollapse = document.getElementById(targetId);
                                if (targetCollapse) {
                                    targetCollapse.classList.add('show');
                                    targetCollapse.querySelectorAll('.nav-link').forEach(child => child.style.display = '');
                                }
                            }
                            // If it's a child, expand its parent
                            const parentCollapse = link.closest('.collapse');
                            if (parentCollapse) {
                                parentCollapse.classList.add('show');
                                const parentToggle = document.querySelector(`[href="#${parentCollapse.id}"]`);
                                if (parentToggle) {
                                    parentToggle.style.display = '';
                                    parentToggle.classList.remove('collapsed');
                                    parentToggle.setAttribute('aria-expanded', 'true');
                                }
                            }
                        } else {
                            // Only hide if it's not already shown by a parent match or sibling match
                            // This logic is complex. Simpler: hide all, then unhide matches.
                        }
                    });

                    // Better Approach: Hide all, then show matches
                    allLinks.forEach(l => l.style.display = 'none');
                    
                    allLinks.forEach(link => {
                        const text = link.textContent.toLowerCase();
                        if (text.includes(filter)) {
                            link.style.display = '';
                            
                            // If toggle, show/expand target
                            if (link.hasAttribute('data-bs-toggle')) {
                                const targetId = link.getAttribute('href').substring(1);
                                const targetCollapse = document.getElementById(targetId);
                                if (targetCollapse) {
                                    targetCollapse.classList.add('show');
                                    targetCollapse.querySelectorAll('.nav-link').forEach(c => c.style.display = '');
                                }
                            }
                            
                            // If child, show parent toggle and expand parent
                            const parentCollapse = link.closest('.collapse');
                            if (parentCollapse) {
                                parentCollapse.classList.add('show');
                                const parentToggle = document.querySelector(`[href="#${parentCollapse.id}"]`);
                                if (parentToggle) {
                                    parentToggle.style.display = '';
                                    parentToggle.classList.remove('collapsed');
                                    parentToggle.setAttribute('aria-expanded', 'true');
                                }
                            }
                        }
                    });
                });
            }

            // Handle resize events to reset states if needed
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                } else {
                    sidebar.classList.remove('collapsed');
                    mainWrapper.classList.remove('collapsed');
                }
            });
        });
    </script>
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('global/toastr/toastr.main.js') }}"></script>

    @stack('scripts')
    
    <!-- Toastr JS -->
    @include('layouts.partials.toaster')
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userId = "{{ auth()->id() }}";
            const notificationList = document.getElementById('notification-list');
            const notificationCount = document.getElementById('notification-count');
            const noNotifications = document.getElementById('no-notifications');

            function updateCount(count) {
                notificationCount.textContent = count;
                if (count > 0) {
                    notificationCount.classList.remove('d-none');
                } else {
                    notificationCount.classList.add('d-none');
                }
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text == null ? '' : String(text);
                return div.innerHTML;
            }
            function addNotificationItem(notification, prepend = false) {
                if (noNotifications) noNotifications.classList.add('d-none');
                const data = notification.data || notification;
                const message = escapeHtml(data.message || '{{ __("New Notification") }}');
                const link = (data.link && data.link !== '#') ? data.link : '#';
                const date = notification.created_at ? new Date(notification.created_at).toLocaleString() : new Date().toLocaleString();
                const item = document.createElement('a');
                item.href = link;
                item.className = 'dropdown-item p-3 border-bottom notification-item unread';
                item.dataset.id = notification.id;
                item.innerHTML = '<div class="d-flex align-items-start">' +
                    '<div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3"><i class="bi bi-bag-check-fill"></i></div>' +
                    '<div><p class="mb-1 small fw-bold text-dark">' + message + '</p><small class="text-muted">' + escapeHtml(date) + '</small></div></div>';
                
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    markAsRead(notification.id, link);
                });

                if (prepend) {
                    notificationList.insertBefore(item, notificationList.firstChild);
                } else {
                    notificationList.appendChild(item);
                }
            }

            // Fetch existing unread
            fetch("{{ route('admin.notifications.unread') }}")
                .then(response => response.json())
                .then(data => {
                    updateCount(data.length);
                    if (data.length > 0) {
                        if (noNotifications) noNotifications.classList.add('d-none');
                        data.forEach(n => addNotificationItem(n));
                    }
                });

            // Listen for new notifications
            if (window.Echo) {
                console.log('Listening for notifications on App.Models.User.' + userId);
                window.Echo.private('App.Models.User.' + userId)
                    .notification((notification) => {
                        console.log('New notification received:', notification);
                        
                        // Increment count
                        let count = parseInt(notificationCount.textContent) || 0;
                        updateCount(count + 1);
                        
                        // Add item
                        addNotificationItem({
                            id: notification.id,
                            data: notification,
                            created_at: new Date().toISOString()
                        }, true);
                        
                        // Show toast
                        if (typeof toastr !== 'undefined') {
                            toastr.info(notification.message, 'New Notification');
                        }
                    });
            }

            window.markAllRead = function() {
                fetch("{{ route('admin.notifications.read-all') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Content-Type': 'application/json'
                    }
                }).then(() => {
                    // Remove all notification items but keep noNotifications element
                    const items = notificationList.querySelectorAll('.notification-item');
                    items.forEach(item => item.remove());
                    
                    if (noNotifications) {
                        noNotifications.classList.remove('d-none');
                    }
                    updateCount(0);
                });
            }
            
            window.markAsRead = function(id, link) {
                fetch(`/admin/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Content-Type': 'application/json'
                    }
                }).then(() => {
                    if (link && link !== '#') {
                        window.location.href = link;
                    } else {
                        // Remove from list
                        const item = document.querySelector(`.notification-item[data-id="${id}"]`);
                        if (item) item.remove();
                        
                        let count = parseInt(notificationCount.textContent) || 0;
                        updateCount(Math.max(0, count - 1));
                        
                        const items = notificationList.querySelectorAll('.notification-item');
                        if (items.length === 0) {
                            if (noNotifications) noNotifications.classList.remove('d-none');
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
