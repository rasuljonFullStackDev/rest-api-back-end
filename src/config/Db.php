<?php

class DB
{

    public $dbName = 'crud';
    public $dbusername  ='root';
    public $dbPasword ='';
    public $db;
    public $version=8;
    
    public function connect()
    {
        if($this->version>7.4){
            $pdo = new PDO("mysql:dbname=$this->dbName;host=localhost", $this->dbusername, $this->dbPasword );
            if( $pdo){
                return $pdo;
            }else{
                echo 'databes not connected';
            }
        }else{
            $db = new mysqli('localhost', $this->dbusername, $this->dbPasword,$this->dbName);
            if($db){
                return $db;
            }else{
                echo 'databes not connected';
            }
        }
      
    }

    
 

}



?>