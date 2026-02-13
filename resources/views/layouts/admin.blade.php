<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('common.admin_dashboard') . ' | ' . config('app.name'))</title>

    <link rel="icon" href="{{ getImageOrPlaceholder(setting('app_favicon'), '32x32') }}" alt="">

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('backend/css/all.min.css') }}" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ asset('backend/css/bootstrap-icons.min.css') }}" crossorigin="anonymous">

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="{{ asset('backend/css/bootstrap.min.css') }}" crossorigin="anonymous">

    <link href="{{ asset('backend/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/loader.css') }}" rel="stylesheet">
    <style>.sidebar-brand .brand-logo img { max-height: 40px; max-width: 100%; object-fit: contain; }</style>

    <!-- ApexCharts -->
    <script src="{{ asset('backend/js/apexcharts.min.js') }}" defer></script>

    <!-- Toastr: base + custom (toast above sidebar) -->
    <link rel="stylesheet" href="{{ asset('global/toastr/toastr.min.css') }}" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('global/toastr/toastr.main.css') }}">
    <style>#toast-container { z-index: 99999 !important; }</style>

    @vite(['resources/js/app.js'])
    @stack('styles')
</head>
<body class="admin-layout">

    <!-- Loader -->
    @include('layouts.partials.loader')
    <div class="sidebar-overlay" id="sidebarOverlay" aria-hidden="true"></div>
    
    <!-- Sidebar -->
    @include('layouts.partials.admin-sidebar')

    <!-- Main Wrapper -->
    <main class="main-wrapper">
        <!-- Top Header -->
        <header class="top-header">
            <div class="d-flex align-items-center">
                <button type="button" class="toggle-sidebar border-0 bg-transparent p-0" id="sidebarToggle" aria-label="{{ __('common.toggle_sidebar') }}" aria-expanded="false" aria-controls="sidebar">
                    <i class="fas fa-bars" aria-hidden="true"></i>
                </button>
                <div class="d-none d-md-block">
                    <h5 class="mb-0 fw-semibold">@yield('page_title', __('Dashboard'))</h5>
                </div>
            </div>

            <div class="header-right">
                <!-- Visit Site Button -->
                <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary-soft btn-sm d-none d-md-flex align-items-center gap-2">
                    <i class="fas fa-external-link-alt" aria-hidden="true"></i> {{ __('common.visit_site') }}
                </a>

                <!-- Notifications -->
                <div class="dropdown notification-dropdown">
                    <button type="button" class="notification-btn border-0 bg-transparent p-0" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ __('common.notifications') }}" id="notificationDropdownBtn">
                        <i class="fas fa-bell" aria-hidden="true"></i>
                        <span class="notification-badge d-none" id="notification-count" aria-live="polite">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-0 shadow-lg border-0 notification-dropdown" aria-labelledby="notificationDropdownBtn">
                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                            <h6 class="m-0 fw-bold">{{ __('common.notifications') }}</h6>
                            <button type="button" class="btn btn-sm btn-link text-decoration-none p-0" id="markAllNotificationsRead" aria-label="{{ __('common.mark_all_read') }}">{{ __('common.mark_all_read') }}</button>
                        </div>
                        <div id="notification-list">
                            <div class="text-center p-4 text-muted" id="no-notifications">
                                <i class="fas fa-bell-slash fs-4 mb-2 d-block" aria-hidden="true"></i>
                                {{ __('common.no_new_notifications') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <div class="dropdown user-dropdown">
                    @php $authUser = auth()->user(); @endphp
                    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true" id="userMenuDropdown">
                        <img src="{{ getImageOrPlaceholder($authUser ? $authUser->image : null, '50x50') }}" alt="{{ __('Admin') }}" class="user-avatar" width="50" height="50" loading="lazy">
                        <div class="d-none d-md-block text-start">
                            <div class="small fw-bold">{{ $authUser ? e($authUser->name) : __('Admin') }}</div>
                            <div class="small text-muted user-role-text">{{ __('common.administrator') }}</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" aria-labelledby="userMenuDropdown">
                        <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}"><i class="fas fa-cog me-2" aria-hidden="true"></i> {{ __('Settings') }}</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item text-danger w-100 text-start" type="submit"><i class="fas fa-sign-out-alt me-2" aria-hidden="true"></i> {{ __('common.logout') }}</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="content-body">
            @yield('content')
        </div>
    </main>

    <script src="{{ asset('backend/js/jquery.min.js') }}"></script>
    <script src="{{ asset('global/toastr/toastr.min.js') }}"></script>
    <script>
        window.toastr = window.toastr || (typeof toastr !== 'undefined' ? toastr : null);
        if (window.toastr) {
            window.toastr.options = { closeButton: true, progressBar: true, positionClass: 'toast-top-right', timeOut: 5000, preventDuplicates: false };
        }
    </script>
    <script src="{{ asset('backend/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        window.AdminLayoutConfig = {
            csrfToken: @json(csrf_token()),
            userId: @json(auth()->check() ? auth()->id() : null),
            routes: {
                unread: @json(route('admin.notifications.unread')),
                readAll: @json(route('admin.notifications.read-all')),
                markAsRead: @json(route('admin.notifications.read', ['id' => '__ID__']))
            },
            labels: {
                newNotification: @json(__('common.new_notification'))
            }
        };
        @php
            $adminFlash = [
                'success' => session('success'),
                'status' => session('status'),
                'error' => session('error'),
                'info' => session('info'),
                'warning' => session('warning'),
                'errors' => isset($errors) && $errors->any() ? $errors->all() : [],
            ];
        @endphp
        window.AdminFlash = @json($adminFlash);
    </script>
    <script src="{{ asset('backend/js/admin-layout.js') }}" defer></script>
    <script>
        (function() {
            var successTitle = @json(__('common.success'));
            var errorTitle = @json(__('common.error'));
            var infoTitle = @json(__('common.info'));
            var warningTitle = @json(__('common.warning'));
            var validationTitle = @json(__('common.validation_error'));
            function showAdminFlash() {
                var t = window.toastr || (typeof toastr !== 'undefined' ? toastr : null);
                if (!t) return false;
                var flash = window.AdminFlash || {};
                if (flash.success) t.success(flash.success, successTitle);
                if (flash.status) t.success(flash.status, successTitle);
                if (flash.error) t.error(flash.error, errorTitle);
                if (flash.info) t.info(flash.info, infoTitle);
                if (flash.warning) t.warning(flash.warning, warningTitle);
                if (flash.errors && flash.errors.length) flash.errors.forEach(function(msg) { t.error(msg, validationTitle); });
                return true;
            }
            var attempts = 0;
            function runWhenReady() {
                if (showAdminFlash() || attempts > 30) return;
                attempts++;
                setTimeout(runWhenReady, 100);
            }
            if (typeof jQuery !== 'undefined') {
                jQuery(function() { setTimeout(runWhenReady, 150); });
            } else {
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function() { setTimeout(runWhenReady, 150); });
                } else {
                    setTimeout(runWhenReady, 150);
                }
            }
        })();
    </script>
    <script>
    (function() {
        function hideLoader() {
            var el = document.getElementById('appLoader');
            if (el) el.classList.add('hidden');
        }
        if (document.readyState === 'complete') {
            hideLoader();
        } else {
            window.addEventListener('load', hideLoader);
        }
    })();
    </script>
    @stack('scripts')
</body>
</html>
