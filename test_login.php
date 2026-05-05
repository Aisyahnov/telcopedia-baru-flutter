<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$plain_password = 'password123';
$hashed_password = \Illuminate\Support\Facades\Hash::make($plain_password);

$u = \App\Models\User::create([
    'name'=>'Test2', 
    'email'=>'test2@test.com', 
    'nim'=>'12345', 
    'password'=>$hashed_password, 
    'role'=>'buyer'
]);

echo "Stored Password Hash: " . $u->password . "\n";
echo "Hash Check: " . (\Illuminate\Support\Facades\Hash::check($plain_password, $u->password) ? 'YES' : 'NO') . "\n";
