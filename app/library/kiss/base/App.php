<?php
/**
* Main Kiss Config File
**
* @category  Config bootstrap
* @package   kiss\base\App
* @author    Scott Dean <scott.dean@marketingevolution.com>
* @version   SVN: <svn_id>
* @tutorial
* Add Config Data Here
*/
namespace kiss\base;
         
class App {
    
    public $response;
    
    public function __construct($file) {
        
    
        // Define path to application directory
        defined('APP_PATH')  || define('APP_PATH', $file . '/../app');
        defined('BASE_PATH') || define("BASE_PATH", $file);
        defined('SITE_NAME') || define("SITE_NAME", "Marketing Evolution");
        defined('SITE_URL')  || define("SITE_URL", "http://marketingevolution.com");
       
        //invoke the rest object.
        $rest = new \kiss\Rest();
        
        //Return response to context
         $this->response = $rest->response();
       
    }

}