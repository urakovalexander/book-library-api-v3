<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    public static function authenticate()
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (!$authHeader) {
            http_response_code(401);
            echo json_encode(['error' => 'Authorization header not found']);
            exit;
        }

        list($type, $token) = explode(' ', $authHeader, 2);

        if (strcasecmp($type, 'Bearer') != 0) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid authorization type']);
            exit;
        }

        $config = require __DIR__ . '/../../config/jwt.php';

        try {
            $decoded = JWT::decode($token, new Key($config['secret_key'], $config['algorithm']));
            return $decoded->user_id;
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid token']);
            exit;
        }
    }
}