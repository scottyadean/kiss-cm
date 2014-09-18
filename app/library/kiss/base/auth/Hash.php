<?php

namespace kiss\base\auth;

class Hash{
    
     public static function SetHmac($password) {
        
        $salt = uniqid(mt_rand());
        return array('hash'=>hash_hmac("sha256", $password, $salt), 'password'=>$password, 'salt'=>$salt);
        
     }
    
     public static function GetHmac($password, $salt, $hash )  {
        
        return (hash_hmac("sha256", $password, $salt) === $hash ) ? true : false;
         
     }
    

    
}
