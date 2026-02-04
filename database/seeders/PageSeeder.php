<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'image' => null,
                'content' => <<<'EOT'
<!-- Hero Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="/placeholder/812x590" alt="About LŪXĒ" class="img-fluid rounded-3 shadow-sm">
            </div>
            <div class="col-lg-6 ps-lg-5">
                <h1 class="display-4 fw-bold mb-3">About <span class="text-primary">LŪXĒ</span> Ecommerce</h1>
                <h4 class="text-muted mb-4">Where Innovation Meets Seamless Online Shopping</h4>
                <p class="lead mb-4">
                    At LŪXĒ Ecommerce, we redefine eCommerce with a seamless, secure, and user-friendly shopping experience. 
                    As a premier online marketplace, we connect buyers and sellers through cutting-edge technology, 
                    ensuring effortless transactions and customer satisfaction.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Our Story Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Our <span class="text-primary">Story</span></h2>
            <p class="text-muted">From a Bold Vision to a Trusted Marketplace: How LŪXĒ Ecommerce Was Built to Transform the Future of eCommerce</p>
        </div>

        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="bg-white p-4 h-100 rounded-3 shadow-sm">
                    <img src="/placeholder/903x880" alt="Our Vision" class="img-fluid mb-3 rounded">
                    <h5 class="fw-bold">Our Vision and Beginning</h5>
                    <p class="text-muted small">Transforming the eCommerce Landscape with a Vision to Connect Buyers and Sellers Seamlessly</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-white p-4 h-100 rounded-3 shadow-sm">
                    <img src="/placeholder/858x600" alt="Challenges" class="img-fluid mb-3 rounded">
                    <h5 class="fw-bold">Overcoming Challenges</h5>
                    <p class="text-muted small">Navigating Obstacles with Innovation to Build a Reliable and Secure Marketplace</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-white p-4 h-100 rounded-3 shadow-sm">
                    <img src="/placeholder/1181x880" alt="Future" class="img-fluid mb-3 rounded">
                    <h5 class="fw-bold">Our Future Vision</h5>
                    <p class="text-muted small">Continuing to Innovate and Grow, Building a Sustainable eCommerce Ecosystem for the Future</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Our <span class="text-primary">Mission</span> & <span class="text-primary">Vision</span></h2>
            <p class="text-muted">At LŪXĒ Ecommerce, we are committed to revolutionizing eCommerce by creating a seamless, secure, and customer-centric marketplace.</p>
        </div>

        <div class="row align-items-center mb-5">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="/placeholder/952x768" alt="Mission" class="img-fluid rounded-3 shadow-sm">
            </div>
            <div class="col-lg-6 ps-lg-5">
                <p class="lead">
                    Make online shopping easy, reliable, and enjoyable for customers. Enable sellers to grow by offering powerful tools and support. 
                    Deliver competitive prices, quality products, and exceptional service. Foster a trusted marketplace built on transparency and integrity.
                </p>
            </div>
        </div>

        <div class="row align-items-center flex-lg-row-reverse">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="/placeholder/858x600" alt="Vision" class="img-fluid rounded-3 shadow-sm">
            </div>
            <div class="col-lg-6 pe-lg-5 text-lg-end">
                <p class="lead">
                    To be the world’s most customer-centric eCommerce platform, where people can find and discover anything they want to buy online. 
                    To empower businesses of all sizes to reach a global audience and thrive in the digital economy.
                </p>
            </div>
        </div>
    </div>
</section>
EOT
            ],
            [
                'title' => 'Terms and Conditions',
                'slug' => 'terms-and-conditions',
                'image' => null,
                'content' => <<<'EOT'
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="mb-4">Terms and Conditions</h2>
        <p>Welcome to LŪXĒ Ecommerce! These terms and conditions outline the rules and regulations for the use of our website.</p>
        
        <h4 class="mt-4">1. Introduction</h4>
        <p>By accessing this website we assume you accept these terms and conditions. Do not continue to use LŪXĒ Ecommerce if you do not agree to take all of the terms and conditions stated on this page.</p>
        
        <h4 class="mt-4">2. Cookies</h4>
        <p>We employ the use of cookies. By accessing LŪXĒ Ecommerce, you agreed to use cookies in agreement with the LŪXĒ Ecommerce's Privacy Policy.</p>
        
        <h4 class="mt-4">3. License</h4>
        <p>Unless otherwise stated, LŪXĒ Ecommerce and/or its licensors own the intellectual property rights for all material on LŪXĒ Ecommerce. All intellectual property rights are reserved.</p>
    </div>
</section>
EOT
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'image' => null,
                'content' => <<<'EOT'
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="mb-4">Privacy Policy</h2>
        <p>At LŪXĒ Ecommerce, accessible from our website, one of our main priorities is the privacy of our visitors.</p>
        
        <h4 class="mt-4">1. Log Files</h4>
        <p>LŪXĒ Ecommerce follows a standard procedure of using log files. These files log visitors when they visit websites. All hosting companies do this and a part of hosting services' analytics.</p>
        
        <h4 class="mt-4">2. Privacy Policies</h4>
        <p>You may consult this list to find the Privacy Policy for each of the advertising partners of LŪXĒ Ecommerce.</p>
        
        <h4 class="mt-4">3. Third Party Privacy Policies</h4>
        <p>LŪXĒ Ecommerce's Privacy Policy does not apply to other advertisers or websites. Thus, we are advising you to consult the respective Privacy Policies of these third-party ad servers for more detailed information.</p>
    </div>
</section>
EOT
            ],
            [
                'title' => 'Shipping Policy',
                'slug' => 'shipping-policy',
                'image' => null,
                'content' => <<<'EOT'
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="mb-4">Shipping Policy</h2>
        <p>At LŪXĒ Ecommerce, we aim to deliver your orders efficiently and safely. Please read our shipping policy carefully.</p>
        
        <h4 class="mt-4">1. Shipping Methods</h4>
        <p>We offer various shipping methods including standard, express, and overnight delivery. Availability depends on your location.</p>
        
        <h4 class="mt-4">2. Processing Time</h4>
        <p>Orders are typically processed within 1-2 business days. During peak seasons, processing times may vary.</p>
        
        <h4 class="mt-4">3. Shipping Rates</h4>
        <p>Shipping rates are calculated based on weight, dimensions, and destination. Free shipping may apply for orders over a certain amount.</p>
    </div>
</section>
EOT
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}
