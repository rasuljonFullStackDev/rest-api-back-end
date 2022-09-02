<?php
require_once __DIR__ . '/Json.php';

class Route{
    public $method;

    public function route($reqMethod,$url,$server_request,$fun) 
    {
        $json = new Response;
        if($this->method===$reqMethod && $url===$server_request){
            // return $fun;
        }else{
            http_response_code(404);
            // $json->json()
            return false;
        }
    }
}



?>