<?php
class Token{
    public function createToken()
    {
        return  md5(base64_encode(time()));
    }
    public function password($key)
    {
        return  md5($key);
    }
    public function key($key)
    {
        return  base64_encode($key);
    }

}

?>