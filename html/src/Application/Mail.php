<?php
namespace Application;

use PDO;

class Mail {
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Create a mail
    public function createMail($subject, $body) {
        $sql = "INSERT INTO mail (subject, body) VALUES (?, ?) RETURNING id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$subject, $body]);

        return $stmt->fetchColumn();
    }

    // Retrieve all mails
    public function getAllMails() {
        $sql = "SELECT * FROM mail ORDER BY id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Retrieve a mail with id
    public function getMail($id) {
        $sql = "SELECT * FROM mail WHERE id=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update a mail
    public function updateMail($id, $subject, $body) {
        $sql = "UPDATE mail SET subject = ?, body = ? WHERE id=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$subject, $body, $id]);

        $updatedMail = $this->getMail($id);
        return $updatedMail;
    }

    // Delete a mail
    public function deleteMail($id) {
        $sql = "DELETE FROM mail WHERE id=?";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([$id]);
    }
}