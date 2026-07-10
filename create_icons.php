<?php
/**
 * PWA Icon Generator
 * 
 * Usage: php create_icons.php <path_to_logo>
 * Example: php create_icons.php /path/to/fireckontrol-logo.png
 */

if ($argc < 2) {
    echo "Usage: php create_icons.php <path_to_logo>\n";
    echo "Example: php create_icons.php C:\\Users\\Downloads\\logo.png\n";
    exit(1);
}

$sourceLogo = $argv[1];

if (!file_exists($sourceLogo)) {
    echo "Error: Logo file not found: $sourceLogo\n";
    exit(1);
}

$outputDir = __DIR__ . '/public/icons';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

// Icon sizes to generate
$sizes = [
    ['width' => 192, 'height' => 192, 'filename' => 'icon-192.png'],
    ['width' => 512, 'height' => 512, 'filename' => 'icon-512.png'],
    ['width' => 180, 'height' => 180, 'filename' => 'apple-touch-icon.png'],
    ['width' => 32, 'height' => 32, 'filename' => 'favicon-32.png'],
];

// Load image
$image = null;
$info = getimagesize($sourceLogo);
$mime = $info['mime'] ?? '';

if (strpos($mime, 'png') !== false) {
    $image = imagecreatefrompng($sourceLogo);
} elseif (strpos($mime, 'jpeg') !== false) {
    $image = imagecreatefromjpeg($sourceLogo);
} elseif (strpos($mime, 'gif') !== false) {
    $image = imagecreatefromgif($sourceLogo);
} else {
    echo "Error: Unsupported image format. Use PNG, JPG, or GIF.\n";
    exit(1);
}

if (!$image) {
    echo "Error: Could not load image.\n";
    exit(1);
}

echo "Processing logo: $sourceLogo\n";
echo "Creating icons in: $outputDir\n\n";

// Create resized versions
foreach ($sizes as $size) {
    $resized = imagecreatetruecolor($size['width'], $size['height']);
    
    // Preserve transparency for PNG
    imagealphablending($resized, false);
    imagesavealpha($resized, true);
    
    // Fill with transparent background
    $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
    imagefilledrectangle($resized, 0, 0, $size['width'], $size['height'], $transparent);
    
    // Resize with better quality
    imagecopyresampled(
        $resized,
        $image,
        0, 0, 0, 0,
        $size['width'],
        $size['height'],
        imagesx($image),
        imagesy($image)
    );
    
    $outputFile = $outputDir . '/' . $size['filename'];
    imagepng($resized, $outputFile, 9);
    imagedestroy($resized);
    
    echo "✓ Created: {$size['filename']} ({$size['width']}x{$size['height']})\n";
}

imagedestroy($image);

echo "\n✓ All icons created successfully!\n";
echo "\nPWA Configuration is ready at: public/manifest.json\n";
?>
