<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Page;

class PageController extends Controller
{
    private function getPage($slug)
    {
        return Page::with('contentTranslations')->where('slug', $slug)->where('is_active', true)->firstOrFail();
    }

    public function about()
    {
        $page = $this->getPage('about-us');

        return view('frontend.pages.about', compact('page'));
    }

    public function contact()
    {
        $page = Page::with('contentTranslations')->where('slug', 'contact-us')->where('is_active', true)->first();

        return view('frontend.pages.contact', compact('page'));
    }

    public function terms()
    {
        $page = $this->getPage('terms-and-conditions');

        return view('frontend.pages.terms', compact('page'));
    }

    public function privacy()
    {
        $page = Page::with('contentTranslations')->where('slug', 'privacy-policy')->where('is_active', true)->firstOrFail();

        return view('frontend.pages.privacy', compact('page'));
    }

    public function shipping()
    {
        $page = Page::with('contentTranslations')->where('slug', 'shipping-policy')->where('is_active', true)->firstOrFail();

        return view('frontend.pages.shipping', compact('page'));
    }

    public function coupons()
    {
        $coupons = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now()->startOfDay());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('frontend.pages.coupons', compact('coupons'));
    }
}
