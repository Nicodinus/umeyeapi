<?php
define('AMP_DEBUG', true);
define('APP_DIRECTORY', __DIR__);

//error_reporting(-1);

require __DIR__.'/vendor/autoload.php';

/*
Loop::setErrorHandler(function ($error) {
    throw $error;
});
*/

$app = \Nicodinus\UmeyeApi\App::createInstance();
$app->run();