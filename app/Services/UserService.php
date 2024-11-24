<?php

namespace App\Services;

use App\Models\Database;
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function grantAccess($ownerId, $userIdToGrant)
    {
        // Проверка существования пользователя
        $stmt = $this->db->prepare("SELECT id FROM users WHERE id = :id");
        $stmt->execute(['id' => $userIdToGrant]);
        if (!$stmt->fetch()) {
            return false;
        }

        // Добавление доступа
        $stmt = $this->db->prepare("INSERT IGNORE INTO access (owner_id, user_id) VALUES (:owner_id, :user_id)");
        $stmt->execute([
            'owner_id' => $ownerId,
            'user_id' => $userIdToGrant,
        ]);

        return true;
    }
}
