<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class PlaceholderController extends Controller
{
    /**
     * Generate a placeholder SVG image.
     *
     * @param  string  $size
     * @return \Illuminate\Http\Response
     */
    public function show($size)
    {
        // Parse size (e.g., 300x200)
        $parts = explode('x', $size);
        $width = isset($parts[0]) ? (int) $parts[0] : 300;
        $height = isset($parts[1]) ? (int) $parts[1] : 300;

        // Ensure valid dimensions
        $width = max(10, min($width, 2000));
        $height = max(10, min($height, 2000));

        $backgroundColor = '#f8f9fa';
        $textColor = '#6c757d';
        $fontSize = min($width, $height) / 5;
        $text = "{$width}x{$height}";

        $svg = <<<SVG
<svg width="{$width}" height="{$height}" xmlns="http://www.w3.org/2000/svg">
    <rect width="100%" height="100%" fill="{$backgroundColor}"/>
    <text x="50%" y="50%" font-family="Arial, sans-serif" font-size="{$fontSize}" fill="{$textColor}" text-anchor="middle" dy=".3em">{$text}</text>
    <rect width="100%" height="100%" fill="none" stroke="#dee2e6" stroke-width="2"/>
</svg>
SVG;

        return Response::make($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
