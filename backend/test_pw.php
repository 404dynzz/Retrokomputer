<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$user = App\Models\User::where('username', 'admin')->first();
echo "admin123 : " . (password_verify('admin123', $user->password) ? 'OK' : 'FAIL') . "\n";
echo "password123 : " . (password_verify('password123', $user->password) ? 'OK' : 'FAIL') . "\n";
