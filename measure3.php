<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function measureController($roleName, $controllerClass, $method) {
    $user = \App\Models\User::role($roleName)->first();
    if (!$user) return "No user for role: $roleName\n";

    $queryCount = 0;
    $totalTime = 0;
    $slowest = 0;
    $slowestSql = '';

    \DB::flushQueryLog();
    \DB::listen(function($query) use (&$queryCount, &$totalTime, &$slowest, &$slowestSql) {
        $queryCount++;
        $totalTime += $query->time;
        if ($query->time > $slowest) {
            $slowest = $query->time;
            $slowestSql = $query->sql;
        }
    });

    global $app;
    $controller = $app->make($controllerClass);
    $request = \Illuminate\Http\Request::create('/', 'GET');
    $request->setUserResolver(function() use ($user) { return $user; });
    \Auth::login($user);
    
    ob_start();
    try {
        $controller->$method($request);
    } catch (\Throwable $e) {
        ob_end_clean();
        return "Error executing $controllerClass@$method: " . $e->getMessage() . "\n";
    }
    ob_end_clean();
    
    $dispatcher = \DB::connection()->getEventDispatcher();
    $dispatcher->forget('Illuminate\Database\Events\QueryExecuted');
    
    return "Controller: $controllerClass@$method ($roleName)\n" .
           "Queries: $queryCount\n" .
           "Total Time: $totalTime ms\n" .
           "Slowest: $slowest ms -> $slowestSql\n\n";
}

echo measureController('admin', \App\Http\Controllers\Admin\UserController::class, 'index');
echo measureController('admin', \App\Http\Controllers\Admin\ProposalController::class, 'index');
echo measureController('student', \App\Http\Controllers\SubmissionController::class, 'index');
