<?php

// Create storage link manually
$target = __DIR__ . '/storage/app/public';
$link = __DIR__ . '/public/storage';

// Remove existing link/directory if it exists
if (file_exists($link)) {
    if (is_link($link)) {
        unlink($link);
    } else {
        rmdir($link);
    }
}

// Create the symbolic link
if (symlink($target, $link)) {
    echo "Storage link created successfully!\n";
    echo "Target: $target\n";
    echo "Link: $link\n";
} else {
    echo "Failed to create storage link.\n";
    echo "You may need to run this as administrator or use: php artisan storage:link\n";
}

// Create media directory if it doesn't exist
$mediaDir = $target . '/media';
if (!file_exists($mediaDir)) {
    mkdir($mediaDir, 0755, true);
    echo "Media directory created: $mediaDir\n";
}
