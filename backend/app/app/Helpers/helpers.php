<?php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

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
function error_response($message = 'Unauthenticated user!', $data = null, $status_code = 422)
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
function image_link_generator($image_file, $thumbnail_path = '/products/', $any_unique_info = '',$size = [400,400])
{
    // Get extension safely
    $ext = $image_file->getClientOriginalExtension();

    $slug = slugify($any_unique_info);
    $uniqueId = Str::random(6);
    $timestamp = now()->format('YmdHis');
    $full_name = "{$slug}-{$uniqueId}-{$timestamp}.{$ext}";

    $path = $thumbnail_path . $full_name;

    // Store file in storage/app/public/gallery/products/
    Storage::disk('public')->putFileAs($thumbnail_path, $image_file, $full_name);

    // Return the path relative to storage
    return 'storage' . $path;
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