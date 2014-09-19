<?php
namespace kiss\base;

class Messages {
    
        public static function FlashMessage( $action="get",  $value = "", $namespace="flash_message") {
            
            
            if(strtolower($action) == 'get'){
                
                $msg = isset($_SESSION[$namespace]) ? $_SESSION[$namespace] : false;
                
                $_SESSION[$namespace] = false;
                
            }else{
               
               $msg = $_SESSION[$namespace] = $value;
            
            }
            
            return $msg;   
        }
    
}