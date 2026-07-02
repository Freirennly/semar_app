<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function measureRole($roleName) {
    $queryCount = 0;
    $totalTime = 0;
    $slowest = 0;
    $slowestSql = '';
    
    $user = \App\Models\User::role($roleName)->first();
    if (!$user) {
        return "No user found for role: $roleName\n";
    }

    \DB::flushQueryLog();
    \DB::listen(function($query) use (&$queryCount, &$totalTime, &$slowest, &$slowestSql) {
        $queryCount++;
        $totalTime += $query->time;
        if ($query->time > $slowest) {
            $slowest = $query->time;
            $slowestSql = $query->sql;
        }
    });

    $controller = new \App\Http\Controllers\DashboardController();
    $request = \Illuminate\Http\Request::create('/dashboard', 'GET');
    $request->setUserResolver(function() use ($user) {
        return $user;
    });
    
    $queryCount = 0;
    $totalTime = 0;
    $slowest = 0;
    $slowestSql = '';
    
    ob_start();
    $controller->index($request);
    ob_end_clean();
    
    // Clear listener
    $dispatcher = \DB::connection()->getEventDispatcher();
    $dispatcher->forget('Illuminate\Database\Events\QueryExecuted');
    
    return strtoupper($roleName) . " DASHBOARD:\n" .
           "Queries: $queryCount\n" .
           "Total Time: $totalTime ms\n" .
           "Slowest: $slowest ms -> $slowestSql\n\n";
}

echo measureRole('student');
echo measureRole('reviewer');
echo measureRole('ketua');
echo measureRole('sekretariat');
