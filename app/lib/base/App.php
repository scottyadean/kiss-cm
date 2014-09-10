<?php

namespace base;
         
class App {
    
    public function __construct($file) {
       
        // Define path to application directory
        defined('APP_PATH')  || define('APP_PATH', $file . '/../app/');
        defined('BASE_PATH') || define("BASE_PATH", $file);
        defined('SITE_NAME') || define("SITE_NAME", "Marketing Evolution");
        defined('SITE_URL')  || define("SITE_URL", "http://marketingevolution.com");
       
    }

}