<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'app_name' => 'nullable|string|max:255',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'app_favicon' => 'nullable|image|mimes:ico,png,jpg,svg|max:1024',
            'app_currency' => 'nullable|string|max:10',

            // Shipping Settings
            'shipping_inside_city_name' => 'nullable|string|max:255',
            'shipping_inside_city_cost' => 'nullable|numeric|min:0',
            'shipping_outside_city_cost' => 'nullable|numeric|min:0',
            'free_shipping_min_amount' => 'nullable|numeric|min:0',

            // Footer Settings
            'footer_description' => 'nullable|string|max:1000',
            'footer_phone' => 'nullable|string|max:50',
            'footer_email' => 'nullable|email|max:100',
            'footer_address' => 'nullable|string|max:500',
            'payment_method_image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'social_facebook' => 'nullable|url|max:255',
            'social_twitter' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_linkedin' => 'nullable|url|max:255',
            'copyright_text' => 'nullable|string|max:255',
        ];
    }
}
