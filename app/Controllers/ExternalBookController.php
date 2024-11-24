<?php

namespace App\Controllers;

use App\Services\ExternalBookService;

class ExternalBookController
{
    private $userId;
    private $externalBookService;

    public function __construct()
    {
        $this->externalBookService = new ExternalBookService();
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    // Поиск книг по внешнему API
    public function search()
    {
        $query = $_GET['q'] ?? '';

        if (empty($query)) {
            http_response_code(400);
            echo json_encode(['error' => 'Search query is required']);
            return;
        }

        $books = $this->externalBookService->searchBooks($query);

        echo json_encode($books);
    }

    // Сохранить найденную книгу
    public function save()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $bookId = $data['id'] ?? '';
        $title = $data['title'] ?? '';
        $text = $data['description'] ?? '';

        if (empty($bookId) || empty($title)) {
            http_response_code(400);
            echo json_encode(['error' => 'Book ID and title are required']);
            return;
        }

        $result = $this->externalBookService->saveBook($this->userId, $bookId, $title, $text);

        if ($result) {
            http_response_code(201);
            echo json_encode(['message' => 'Book saved successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save book']);
        }
    }
}