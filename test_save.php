<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $t = App\Models\Trainee::first();
    if ($t) {
        $t->password = bcrypt('123456');
        $t->save();
        echo "Success: " . $t->password;
    } else {
        echo "No trainee found.";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
