<?php
require_once('../request.php');
require_once('../response.php');
require_once('../router.php');
require_once('../db.php');
require_once('../model.php');
require_once('../controller.php');
require_once('../view.php');

use Router\Router;
use Controller\CountryController;
use Controller\ConferenceController;

$router = new Router();

// routes
$router->get('/', [ConferenceController::class, 'getAll']);
$router->get('/country', [CountryController::class, 'getAll']);
$router->post('/country', [CountryController::class, 'create']);
$router->get('/add', [ConferenceController::class, 'add']);
$router->post('/create', [ConferenceController::class, 'create']);
$router->get('/edit/:id', [ConferenceController::class, 'edit']);
$router->post('/save/:id', [ConferenceController::class, 'save']);
$router->get('/delete/:id', [ConferenceController::class, 'delete']);
$router->get('/test/:id/test1/:id', [ConferenceController::class, 'test']);
$router->get('/:id', [ConferenceController::class, 'getOne']);

$router->resolveRoute();

?>
