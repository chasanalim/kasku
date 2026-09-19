<?php

/**
 * Generate PWA icons from public/assets/logo.png.
 *
 * Usage: php scripts/generate-pwa-icons.php
 *
 * Output:
 *   public/icons/icon-192x192.png          (any purpose)
 *   public/icons/icon-512x512.png          (any purpose)
 *   public/icons/icon-512x512-maskable.png (maskable, background filled)
 *   public/icons/apple-touch-icon.png      (180x180, opaque for iOS)
 *
 * Regenerate whenever logo.png changes.
 */

$source = __DIR__ . '/../public/assets/logo.png';
$outDir = __DIR__ . '/../public/icons';

if (! extension_loaded('imagick')) {
    fwrite(STDERR, "Ekstensi PHP Imagick tidak tersedia.\n");
    exit(1);
}

if (! file_exists($source)) {
    fwrite(STDERR, "Logo tidak ditemukan di {$source}\n");
    exit(1);
}

if (! is_dir($outDir) && ! mkdir($outDir, 0755, true)) {
    fwrite(STDERR, "Gagal membuat direktori {$outDir}\n");
    exit(1);
}

$logo = new Imagick($source);
$logo->setImageBackgroundColor(new ImagickPixel('transparent'));
$logo->setImageAlphaChannel(Imagick::ALPHACHANNEL_SET);

$background = new ImagickPixel('#ffffff');

/**
 * Render logo (scaled to a fraction of the canvas, centered) onto an NxN canvas.
 */
function renderIcon(Imagick $logo, int $size, string|ImagickPixel $fill, float $scale): Imagick
{
    $canvas = new Imagick();
    $canvas->newImage($size, $size, $fill, 'png');

    $copy = clone $logo;
    $copy->setImageBackgroundColor(new ImagickPixel('transparent'));
    $copy->setImageAlphaChannel(Imagick::ALPHACHANNEL_SET);

    // Fit the logo inside the scaled box, preserving aspect ratio.
    $box = (int) round($size * $scale);
    $copy->resizeImage($box, $box, Imagick::FILTER_LANCZOS, 1, true);

    // Center it.
    $canvas->compositeImage($copy, Imagick::COMPOSITE_OVER, (int) (($size - $copy->getImageWidth()) / 2), (int) (($size - $copy->getImageHeight()) / 2));

    $copy->clear();
    $canvas->setImageFormat('png');

    return $canvas;
}

$jobs = [
    'icon-192x192.png'          => [192, 'transparent', 0.90],
    'icon-512x512.png'          => [512, 'transparent', 0.90],
    'icon-512x512-maskable.png' => [512, $background, 0.62], // safe zone adaptive icon
    'apple-touch-icon.png'      => [180, $background, 0.85],
];

foreach ($jobs as $file => [$size, $fill, $scale]) {
    $icon = renderIcon($logo, $size, $fill, $scale);
    $icon->writeImage($outDir . '/' . $file);
    printf("Generated %s (%dx%d)\n", $file, $size, $size);
    $icon->clear();
}

$logo->clear();
echo "Selesai.\n";
