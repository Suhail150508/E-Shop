<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Staff Dashboard') }} | {{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="{{ asset('global/bootstrap/css/bootstrap.min.css') }}">
    
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        :root {
            --sidebar-width: 260px;
            --header-height: 70px;
            --primary-color: #4f46e5;
            --secondary-color: #64748b;
            --sidebar-bg: #0f172a;
            --sidebar-text: #94a3b8;
            --sidebar-text-active: #ffffff;
            --sidebar-hover: #1e293b;
            --bg-body: #f1f5f9;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: #334155;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background-color: var(--sidebar-bg);
            z-index: 1000;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            color: #fff;
            font-weight: 700;
            font-size: 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .sidebar-menu {
            padding: 1rem 0;
            flex-grow: 1;
            overflow-y: auto;
        }

        .menu-header {
            padding: 0.75rem 1.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            font-weight: 600;
            margin-top: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: all 0.2s;
            font-weight: 500;
            border-left: 3px solid transparent;
        }

        .nav-link:hover {
            color: var(--sidebar-text-active);
            background-color: var(--sidebar-hover);
        }

        .nav-link.active {
            color: var(--sidebar-text-active);
            background-color: var(--sidebar-hover);
            border-left-color: var(--primary-color);
        }

        .nav-link i {
            width: 24px;
            margin-right: 10px;
            font-size: 1.1rem;
            text-align: center;
        }

        .nav-link .arrow {
            margin-left: auto;
            font-size: 0.8rem;
            transition: transform 0.2s;
        }

        /* Main Content Wrapper */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header Styles */
        .top-header {
            height: var(--header-height);
            background: #fff;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .toggle-sidebar {
            cursor: pointer;
            font-size: 1.25rem;
            color: #64748b;
            margin-right: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .toggle-sidebar:hover {
            background-color: #f1f5f9;
            color: var(--primary-color);
        }

        /* Collapsed Sidebar Styles (Desktop) */
        @media (min-width: 992px) {
            .sidebar.collapsed {
                width: 70px;
            }

            .sidebar.collapsed .sidebar-brand {
                padding: 0;
                justify-content: center;
            }

            .sidebar.collapsed .sidebar-brand span {
                display: none;
            }
            
            .sidebar.collapsed .sidebar-brand i {
                margin-right: 0 !important;
                font-size: 1.5rem;
            }

            .sidebar.collapsed .menu-header {
                display: none;
            }

            .sidebar.collapsed .nav-link {
                padding: 0.75rem 0;
                justify-content: center;
            }

            .sidebar.collapsed .nav-link span,
            .sidebar.collapsed .nav-link .arrow {
                display: none;
            }

            .sidebar.collapsed .nav-link i {
                margin-right: 0;
                font-size: 1.25rem;
            }
            
            .sidebar.collapsed .p-3.border-top {
                padding: 1rem 0.5rem !important;
            }
            
            .sidebar.collapsed .btn-danger {
                padding: 0.5rem;
                justify-content: center;
            }
            
            .sidebar.collapsed .btn-danger span,
            .sidebar.collapsed .btn-danger i + span {
                display: none;
            }
            
            .sidebar.collapsed .btn-danger {
                font-size: 0;
            }
            .sidebar.collapsed .btn-danger i {
                font-size: 1rem;
                margin: 0;
            }

            .main-wrapper.collapsed {
                margin-left: 70px;
            }
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .notification-btn {
            position: relative;
            color: #64748b;
            font-size: 1.25rem;
            cursor: pointer;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #ef4444;
            color: #fff;
            font-size: 0.65rem;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
        }

        .user-dropdown .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #334155;
            font-weight: 500;
            text-decoration: none;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #e2e8f0;
            object-fit: cover;
        }

        /* Content Area */
        .content-body {
            padding: 2rem;
            flex-grow: 1;
        }

        /* Bootstrap Overrides & Global Utilities */
        .btn-white {
            background-color: #fff;
            border-color: #e2e8f0;
            color: #475569;
        }
        
        .btn-white:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: #1e293b;
        }

        .hover-scale {
            transition: transform 0.2s;
        }
        
        .hover-scale:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .bg-primary-subtle {
            background-color: #e0e7ff !important;
            color: var(--primary-color) !important;
        }
        
        .bg-success-subtle {
            background-color: #dcfce7 !important;
            color: #166534 !important;
        }

        .bg-warning-subtle {
            background-color: #fef9c3 !important;
            color: #854d0e !important;
        }

        .bg-danger-subtle {
            background-color: #fee2e2 !important;
            color: #991b1b !important;
        }
        
        .bg-secondary-subtle {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
        }
        
        .bg-info-subtle {
            background-color: #e0f2fe !important;
            color: #075985 !important;
        }

        .card {
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 0.75rem;
        }
        
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.25rem 1.5rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }

        .table > :not(caption) > * > * {
            padding: 1rem 1rem;
            background-color: transparent;
            vertical-align: middle;
        }
        
        .table > thead {
            background-color: #f8fafc;
        }
        
        .table > thead th {
            color: #64748b;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .avatar-sm {
            width: 40px;
            height: 40px;
            object-fit: cover;
        }
        
        .avatar-md {
            width: 64px;
            height: 64px;
            object-fit: cover;
        }
        
        .form-label {
            font-weight: 500;
            color: #334155;
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            border-color: #e2e8f0;
            padding: 0.625rem 0.875rem;
            border-radius: 0.5rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        /* Pagination customization */
        .page-link {
            color: var(--primary-color);
        }
        
        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Mobile Responsiveness */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
            }

            .toggle-sidebar {
                display: block;
                margin-right: 1rem;
            }
            
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0,0,0,0.5);
                z-index: 999;
                display: none;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
            
            .top-header {
                padding: 0 1rem;
            }
            
            .content-body {
                padding: 1rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-id-badge me-2 text-primary"></i>
            <span>{{ __('Staff Panel') }}</span>
        </div>

        <nav class="sidebar-menu">
            <div class="menu-header">Main</div>
            <a href="{{ route('staff.dashboard') }}" class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span>{{ __('Dashboard') }}</span>
            </a>

            <div class="menu-header">{{ __('Management') }}</div>
            <a href="{{ route('staff.orders.index') }}" class="nav-link {{ request()->routeIs('staff.orders.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i>
                <span>{{ __('Assigned Orders') }}</span>
            </a>

            <div class="menu-header">{{ __('Settings') }}</div>
            <a href="{{ route('staff.profile') }}" class="nav-link {{ request()->routeIs('staff.profile') ? 'active' : '' }}">
                <i class="fas fa-user-circle"></i>
                <span>{{ __('My Profile') }}</span>
            </a>
        </nav>
        
        <div class="p-3 border-top border-secondary border-opacity-10">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                </button>
            </form>
        </div>
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

                <div class="dropdown user-dropdown">
                    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                        <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 36px; height: 36px;">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="d-none d-md-block text-start">
                            <div class="small fw-bold">{{ auth()->user()->name }}</div>
                            <div class="small text-muted" style="font-size: 0.7rem; line-height: 1;">{{ __('Staff Member') }}</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item text-danger" type="submit"><i class="fas fa-sign-out-alt me-2"></i> {{ __('Logout') }}</button>
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
    <script src="{{ asset('global/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @include('layouts.partials.toaster')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainWrapper = document.querySelector('.main-wrapper');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
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
            
            sidebarToggle.addEventListener('click', toggleSidebar);
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });

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
    @stack('scripts')
</body>
</html>
