<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    public function setLocale($lang)
    {
        $available = config('app.available_locales', ['en', 'bn', 'ar']);
        if (in_array($lang, $available)) {
            Session::put('locale', $lang);
        }

        return back();
    }
}
