<?php

/**
* Config Setter/Getter
* @category  Config
* @package   kiss\base\Config
* @author    Scott Dean <scott.dean@graphicdesignhouse.com>
* Add Config Data Here
*/
namespace kiss\base;

class Config {
    
    private static $_configuration = array();
    private static $_environment   = "development";
    
        
        public static function write($key, $value) {
            self::$_configuration[$key] = $value;
        }
       
        public static function read($key) {
            return isset(self::$_configuration[$key]) ? self::$_configuration[$key] : null;
        }
    
    }