<?php
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;

// api success response
function success_response($message = 'data fetched', $data = null, $status_code = 200)
{
    $res = [
        'status' => true,
        'message' => $message,
        'data' => $data,
    ];

    return response()->json($res, $status_code);
}

// api error response
function error_response($message = 'Unauthenticated Admin!', $data = null, $status_code = 422)
{
    $res = [
        'status' => false,
        'message' => $message,
        'data' => $data,
    ];

    return response()->json($res, $status_code);
}

#slugify category or post
/**
 * @param $text
 * @return bool|false|string|string[]|null
 */
function slugify($text)
{
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, '-');

    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }
    return $text;
}


# image link generator
// function image_link_generator($image_file, $thumbnail_path = '/products/', $any_unique_info = '',$size = [400,400])
// {
//     // Get extension safely
//     $ext = $image_file->getClientOriginalExtension();

//     $slug = slugify($any_unique_info);
//     $uniqueId = Str::random(6);
//     $timestamp = now()->format('YmdHis');
//     $full_name = "{$slug}-{$uniqueId}-{$timestamp}.{$ext}";

//     $path = $thumbnail_path . $full_name;

//     // Store file in storage/app/public/gallery/products/
//     Storage::disk('public')->putFileAs($thumbnail_path, $image_file, $full_name);

//     // Return the path relative to storage
//     return 'storage' . $path;
// }

// function delete_image($path)
// {
//     $image = str_replace('storage/', '', $path);
//     if (Storage::disk('public')->exists($image)) {
//         Storage::disk('public')->delete($image);
//         return true;
//     }
//     return false;
// }

// function image_url($image){
//     $baseUrl = config('app.user_backend_url', config('app.url', 'http://api.mydomain.com'));
//     // Remove trailing slash if present
//     $baseUrl = rtrim($baseUrl, '/');
//     return $image?($baseUrl . '/' . $image):'';
// }

function image_link_generator($image_file, $thumbnail_path = 'products/', $any_unique_info = '', $size = 820, $crop = true)
{
    // 1. Sanitize Path
    $thumbnail_path = rtrim($thumbnail_path, '/') . '/';

    // 2. SEO Friendly Filename
    $slug = Str::slug($any_unique_info) ?: 'shared-file';
    $uniqueId = Str::random(6);
    $timestamp = now()->format('YmdHis');
    $full_name = "{$slug}-{$uniqueId}-{$timestamp}.webp";

    // 3. Read Image
    $img = Image::read($image_file);

    if ($crop) {
        // --- SHARED IMAGE MODE (Square) ---
        // 'cover' crops the image to fill the $size x $size area.
        // We use the original dimension if it's smaller than $size to prevent upscaling blur.
        $originalMinSide = min($img->width(), $img->height());
        $targetSize = ($originalMinSide < $size) ? $originalMinSide : $size;

        $img->cover($targetSize, $targetSize, 'center');
        
        // Light sharpening makes image edges "pop" after resizing
        $img->sharpen(10);
    } else {
        // --- DOCUMENT MODE (Natural Ratio) ---
        // We only scale down if it's huge (1600px+). 
        // scaleDown ensures we NEVER stretch a small image upward (which causes blur).
        $img->scaleDown(width: 1200);
        
        // Documents need more detail preserved
        $img->sharpen(5);
    }

    // 4. Encode to WebP with High Quality
    // 92 quality provides near-lossless clarity while still being much smaller than PNG/JPG
    $processedImage = $img->toWebp(92);

    // 5. Save to Public Storage (now shared)
    $savePath = $thumbnail_path . $full_name;
    Storage::disk('public')->put($savePath, (string) $processedImage);

    // 6. Return the relative URL for database storage
    return 'storage/' . $savePath;
}

function delete_image($path)
{
    $image = str_replace('storage/', '', $path);
    if (Storage::disk('public')->exists($image)) {
        Storage::disk('public')->delete($image);
        return true;
    }
    return false;
}

function image_url($image){
    return $image?asset($image):'';
}