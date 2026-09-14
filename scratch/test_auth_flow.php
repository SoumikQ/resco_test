<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// 1. Test unauthenticated request to /dashboard
$request = Illuminate\Http\Request::create('/dashboard', 'GET');
$response = $kernel->handle($request);
echo "1. Unauthenticated /dashboard status: " . $response->getStatusCode() . " (Redirected to: " . $response->headers->get('Location') . ")\n";

// 2. Test unauthenticated request to /
$request = Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);
echo "2. Unauthenticated / status: " . $response->getStatusCode() . " (Redirected to: " . $response->headers->get('Location') . ")\n";

// 3. Test unauthenticated request to /review (should be 200 OK)
$request = Illuminate\Http\Request::create('/review', 'GET');
$response = $kernel->handle($request);
echo "3. Public /review status: " . $response->getStatusCode() . "\n";

// 4. Test authenticated request with User ID 1
$user = App\Models\User::first();
Illuminate\Support\Facades\Auth::login($user);
$request = Illuminate\Http\Request::create('/dashboard', 'GET');
$response = $kernel->handle($request);
echo "4. Authenticated /dashboard status: " . $response->getStatusCode() . "\n";
