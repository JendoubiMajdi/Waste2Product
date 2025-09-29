<?php

// Move uploaded files from wrong location to correct location
$wrongPath = __DIR__ . '/storage/app/private/public/media';
$correctPath = __DIR__ . '/storage/app/public/media';

// Create correct directory if it doesn't exist
if (!file_exists($correctPath)) {
    mkdir($correctPath, 0755, true);
    echo "Created directory: $correctPath\n";
}

// Move files if wrong directory exists
if (file_exists($wrongPath)) {
    $files = glob($wrongPath . '/*');
    foreach ($files as $file) {
        $filename = basename($file);
        $newPath = $correctPath . '/' . $filename;
        
        if (rename($file, $newPath)) {
            echo "Moved: $filename\n";
        } else {
            echo "Failed to move: $filename\n";
        }
    }
    
    // Remove empty wrong directory
    if (count(glob($wrongPath . '/*')) === 0) {
        rmdir($wrongPath);
        echo "Removed empty directory: $wrongPath\n";
    }
} else {
    echo "No files to move from wrong location.\n";
}

echo "File migration completed!\n";
