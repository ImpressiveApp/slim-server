<?php

// DIC configuration
$container =  $app->getContainer();

// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------


// -----------------------------------------------------------------------------
// Service factories
// -----------------------------------------------------------------------------

// Silalahi Logger
$container['logger'] = function($c) {
  	$logger = $c->get('settings')['logger'];
    return new Silalahi\Slim\Logger($logger);
};


// Silalahi Cron Job Logger
$container['cronjob_logger'] = function($c) {
    $logger = $c->get('settings')['cronjob_logger'];
    return new Silalahi\Slim\Logger($logger);
};


// Database connection
$container['db'] = function ($c) {

    $settings = $c->get('settings')['db'];
    $pdo = new PDO("mysql:host=" . $settings['host'] . ";dbname=" . $settings['dbname'],
        $settings['user'], $settings['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$container['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {


        $error = [
            'Resultcode' => 1,      
            'Message' => $exception->getMessage(),
            'Data' => null,      
            'StatusCode' => $exception->getCode(),
            'File' => $exception->getFile(),
            'Line' => $exception->getLine(),
      //    'trace' => explode("\n", $exception->getTraceAsString()),
        ];

    $c->get('logger')->error($error['Message'].' | '.$error['File'].' | line: '.$error['Line']);
    $c->get('db')->rollBack();
  
    return $c['response']
        ->withStatus(500)
        ->withHeader('Content-Type', 'application/json')
          //    ->write('Somethingggggg went wrong!');
        ->withJson($error);
    };
};


$container['phpErrorHandler'] = function ($c) {
    return function ($request, $response, $error) use ($c) {
        return $c['response']
            ->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write('Something went wrong!!!!!!!!!!!!');
    };
};


$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        $error = [
            'Resultcode' => 1,      
            'Message' => 'Page Not Found',
        ];

        $c->get('logger')->error("Page Not Found");

        return $c['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($error));

    };
};

$container['notAllowedHandler'] = function ($c) {
    return function ($request, $response, $methods) use ($c) {
      
        $c->get('logger')->error('Method must be one of: ' . implode(', ', $methods));
        return $c['response']
            ->withStatus(405)
            ->withHeader('Allow', implode(', ', $methods))
            ->withHeader('Content-type', 'application/json')
            ->write(json_encode(array
                        (
                            'Resultcode' => 1,      
                            'Message'=>'Method must be one of: ' . implode(', ', $methods)
                        )
                    )
                );
           
    };
};

//unset($app->getContainer()['notFoundHandler']);

// -----------------------------------------------------------------------------
// Action factories
// -----------------------------------------------------------------------------


//$container[App\Controllers\CustomerDetails::class] = function($c){
$container['CustomerDetails'] = function($c){

  //return new \App\Controllers\CustomerDetails($c);
return new \App\Controllers\CustomerDetails($c->get('logger'),$c['db']);

};

$container['OrderDetails'] = function($c){
    return new \App\Controllers\OrderDetails($c->get('logger'),$c['db']);
};

$container['TimeSlots'] = function($c){
    return new \App\Controllers\TimeSlots($c->get('logger'),$c['db']);
};

$container['Transactions'] = function($c){
    return new \App\Controllers\Transactions($c->get('logger'),$c['db']);
};

$container['Promocodes'] = function($c){
    return new \App\Controllers\Promocodes($c->get('logger'),$c['db']);
};
$container['RateCard'] = function($c){
    return new \App\Controllers\RateCard($c->get('logger'),$c['db']);
};


$container['CronJobs'] = function($c){
    return new \App\Controllers\CronJobs($c->get('cronjob_logger'),$c['db']);
};

?>