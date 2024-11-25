<?php

namespace App\Models;

class Book
{
    private $id;
    private $userId;
    private $title;
    private $text;
    private $deletedAt;

    public function __construct($id = null, $userId = null, $title = null, $text = null, $deletedAt = null)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->title = $title;
        $this->text = $text;
        $this->deletedAt = $deletedAt;
    }

    // Геттеры и сеттеры
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }
}
