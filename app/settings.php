<?php
return [
    'settings' => [
     // Slim Settings
        'displayErrorDetails' => false, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        'determineRouteBeforeAppMiddleware' => true,
     
    // logger settings
        'logger' =>   [
                'path' => __DIR__. '/../logs/',
                'name' => 'logs_',
                'name_format' => 'Y-m-d',
                'extension' => 'log',
                'message_format' => '[%label%]' .@date(' [D M d h:i:s a Y] ') .'%message%'
            ],
    // Cron Jobs logger settings
        'cronjob_logger' =>   [
                'path' => __DIR__. '/../logs/',
                'name' => 'cronjob_logs_',
                'name_format' => 'Y-m-d',
                'extension' => 'log',
                'message_format' => '[%label%]' .@date(' [D M d h:i:s a Y] ') .'%message%'
            ],

    // Database connection settings           
        "db" => [
            "host" => "localhost",
            "dbname" => "impressivedb",
            "user" => "root",
            "pass" => "system"
        ]
       
    ],
];
