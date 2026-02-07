<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Product\App\Models\Product;

class TryOnController extends Controller
{
    /**
     * Virtual Try-On (Preview On Me): merge customer photo with product image.
     * Uses Intervention/Image if available, otherwise PHP GD (no extra package required).
     */
    public function try(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,jpg,png|max:5120',
                'product_id' => 'required|exists:products,id',
            ]);

            $product = Product::findOrFail($request->product_id);

            if (! $product->is_tryable) {
                return response()->json([
                    'success' => false,
                    'message' => __('common.tryon_product_not_available'),
                ], 400);
            }

            $productImagePath = $this->resolveProductImagePath($product);
            if (! $productImagePath || ! file_exists($productImagePath)) {
                return response()->json([
                    'success' => false,
                    'message' => __('common.tryon_product_image_not_found'),
                ], 404);
            }

            $dir = public_path('uploads/tryon');
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            if (! is_dir($dir . '/temp')) {
                mkdir($dir . '/temp', 0755, true);
            }

            $filename = 'tryon_' . uniqid() . '_' . time() . '.png';
            $path = $dir . '/' . $filename;

            $overlayUrl = null;
            $overlayLeft = 0;
            $overlayTop = 0;
            $overlayWidth = 0;
            $overlayHeight = 0;

            $manager = $this->makeImageManager($this->getImageDriver());
            if ($manager) {
                $userImage = $manager->make($request->file('image'));
                $productImage = $manager->make($productImagePath);
                $userImage = $this->optimizeImage($userImage);
                [$targetW, $targetH, $offsetX, $offsetY] = $this->calculateOverlayDimensions(
                    $userImage->width(),
                    $userImage->height(),
                    $productImage->width(),
                    $productImage->height()
                );
                $productImage->resize($targetW, $targetH);
                $productImage->sharpen(8);
                $productImage->contrast(4);
                if (method_exists($productImage, 'blur')) {
                    $productImage->blur(1);
                }
                $overlayFilename = 'overlay_' . uniqid() . '_' . time() . '.png';
                $overlayPath = $dir . '/temp/' . $overlayFilename;
                $productImage->encode('png', 100)->save($overlayPath);
                $overlayUrl = asset('uploads/tryon/temp/' . $overlayFilename);
                $overlayLeft = max(0, $offsetX);
                $overlayTop = max(0, $offsetY);
                $overlayWidth = $targetW;
                $overlayHeight = $targetH;
                $userImage->insert($productImage, 'top-left', $overlayLeft, $overlayTop);
                $userImage->sharpen(8);
                $userImage->encode('png', 100)->save($path);
            } else {
                $gdResult = $this->mergeWithGd(
                    $request->file('image')->getRealPath(),
                    $productImagePath,
                    $path,
                    $dir . '/temp'
                );
                if (! $gdResult['success']) {
                    Log::warning('Virtual Try-On: GD merge failed.');
                    return response()->json([
                        'success' => false,
                        'message' => __('common.tryon_processing_unavailable'),
                    ], 503)->header('Cache-Control', 'no-cache, must-revalidate');
                }
                $overlayUrl = $gdResult['overlay_url'] ?? null;
                $overlayLeft = $gdResult['overlay_left'] ?? 0;
                $overlayTop = $gdResult['overlay_top'] ?? 0;
                $overlayWidth = $gdResult['overlay_width'] ?? 0;
                $overlayHeight = $gdResult['overlay_height'] ?? 0;
            }

            $this->cleanOldFiles();

            $response = [
                'success' => true,
                'image' => asset('uploads/tryon/' . $filename),
                'message' => __('common.tryon_preview_success'),
            ];
            if ($overlayUrl !== null) {
                $response['overlay_image'] = $overlayUrl;
                $response['overlay_left'] = (int) $overlayLeft;
                $response['overlay_top'] = (int) $overlayTop;
                $response['overlay_width'] = (int) $overlayWidth;
                $response['overlay_height'] = (int) $overlayHeight;
            }

            return response()->json($response)->header('Cache-Control', 'no-cache, must-revalidate');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Virtual Try-On Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => __('common.tryon_preview_failed'),
            ], 500)->header('Cache-Control', 'no-cache, must-revalidate');
        }
    }

    /**
     * Get image driver (gd or imagick). Prefer config, fallback to gd.
     */
    private function getImageDriver(): string
    {
        $driver = config('image.driver', config('app.image_driver', 'gd'));
        return in_array($driver, ['gd', 'imagick'], true) ? $driver : 'gd';
    }

    /**
     * Create Intervention ImageManager if the package is available.
     *
     * @return \Intervention\Image\ImageManager|null
     */
    private function makeImageManager(string $driver)
    {
        if (! class_exists(\Intervention\Image\ImageManager::class)) {
            return null;
        }
        try {
            return new \Intervention\Image\ImageManager(['driver' => $driver]);
        } catch (\Exception $e) {
            Log::warning('Virtual Try-On: ImageManager failed with driver ' . $driver . ': ' . $e->getMessage());
            if ($driver === 'imagick') {
                try {
                    return new \Intervention\Image\ImageManager(['driver' => 'gd']);
                } catch (\Exception $e2) {
                    return null;
                }
            }
            return null;
        }
    }

    /**
     * Merge user photo and product image using PHP GD (no Intervention required).
     * Saves overlay to temp for client-side position adjustment.
     *
     * @param  string  $userImagePath  Absolute path to uploaded user image
     * @param  string  $productImagePath  Absolute path to product image
     * @param  string  $outputPath  Where to save the result PNG
     * @param  string  $tempDir  Directory to save overlay PNG for adjustment
     * @return array{success: bool, overlay_url?: string, overlay_left?: int, overlay_top?: int, overlay_width?: int, overlay_height?: int}
     */
    private function mergeWithGd(string $userImagePath, string $productImagePath, string $outputPath, string $tempDir): array
    {
        if (! extension_loaded('gd')) {
            return ['success' => false];
        }

        $user = $this->gdLoadImage($userImagePath);
        $product = $this->gdLoadImage($productImagePath);
        if (! $user || ! $product) {
            if ($user) {
                imagedestroy($user);
            }
            if ($product) {
                imagedestroy($product);
            }
            return ['success' => false];
        }

        $userW = imagesx($user);
        $userH = imagesy($user);
        $prodW = imagesx($product);
        $prodH = imagesy($product);

        if ($userW > 1500) {
            $ratio = 1500 / $userW;
            $newW = 1500;
            $newH = (int) ($userH * $ratio);
            $resized = imagecreatetruecolor($newW, $newH);
            if ($resized) {
                imagecopyresampled($resized, $user, 0, 0, 0, 0, $newW, $newH, $userW, $userH);
                imagedestroy($user);
                $user = $resized;
                $userW = $newW;
                $userH = $newH;
            }
        }

        [$newProdW, $newProdH, $destX, $destY] = $this->calculateOverlayDimensions($userW, $userH, $prodW, $prodH);
        $newProdW = (int) $newProdW;
        $newProdH = (int) $newProdH;
        $destX = (int) $destX;
        $destY = (int) $destY;

        $productResized = imagecreatetruecolor($newProdW, $newProdH);
        if (! $productResized) {
            imagedestroy($user);
            imagedestroy($product);
            return ['success' => false];
        }
        imagealphablending($productResized, false);
        imagesavealpha($productResized, true);
        $transparent = imagecolorallocatealpha($productResized, 0, 0, 0, 127);
        imagefill($productResized, 0, 0, $transparent);
        imagecopyresampled($productResized, $product, 0, 0, 0, 0, $newProdW, $newProdH, $prodW, $prodH);
        imagedestroy($product);

        if (defined('IMG_FILTER_GAUSSIAN_BLUR')) {
            imagefilter($productResized, IMG_FILTER_GAUSSIAN_BLUR);
        }

        $overlayFilename = 'overlay_' . uniqid() . '_' . time() . '.png';
        $overlayPath = rtrim($tempDir, '/') . '/' . $overlayFilename;
        if (is_dir($tempDir)) {
            imagepng($productResized, $overlayPath, 9);
            $overlayUrl = asset('uploads/tryon/temp/' . $overlayFilename);
        } else {
            $overlayUrl = null;
        }

        $destX = max(0, min($destX, $userW - $newProdW));
        $destY = min(max(0, $destY), max(0, $userH - $newProdH));

        imagealphablending($user, true);
        imagesavealpha($user, true);
        imagecopy($user, $productResized, $destX, $destY, 0, 0, $newProdW, $newProdH);
        imagedestroy($productResized);

        $result = imagepng($user, $outputPath, 9);
        imagedestroy($user);

        return [
            'success' => (bool) $result,
            'overlay_url' => $overlayUrl,
            'overlay_left' => $destX,
            'overlay_top' => $destY,
            'overlay_width' => $newProdW,
            'overlay_height' => $newProdH,
        ];
    }

    /**
     * Load image from path into GD resource (JPEG/PNG).
     */
    private function gdLoadImage(string $path)
    {
        if (! file_exists($path)) {
            return null;
        }
        $info = @getimagesize($path);
        if (! $info) {
            return null;
        }
        $mime = $info['mime'] ?? '';
        if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
            return @imagecreatefromjpeg($path);
        }
        if ($mime === 'image/png') {
            $img = @imagecreatefrompng($path);
            if ($img) {
                imagealphablending($img, true);
                imagesavealpha($img, true);
            }
            return $img;
        }
        if ($mime === 'image/gif') {
            return @imagecreatefromgif($path);
        }
        return null;
    }

    /**
     * Resolve product image to absolute filesystem path.
     */
    private function resolveProductImagePath(Product $product): ?string
    {
        $path = $product->image;
        if (empty($path)) {
            return null;
        }
        $path = ltrim((string) $path, '/');
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->path($path);
        }
        if (strpos($path, 'storage/') === 0) {
            $rel = substr($path, strlen('storage/'));
            if (Storage::disk('public')->exists($rel)) {
                return Storage::disk('public')->path($rel);
            }
        }
        if (file_exists(public_path($path))) {
            return public_path($path);
        }

        return null;
    }

    private function optimizeImage($image)
    {
        if ($image->width() > 1500) {
            $image->resize(1500, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }
        $image->orientate();
        $image->contrast(3);
        $image->brightness(2);

        return $image;
    }

    /**
     * Calculate overlay size and position: product width = customer image width
     * so widths match; height from aspect ratio, then fit to frame.
     *
     * @return array{0: int, 1: int, 2: int, 3: int} [targetWidth, targetHeight, offsetX, offsetY]
     */
    private function calculateOverlayDimensions(int $userW, int $userH, int $prodW, int $prodH): array
    {
        $prodAspect = $prodW / max(1, $prodH);

        // Product width = customer image width (match widths)
        $targetProdW = $userW;
        $targetProdH = (int) ($targetProdW / $prodAspect);

        // If height exceeds customer image, scale down to fit
        if ($targetProdH > $userH) {
            $targetProdH = $userH;
            $targetProdW = (int) ($targetProdH * $prodAspect);
        }
        $targetProdW = min(max(1, $targetProdW), $userW);
        $targetProdH = min(max(1, $targetProdH), $userH);

        $offsetX = (int) (($userW - $targetProdW) / 2);
        $offsetY = (int) ($userH * 0.33);
        $offsetY = min(max(0, $offsetY), max(0, $userH - $targetProdH));

        return [$targetProdW, $targetProdH, $offsetX, $offsetY];
    }

    private function cleanOldFiles(): void
    {
        $directory = public_path('uploads/tryon/');
        $now = time();
        foreach (glob($directory . 'tryon_*.png') ?: [] as $file) {
            if (is_file($file) && ($now - filemtime($file)) >= 3600) {
                @unlink($file);
            }
        }
        $tempDir = $directory . 'temp';
        if (is_dir($tempDir)) {
            foreach (glob($tempDir . '/overlay_*.png') ?: [] as $file) {
                if (is_file($file) && ($now - filemtime($file)) >= 3600) {
                    @unlink($file);
                }
            }
        }
    }
}