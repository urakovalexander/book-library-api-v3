<?php

namespace App\Services;

use App\Models\Database;
use PDO;

class ExternalBookService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Поиск книг через внешнее API
    public function searchBooks($query)
    {
        $encodedQuery = urlencode($query);

        // URL внешнего API
        $apiUrl = "https://www.googleapis.com/books/v1/volumes?q={$encodedQuery}";

        // Выполняем запрос к внешнему API
        $response = file_get_contents($apiUrl);
        $data = json_decode($response, true);

        $books = [];

        if (isset($data['items'])) {
            foreach ($data['items'] as $item) {
                $books[] = [
                    'id' => $item['id'] ?? null,
                    'title' => $item['volumeInfo']['title'] ?? 'No Title',
                    'description' => $item['volumeInfo']['description'] ?? '',
                ];
            }
        }

        return $books;
    }

    // Сохранить найденную книгу в базу данных
    public function saveBook($userId, $bookId, $title, $text)
    {
        $stmt = $this->db->prepare("INSERT INTO books (user_id, title, text) VALUES (:user_id, :title, :text)");
        return $stmt->execute([
            'user_id' => $userId,
            'title' => $title,
            'text' => $text,
        ]);
    }
}