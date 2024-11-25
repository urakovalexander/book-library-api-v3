<?php

namespace App\Controllers;

use App\Services\ExternalBookService;
use App\Models\Book;

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

    // Поиск книг
    public function search()
    {
        $query = $_GET['q'] ?? '';

        if (empty($query)) {
            http_response_code(400);
            echo json_encode(['error' => 'Search query is required']);
            return;
        }

        $books = $this->externalBookService->searchBooks($query);

        // Преобразуем объекты Book в массивы для JSON
        $booksArray = array_map(function (Book $book) {
            return [
                'id'          => $book->getId(),
                'title'       => $book->getTitle(),
                'description' => $book->getText(),
            ];
        }, $books);

        echo json_encode($booksArray);
    }

    // Сохранение найденной книги
    public function save()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['title'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Title is required']);
            return;
        }

        $book = new Book();
        $book->setTitle($data['title']);
        $book->setText($data['description'] ?? '');

        $result = $this->externalBookService->saveBook($this->userId, $book);

        if ($result) {
            http_response_code(201);
            echo json_encode(['message' => 'Book saved successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save book']);
        }
    }
}