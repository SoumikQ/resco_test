<?php
$sourcePath = 'public/images/food-pattern-raw.png';
$img = imagecreatefrompng($sourcePath);
$width = imagesx($img);
$height = imagesy($img);

// Clean the Google lens icon at bottom-left:
// It's in the box: x: 10 to 80, y: $height - 85 to $height - 15.
// Let's replace that box with a clean sample from the pattern (e.g. from x: 220 to 290, y: 50 to 120):
imagecopy($img, $img, 15, $height - 85, 220, 50, 70, 70);

// Save cleaned original
imagepng($img, 'public/images/food-pattern.png');

function createColoredPattern($sourceImg, $width, $height, $targetR, $targetG, $targetB, $outPath) {
    $transImg = imagecreatetruecolor($width, $height);
    imagealphablending($transImg, false);
    imagesavealpha($transImg, true);
    $transparent = imagecolorallocatealpha($transImg, 0, 0, 0, 127);
    imagefilledrectangle($transImg, 0, 0, $width, $height, $transparent);

    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $rgb = imagecolorat($sourceImg, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            $brightness = ($r + $g + $b) / 3;
            if ($brightness > 240) {
                continue;
            }

            // Darkness from 0 to 1
            $darkness = (255 - $brightness) / 255;
            // Scale alpha: GD alpha goes from 0 (opaque) to 127 (transparent)
            $alpha = (int) round(127 - ($darkness * 127));
            if ($alpha < 0) $alpha = 0;
            if ($alpha > 127) $alpha = 127;

            $color = imagecolorallocatealpha($transImg, $targetR, $targetG, $targetB, $alpha);
            imagesetpixel($transImg, $x, $y, $color);
        }
    }

    imagepng($transImg, $outPath);
    echo "Generated: {$outPath}\n";
}

// 1. Red version (Exact user match: #e11d48 / rgb(225, 29, 72))
createColoredPattern($img, $width, $height, 225, 29, 72, 'public/images/food-pattern-red.png');

// 2. Brand Orange version (#ea580c / rgb(234, 88, 12))
createColoredPattern($img, $width, $height, 234, 88, 12, 'public/images/food-pattern-orange.png');

// 3. Neutral Slate version (#475569 / rgb(71, 85, 105))
createColoredPattern($img, $width, $height, 71, 85, 105, 'public/images/food-pattern-slate.png');
