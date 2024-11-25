<?php

namespace App\Controllers;

use App\Services\UserService;

class UserController
{
    private $userId;
    private $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function list()
    {
        $users = $this->userService->getAllUsers();

        // Преобразуем модели пользователей в массивы для кодирования в JSON
        $usersArray = array_map(function ($user) {
            return [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
            ];
        }, $users);

        echo json_encode($usersArray);
    }


    public function grantAccess()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $userIdToGrant = $data['user_id'] ?? null;

        if (!$userIdToGrant) {
            http_response_code(400);
            echo json_encode(['error' => 'User ID is required']);
            return;
        }

        // Используем UserService для предоставления доступа
        $result = $this->userService->grantAccess($this->userId, $userIdToGrant);

        if ($result) {
            echo json_encode(['message' => 'Access granted']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
        }
    }
}
