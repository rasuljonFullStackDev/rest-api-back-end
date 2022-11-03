 <?php
        
    class BlogesController  
     {
        public $title;  public $description;  public $author;  public $db;
        public $request;
        ///create data
        public function create(){
            try{
                $data = [
                    'title' => "sadsad",
                    'description' => "sadsadsadsa'",
                    'author' => "asdsad'",
                ];
                $pdo = new PDO("mysql:dbname=crud;host=localhost", "root", "" );
                $sql="INSERT INTO `blogs`  ( `title` , `description` , `author` ) VALUES (:title, :description, :author)";
                if($pdo->prepare($sql)->execute($this->request)){
                    http_response_code(201);
                    return array('xabar'=>'blogs table add');
                }else {
                    http_response_code(403);
                    return false;
                }
            } catch (Exception $e) {
                http_response_code(500);
                return array('xabar'=>$e->getMessage());
            }
        }
        // update
        public function update($id){
            try{
                $sql="UPDATE `blogs`  SET title='$this->title',description='$this->description',author='$this->author' WHERE id='$id'";  
                if($this->db->query($sql)){
                    http_response_code(200);
                    return array('xabar'=>'blogs table update');
                 } else {
                    return false;
                 }
            } catch (Exception $e) {
                http_response_code(500);
                return array('xabar'=>$e->getMessage());
            }
        }
        // delete
        public function delete($id){
            try{
                $sql="DELETE FROM `blogs` WHERE id=$id";  
                if($this->db->query($sql)){
                    http_response_code(200);
                    return array('xabar'=>'delete users');
                }else {
                    return false;
                }
            } catch (Exception $e) {
                http_response_code(500);
                return array('xabar'=>$e->getMessage());
            }
            
        }
        // delete keys
        public function deleteKey($key,$id){
            try{
                $sql="DELETE FROM `blogs` WHERE $key=$id";  
                if($this->db->query($sql)){
                    http_response_code(200);
                    return array('xabar'=>'delete users');
                }else {
                    return false;
                }
            } catch (Exception $e) {
                http_response_code(500);
                return array('xabar'=>$e->getMessage());
            }
            
        }
        // show id
        public function showId($id){
            try{
                $sql="SELECT * FROM `blogs` WHERE id=$id";  
                if($this->db->query($sql)){
                    http_response_code(200);
                    return [...$this->db->query($sql)];
                }else {
                    return false;
                }
            } catch (Exception $e) {
                http_response_code(500);
                return array('xabar'=>$e->getMessage());
            }
            
        }
        // filter key
        public function filter($key,$value){
            try{
                $sql="SELECT * FROM `blogs` WHERE $key='$value'";  
                if($this->db->query($sql)){
                    http_response_code(200);
                    return [...$this->db->query($sql)];
                }else {
                    return false;
                }
            } catch (Exception $e) {
                http_response_code(500);
                return array('xabar'=>$e->getMessage());
            }
        }
        public function all(){
            try{
                $sql="SELECT * FROM `blogs`";  
                if($this->db->query($sql)){
                    http_response_code(200);
                    return [...$this->db->query($sql)];
                }else {
                    return false;
                }
            } catch (Exception $e) {
                http_response_code(500);
                return array('xabar'=>$e->getMessage());
            }
            
        }
        
      
        // request 
        public function requestDate(){
            try{
                $this->title=$this->request['title'] ?? "";$this->description=$this->request['description'] ?? "";$this->author=$this->request['author'] ?? "";
            } catch (Exception $e) {
                return array('xabar'=>$e->getMessage());
            }
        }
        // filterOr and 
        public function where($data,$type){
            try{
                $keys = array_keys($data);
                $keyFilter = '';
                foreach ($keys as $key => $value) {
                    if(count($keys)-1===$key){
                        $keyFilter =  $keyFilter."$value="."'".$data[$value]."'";
                    }    else{
                        $keyFilter =  $keyFilter."$value="."'".$data[$value]."'"."  $type  ";
                    }
                }
                $sql="SELECT * FROM `blogs` WHERE  $keyFilter";  
                if($this->db->query($sql)){
                    http_response_code(200);
                    return [...$this->db->query($sql)];
                }else {
                    http_response_code(500);
                    return false;
                }
            } catch (Exception $e) {
                http_response_code(500);
                return array('xabar'=>$e->getMessage());
            }
            
         
        }
        // file upload
        public function file($file,$FileType){
            $url = '/'."store/".md5(time());
            $filePath =__DIR__.$url;
            $fileRes = "";
            $type = explode('.',$file["name"])[count(explode('.',$file["name"]))-1];
            foreach ($FileType as $key => $value) {
                if($file['type']===$value){
                    if(move_uploaded_file($file["tmp_name"],$filePath.".$type")){
                        $fileRes = $filePath;
                    }else{
                        return '';
                    }
                    break;
                }
            }
            if(empty($fileRes)){
                return '';
            }else{
               return  $url.$type;
            }
        }
        // file upload url
        public function fileUrl($file,$fileType){
            $url = '/'."store/".md5(time());
            $filePath =__DIR__.$url;
            $fileRes = "";
            $type = explode('.',$file["name"])[count(explode('.',$file["name"]))-1];
            foreach ($fileType as $key => $value) {
                if($file['type']===$value){
                    $fileRes = $filePath;
                    break;
                }
            }
            if(empty($fileRes)){
                return '';
            }else{
               return  $url.".".$type;
            }
        }

        // require
        public function required($required)
        {
            try {
                $key__ = array_keys($required);
                $response = [];
                foreach ($key__ as $key => $value) {
                    $res = [...$this->db->query("SELECT * FROM `blogs` WHERE $value='$required[$value]'")];
                    if (count($res) > 0) {
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
                return array("xabar" => $e->getMessage()); 
            }
        }
    }
?>
        