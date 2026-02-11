<?php

namespace App\Http\Controllers;
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

        $backgroundColor = '#e8e8e8';
        $textColor = '#9e9e9e';
        $dimensionText = $width . 'x' . $height;
        $fontSize = (int) min($width, $height) * 0.08;
        $fontSize = max(12, min($fontSize, 72));
        $cx = (int) ($width / 2);
        $cy = (int) ($height / 2);

        $svg = <<<SVG
<svg width="{$width}" height="{$height}" xmlns="http://www.w3.org/2000/svg">
    <rect width="100%" height="100%" fill="{$backgroundColor}"/>
    <text x="{$cx}" y="{$cy}" font-family="Arial, sans-serif" font-size="{$fontSize}" fill="{$textColor}" text-anchor="middle" dominant-baseline="middle">{$dimensionText}</text>
</svg>
SVG;

        return Response::make($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'no-cache, max-age=0, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }
}
