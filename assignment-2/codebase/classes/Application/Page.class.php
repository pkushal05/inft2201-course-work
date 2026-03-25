<?php
namespace Application;

class Page
{
    // Bad request controller
    public function badRequest()
    {
        http_response_code(400);
        echo json_encode(["error" => "Bad Request!"]);
    }

    // Data controller
    public function item($data)
    {
        // If user found, but has no mails
        if (is_array($data) && count($data) === 0) {
            http_response_code(200);
            echo json_encode(["message" => "No mails yet!"]);
            return;
        }
        // User found, with mails
        http_response_code(200);
        echo json_encode($data);
    }

    // Trying to access without logging in
    public function tokenNotFound()
    {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(["error" => "Please login and Try again"]);
    }

    // Unauthorized access request
    public function unAuthorized()
    {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(["error" => "Not authorized to access the content"]);
    }
}