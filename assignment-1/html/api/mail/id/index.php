<?php
require '../../../vendor/autoload.php';

use Application\Mail;
use Application\Page;

// Connect with database
$dsn = "pgsql:host=" . getenv('DB_PROD_HOST') . ";dbname=" . getenv('DB_PROD_NAME');
try {
    $pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo "Database connection failed: " . $e->getMessage();
    exit;
}

// Create objects
$mail = new Mail($pdo);
$page = new Page();

// Get id from params
$uri = $_SERVER['REQUEST_URI'];
$parts = explode('/', trim($uri, '/'));
$id = end($parts);

// Get request method
$method = $_SERVER['REQUEST_METHOD'];


if ($uri === "/api/mail/$id") {
    switch ($method) {
        // GET specific mail
        case 'GET':
            $page->item($mail->getMail($id));
            break;
        // PUT specific mail
        case 'PUT':
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            $page->item($mail->updateMail($id, $data['subject'], $data['body']));
            break;
        // DELETE sepcific mail
        case 'DELETE':
            $page->item($mail->deleteMail($id));
            break;
        // Default condition
        default:
            $page->notFound();
    }
};