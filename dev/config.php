<?php
declare(strict_types=1);

define('CONTROLLER', 'controller');
define('METHOD', 'method');
define('MODEL', 'model');
define('SETTINGS', 'settings');

return [
    'test.call.dev' => [
        CONTROLLER => TestController::class,
        METHOD => 'doSomeAction',
        MODEL => ModelRequest::class,
        SETTINGS => new SettingsIn('vlad')
    ]
];
