<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use App\Core\Router;
use App\Core\Request;

$routes = require_once __DIR__ . '/../routes/api.php';

$router = new Router($routes);

$request = new Request($_SERVER, $_GET, $_POST, $_FILES, file_get_contents('php://input'));

try {
    $response = $router->dispatch($request);
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}