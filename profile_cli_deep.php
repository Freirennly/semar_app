<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$metrics = [
    'db_time' => 0,
    'queries' => 0,
    'routing_start' => 0,
    'routing_end' => 0,
    'view_rendering_start' => 0,
    'view_rendering_end' => 0,
];

$db = $app->make('db');
$db->listen(function($query) use (&$metrics) {
    $metrics['db_time'] += $query->time;
    $metrics['queries']++;
});

$events = $app->make('events');
$events->listen(\Illuminate\Routing\Events\RouteMatched::class, function() use (&$metrics) {
    $metrics['routing_end'] = microtime(true);
});

// Since there is no built-in Laravel event for controller start/end, we'll wrap the method call if we can,
// or just measure View events.
$events->listen('composing:*', function($view) use (&$metrics) {
    if ($metrics['view_rendering_start'] === 0) {
        $metrics['view_rendering_start'] = microtime(true);
    }
});

$request = Illuminate\Http\Request::create('/', 'GET');

$t_before_handle = microtime(true);
$response = $kernel->handle($request);
$t_after_handle = microtime(true);

$metrics['view_rendering_end'] = microtime(true); // Rough estimate of view end
$kernel->terminate($request, $response);

$bootstrap = ($t_before_handle - LARAVEL_START) * 1000;
$handling = ($t_after_handle - $t_before_handle) * 1000;
$total = ($t_after_handle - LARAVEL_START) * 1000;

echo "--- DEEP PROFILING LANDING PAGE (/) ---\n";
echo "Bootstrap: " . number_format($bootstrap, 2) . " ms\n";

if ($metrics['routing_end']) {
    $middlewareTime = ($metrics['routing_end'] - $t_before_handle) * 1000;
    echo "Middleware: " . number_format($middlewareTime, 2) . " ms\n";
}

if ($metrics['routing_end'] && $metrics['view_rendering_start']) {
    $controllerTime = ($metrics['view_rendering_start'] - $metrics['routing_end']) * 1000;
    echo "Controller: " . number_format($controllerTime, 2) . " ms\n";
}

if ($metrics['view_rendering_start']) {
    $viewTime = ($metrics['view_rendering_end'] - $metrics['view_rendering_start']) * 1000;
    echo "View Rendering: " . number_format($viewTime, 2) . " ms\n";
}

echo "Database Time: " . number_format($metrics['db_time'], 2) . " ms (" . $metrics['queries'] . " queries)\n";
echo "Total CLI Time: " . number_format($total, 2) . " ms\n";
