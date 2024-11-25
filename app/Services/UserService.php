<?php

namespace App\Services;

use App\Models\Database;
use App\Models\User;
use PDO;

class UserService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllUsers()
    {
        $stmt = $this->db->query("SELECT id, username FROM users");
        $usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $users = [];
        foreach ($usersData as $userData) {
            $user = new User();
            $user->setId($userData['id']);
            $user->setUsername($userData['username']);
            $users[] = $user;
        }

        return $users;
    }

    public function grantAccess($ownerId, $userIdToGrant)
    {
        // Проверяем, существует ли пользователь
        $stmt = $this->db->prepare("SELECT id FROM users WHERE id = :id");
        $stmt->execute(['id' => $userIdToGrant]);
        if (!$stmt->fetch()) {
            return false;
        }

        // Предоставляем доступ
        $stmt = $this->db->prepare("INSERT IGNORE INTO access (owner_id, user_id) VALUES (:owner_id, :user_id)");
        $stmt->execute([
            'owner_id' => $ownerId,
            'user_id' => $userIdToGrant,
        ]);

        return true;
    }
}
