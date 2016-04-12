<?php

class Crypt{
    static $key = '';
    static function encrypt($str)
    {
        return md5(self::$key . $str);
    }
    
    static function set_key($str){
        self::$key = $str;
    }
    
    static function get_key(){
        return self::$key;
    }
}
?>
