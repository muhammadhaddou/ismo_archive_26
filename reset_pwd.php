<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::query()->where('email', '=', 'admin@ismo.ma')->first();
if ($user) {
    $user->password = \Illuminate\Support\Facades\Hash::make('password123');
    $user->save();
    echo "Password for admin@ismo.ma has been reset to: password123\n";
} else {
    echo "User not found.\n";
}
