<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

// Create admin user
$admin = User::updateOrCreate(
    ['email' => 'admin@example.com'],
    [
        'name' => 'Admin User',
        'password' => bcrypt('admin12345'),
        'is_admin' => true,
        'email_verified_at' => now(),
    ]
);

echo "Admin user created successfully!\n";
echo "Email: admin@example.com\n";
echo "Password: admin12345\n";
echo "Admin status: " . ($admin->is_admin ? 'Yes' : 'No') . "\n";
