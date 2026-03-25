<?php
require  __DIR__ . '/../../../autoload.php';

use Application\Mail;
use Application\Database;
use Application\Page;
use Application\Verifier;

$database = new Database('prod');
$page = new Page();

$mail = new Mail($database->getDb());

// Instantiate Verifier class and pass Auth Headers
$verifier = new Verifier();
$verifier->decode($_SERVER['HTTP_AUTHORIZATION']);

// If nothing is found in headers, Send error message
if (empty($verifier->role)) {
    $page->tokenNotFound();
    exit;
}
// POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    // If body has valid items, create new mail
    if (array_key_exists('name', $data) && array_key_exists('message', $data)) {
        $id = $mail->createMail($data['name'], $data['message'], $verifier->userId);
        $page->item(array("id" => $id));
    } else {
        // Else, Bad request
        $page->badRequest();
    }
    // GET method
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // User is user, get mails for user only
    if ($verifier->role === "user") {
        $page->item($mail->listMailById($verifier->userId));
        // User is admin, get all mails
    } elseif ($verifier->role === "admin") {
        $page->item($mail->listAllMail());
    }
    // Else, bad request
} else {
    $page->badRequest();
}
