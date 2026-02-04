<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

// Mock asset function since we are in CLI
if (!function_exists('asset')) {
    function asset($path) {
        return 'http://localhost/' . $path;
    }
}

// Mock route function
if (!function_exists('route')) {
    function route($name, $parameters = []) {
        return 'http://localhost/placeholder/' . ($parameters['size'] ?? '300x300');
    }
}

// Test cases
$testCases = [
    ['path' => null, 'size' => '300x300', 'expected' => 'http://localhost/placeholder/300x300'],
    ['path' => 'https://example.com/image.jpg', 'size' => '300x300', 'expected' => 'https://example.com/image.jpg'],
    ['path' => 'uploads/image.jpg', 'size' => '300x300', 'expected' => 'http://localhost/uploads/image.jpg'],
    ['path' => 'products/image.jpg', 'size' => '300x300', 'expected' => 'http://localhost/storage/products/image.jpg'],
    ['path' => 'products/image.jpg', 'size' => 'invalid', 'expected' => 'http://localhost/storage/products/image.jpg'], // Should fallback to storage path, but helper logic for size is only for placeholder
];

// We need to load the helper file manually as it might not be autoloaded in this simple script context if not part of composer autoload files yet (it is usually)
require_once __DIR__ . '/app/Helpers/helpers.php';

foreach ($testCases as $case) {
    $result = getImageOrPlaceholder($case['path'], $case['size']);
    echo "Path: " . ($case['path'] ?? 'null') . ", Size: " . $case['size'] . "\n";
    echo "Result: " . $result . "\n";
    // echo "Expected: " . $case['expected'] . "\n"; // Expected might differ due to asset/route mocking details
    echo "--------------------------------\n";
}
