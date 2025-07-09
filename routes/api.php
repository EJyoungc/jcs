<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/avatar', function (Request $request) {
    // Retrieve query parameters.
    $name = $request->query('name', 'JD'); // Expected to be initials.
    $size = intval($request->query('size', 128));
    $textColor = $request->query('color', '7F9CF5'); // Without the '#' character.
    $backgroundColor = $request->query('background', 'EBF4FF'); // Without the '#' character.

    // Create a blank image canvas.
    $img = imagecreatetruecolor($size, $size);
    imagesavealpha($img, true);
    imagealphablending($img, false);

    // Convert hex colors to RGB values.
    list($bgR, $bgG, $bgB) = sscanf($backgroundColor, "%02x%02x%02x");
    list($txtR, $txtG, $txtB) = sscanf($textColor, "%02x%02x%02x");

    // Allocate colors.
    $bgColorAllocated = imagecolorallocate($img, $bgR, $bgG, $bgB);
    $textColorAllocated = imagecolorallocate($img, $txtR, $txtG, $txtB);

    // Fill the background.
    imagefill($img, 0, 0, $bgColorAllocated);

    // Set font parameters.
    $fontSize = intval($size / 3.5);
    $fontPath = public_path('fonts/static/OpenSans-Medium.ttf');
    if (!file_exists($fontPath)) {
        abort(500, 'Font file not found.');
    }

    // Get the bounding box of the text.
    $bbox = imagettfbbox($fontSize, 0, $fontPath, $name);
    // Calculate text width.
    $textWidth = $bbox[2] - $bbox[0];
    // Calculate text height (using baseline to top of text).
    $textHeight = $bbox[1] - $bbox[7];

    // Center the text.
    $x = ($size - $textWidth) / 2;
    $y = ($size + $textHeight) / 2;

    // Add the initials text to the image.
    imagettftext($img, $fontSize, 0, $x, $y, $textColorAllocated, $fontPath, $name);

    // Output the image as a PNG.
    header('Content-Type: image/png');
    imagepng($img);
    imagedestroy($img);
    exit;
});

