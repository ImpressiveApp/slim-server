<?php
// Application middleware

// Adding Slim Logger as part of Middleware

$logger = $container->get('settings')['logger'];

$app->add(new Silalahi\Slim\Logger($logger));


?>