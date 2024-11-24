<?php

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use FastRoute\RouteCollector;

use function FastRoute\simpleDispatcher;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$dispatcher = simpleDispatcher(require __DIR__ . '/../routes/routes.php');

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Удаляем query string
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}

// Декодируем URI
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case \FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
        break;
    case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
    case \FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        list($class, $method) = $handler;
        $controller = new $class();

        // Если маршрут требует аутентификации
        if (!in_array($uri, ['/login', '/register'])) {
            $userId = \App\Middleware\AuthMiddleware::authenticate();
            $controller->setUserId($userId);
        }

        call_user_func_array([$controller, $method], $vars);
        break;
}
