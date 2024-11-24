<?php

namespace App\Services;

use App\Models\Database;
use PDO;

class BookService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Получить книги пользователя
    public function getUserBooks($userId)
    {
        $stmt = $this->db->prepare("SELECT id, title FROM books WHERE user_id = :user_id AND deleted_at IS NULL");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Создать новую книгу
    public function createBook($userId, $title, $text)
    {
        $stmt = $this->db->prepare("INSERT INTO books (user_id, title, text) VALUES (:user_id, :title, :text)");
        return $stmt->execute([
            'user_id' => $userId,
            'title' => $title,
            'text' => $text,
        ]);
    }

    // Получить книгу по ID
    public function getBookById($userId, $bookId)
    {
        $stmt = $this->db->prepare("SELECT id, title, text FROM books WHERE id = :id AND user_id = :user_id AND deleted_at IS NULL");
        $stmt->execute([
            'id' => $bookId,
            'user_id' => $userId,
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Обновить книгу
    public function updateBook($userId, $bookId, $title, $text)
    {
        $stmt = $this->db->prepare("UPDATE books SET title = :title, text = :text WHERE id = :id AND user_id = :user_id");
        $stmt->execute([
            'title' => $title,
            'text' => $text,
            'id' => $bookId,
            'user_id' => $userId,
        ]);
        return $stmt->rowCount() > 0;
    }

    // Удалить книгу (мягкое удаление)
    public function deleteBook($userId, $bookId)
    {
        $stmt = $this->db->prepare("UPDATE books SET deleted_at = NOW() WHERE id = :id AND user_id = :user_id");
        $stmt->execute([
            'id' => $bookId,
            'user_id' => $userId,
        ]);
        return $stmt->rowCount() > 0;
    }

    // Восстановить книгу
    public function restoreBook($userId, $bookId)
    {
        $stmt = $this->db->prepare("UPDATE books SET deleted_at = NULL WHERE id = :id AND user_id = :user_id");
        $stmt->execute([
            'id' => $bookId,
            'user_id' => $userId,
        ]);
        return $stmt->rowCount() > 0;
    }

    // Получить книги другого пользователя, если есть доступ
    public function getBooksByUser($currentUserId, $otherUserId)
    {
        // Проверяем, есть ли доступ
        $accessStmt = $this->db->prepare("SELECT id FROM access WHERE owner_id = :owner_id AND user_id = :user_id");
        $accessStmt->execute([
            'owner_id' => $otherUserId,
            'user_id' => $currentUserId,
        ]);

        if (!$accessStmt->fetch()) {
            // Доступа нет
            return false;
        }

        // Получаем книги другого пользователя
        $stmt = $this->db->prepare("SELECT id, title FROM books WHERE user_id = :user_id AND deleted_at IS NULL");
        $stmt->execute(['user_id' => $otherUserId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
