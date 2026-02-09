<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class HomePageSeeder extends Seeder
{
    /**
     * Seed home page content so the frontend displays correctly after db:seed.
     * All keys are editable from Admin → Website Setup.
     */
    public function run(): void
    {
        $settings = [
            'home_hero_title' => 'Elegance in Every Stitch',
            'home_hero_subtitle' => 'Discover our exclusive collection of premium dresses and fashion wear designed to make you shine on every occasion.',
            'home_category_title' => 'Shop by Category',
            'home_category_subtitle' => 'Explore our diverse range of carefully selected products for every room and occasion',
            'home_category_badge' => 'CATEGORIES',
            'home_flash_title' => 'Limited Time Offers',
            'home_flash_badge' => 'OFFERS',
            'home_promo_title' => 'Fashion That Speaks For Itself',
            'home_promo_subtitle' => 'Discover the latest trends in kids\' and women\'s fashion. From elegant evening gowns to comfortable casual wear, we have it all.',
            'home_promo_badge' => 'HOT TOPIC',
            'home_promo_btn1_text' => 'Shop Now',
            'home_promo_btn1_link' => '',
            'home_promo_btn2_text' => 'View Collections',
            'home_promo_btn2_link' => '',
            'home_featured_title' => 'Trending Products',
            'home_featured_badge' => 'LATEST',
            'home_latest_title' => 'Latest Products',
            'home_latest_badge' => 'NEW ARRIVALS',
            'home_banner_title' => 'Crafted with Care, Designed for You',
            'home_banner_badge' => 'TESTIMONIALS',
            'home_banner_text' => 'Every piece in our collection tells a story of craftsmanship, sustainability, and timeless design. We believe in creating fashion that enhances your personal style.',
            'home_banner_btn_text' => 'Shop Now',
            'home_banner_btn_link' => '',
            'home_banner_rating' => '4.9',
            'home_banner_review_count' => '15K+',
            'home_banner_testimonial_1_name' => 'John D.',
            'home_banner_testimonial_2_name' => 'Sarah J.',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value === null ? '' : (string) $value]
            );
        }

        Cache::forget('settings_all');
    }
}
