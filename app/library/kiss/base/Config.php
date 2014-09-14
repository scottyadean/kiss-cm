<?php

/**
* Main Kiss Config File
**
**
| | (_)
| | __ _ ___ ___
| |/ /| |/ __|/ __|
| < | |\__ \\__ \
|_|\_\|_||___/|___/
+++++++++++++++++++++++++++++++++++
* @category  Config
* @package   kiss\base\Config
* @author    Scott Dean <scott.dean@graphicdesignhouse.com>
* @version   SVN: <svn_id>
* @tutorial
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