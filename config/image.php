<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Image driver (Virtual Try-On / Intervention Image)
    |--------------------------------------------------------------------------
    | Use 'gd' (default) or 'imagick'. Imagick gives better quality.
    | Ensure the chosen extension is enabled in PHP.
    */
    'driver' => env('IMAGE_DRIVER', 'gd'),
];
