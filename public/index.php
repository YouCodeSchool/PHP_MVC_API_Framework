<?php
use App\Controller\WebController;
use App\Controller\ExampleApiController;
use App\Application;

require_once __DIR__ . '../../composer_vendor/autoload.php';

$app = new Application(dirname(__DIR__));

// * WebController manages the Web side of the framework

// Web routes for views : GET
$app->router->get('/', [WebController::class, 'home']);
$app->router->get('/home', [WebController::class, 'home']);
$app->router->get('/login', [WebController::class, 'getLogin']);
// Web routes for views : POST
$app->router->post('/login', [WebController::class, 'postLogin']);

// * Any ExampleApiController manages the API side of the framework with the Middleware

// API routes for endpoints : GET
$app->router->get('/endpoint', [ExampleApiController::class, 'processExampleMethods']);

// API routes for endpoints : POST
$app->router->post('/endpoint', [ExampleApiController::class, 'processExampleMethods']);

$app->run();
?>