<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::factory()->make(['role' => 'client', 'password_changed_at' => now()]);
$user->save();

Auth::login($user);

$request = Illuminate\Http\Request::create('/client/dashboard', 'GET');
$response = Illuminate\Support\Facades\Route::dispatch($request);
echo "Status: " . $response->getStatusCode() . "\n";
$content = $response->getContent();
file_put_contents(__DIR__ . '/test-debug-output.html', $content);
echo "Output saved to test-debug-output.html\n";
