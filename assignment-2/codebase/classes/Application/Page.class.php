<?php
namespace Application;

class Page
{
    public function badRequest()
    {
        http_response_code(400);
        echo json_encode(["error" => "Bad Request!"]);
    }

    public function item($data)
    {
        if (is_array($data) && count($data) === 0) {
            http_response_code(200);
            echo json_encode(["message" => "No mails yet!"]);
            return;
        }
        http_response_code(200);
        echo json_encode($data);
    }

    public function tokenNotFound()
    {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(["error" => "Please login and Try again"]);
    }

    public function unAuthorized()
    {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(["error" => "Not authorized to access the content"]);
    }
}