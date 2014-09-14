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

* @category  Config bootstrap
* @package   kiss\base\App
* @author    Scott Dean <scott.dean@marketingevolution.com>
* @tutorial
* Add Config Data Here
*/
namespace kiss\base;
         
class App  {
    
    public $response;
    
    public function __construct($file) {
        
        // Define path to application directory
        Config::write('BASE_PATH', $file);
        Config::write('APP_PATH' , $file . '/../app');
        Config::write('SITE_NAME', 'GDH Kiss');
        Config::write('SITE_URL',  'http://graphicdesignhouse.com');
        
        //invoke the rest object.
        $rest = new \kiss\Rest();
        
        //Return response to context
        $this->response = $rest->response();
       
    }

}