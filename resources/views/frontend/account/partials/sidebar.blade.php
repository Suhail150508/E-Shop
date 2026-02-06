<div class="bg-white rounded-3 shadow-sm p-3 h-100 sidebar-container">
    <div class="d-flex flex-column align-items-center mb-4 pt-3">
        <div class="avatar-container mb-3 position-relative">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold fs-3 sidebar-avatar">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
        </div>
        <h5 class="fw-bold mb-1">{{ Auth::user()->name }}</h5>
        <p class="text-muted small mb-0">{{ Auth::user()->email }}</p>
    </div>

    <div class="nav flex-column nav-pills custom-sidebar-nav gap-1">
        <a href="{{ route('customer.dashboard') }}" class="nav-link d-flex align-items-center px-3 py-2 {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge-high fa-fw me-3"></i>
            <span>{{ __('Dashboard') }}</span>
        </a>
        
        <a href="{{ route('customer.orders.index') }}" class="nav-link d-flex align-items-center px-3 py-2 {{ request()->routeIs('customer.orders.*') ? 'active' : '' }}">
            <i class="fa-solid fa-box-open fa-fw me-3"></i>
            <span>{{ __('My Order') }}</span>
        </a>
        
        <a href="{{ route('customer.wishlist.index') }}" class="nav-link d-flex align-items-center px-3 py-2 {{ request()->routeIs('customer.wishlist.*') ? 'active' : '' }}">
            <i class="fa-regular fa-heart fa-fw me-3"></i>
            <span>{{ __('My Wishlist') }}</span>
        </a>

        @if(Route::has('customer.wallet.index'))
        <a href="{{ route('customer.wallet.index') }}" class="nav-link d-flex align-items-center px-3 py-2 {{ request()->routeIs('customer.wallet.*') ? 'active' : '' }}">
            <i class="fa-solid fa-wallet fa-fw me-3"></i>
            <span>{{ __('Wallet') }}</span>
        </a>
        @endif

        @if(Route::has('livechat.index'))
        <a href="{{ route('livechat.index') }}" class="nav-link d-flex align-items-center px-3 py-2 {{ request()->routeIs('livechat.index') ? 'active' : '' }}">
            <i class="fa-regular fa-comments fa-fw me-3"></i>
            <span>{{ __('Chat') }}</span>
        </a>
        @endif

        <a href="{{ route('customer.support-tickets.index') }}" class="nav-link d-flex align-items-center px-3 py-2 {{ request()->routeIs('customer.support-tickets.*') ? 'active' : '' }}">
            <i class="fa-solid fa-headset fa-fw me-3"></i>
            <span>{{ __('Support Ticket') }}</span>
        </a>
        
        <a href="{{ route('customer.addresses.index') }}" class="nav-link d-flex align-items-center px-3 py-2 {{ request()->routeIs('customer.addresses.*') ? 'active' : '' }}">
            <i class="fa-solid fa-location-dot fa-fw me-3"></i>
            <span>{{ __('Address') }}</span>
        </a>

        <a href="{{ route('customer.password.edit') }}" class="nav-link d-flex align-items-center px-3 py-2 {{ request()->routeIs('customer.password.edit') ? 'active' : '' }}">
            <i class="fa-solid fa-gear fa-fw me-3"></i>
            <span>{{ __('Settings') }}</span>
        </a>

        <a href="{{ route('customer.profile.edit') }}" class="nav-link d-flex align-items-center px-3 py-2 {{ request()->routeIs('customer.profile.edit') ? 'active' : '' }}">
            <i class="fa-regular fa-user fa-fw me-3"></i>
            <span>{{ __('Profile') }}</span>
        </a>

        <form action="{{ route('logout') }}" method="POST" class="mt-2 pt-2 border-top">
            @csrf
            <button type="submit" class="nav-link d-flex align-items-center px-3 py-2 text-danger w-100 border-0 bg-transparent">
                <i class="fa-solid fa-right-from-bracket fa-fw me-3"></i>
                <span>{{ __('Logout') }}</span>
            </button>
        </form>
    </div>
</div>