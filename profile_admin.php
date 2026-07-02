<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$metrics = [
    'db_time' => 0,
    'queries' => [],
    'query_count' => 0,
];

$db = $app->make('db');
$db->listen(function($query) use (&$metrics) {
    $metrics['db_time'] += $query->time;
    $metrics['query_count']++;
    $metrics['queries'][] = ['sql' => $query->sql, 'time' => $query->time];
});

$user = \App\Models\User::role('admin')->first();
$request = Illuminate\Http\Request::create('/admin', 'GET');
$request->setUserResolver(function() use ($user) { return $user; });
\Auth::login($user);

$controller = $app->make(\App\Http\Controllers\AdminDashboardController::class);

$t_before_controller = microtime(true);
$response = $controller->index($request);
$t_after_controller = microtime(true);

$t_before_view = microtime(true);
ob_start();
echo $response->render();
ob_end_clean();
$t_after_view = microtime(true);

$controllerTime = ($t_after_controller - $t_before_controller) * 1000;
$viewTime = ($t_after_view - $t_before_view) * 1000;
$total = ($t_after_view - $t_before_controller) * 1000;

echo "--- DEEP PROFILING AdminDashboardController@index ---\n";
echo "Controller Logic (incl. Service): " . number_format($controllerTime, 2) . " ms\n";
echo "Blade Rendering Time: " . number_format($viewTime, 2) . " ms\n";
echo "Database Time: " . number_format($metrics['db_time'], 2) . " ms (" . $metrics['query_count'] . " queries)\n";
echo "Total Execution: " . number_format($total, 2) . " ms\n\n";

echo "Top 10 Slowest Queries:\n";
usort($metrics['queries'], fn($a, $b) => $b['time'] <=> $a['time']);
foreach (array_slice($metrics['queries'], 0, 10) as $q) {
    echo "- [{$q['time']} ms] {$q['sql']}\n";
}
