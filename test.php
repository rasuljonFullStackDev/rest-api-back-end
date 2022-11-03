
<?php

public function update($id){
    try{
        $sql="UPDATE `users`  SET username=:username,password=:password,email=:email,img=:img WHERE id=:id";  
        $res = $this->db->prepare($sql);
        $res->bindParam(':username',$this->username);
        $res->bindParam(':password',$this->password);
        $res->bindParam(':email',$this->email);
        $res->bindParam(':img',$this->img);
        $res->bindParam(':id',$id);
        $sql="SELECT * FROM `users` WHERE id=:id";  
        $res=$this->db->prepare($sql);
        $res->bindParam(':id',$id,PDO::PARAM_INT);
        $res->execute();
        if($res->execute()){
            http_response_code(200);
            return array('xabar'=>'users table update');
         } else {
            return false;
         }
    } catch (Exception $e) {
        http_response_code(500);
        return array('xabar'=>$e->getMessage());
    }
}