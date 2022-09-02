<?php

 
class Response {
    public function  json($status,$data){
        http_response_code($status);
        echo json_encode($data);
    }
}