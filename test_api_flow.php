<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'flowtest' . rand() . '@test.com';

// 1. Register
$req1 = Illuminate\Http\Request::create('/api/register', 'POST', [
    'name' => 'Flow Test',
    'email' => $email,
    'nim' => 'nim123',
    'password' => 'password123',
    'role' => 'buyer'
]);
$res1 = app()->handle($req1);
echo "Register Status: " . $res1->getStatusCode() . "\n";
echo "Register Body: " . $res1->getContent() . "\n";

// 2. Login
$req2 = Illuminate\Http\Request::create('/api/login', 'POST', [
    'email' => $email,
    'nim' => 'nim123',
    'password' => 'password123'
]);
$res2 = app()->handle($req2);
echo "Login Status: " . $res2->getStatusCode() . "\n";
echo "Login Body: " . $res2->getContent() . "\n";
