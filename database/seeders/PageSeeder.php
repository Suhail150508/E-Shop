<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates standard store pages with professional, project-appropriate content.
     */
    public function run(): void
    {
        $appName = config('app.name');

        $aboutMeta = [
            'about_hero_title' => 'About ' . $appName,
            'about_hero_subtitle' => 'Where Innovation Meets Seamless Online Shopping',
            'about_hero_text' => 'We are committed to redefining eCommerce with a seamless, secure, and user-friendly experience—connecting buyers and sellers through cutting-edge technology.',
            'about_hero_image' => null,
            'about_story_title' => 'Our Story',
            'about_story_subtitle' => 'From a Bold Vision to a Trusted Marketplace: How ' . $appName . ' was built to transform the future of eCommerce.',
            'about_story_1_image' => null,
            'about_story_1_heading' => 'Our Vision and Beginning',
            'about_story_1_text' => 'Redefining the eCommerce landscape with a vision to connect buyers and sellers seamlessly.',
            'about_story_2_image' => null,
            'about_story_2_heading' => 'Overcoming Challenges',
            'about_story_2_text' => 'Navigating obstacles with innovation to build a reliable and secure marketplace.',
            'about_story_3_image' => null,
            'about_story_3_heading' => 'Our Future Vision',
            'about_story_3_text' => 'Continuing to innovate and grow, building a sustainable eCommerce ecosystem for the future.',
            'about_mission_title' => 'Our Mission & Vision',
            'about_mission_intro' => 'We are committed to revolutionizing eCommerce by creating a seamless, secure, and customer-centric marketplace.',
            'about_mission_1_image' => null,
            'about_mission_1_text' => 'We make online shopping easy and reliable, empower sellers, and foster a trusted marketplace for everyone.',
            'about_mission_2_image' => null,
            'about_mission_2_text' => 'We revolutionize digital commerce by integrating cutting-edge technology, expanding globally, and connecting buyers and sellers worldwide.',
            'about_testimonial_title' => 'Testimonials & Success Stories',
            'about_testimonial_subtitle' => 'From a Bold Vision to a Trusted Marketplace: How ' . $appName . ' was built to transform the future of eCommerce.',
            'about_testimonial_1_avatar' => null,
            'about_testimonial_1_name' => 'Bill Gates',
            'about_testimonial_1_role' => 'CIO',
            'about_testimonial_1_quote' => 'A game-changing platform for modern commerce.',
            'about_testimonial_2_avatar' => null,
            'about_testimonial_2_name' => 'John Anderson',
            'about_testimonial_2_role' => 'CTO',
            'about_testimonial_2_quote' => 'Innovation and reliability at its best.',
        ];

        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'image' => null,
                'is_active' => true,
                'content' => '',
                'meta' => $aboutMeta,
            ],
            [
                'title' => 'Terms and Conditions',
                'slug' => 'terms-and-conditions',
                'image' => null,
                'is_active' => true,
                'content' => <<<EOT
<section class="py-5 bg-white">
    <div class="container">
        <h1 class="mb-4">Terms and Conditions</h1>
        <p>Welcome to {$appName}. By using this website you accept these terms. Please read them carefully.</p>
        <h2 class="h5 fw-bold mt-4">1. Use of Service</h2>
        <p>You may use our store for lawful shopping only. You must provide accurate information when creating an account or placing an order.</p>
        <h2 class="h5 fw-bold mt-4">2. Orders and Payment</h2>
        <p>By placing an order you agree to pay the total amount due. We reserve the right to refuse or cancel orders in case of error, fraud, or stock unavailability.</p>
        <h2 class="h5 fw-bold mt-4">3. Intellectual Property</h2>
        <p>All content on this site (text, images, logos) is owned by {$appName} or its licensors and is protected by applicable law. You may not copy or use it without permission.</p>
        <h2 class="h5 fw-bold mt-4">4. Contact</h2>
        <p>For questions about these terms, please contact us via the contact page.</p>
    </div>
</section>
EOT
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'image' => null,
                'is_active' => true,
                'content' => <<<EOT
<section class="py-5 bg-white">
    <div class="container">
        <h1 class="mb-4">Privacy Policy</h1>
        <p>At {$appName}, we take your privacy seriously. This policy explains how we collect, use, and protect your information.</p>
        <h2 class="h5 fw-bold mt-4">1. Information We Collect</h2>
        <p>We collect information you provide when registering, placing orders, or contacting us (e.g. name, email, address, phone). We also collect technical data such as IP address and browser type for security and analytics.</p>
        <h2 class="h5 fw-bold mt-4">2. How We Use Your Information</h2>
        <p>We use your information to process orders, communicate with you, improve our service, and comply with legal obligations. We do not sell your personal data to third parties.</p>
        <h2 class="h5 fw-bold mt-4">3. Data Security</h2>
        <p>We use industry-standard measures to protect your data. Payment information is processed securely via our payment providers.</p>
        <h2 class="h5 fw-bold mt-4">4. Your Rights</h2>
        <p>You may request access to or correction of your personal data by contacting us. You can also opt out of marketing communications at any time.</p>
    </div>
</section>
EOT
            ],
            [
                'title' => 'Shipping Policy',
                'slug' => 'shipping-policy',
                'image' => null,
                'is_active' => true,
                'content' => <<<EOT
<section class="py-5 bg-white">
    <div class="container">
        <h1 class="mb-4">Shipping Policy</h1>
        <p>We aim to deliver your orders safely and on time. Please read the following information.</p>
        <h2 class="h5 fw-bold mt-4">1. Processing Time</h2>
        <p>Orders are typically processed within 1–2 business days. You will receive an email when your order is shipped.</p>
        <h2 class="h5 fw-bold mt-4">2. Shipping Methods and Delivery</h2>
        <p>We use reliable courier or postal services. Delivery times depend on your location. Tracking information will be shared when available.</p>
        <h2 class="h5 fw-bold mt-4">3. Shipping Costs</h2>
        <p>Shipping costs are calculated at checkout based on your address and order details. Free shipping may apply for orders above a certain value as shown on the site.</p>
        <h2 class="h5 fw-bold mt-4">4. Damaged or Lost Items</h2>
        <p>If your order arrives damaged or is lost in transit, please contact us with your order number and we will assist you according to our refund and return policy.</p>
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
