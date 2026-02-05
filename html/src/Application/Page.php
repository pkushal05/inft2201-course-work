<?php
namespace Application;
use Application\Mail;
use PDO;

class Page {
    public function list($items) {
        try {
            http_response_code(200);
            echo json_encode($items);
        } catch (\Exception $e){
            http_response_code(500);
            echo json_encode(["error" => "Failed to encode JSON"]);
        }
    }

    public function item($item) {
        try {
            if (is_array($item) && count($item) === 0) {
                http_response_code(404);
                echo json_encode(["error" => "Mail not found"]);
                return;
            }
                http_response_code(200);
                echo json_encode($item);
        } catch (\Exception $e){
            http_response_code(500);
            echo json_encode(["error" => "Failed to encode JSON"]);
        }
    }

    public function itemPost($item) {
        try {
            if (is_array($item) && count($item) === 0) {
                http_response_code(404);
                echo json_encode(["error" => "Mail not found"]);
                return;
            }
                http_response_code(201);
                echo json_encode($item);
        } catch (\Exception $e){
            http_response_code(500);
            echo json_encode(["error" => "Failed to encode JSON"]);
        }
    }

    public function notFound() {
        http_response_code(404);
        echo json_encode(["error" => "Not found"]);
    }

    public function badRequest() {
        http_response_code(400);
        echo json_encode(["error" => "Bad request"]);
    }
}