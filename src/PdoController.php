<?php

class PdoController{
    public $tableKey;
    public $tableName;
    public $ControllerName;
    public $public;
    public $keySql;
    public $keySqlValue;
    public $sqlInsert;
    // update
    public $sqlUpdate;
    // injection
    public $injection;
    public $request;
    public $bindParams;

    public function CrudCreate()

    {

        $path = __DIR__;
    //    ozgaruvhilarni elon qilish
       foreach ($this->tableKey as $key => $value) {
           $this->public = $this->public."public $$value;  "; 
        }
         $this->public = $this->public.'public $db;'; 
        //    inser uchun keylarni yozish
        foreach ($this->tableKey as $key => $value) {
            if(count($this->tableKey)-1===$key){
                $this->keySql = $this->keySql." `$value` "; 
            }else{
                $this->keySql = $this->keySql." `$value` ,"; 
            }
               $this->bindParams = $this->bindParams.'$res->'."bindParam(':$value',".'$this->'.$value.");" ; 
        }
        // insert uchun valu elon qilish
        foreach ($this->tableKey as $key => $value) {
            if(count($this->tableKey)-1===$key){
                $this->keySqlValue = $this->keySqlValue." :$value "; 
            }else{
                $this->keySqlValue = $this->keySqlValue.":$value ,";; 
            }
        }
        $this->sqlInsert = '$sql="'. "INSERT INTO `$this->tableName`  ($this->keySql) VALUES ($this->keySqlValue)".'"';
        // update uchun funksiya
        foreach ($this->tableKey as $key => $value) {

            // $res = $this->db->prepare($sql);
            // $res->bindParam(':username',$this->username);
            // $res->bindParam(':password',$this->password);
            // $res->bindParam(':email',$this->email);
            // $res->bindParam(':img',$this->img);
            // $res->bindParam(':id',$id);
            if(count($this->tableKey)-1===$key){
                $this->sqlUpdate = $this->sqlUpdate."$value="."".':'.$value.""; 
            }else{
                $this->sqlUpdate = $this->sqlUpdate."$value="."".':'.$value.","; 
            }
            $this->bindParams = $this->bindParams.'$res->'."bindParam(':$value',".'$this->'.$value.");" ; 
        }


        // sql injection
    
        foreach ($this->tableKey as $key => $value) {
            if(count($this->tableKey)-1===$key){
                $this->injection = $this->injection.'$this->'.$value.' = mysqli_real_escape_string($this->db,$this->'.$value.')'.";"; 
            }else{
                $this->injection = $this->injection.'$this->'.$value.' = mysqli_real_escape_string($this->db,$this->'.$value.')'.";"; 
            }
        }
        // malumot toldirish
        foreach ($this->tableKey as $key => $value) {
            if(count($this->tableKey)-1===$key){
                $this->request = $this->request.'$this->'.$value.'='.'$this->request['."'".$value."'".'] ?? "";'; 
            }else{
                $this->request = $this->request.'$this->'.$value.'='.'$this->request['."'".$value."'".'] ?? "";'; 
            }
        }
        $res =   file_put_contents($path."/controller/$this->ControllerName.php"," <?php
        
    class $this->ControllerName  
     {
        $this->public
        ".'public $request;'."
        ///create data
        public function create(){
            try{
                $this->sqlInsert;
                ".'$res=$this->db->prepare($sql);'."
                $this->bindParams 
              if(".'$res->execute()'."){
                    http_response_code(201);
                    return array('xabar'=>'$this->tableName table add');
                }else {
                    http_response_code(403);
                    return false;
                }
            } catch (Exception ".'$e'.") {
                http_response_code(500);
                return array('xabar'=>".'$e->getMessage()'.");
            }
        }
        // update
        public function update(".'$id'."){
            try{
                ".'$sql="UPDATE `'.$this->tableName.'`  SET '.$this->sqlUpdate.' WHERE id='."".':id'."".'";'."  
                ".'$res=$this->db->prepare($sql);'."
                $this->bindParams 
              ".' $res->bindParam('."':id'".',$id,PDO::PARAM_INT)'.";
                if(".'$res->execute()'."){
                    http_response_code(200);
                    return array('xabar'=>'$this->tableName table update');
                 } else {
                    return false;
                 }
            } catch (Exception ".'$e'.") {
                http_response_code(500);
                return array('xabar'=>".'$e->getMessage()'.");
            }
        }
        // delete
        public function delete(".'$id'."){
            try{
                ".'$sql="DELETE FROM `'.$this->tableName.'` WHERE id=:id";'."  
                ".'$res=$this->db->prepare($sql);'."
                ".' $res->bindParam('."':id'".',$id,PDO::PARAM_INT)'.";
                if(".'$res->execute()'."){
                    http_response_code(200);
                    return array('xabar'=>'delete users');
                }else {
                    return false;
                }
            } catch (Exception ".'$e'.") {
                http_response_code(500);
                return array('xabar'=>".'$e->getMessage()'.");
            }
            
        }
        // delete keys
        public function deleteKey(".'$key,$id'."){
            try{
                ".'$sql="DELETE FROM `'.$this->tableName.'` WHERE id=:id";'."  
                ".'$res=$this->db->prepare($sql);'."
                ".' $res->bindParam('."':id'".',$id,PDO::PARAM_INT)'.";
                if(".'$res->execute()'."){
                    http_response_code(200);
                    return array('xabar'=>'delete users');
                }else {
                    return false;
                }
            } catch (Exception ".'$e'.") {
                http_response_code(500);
                return array('xabar'=>".'$e->getMessage()'.");
            }
            
        }
        // show id
        public function showId(".'$id'."){
            try{
                ".'$sql="SELECT * FROM `'.$this->tableName.'` WHERE id=:id";'."  
                ".'$res=$this->db->prepare($sql);'."
              ".' $res->bindParam('."':id'".',$id,PDO::PARAM_INT)'.";
              if(".'$res->execute()'."){
                    http_response_code(200);
                    return [...".'$res'."];
                }else {
                    return false;
                }
            } catch (Exception ".'$e'.") {
                http_response_code(500);
                return array('xabar'=>".'$e->getMessage()'.");
            }
            
        }
        // filter key
        public function filter(".'$key,$value'."){
            try{
                ".'$sql="SELECT * FROM `'.$this->tableName.'` WHERE $key=:$key";'."  
                ".'$res=$this->db->prepare($sql);'."
              ".' $res->bindParam('.'":$key'.'"'.',$value)'.";
              if(".'$res->execute()'."){
                    http_response_code(200);
                    return [...".'$res'."];
                }else {
                    return false;
                }
            } catch (Exception ".'$e'.") {
                http_response_code(500);
                return array('xabar'=>".'$e->getMessage()'.");
            }
        }
        public function filterLike(".'$key,$value'."){
            try{
                ".'$sql="SELECT * FROM `'.$this->tableName.'` WHERE $key LIKE :$key";'."  
                ".'$res=$this->db->prepare($sql);'."
              if(".'$res->execute(array('.'":$key'.'"'.'=>'.'"%'.'$value'.'%"'.')'.")){
                    http_response_code(200);
                    return [...".'$res'."];
                }else {
                    return false;
                }
            } catch (Exception ".'$e'.") {
                http_response_code(500);
                return array('xabar'=>".'$e->getMessage()'.");
            }
        }
        public function all(){
            try{
                ".'$sql="SELECT * FROM `'.$this->tableName.'`";'."  
                ".'$res = $this->db'."->query(".'$sql'.");
                if(".'$res'."){
                    http_response_code(200);
                    return [...".'$res'."];
                }else {
                    return false;
                }
            } catch (Exception ".'$e'.") {
                http_response_code(500);
                return array('xabar'=>".'$e->getMessage()'.");
            }
            
        }
        // request 
        public function requestDate(){
            try{
                $this->request
            } catch (Exception ".'$e'.") {
                return array('xabar'=>".'$e->getMessage()'.");
            }
        }
        // filterOr and 
        public function where(".'$data,$type'."){
            try{
                ".'$keys = array_keys($data);'."
                ".'$keyFilter = '."'';"."
                foreach (".'$keys as $key => $value) {
                    if(count($keys)-1===$key'."){
                        ".'$keyFilter =  $keyFilter.'.'"'.'$value='.'".'.'"'."'".'".$data[$value].'.'"'."'".'";'."
                    }    else{
                        ".'$keyFilter =  $keyFilter.'.'"'.'$value='.'".'.'"'."'".'".$data[$value].'.'"'."'".'".'.'"'.'  $type  '.'";'."
                    }
                }
                ".'$sql="SELECT * FROM `'.$this->tableName.'` WHERE  '.'$keyFilter'.'";'."  
                if(".'$this->db'."->query(".'$sql'.")){
                    http_response_code(200);
                    return [...".'$this->db->query($sql)'."];
                }else {
                    http_response_code(500);
                    return false;
                }
            } catch (Exception ".'$e'.") {
                http_response_code(500);
                return array('xabar'=>".'$e->getMessage()'.");
            }
            
         
        }
        // file upload
        public function file(".'$file,$FileType'."){
            ".'$url = '."'/'.".'"store/".md5(time());'."
            ".'$filePath =__DIR__.$url;'."
            ".'$fileRes = "";'."
            ".'$type = explode('."'.'".',$file["name"])[count(explode('."'.'".',$file["name"]))-1];'."
            foreach (".'$FileType as $key => $value'.") {
                if".'($file'."['type']===".'$value'."){
                    if(move_uploaded_file(".'$file["tmp_name"],$filePath.".$type"'.")){
                        ".'$fileRes = $filePath;'."
                    }else{
                        return '';
                    }
                    break;
                }
            }
            if(empty(".'$fileRes'.")){
                return '';
            }else{
               return  ".'$url.$type'.";
            }
        }
        // file upload url
        public function fileUrl(".'$file,$fileType'."){
            ".'$url = '."'/'.".'"store/".md5(time());'."
            ".'$filePath =__DIR__.$url;'."
            ".'$fileRes = "";'."
            ".'$type = explode('."'.'".',$file["name"])[count(explode('."'.'".',$file["name"]))-1];'."
            foreach (".'$fileType as $key => $value'.") {
                if".'($file'."['type']===".'$value'."){
                    ".'$fileRes = $filePath;'."
                    break;
                }
            }
            if(empty(".'$fileRes'.")){
                return '';
            }else{
               return  ".'$url.".".$type'.";
            }
        }

        // require
        public function required(".'$required'.")
        {
            try {
                ".'$key__ = array_keys($required);
                $response = [];
                foreach ($key__ as $key => $value) {
                    $res = [...$this->db->prepare("SELECT * FROM `'.$this->tableName.'` WHERE $value='."".':$required[$value]'."".'")->execute()];'."
                    if (count(".'$res) > 0) {
                        $response[$value] = "Required $value";
                    }
                }
                if (count($response) > 0) {
                 return $response;
                } else {
                    return true;
                }
            } catch (Exception $e) {
                http_response_code(500);
                return array("xabar" => $e->getMessage()); '."
            }
        }
        public function deleteFile(".'$url){
            try {
                if(unlink(__DIR__.$url)){
                    return true;
                }else {
                    return false;
                }
            } catch (Exception $e) {
                http_response_code(500);
                return array("xabar" => $e->getMessage()); '."
            }
        }
    }
?>
        ");
        if($res){
            echo $this->ControllerName.' class funtion create';
        }else{
            echo $this->ControllerName.' class funtion error' ;
        }
    }
    public function reset(){
        $this->tableKey = '';
        $this->tableName = '';
        $this->ControllerName = '';
        $this->public = '';
        $this->keySql = '';
        $this->keySqlValue = '';
        $this->sqlInsert = '';
        // update
        $this->sqlUpdate = '';
        // injection
        $this->injection = '';
        $this->request = '';
    }
}


?>