<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function measureRoute($roleName, $uri, $method = 'GET') {
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

    $request = \Illuminate\Http\Request::create($uri, $method);
    $request->setUserResolver(function() use ($user) { return $user; });
    
    global $kernel;
    
    // We bind the auth user in the container just in case
    \Auth::login($user);
    
    ob_start();
    $response = $kernel->handle($request);
    ob_end_clean();
    
    $dispatcher = \DB::connection()->getEventDispatcher();
    $dispatcher->forget('Illuminate\Database\Events\QueryExecuted');
    
    return "Route: $uri ($roleName)\n" .
           "Status: " . $response->getStatusCode() . "\n" .
           "Queries: $queryCount\n" .
           "Total Time: $totalTime ms\n" .
           "Slowest: $slowest ms -> $slowestSql\n\n";
}

echo measureRoute('admin', '/admin/users');
echo measureRoute('admin', '/admin/proposals');
echo measureRoute('student', '/submissions');
