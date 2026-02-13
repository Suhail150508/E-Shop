    @php
        $cartService = app(\App\Services\CartService::class);
        $wishlistService = app(\App\Services\WishlistService::class);
        $cartCount = $cartService->count();
        $wishlistCount = $wishlistService->count();
    @endphp
    <!-- Top Bar -->
    <div class="top-bar text-center pt-3">
        <div class="container">
            <span>{{ __('common.free_shipping_banner') }}</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top height-auto">
        <div class="container">

            <!-- Mobile Menu Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Logo - Mobile -->
            <a class="navbar-brand logo d-lg-none" href="{{ route('home') }}">
                <img src="{{ getImageOrPlaceholder(setting('app_logo'), '120x40') }}" alt="{{ config('app.name') }}" style="max-height: 40px;">
            </a>

            <!-- Categories Button - Visible on mobile -->
            <a href="{{ route('shop.index') }}" class="btn btn-categories d-lg-none ms-auto me-3">
                <i class="fas fa-bars me-1"></i> {{ __('common.categories_label') }}
            </a>

            <!-- Cart Icon - Mobile -->
            <a href="{{ route('cart.index') }}" class="d-lg-none action-btn position-relative">
                <i class="fas fa-shopping-cart fs-5"></i>
                <span class="badge-count">{{ $cartCount }}</span>
            </a>

            <!-- Main Navigation Content -->
            <div class="collapse navbar-collapse w-100" id="navbarContent">
                <div class="w-100">
                    <!-- Top Row: Search Bar and Header Actions -->
                    <div class="nav-top-row d-flex align-items-center justify-content-between">
                        <!-- Logo - Desktop -->
                        <a class="navbar-brand logo d-none d-lg-block me-3" href="{{ route('home') }}">
                            <img src="{{ getImageOrPlaceholder(setting('app_logo'), '150x50') }}" alt="{{ config('app.name') }}" style="max-height: 50px;">
                        </a>

                        <form class="search-form d-flex flex-grow-1 me-lg-4 my-3 my-lg-0"
                              method="GET"
                              action="{{ route('shop.index') }}">
                            <input
                                class="form-control search-input rounded-0"
                                type="search"
                                name="q"
                                value="{{ is_string(request('q')) ? request('q') : '' }}"
                                placeholder="{{ __('common.search_placeholder') }}"
                            >
                            <button class="btn search-btn rounded-0" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>

                        <div class="header-actions d-none d-lg-flex ms-lg-3">
                            @php $currentCurrency = current_currency(); @endphp
                            @if($currentCurrency)
                                <div class="dropdown me-3">
                                    <a href="#" class="action-btn d-flex align-items-center text-decoration-none" id="currencyDropdown" data-bs-toggle="dropdown">
                                        <i class="fas fa-coins fs-5 me-1"></i>
                                        <span class="d-none d-xl-inline fw-bold">{{ $currentCurrency->symbol }} {{ $currentCurrency->code }}</span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="currencyDropdown">
                                        @foreach(\App\Models\Currency::where('status', true)->orderByDesc('is_default')->get() as $currency)
                                            <li>
                                                <a class="dropdown-item {{ $currency->code === $currentCurrency->code ? 'active' : '' }}" href="{{ route('currency.switch', $currency->code) }}">
                                                    {{ $currency->symbol }} {{ $currency->code }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <!-- Language Switcher -->
                            <div class="dropdown me-3">
                                <a href="#" class="action-btn d-flex align-items-center text-decoration-none" id="languageDropdown" data-bs-toggle="dropdown">
                                    <i class="fas fa-globe fs-5 me-1"></i>
                                    <span class="d-none d-xl-inline text-uppercase fw-bold">{{ app()->getLocale() }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                                    <li><a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('lang.switch', 'en') }}">English</a></li>
                                    <li><a class="dropdown-item {{ app()->getLocale() == 'bn' ? 'active' : '' }}" href="{{ route('lang.switch', 'bn') }}">বাংলা</a></li>
                                    <li><a class="dropdown-item {{ app()->getLocale() == 'ar' ? 'active' : '' }}" href="{{ route('lang.switch', 'ar') }}">العربية</a></li>
                                </ul>
                            </div>

                            <a href="{{ route('wishlist.index') }}" class="action-btn position-relative">
                                <i class="far fa-heart fs-5"></i>
                                <span class="badge-count">{{ $wishlistCount }}</span>
                            </a>
                            <div class="dropdown me-3">
                                <a href="#" class="action-btn position-relative" data-bs-toggle="dropdown">
                                    <i class="far fa-bell fs-5"></i>
                                    <span class="badge-count d-none" id="customer-notification-badge">0</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end p-0 shadow-lg border-0" style="width: 300px; max-height: 400px; overflow-y: auto;">
                                    <li class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-bold">{{ __('common.notifications') }}</h6>
                                        <a href="#" class="text-decoration-none small" onclick="markAllRead(event)">{{ __('common.mark_all_read') }}</a>
                                    </li>
                                    <div id="customer-notification-list">
                                        <li class="p-4 text-center text-muted empty-state">
                                            <i class="far fa-bell-slash fa-2x mb-2"></i>
                                            <p class="mb-0 small">{{ __('common.no_new_notifications') }}</p>
                                        </li>
                                    </div>
                                </ul>
                            </div>

                            <a href="{{ route('cart.index') }}" class="action-btn position-relative">
                                <i class="fas fa-shopping-cart fs-5"></i>
                                <span class="badge-count">{{ $cartCount }}</span>
                            </a>
                            <div class="dropdown ms-3">
                                <a href="#" class="action-btn d-flex align-items-center text-decoration-none" id="userDropdown" data-bs-toggle="dropdown">
                                    <i class="far fa-user fs-5 me-2"></i>
                                    <div class="d-flex flex-column">
                                        @auth
                                            <small class="user-greeting">{{ __('common.hello') }}, {{ auth()->user()->name }}</small>
                                            <span class="user-login">{{ __('common.account') }}</span>
                                        @else
                                            <small class="user-greeting">{{ __('common.hello') }}, {{ __('common.guest') }}</small>
                                            <span class="user-login">{{ __('common.login') }} / {{ __('common.register') }}</span>
                                        @endauth
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    @guest
                                        <li><a class="dropdown-item" href="{{ route('login') }}">{{ __('common.sign_in') }}</a></li>
                                        <li><a class="dropdown-item" href="{{ route('register') }}">{{ __('common.create_account') }}</a></li>
                                    @else
                                        <li><a class="dropdown-item" href="{{ route('customer.orders.index') }}">{{ __('common.my_orders') }}</a></li>
                                        <li><a class="dropdown-item" href="{{ route('wishlist.index') }}">{{ __('common.wishlist') }}</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                                {{ __('common.logout') }}
                                            </button>
                                        </li>
                                    @endguest
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Row: Categories and Navigation Links -->
                    <div class="nav-bottom-row d-flex align-items-center justify-content-lg-center">
                        <!-- Categories - Desktop -->
                        <div class="d-none d-lg-flex me-4 categories-wrapper">
                            <button
                                type="button"
                                class="btn btn-rust categories-toggle"
                                id="headerCategoriesToggle"
                                aria-expanded="false"
                                aria-controls="headerCategoriesDropdown"
                            >
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-bars"></i>
                                    <span>{{ __('common.all_categories') }}</span>
                                </div>
                                <i class="fas fa-chevron-down" style="font-size: 0.75rem;"></i>
                            </button>
                            @if($headerCategories->isNotEmpty())
                                <div class="categories-dropdown" id="headerCategoriesDropdown">
                                    <ul class="categories-list">
                                        @foreach($headerCategories as $navCategory)
                                            <li class="categories-item">
                                                <a
                                                    href="{{ route('shop.category', $navCategory) }}"
                                                    class="categories-link"
                                                >
                                                    <div class="d-flex align-items-center">
                                                        <div class="categories-icon">
                                                        <img src="{{ $navCategory->image_url ?? 'https://placehold.co/40' }}" 
                                                             alt="{{ $navCategory->name }}" 
                                                             class="img-fluid w-100 h-100 object-fit-contain"
                                                             style="{{ !$navCategory->image_url ? 'opacity: 0.6;' : '' }}"
                                                             onerror="this.onerror=null;this.src='https://placehold.co/40';this.style.opacity='0.6';">
                                                    </div>
                                                        <span class="categories-link-main">
                                                            {{ $navCategory->name }}
                                                        </span>
                                                    </div>
                                                    @if($navCategory->children->isNotEmpty())
                                                        <i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i>
                                                    @endif
                                                </a>
                                                @if($navCategory->children->isNotEmpty())
                                                    <div class="categories-children">
                                                        @foreach($navCategory->children as $childCategory)
                                                            <a href="{{ route('shop.category', $childCategory) }}">
                                                                <div class="categories-child-icon">
                                                                    <img src="{{ $childCategory->image_url ?? 'https://placehold.co/40' }}" 
                                                                         alt="{{ $childCategory->name }}" 
                                                                         class="img-fluid"
                                                                         style="width: 100%; height: 100%; object-fit: contain; {{ !$childCategory->image_url ? 'opacity: 0.6;' : '' }}"
                                                                         onerror="this.onerror=null;this.src='https://placehold.co/40';this.style.opacity='0.6';">
                                                                </div>
                                                                <span>{{ $childCategory->name }}</span>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <!-- Navigation Links -->
                        <ul class="navbar-nav nav-main-links mx-lg-auto mb-2 mb-lg-0">
                            @if(isset($headerMenu) && $headerMenu->items->isNotEmpty())
                                @foreach($headerMenu->items->where('parent_id', null) as $item)
                                    @if($item->children->count() > 0)
                                        <li class="nav-item dropdown">
                                            <div class="d-flex align-items-center">
                                                <a class="nav-link pe-2" href="{{ $item->url }}" target="{{ $item->target }}">
                                                    {{ $item->title }}
                                                </a>
                                                <a class="nav-link dropdown-toggle ps-1" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                </a>
                                            </div>
                                            <ul class="dropdown-menu border-0 shadow-lg rounded-0 mt-0">
                                                @foreach($item->children as $child)
                                                    <li>
                                                        <a class="dropdown-item py-2" href="{{ $child->url }}" target="{{ $child->target }}">
                                                            {{ $child->title }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @else
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ $item->url }}" target="{{ $item->target }}">
                                                {{ $item->title }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            @else
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">{{ __('common.home') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('shop.*') ? 'active' : '' }}" href="{{ route('shop.index') }}">{{ __('common.shop') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('/#products') }}">{{ __('common.latest_products') }}</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle {{ request()->routeIs('pages.*') ? 'active' : '' }}" href="#" id="pagesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ __('common.pages') }}
                                    </a>
                                    <ul class="dropdown-menu border-0 shadow-lg rounded-0 mt-0" aria-labelledby="pagesDropdown">
                                        <li><a class="dropdown-item py-2" href="{{ route('pages.about') }}">{{ __('common.about') }}</a></li>
                                        <li><a class="dropdown-item py-2" href="{{ route('pages.contact') }}">{{ __('common.contact') }}</a></li>
                                        <li><a class="dropdown-item py-2" href="{{ route('pages.coupons') }}">{{ __('common.coupons') }}</a></li>
                                        <li><a class="dropdown-item py-2" href="{{ route('pages.terms') }}">{{ __('common.terms') }}</a></li>
                                        <li><a class="dropdown-item py-2" href="{{ route('pages.privacy') }}">{{ __('common.privacy') }}</a></li>
                                        <li><a class="dropdown-item py-2" href="{{ route('pages.shipping') }}">{{ __('common.shipping') }}</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('pages.contact') }}">{{ __('common.contact') }}</a>
                                </li>
                            @endif
                            
                            <!-- Mobile Language Switcher -->
                            <li class="nav-item dropdown d-lg-none mt-2 border-top pt-2">
                                <a class="nav-link dropdown-toggle" href="#" id="langDropdownMobile" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-globe me-2"></i> {{ app()->getLocale() == 'en' ? 'English' : (app()->getLocale() == 'bn' ? 'বাংলা' : 'العربية') }}
                                </a>
                                <ul class="dropdown-menu border-0 shadow-none rounded-0 mt-0 bg-light" aria-labelledby="langDropdownMobile">
                                    <li><a class="dropdown-item py-2" href="{{ route('lang.switch', 'en') }}">{{ __('common.english') }}</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('lang.switch', 'bn') }}">{{ __('common.bangla') }}</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('lang.switch', 'ar') }}">{{ __('common.arabic') }}</a></li>
                                </ul>
                            </li>
                            @if(current_currency())
                                <li class="nav-item dropdown d-lg-none mt-2">
                                    <a class="nav-link dropdown-toggle" href="#" id="currencyDropdownMobile" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-coins me-2"></i> {{ current_currency()->symbol }} {{ current_currency()->code }}
                                    </a>
                                    <ul class="dropdown-menu border-0 shadow-none rounded-0 mt-0 bg-light" aria-labelledby="currencyDropdownMobile">
                                        @foreach(\App\Models\Currency::where('status', true)->orderByDesc('is_default')->get() as $currency)
                                            <li>
                                                <a class="dropdown-item py-2 {{ $currency->code === current_currency()->code ? 'active' : '' }}" href="{{ route('currency.switch', $currency->code) }}">
                                                    {{ $currency->symbol }} {{ $currency->code }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endif

                            <!-- Mobile User Menu -->
                            @guest
                                <li class="nav-item d-lg-none">
                                    <a class="nav-link" href="{{ route('login') }}"><i class="fas fa-sign-in-alt me-2"></i> {{ __('common.sign_in') }}</a>
                                </li>
                                <li class="nav-item d-lg-none">
                                    <a class="nav-link" href="{{ route('register') }}"><i class="fas fa-user-plus me-2"></i> {{ __('common.create_account') }}</a>
                                </li>
                            @else
                                <li class="nav-item d-lg-none">
                                    <a class="nav-link" href="{{ route('customer.dashboard') }}"><i class="fas fa-user me-2"></i> {{ __('common.my_account') }}</a>
                                </li>
                                <li class="nav-item d-lg-none">
                                    <button type="button" class="nav-link bg-transparent border-0 w-100 text-start" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                        <i class="fas fa-sign-out-alt me-2"></i> {{ __('common.logout') }}
                                    </button>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

