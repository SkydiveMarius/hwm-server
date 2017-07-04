<?php
use SkydiveMarius\HWM\Server\Src\Controller;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../vendor/autoload.php';

$dotEnv = new \Dotenv\Dotenv(__DIR__ . '/..');
$dotEnv->load();

$controller = new Controller();
$request = Request::createFromGlobals();

$response = $controller->handle($request);
$response->send();