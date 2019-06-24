<?php

use Wumvi\JsonRpc\Init;

include '../vendor/autoload.php';
include './TestController.php';
include './ModelSession.php';
include './ModelError.php';
include './ModelRequest.php';
include './ModelResponse.php';
include './SettingsIn.php';

function checkSession(ModelSession $model, $di): ?ModelError
{
    $error = new ModelError();
    $error->setError('wrong-session', 'Wrong session ' . $model->getSession());

    return $error;
}

$methodRequest = $_SERVER['REQUEST_METHOD'];
$requestData = file_get_contents('php://input');
if (($_SERVER['RUN_ENV'] ?? '') === 'DEV' && empty($requestData)) {
    $requestData = $_GET['json'] ?? '';
    $methodRequest = 'POST';
}


$di = Init::getDi(__DIR__ . '/services.yaml');

echo Init::getResponseJson(
    include './config.php',
    $di,
    $methodRequest,
    $requestData,
    'checkSession'
);
