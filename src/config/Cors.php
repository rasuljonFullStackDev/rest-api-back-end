<?php

class Header {
    public $tokenVerify = [
        // local project
        // '/project-name/v1/api/users/',
     '/rest-api-back-end/v1/api/register/',
     '/rest-api-back-end/v1/api/users/',
    //  '/rest-api-back-end/v1/api/users/',
     '/rest-api-back-end/v1/api/cars/',
    //  '/rest-api-back-end/v1/api/category/'
    '/rest-api-back-end/v1/api/blog/'
    ];
    public  $results = true;
    public function auth($server_request,$user){
        $token = apache_request_headers()['token__'] ?? '';
            if(count($user->filter('token',$token))===1){
             $this->results = true;
            }else{
                foreach ($this->tokenVerify as $key => $value) {
                    if($value===$server_request){
                        $this->results = true;
                    }
                }
            }
         if($this->results===true){
            return $this->results;
         }else{
            http_response_code(401);
            return $this->results;
         }
    }
}

?>