<?php

use FastRoute\RouteCollector;
use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Controllers\BookController;
use App\Controllers\ExternalBookController;

return function (RouteCollector $r) {
    // Маршруты без аутентификации
    $r->addRoute('POST', '/register', [AuthController::class, 'register']);
    $r->addRoute('POST', '/login', [AuthController::class, 'login']);

    // Маршруты с аутентификацией
    $r->addGroup('', function (RouteCollector $r) {
        // Пользователи
        $r->addRoute('GET', '/users', [UserController::class, 'list']);
        $r->addRoute('POST', '/grant-access', [UserController::class, 'grantAccess']);

        // Книги
        $r->addRoute('GET', '/books', [BookController::class, 'list']);
        $r->addRoute('POST', '/books', [BookController::class, 'create']);
        $r->addRoute('GET', '/books/{id:\d+}', [BookController::class, 'show']);
        $r->addRoute('PUT', '/books/{id:\d+}', [BookController::class, 'update']);
        $r->addRoute('DELETE', '/books/{id:\d+}', [BookController::class, 'delete']);
        $r->addRoute('POST', '/books/{id:\d+}/restore', [BookController::class, 'restore']);

        // Доступ к книгам других пользователей
        $r->addRoute('GET', '/users/{id:\d+}/books', [BookController::class, 'listByUser']);

        // Поиск и сохранение книг
        $r->addRoute('GET', '/search-books', [ExternalBookController::class, 'search']);
        $r->addRoute('POST', '/save-book', [ExternalBookController::class, 'save']);
    });
};