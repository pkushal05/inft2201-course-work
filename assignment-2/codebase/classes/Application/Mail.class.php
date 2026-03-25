<?php
namespace Application;

// required for using the PDO::FETCH_ASSOC constant
use PDO;

class Mail
{
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Create mail with userid
    public function createMail($name, $message, $id)
    {
        $stmt = $this->db->prepare("INSERT INTO mail (name, message, userId) VALUES (:name, :message, :userId)");
        $stmt->execute(['name' => $name, 'message' => $message, 'userId' => $id]);

        return $this->db->lastInsertId();
    }

    // Get all mails
    public function listAllMail()
    {
        $result = $this->db->query("SELECT * FROM mail ORDER BY id");

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get mails for specific id only
    public function listMailById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM mail WHERE userId=?");
        $stmt->execute([$id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}