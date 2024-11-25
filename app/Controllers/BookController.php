<?php

namespace App\Controllers;

use App\Services\BookService;

class BookController
{
    private $userId;
    private $bookService;

    public function __construct()
    {
        $this->bookService = new BookService();
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    // Получить список книг текущего пользователя
    public function list()
    {
        $books = $this->bookService->getUserBooks($this->userId);

        // Преобразуем модели книг в массивы для кодирования в JSON
        $booksArray = array_map(function ($book) {
            return [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
            ];
        }, $books);

        echo json_encode($booksArray);
    }

    // Создать новую книгу
    public function create()
    {
        $title = $_POST['title'] ?? '';
        $text = $_POST['text'] ?? '';

        if (empty($title)) {
            http_response_code(400);
            echo json_encode(['error' => 'Title is required']);
            return;
        }

        // Обработка загруженного файла, если предоставлен
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $fileContent = file_get_contents($_FILES['file']['tmp_name']);
            $text = $fileContent;
        }

        $result = $this->bookService->createBook($this->userId, $title, $text);

        if ($result) {
            http_response_code(201);
            echo json_encode(['message' => 'Book created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create book']);
        }
    }

    // Получить информацию о книге по ID
    public function show($id)
    {
        $book = $this->bookService->getBookById($this->userId, $id);

        if ($book) {
            echo json_encode([
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'text' => $book->getText(),
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Book not found']);
        }
    }

    // Обновить книгу
    public function update($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $title = $data['title'] ?? '';
        $text = $data['text'] ?? '';

        if (empty($title)) {
            http_response_code(400);
            echo json_encode(['error' => 'Title is required']);
            return;
        }

        $result = $this->bookService->updateBook($this->userId, $id, $title, $text);

        if ($result) {
            echo json_encode(['message' => 'Book updated successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Book not found or not accessible']);
        }
    }

    // Удалить книгу
    public function delete($id)
    {
        $result = $this->bookService->deleteBook($this->userId, $id);

        if ($result) {
            echo json_encode(['message' => 'Book deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Book not found or not accessible']);
        }
    }

    // Восстановить книгу
    public function restore($id)
    {
        $result = $this->bookService->restoreBook($this->userId, $id);

        if ($result) {
            echo json_encode(['message' => 'Book restored successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Book not found or not accessible']);
        }
    }

    // Получить список книг другого пользователя (если есть доступ)
    public function listByUser($otherUserId)
    {
        $books = $this->bookService->getBooksByUser($this->userId, $otherUserId);

        if ($books !== false) {
            // Преобразуем модели книг в массивы для кодирования в JSON
            $booksArray = array_map(function ($book) {
                return [
                    'id' => $book->getId(),
                    'title' => $book->getTitle(),
                ];
            }, $books);

            echo json_encode($booksArray);
        } else {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
        }
    }
}
