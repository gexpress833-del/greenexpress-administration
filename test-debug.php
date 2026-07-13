<?php

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$user = User::factory()->make(['role' => 'client', 'password_changed_at' => now()]);
$user->save();

Auth::login($user);

$request = Request::create('/client/dashboard', 'GET');
$response = Route::dispatch($request);
echo 'Status: '.$response->getStatusCode()."\n";
$content = $response->getContent();
file_put_contents(__DIR__.'/test-debug-output.html', $content);
echo "Output saved to test-debug-output.html\n";
