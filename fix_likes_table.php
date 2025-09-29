<?php

// Fix likes table to make post_id nullable
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Make post_id nullable in likes table
    DB::statement('ALTER TABLE likes MODIFY post_id BIGINT UNSIGNED NULL');
    echo "✓ Successfully made post_id nullable in likes table\n";
    
    // Verify the change
    $result = DB::select("DESCRIBE likes");
    foreach ($result as $column) {
        if ($column->Field === 'post_id') {
            echo "✓ post_id column: " . $column->Type . " " . ($column->Null === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
            break;
        }
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "You may need to run: php artisan migrate\n";
}

echo "\nLikes table fix completed!\n";
