<?php

$loader = require_once __DIR__ . "/../symfony-app/autoload.php";
\Symfony\Component\Debug\Debug::enable();

require_once __DIR__ . "/../symfony-app/AppKernel.php";

$kernel = new TodoExample\AppKernel("dev", true);

$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
