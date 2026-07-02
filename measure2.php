<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$queryCount = 0;
$totalTime = 0;

\DB::listen(function($query) use (&$queryCount, &$totalTime) {
    $queryCount++;
    $totalTime += $query->time;
});

$start = microtime(true);
$request = \Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);
$time = (microtime(true) - $start) * 1000;

echo "LANDING PAGE:\n";
echo "Queries: $queryCount\n";
echo "DB Time: $totalTime ms\n";
echo "Total Time: $time ms\n";

$queryCount = 0;
$totalTime = 0;

$start = microtime(true);
$request = \Illuminate\Http\Request::create('/dashboard', 'GET');
// We won't log in for this test, it might redirect
$response = $kernel->handle($request);
$time = (microtime(true) - $start) * 1000;

echo "DASHBOARD (Guest -> Redirect):\n";
echo "Queries: $queryCount\n";
echo "DB Time: $totalTime ms\n";
echo "Total Time: $time ms\n";
