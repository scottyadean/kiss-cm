<?php

/**
* Config Settings Setter/Getter
* @category  Config
* @package   kiss\base\Config
* @author    Scott Dean <scott.dean@graphicdesignhouse.com>
* Add APP Wide Settings Data Here
*/
namespace kiss\base;

class Settings {
    
    private static $_configuration = array();
    private static $_environment   = "development";
    
        
        public static function write($key, $value) {
            self::$_configuration[$key] = $value;
        }
       
        public static function read($key) {
            return isset(self::$_configuration[$key]) ? self::$_configuration[$key] : null;
        }
        
        
        public static function delete($key) {
            
            if(isset(self::$_configuration[$key])) {
                
                unset(self::$_configuration[$key]);
               
            }
            
        }
        
    
    }