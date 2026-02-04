<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    public function setLocale($lang)
    {
        if (in_array($lang, ['en', 'bn', 'ar'])) {
            Session::put('locale', $lang);
        }

        return back();
    }
}
