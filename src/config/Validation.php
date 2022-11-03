<?php

class Validation{
    public $key;
    public $rul;
    public $request;
    public function valid(){
        $res = [];
        foreach ($this->key as $key => $value) {
            $keys = $this->request[$value] ?? false;
           if($keys!==false){
            if(empty($this->request[$value] )){
                $res[$value] =  "Error $value";
            }
            
           }else{
            $res[$value] =  "Error $value";
           }
            
        }
         
        if(count($res)===0){
            return true;
        }
        else{
           return $res;
        }
       
       
    }
}


?>