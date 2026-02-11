<!-- Newsletter -->
<section class="newsletter-section newsletter-terracotta">
    <div class="container">
        <div class="fade-in">
            <h2 class="newsletter-title">{{ __('common.subscribe_newsletter') }}</h2>
            <p class="newsletter-description">
                {{ __('common.subscribe_text') }}
            </p>
            <form class="newsletter-form" id="newsletterForm" action="{{ route('newsletter.subscribe') }}" method="POST">
                @csrf
                <div class="row g-3 justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <div class="input-group newsletter-input-group">
                            <input type="email" name="email" class="form-control newsletter-input" placeholder="{{ __('common.enter_email') }}" required aria-label="Email">
                            <button class="btn newsletter-btn position-relative" type="submit" id="newsletterBtn">
                                <span class="btn-text">{{ __('common.subscribe') }}</span>
                                <span class="loader-inline d-none" role="status" aria-hidden="true"><span class="block"></span><span class="block"></span><span class="block"></span><span class="block"></span></span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <div class="footer-brand">{{ setting('app_name') ?: config('app.name') }}</div>
                <p class="footer-description">
                    {{ setting('footer_description') ?: __('common.footer_description') }}
                </p>

                <div class="footer-contact mb-4">
                    @if(setting('footer_phone') || setting('footer_email') || setting('footer_address'))
                        @if(setting('footer_phone'))
                            <div class="d-flex align-items-center mb-2 text-white-50">
                                <i class="fas fa-phone-alt me-2"></i>
                                <span class="fs-14">{{ setting('footer_phone') }}</span>
                            </div>
                        @endif
                        @if(setting('footer_email'))
                            <div class="d-flex align-items-center mb-2 text-white-50">
                                <i class="fas fa-envelope me-2"></i>
                                <a href="mailto:{{ setting('footer_email') }}" class="text-white-50 text-decoration-none fs-14 hover-white">{{ setting('footer_email') }}</a>
                            </div>
                        @endif
                        @if(setting('footer_address'))
                            <div class="d-flex align-items-start text-white-50">
                                <i class="fas fa-map-marker-alt mt-1 me-2"></i>
                                <span class="fs-14">{{ setting('footer_address') }}</span>
                            </div>
                        @endif
                    @else
                        <div class="d-flex align-items-center mb-2 text-white-50">
                            <i class="fas fa-phone-alt me-2"></i>
                            <span class="fs-14">{{ __('common.phone_number') }}</span>
                        </div>
                        <div class="d-flex align-items-center mb-2 text-white-50">
                            <i class="fas fa-envelope me-2"></i>
                            <a href="mailto:{{ __('common.email_address') }}" class="text-white-50 text-decoration-none fs-14 hover-white">{{ __('common.email_address') }}</a>
                        </div>
                        <div class="d-flex align-items-start text-white-50">
                            <i class="fas fa-map-marker-alt mt-1 me-2"></i>
                            <span class="fs-14">{{ __('common.address') }}</span>
                        </div>
                    @endif
                </div>

                <div class="social-links">
                    @php
                        $socials = [
                            'facebook' => ['url' => setting('social_facebook') ?: config('services.social.facebook'), 'icon' => 'fab fa-facebook-f'],
                            'instagram' => ['url' => setting('social_instagram') ?: config('services.social.instagram'), 'icon' => 'fab fa-instagram'],
                            'twitter' => ['url' => setting('social_twitter') ?: config('services.social.twitter'), 'icon' => 'fab fa-twitter'],
                            'linkedin' => ['url' => setting('social_linkedin') ?: config('services.social.linkedin'), 'icon' => 'fab fa-linkedin-in'],
                        ];
                    @endphp
                    @foreach ($socials as $social)
                        @if ($social['url'])
                            <a href="{{ $social['url'] }}" class="social-link" target="_blank" rel="noopener noreferrer">
                                <i class="{{ $social['icon'] }}"></i>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
            
            <div class="col-lg-2 col-6 col-md-4">
                @if(isset($footerMenu1) && $footerMenu1->items->isNotEmpty())
                    <h4 class="footer-title">{{ $footerMenu1->name }}</h4>
                    <ul class="footer-links">
                        @foreach($footerMenu1->items as $item)
                            <li><a href="{{ $item->url }}" target="{{ $item->target }}">{{ $item->title }}</a></li>
                        @endforeach
                    </ul>
                @else
                    <h4 class="footer-title">{{ __('common.shop') }}</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('shop.index') }}">{{ __('common.new_arrivals') }}</a></li>
                        <li><a href="{{ route('shop.index') }}">{{ __('common.best_sellers') }}</a></li>
                        <li><a href="{{ route('shop.index') }}">{{ __('common.sale') }}</a></li>
                        <li><a href="{{ route('shop.index') }}">{{ __('common.gift_cards') }}</a></li>
                    </ul>
                @endif
            </div>
            <div class="col-lg-2 col-6 col-md-4">
                @if(isset($footerMenu2) && $footerMenu2->items->isNotEmpty())
                    <h4 class="footer-title">{{ $footerMenu2->name }}</h4>
                    <ul class="footer-links">
                        @foreach($footerMenu2->items as $item)
                            <li><a href="{{ $item->url }}" target="{{ $item->target }}">{{ $item->title }}</a></li>
                        @endforeach
                    </ul>
                @else
                    <h4 class="footer-title">{{ __('common.help') }}</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('pages.contact') }}">{{ __('common.customer_service') }}</a></li>
                        <li><a href="{{ route('customer.orders.index') }}">{{ __('common.track_order') }}</a></li>
                        <li><a href="{{ route('pages.contact') }}">{{ __('common.returns') }}</a></li>
                        <li><a href="{{ route('pages.shipping') }}">{{ __('common.shipping_info') }}</a></li>
                    </ul>
                @endif
            </div>
            <div class="col-lg-2 col-6 col-md-4">
                @if(isset($footerMenu3) && $footerMenu3->items->isNotEmpty())
                    <h4 class="footer-title">{{ $footerMenu3->name }}</h4>
                    <ul class="footer-links">
                        @foreach($footerMenu3->items as $item)
                            <li><a href="{{ $item->url }}" target="{{ $item->target }}">{{ $item->title }}</a></li>
                        @endforeach
                    </ul>
                @else
                    <h4 class="footer-title">{{ __('common.account') }}</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('customer.dashboard') }}">{{ __('common.my_account') }}</a></li>
                        <li><a href="{{ route('wishlist.index') }}">{{ __('common.wishlist') }}</a></li>
                        <li><a href="{{ route('cart.index') }}">{{ __('common.cart') }}</a></li>
                        <li><a href="{{ route('checkout.shipping') }}">{{ __('common.checkout') }}</a></li>
                    </ul>
                @endif
            </div>
            <div class="col-lg-2 col-6 col-md-4">
                @if(isset($footerMenu4) && $footerMenu4->items->isNotEmpty())
                    <h4 class="footer-title">{{ $footerMenu4->name }}</h4>
                    <ul class="footer-links">
                        @foreach($footerMenu4->items as $item)
                            <li><a href="{{ $item->url }}" target="{{ $item->target }}">{{ $item->title }}</a></li>
                        @endforeach
                    </ul>
                @else
                    <h4 class="footer-title">{{ __('common.about') }}</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('pages.about') }}">{{ __('common.our_story') }}</a></li>
                        <li><a href="{{ route('pages.about') }}">{{ __('common.careers') }}</a></li>
                        <li><a href="{{ route('pages.contact') }}">{{ __('common.contact') }}</a></li>
                        <li><a href="{{ route('pages.terms') }}">{{ __('common.terms') }}</a></li>
                    </ul>
                @endif
            </div>
        </div>
        
        <div class="footer-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <p class="mb-0">{{ setting('copyright_text') ?? __('common.copyright', ['year' => date('Y')]) }} | <a href="{{ route('pages.privacy') }}" class="text-white text-decoration-none">{{ __('common.privacy') }}</a> | <a href="{{ route('pages.terms') }}" class="text-white text-decoration-none">{{ __('common.terms') }}</a></p>
            @if(setting('payment_method_image'))
                <div class="payment-methods">
                    <img src="{{ getImageOrPlaceholder(setting('payment_method_image'), '400x30') }}" alt="{{ __('common.payment_methods') }}" class="img-fluid" style="max-height: 30px;">
                </div>
            @endif
        </div>
    </div>
</footer>

@push('scripts')
<script src="{{ asset('frontend/js/newsletter.js') }}"></script>
@endpush
