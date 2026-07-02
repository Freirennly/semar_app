<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$metrics = [
    'db_time' => 0,
    'queries' => 0,
];

$db = $app->make('db');
$db->listen(function($query) use (&$metrics) {
    $metrics['db_time'] += $query->time;
    $metrics['queries']++;
});

$request = Illuminate\Http\Request::create('/', 'GET');

$t_before_handle = microtime(true);
$response = $kernel->handle($request);
$t_after_handle = microtime(true);
$kernel->terminate($request, $response);

$bootstrap = ($t_before_handle - LARAVEL_START) * 1000;
$handling = ($t_after_handle - $t_before_handle) * 1000;
$total = ($t_after_handle - LARAVEL_START) * 1000;

echo "--- PROFILING LANDING PAGE (/) ---\n";
echo "Bootstrap Time: " . number_format($bootstrap, 2) . " ms\n";
echo "Framework/Middleware/Controller/View Time: " . number_format($handling, 2) . " ms\n";
echo "Database Time: " . number_format($metrics['db_time'], 2) . " ms (" . $metrics['queries'] . " queries)\n";
echo "Total CLI Time: " . number_format($total, 2) . " ms\n";
