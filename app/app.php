<?php

require __DIR__ . '/../vendor/autoload.php';

// Setting Default Date Timezone
date_default_timezone_set("Asia/Kolkata");

// Instantiate the Slim App
$settings = require __DIR__ . '/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/dependencies.php';

// Register middleware
require __DIR__ . '//middleware.php';

// Register routes
require __DIR__ . '/routes.php';

?>