<?php

namespace App\Controllers;

use App\Models\Database;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PDO;

class AuthController
{
    public function register()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $username = trim($data['username'] ?? '');
        $password = trim($data['password'] ?? '');
        $confirmPassword = trim($data['confirm_password'] ?? '');

        if ($password !== $confirmPassword) {
            http_response_code(400);
            echo json_encode(['error' => 'Passwords do not match']);
            return;
        }

        if (empty($username) || empty($password)) {
            http_response_code(400);
            echo json_encode(['error' => 'Username and password are required']);
            return;
        }

        $db = Database::getInstance();

        // Проверка существующего пользователя
        $stmt = $db->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['error' => 'Username already exists']);
            return;
        }

        // Хеширование пароля
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Создание пользователя
        $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $stmt->execute([
            'username' => $username,
            'password' => $hashedPassword,
        ]);

        $userId = $db->lastInsertId();

        // Генерация JWT
        $token = $this->generateJWT($userId);

        http_response_code(201);
        echo json_encode(['message' => 'User registered successfully', 'token' => $token]);
    }

    public function login()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $username = trim($data['username'] ?? '');
        $password = trim($data['password'] ?? '');

        if (empty($username) || empty($password)) {
            http_response_code(400);
            echo json_encode(['error' => 'Username and password are required']);
            return;
        }

        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT id, password FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
            return;
        }

        // Генерация JWT
        $token = $this->generateJWT($user['id']);

        echo json_encode(['token' => $token]);
    }

    private function generateJWT($userId)
    {
        $config = require __DIR__ . '/../../config/jwt.php';

        $payload = [
            'iss' => $config['issuer'],
            'aud' => $config['audience'],
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + $config['expiration_time'],
            'user_id' => $userId,
        ];

        return JWT::encode($payload, $config['secret_key'], $config['algorithm']);
    }
}
