<?php

echo "Fixing storage access...\n";

// Paths
$storageAppPublic = __DIR__ . '/storage/app/public';
$publicStorage = __DIR__ . '/public/storage';
$wrongLocation = __DIR__ . '/storage/app/private/public/media';
$correctLocation = __DIR__ . '/storage/app/public/media';

// Step 1: Create public/storage directory
if (!file_exists($publicStorage)) {
    if (mkdir($publicStorage, 0755, true)) {
        echo "✓ Created public/storage directory\n";
    } else {
        echo "✗ Failed to create public/storage directory\n";
    }
}

// Step 2: Create media subdirectory in storage/app/public
if (!file_exists($correctLocation)) {
    if (mkdir($correctLocation, 0755, true)) {
        echo "✓ Created storage/app/public/media directory\n";
    } else {
        echo "✗ Failed to create media directory\n";
    }
}

// Step 3: Create media subdirectory in public/storage
$publicMediaDir = $publicStorage . '/media';
if (!file_exists($publicMediaDir)) {
    if (mkdir($publicMediaDir, 0755, true)) {
        echo "✓ Created public/storage/media directory\n";
    } else {
        echo "✗ Failed to create public media directory\n";
    }
}

// Step 4: Move files from wrong location to correct location
if (file_exists($wrongLocation)) {
    $files = glob($wrongLocation . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            $filename = basename($file);
            $newStoragePath = $correctLocation . '/' . $filename;
            $newPublicPath = $publicMediaDir . '/' . $filename;
            
            // Move to correct storage location
            if (copy($file, $newStoragePath)) {
                echo "✓ Moved to storage: $filename\n";
            }
            
            // Copy to public location for immediate access
            if (copy($file, $newPublicPath)) {
                echo "✓ Copied to public: $filename\n";
            }
            
            // Remove original file
            unlink($file);
        }
    }
    
    // Remove empty directories
    @rmdir($wrongLocation);
    @rmdir(dirname($wrongLocation));
    echo "✓ Cleaned up wrong location\n";
}

// Step 5: Copy all files from storage/app/public to public/storage
function copyDirectory($src, $dst) {
    if (!file_exists($dst)) {
        mkdir($dst, 0755, true);
    }
    
    $files = scandir($src);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $srcFile = $src . '/' . $file;
            $dstFile = $dst . '/' . $file;
            
            if (is_dir($srcFile)) {
                copyDirectory($srcFile, $dstFile);
            } else {
                copy($srcFile, $dstFile);
            }
        }
    }
}

if (file_exists($storageAppPublic)) {
    copyDirectory($storageAppPublic, $publicStorage);
    echo "✓ Synced storage/app/public to public/storage\n";
}

echo "\n🎉 Storage fix completed!\n";
echo "Files should now be accessible at: http://127.0.0.1:8000/storage/media/filename\n";
