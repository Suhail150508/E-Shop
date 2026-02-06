 @extends('layouts.admin')

@section('page_title', __('Website Setup'))

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom-0 py-3">
        <h6 class="card-title mb-0 fw-bold">{{ __('Website Content Setup') }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.website-setup.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <ul class="nav nav-tabs mb-4" id="websiteSetupTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab">{{ __('Home Page') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="shop-tab" data-bs-toggle="tab" data-bs-target="#shop" type="button" role="tab">{{ __('Shop Page') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="auth-tab" data-bs-toggle="tab" data-bs-target="#auth" type="button" role="tab">{{ __('Auth Pages') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="cart-tab" data-bs-toggle="tab" data-bs-target="#cart" type="button" role="tab">{{ __('Cart & Checkout') }}</button>
                </li>
            </ul>

            <div class="tab-content" id="websiteSetupContent">
                
                <!-- HOME PAGE TAB -->
                <div class="tab-pane fade show active" id="home" role="tabpanel">
                    <h5 class="mb-3 text-primary">{{ __('Hero Section') }}</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Hero Title') }}</label>
                            <input type="text" class="form-control" name="home_hero_title" value="{{ $settings['home_hero_title'] ?? '' }}" placeholder="{{ __('Enter hero title') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Hero Subtitle') }}</label>
                            <input type="text" class="form-control" name="home_hero_subtitle" value="{{ $settings['home_hero_subtitle'] ?? '' }}" placeholder="{{ __('Enter hero subtitle') }}">
                        </div>
                    </div>

                    <h5 class="mb-3 text-primary">{{ __('Hero Gallery') }} (3 {{ __('Columns') }} x 3 {{ __('Images') }})</h5>
                    <p class="text-muted small mb-3">
                        {{ __('Recommended Size') }}: <strong>600x800px</strong> (Portrait). 
                        {{ __('common.max_size_per_image_hint', ['size' => '2MB']) }}
                    </p>
                    <div class="row">
                        @php
                            $positions = [__('Left Column'), __('Center Column'), __('Right Column')];
                            $defaultGallery = $home_hero_gallery ?? [];
                        @endphp
                        
                        @foreach($positions as $posIndex => $posName)
                            <div class="col-md-4">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-3">{{ $posName }}</h6>
                                        @for($i = 0; $i < 3; $i++)
                                            <div class="mb-3 p-2 bg-white rounded shadow-sm">
                                                <label class="small fw-bold">{{ __('Image') }} {{ $i + 1 }}</label>
                                                
                                                <div class="mb-2">
                                                    @if(isset($defaultGallery[$posIndex][$i]['image']) && $defaultGallery[$posIndex][$i]['image'])
                                                        <img src="{{ getImageOrPlaceholder($defaultGallery[$posIndex][$i]['image'], '300x300') }}" class="img-thumbnail mb-2" style="height: 60px;">
                                                    @endif
                                                    <input type="hidden" name="home_hero_gallery[{{ $posIndex }}][{{ $i }}][image]" value="{{ $defaultGallery[$posIndex][$i]['image'] ?? '' }}">
                                                    <input type="file" class="form-control form-control-sm" name="home_hero_gallery_files[{{ $posIndex }}][{{ $i }}]" accept="image/jpeg,image/png,image/gif,image/webp">
                                                </div>
                                                
                                                <input type="text" class="form-control form-control-sm mb-1" name="home_hero_gallery[{{ $posIndex }}][{{ $i }}][name]" value="{{ $defaultGallery[$posIndex][$i]['name'] ?? '' }}" placeholder="Alt Name">
                                                <input type="text" class="form-control form-control-sm" name="home_hero_gallery[{{ $posIndex }}][{{ $i }}][badge]" value="{{ $defaultGallery[$posIndex][$i]['badge'] ?? '' }}" placeholder="Badge (e.g. NEW)">
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3 text-primary">Category Section</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="home_category_title" value="{{ $settings['home_category_title'] ?? '' }}" placeholder="Shop by Category">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Subtitle</label>
                            <input type="text" class="form-control" name="home_category_subtitle" value="{{ $settings['home_category_subtitle'] ?? '' }}" placeholder="Browse our wide range of categories...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Badge Text</label>
                            <input type="text" class="form-control" name="home_category_badge" value="{{ $settings['home_category_badge'] ?? '' }}" placeholder="e.g. CATEGORIES">
                        </div>
                    </div>

                    <h5 class="mb-3 text-primary">Flash Sale Section</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="home_flash_title" value="{{ $settings['home_flash_title'] ?? '' }}" placeholder="Limited Time Offers">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Subtitle</label>
                            <input type="text" class="form-control" name="home_flash_subtitle" value="{{ $settings['home_flash_subtitle'] ?? '' }}" placeholder="Don't miss out on these deals...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Badge Text</label>
                            <input type="text" class="form-control" name="home_flash_badge" value="{{ $settings['home_flash_badge'] ?? '' }}" placeholder="e.g. OFFERS">
                        </div>
                    </div>

                    <h5 class="mb-3 text-primary">Promo Banner (Mid-Page)</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="home_promo_title" value="{{ $settings['home_promo_title'] ?? '' }}" placeholder="Promo Title">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subtitle</label>
                            <input type="text" class="form-control" name="home_promo_subtitle" value="{{ $settings['home_promo_subtitle'] ?? '' }}" placeholder="Promo Subtitle">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Badge Text</label>
                            <input type="text" class="form-control" name="home_promo_badge" value="{{ $settings['home_promo_badge'] ?? '' }}" placeholder="e.g. HOT TOPIC">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Banner Image</label>
                            @if(isset($settings['home_promo_image']))
                                <div class="mb-2"><img src="{{ getImageOrPlaceholder($settings['home_promo_image'], '800x200') }}" height="60" class="rounded object-fit-cover"></div>
                            @endif
                            <input type="file" class="form-control" name="home_promo_image">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Button 1 Text</label>
                            <input type="text" class="form-control" name="home_promo_btn1_text" value="{{ $settings['home_promo_btn1_text'] ?? '' }}" placeholder="Shop Now">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Button 1 Link</label>
                            <input type="text" class="form-control" name="home_promo_btn1_link" value="{{ $settings['home_promo_btn1_link'] ?? '' }}" placeholder="URL">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Button 2 Text</label>
                            <input type="text" class="form-control" name="home_promo_btn2_text" value="{{ $settings['home_promo_btn2_text'] ?? '' }}" placeholder="View Collection">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Button 2 Link</label>
                            <input type="text" class="form-control" name="home_promo_btn2_link" value="{{ $settings['home_promo_btn2_link'] ?? '' }}" placeholder="URL">
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3 text-primary">Testimonials Banner (Dark)</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="home_banner_title" value="{{ $settings['home_banner_title'] ?? '' }}" placeholder="Banner Title">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Badge Text</label>
                            <input type="text" class="form-control" name="home_banner_badge" value="{{ $settings['home_banner_badge'] ?? '' }}" placeholder="e.g. TESTIMONIALS">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="home_banner_text" rows="2" placeholder="Banner description text...">{{ $settings['home_banner_text'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Banner Image</label>
                            @if(isset($settings['home_banner_image']))
                                <div class="mb-2"><img src="{{ getImageOrPlaceholder($settings['home_banner_image'], '1200x300') }}" height="60" class="rounded object-fit-cover"></div>
                            @endif
                            <input type="file" class="form-control" name="home_banner_image">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Button Text</label>
                            <input type="text" class="form-control" name="home_banner_btn_text" value="{{ $settings['home_banner_btn_text'] ?? '' }}" placeholder="Shop Now">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Button Link</label>
                            <input type="text" class="form-control" name="home_banner_btn_link" value="{{ $settings['home_banner_btn_link'] ?? '' }}" placeholder="URL">
                        </div>
                    </div>
                    <h5 class="mb-3 text-primary">Featured Products Section</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="home_featured_title" value="{{ $settings['home_featured_title'] ?? '' }}" placeholder="Trending Products">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Subtitle</label>
                            <input type="text" class="form-control" name="home_featured_subtitle" value="{{ $settings['home_featured_subtitle'] ?? '' }}" placeholder="Check out what's popular...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Badge Text</label>
                            <input type="text" class="form-control" name="home_featured_badge" value="{{ $settings['home_featured_badge'] ?? '' }}" placeholder="e.g. LATEST">
                        </div>
                    </div>
                </div>

                <!-- SHOP PAGE TAB -->
                <div class="tab-pane fade" id="shop" role="tabpanel">
                    <h5 class="mb-3 text-primary">Shop Page Content</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Page Title (Browser Tab)</label>
                            <input type="text" class="form-control" name="shop_page_title" value="{{ $settings['shop_page_title'] ?? '' }}" placeholder="Shop All">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Breadcrumb Text</label>
                            <input type="text" class="form-control" name="shop_breadcrumb" value="{{ $settings['shop_breadcrumb'] ?? '' }}" placeholder="Shop All">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Header Title</label>
                            <input type="text" class="form-control" name="shop_header_title" value="{{ $settings['shop_header_title'] ?? '' }}" placeholder="All Products">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Header Subtitle</label>
                            <input type="text" class="form-control" name="shop_header_subtitle" value="{{ $settings['shop_header_subtitle'] ?? '' }}" placeholder="Explore our collection...">
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h5 class="mb-3 text-primary">Category Page</h5>
                    <div class="row g-3">
                         <div class="col-md-12">
                            <label class="form-label">Default Subtitle (Fallback)</label>
                            <input type="text" class="form-control" name="category_default_subtitle" value="{{ $settings['category_default_subtitle'] ?? '' }}" placeholder="Explore our complete collection...">
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3 text-primary">Product Details Page</h5>
                    <div class="row g-3">
                         <div class="col-md-12">
                            <label class="form-label">Related Products Title</label>
                            <input type="text" class="form-control" name="product_related_title" value="{{ $settings['product_related_title'] ?? '' }}" placeholder="You May Also Like">
                        </div>
                    </div>
                </div>

                <!-- AUTH PAGES TAB -->
                <div class="tab-pane fade" id="auth" role="tabpanel">
                    <h5 class="mb-3 text-primary">Login Page</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Page Title</label>
                            <input type="text" class="form-control" name="auth_login_title" value="{{ $settings['auth_login_title'] ?? '' }}" placeholder="Login">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subtitle</label>
                            <input type="text" class="form-control" name="auth_login_subtitle" value="{{ $settings['auth_login_subtitle'] ?? '' }}" placeholder="Welcome back...">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Side Image</label>
                            @if(isset($settings['auth_login_image']))
                                <div class="mb-2"><img src="{{ getImageOrPlaceholder($settings['auth_login_image'], '600x800') }}" height="100" class="rounded object-fit-cover"></div>
                            @endif
                            <input type="file" class="form-control" name="auth_login_image">
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3 text-primary">Register Page</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Page Title</label>
                            <input type="text" class="form-control" name="auth_register_title" value="{{ $settings['auth_register_title'] ?? '' }}" placeholder="Create Account">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subtitle</label>
                            <input type="text" class="form-control" name="auth_register_subtitle" value="{{ $settings['auth_register_subtitle'] ?? '' }}" placeholder="Join our community...">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Side Image</label>
                            @if(isset($settings['auth_register_image']))
                                <div class="mb-2"><img src="{{ asset($settings['auth_register_image']) }}" height="100" class="rounded object-fit-cover"></div>
                            @endif
                            <input type="file" class="form-control" name="auth_register_image">
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3 text-primary">Forgot Password Page</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Page Title</label>
                            <input type="text" class="form-control" name="auth_forgot_title" value="{{ $settings['auth_forgot_title'] ?? '' }}" placeholder="Forgot Password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subtitle</label>
                            <input type="text" class="form-control" name="auth_forgot_subtitle" value="{{ $settings['auth_forgot_subtitle'] ?? '' }}" placeholder="Enter your email to reset password...">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Side Image</label>
                            @if(isset($settings['auth_forgot_image']))
                                <div class="mb-2"><img src="{{ getImageOrPlaceholder($settings['auth_forgot_image'], '600x800') }}" height="100" class="rounded object-fit-cover"></div>
                            @endif
                            <input type="file" class="form-control" name="auth_forgot_image">
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3 text-primary">Reset Password Page</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Page Title</label>
                            <input type="text" class="form-control" name="auth_reset_title" value="{{ $settings['auth_reset_title'] ?? '' }}" placeholder="Reset Password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subtitle</label>
                            <input type="text" class="form-control" name="auth_reset_subtitle" value="{{ $settings['auth_reset_subtitle'] ?? '' }}" placeholder="Enter your new password...">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Side Image</label>
                            @if(isset($settings['auth_reset_image']))
                                <div class="mb-2"><img src="{{ getImageOrPlaceholder($settings['auth_reset_image'], '600x800') }}" height="100" class="rounded object-fit-cover"></div>
                            @endif
                            <input type="file" class="form-control" name="auth_reset_image">
                        </div>
                    </div>
                </div>

                <!-- CART & CHECKOUT TAB -->
                <div class="tab-pane fade" id="cart" role="tabpanel">
                    <h5 class="mb-3 text-primary">Cart Page</h5>
                     <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Breadcrumb Text</label>
                            <input type="text" class="form-control" name="cart_breadcrumb" value="{{ $settings['cart_breadcrumb'] ?? '' }}" placeholder="Cart">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Page Title</label>
                            <input type="text" class="form-control" name="cart_title" value="{{ $settings['cart_title'] ?? '' }}" placeholder="Shopping Cart">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Page Subtitle</label>
                            <input type="text" class="form-control" name="cart_subtitle" value="{{ $settings['cart_subtitle'] ?? '' }}" placeholder="Review your items...">
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3 text-primary">Checkout Page</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Shipping Section Title</label>
                            <input type="text" class="form-control" name="checkout_shipping_title" value="{{ $settings['checkout_shipping_title'] ?? '' }}" placeholder="Pickup and delivery options">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Section Title</label>
                            <input type="text" class="form-control" name="checkout_payment_title" value="{{ $settings['checkout_payment_title'] ?? '' }}" placeholder="Payment Method">
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-2"></i> {{ __('Save Changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
