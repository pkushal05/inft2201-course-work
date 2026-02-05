<?php
require '../../../vendor/autoload.php';

use Application\Mail;
use Application\Page;

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

$mail = new Mail($pdo);
$page = new Page();

$uri = $_SERVER['REQUEST_URI'];
$parts = explode('/', trim($uri, '/'));
$id = end($parts);
$method = $_SERVER['REQUEST_METHOD'];


if ($uri === "/api/mail/$id") {
    switch ($method) {
        case 'GET':
            $page->item($mail->getMail($id));
            break;
        case 'PUT':
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            $page->item($mail->updateMail($id, $data['subject'], $data['body']));
            break;
        case 'DELETE':
            $page->item($mail->deleteMail($id));
            break;
        default:
            $page->notFound();
    }
};