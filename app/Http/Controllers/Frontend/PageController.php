<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
    private function getPage($slug)
    {
        return Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
    }

    public function about()
    {
        $page = $this->getPage('about-us');

        return view('frontend.pages.about', compact('page'));
    }

    public function contact()
    {
        // Contact page usually has a form, so it might be hybrid.
        // But we can still fetch dynamic content if available.
        // If not seeded, we might fallback or handle gracefully.
        // For now, I'll assume standard pages are in DB or will be.
        // Since I only seeded About and Terms, I should be careful.

        // Actually, for contact, it's often a form. The user asked for "dynamic admin panel".
        // If I force it to be a generic page, I lose the form.
        // I should check if there's a specific contact page content in DB.
        // If not, I'll keep the view static but maybe pass a page object if it exists.

        $page = Page::where('slug', 'contact-us')->where('is_active', true)->first();

        return view('frontend.pages.contact', compact('page'));
    }

    public function terms()
    {
        $page = $this->getPage('terms-and-conditions');

        return view('frontend.pages.terms', compact('page'));
    }

    public function privacy()
    {
        $page = Page::where('slug', 'privacy-policy')->where('is_active', true)->firstOrFail();

        return view('frontend.pages.privacy', compact('page'));
    }

    public function shipping()
    {
        $page = Page::where('slug', 'shipping-policy')->where('is_active', true)->firstOrFail();

        return view('frontend.pages.shipping', compact('page'));
    }

    public function coupons()
    {
        // Coupons page is likely functional (listing coupons).
        // Maybe just a header/banner from dynamic page?
        $page = Page::where('slug', 'coupons')->where('is_active', true)->first();

        return view('frontend.pages.coupons', compact('page'));
    }
}
