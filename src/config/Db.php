<?php

class DB
{

    public $dbName = 'crud';
    public $dbusername  ='root';
    public $dbPasword ='';
    public $db;
    
    public function connect()
    {
        $db = new mysqli('localhost', $this->dbusername, $this->dbPasword,$this->dbName);
        if($db){
            return $db;
        }else{
            echo 'databes not connected';
        }
    }

    
 

}



?>