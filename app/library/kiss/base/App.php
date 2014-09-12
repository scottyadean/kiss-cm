<?php
/**
* Main Kiss Config File
**
**
 _
| | (_)
| | __ _ ___ ___
| |/ /| |/ __|/ __|
| < | |\__ \\__ \
|_|\_\|_||___/|___/
+++++++++++++++++++++++++++++++++++

* @category  Config bootstrap
* @package   kiss\base\App
* @author    Scott Dean <scott.dean@marketingevolution.com>
* @version   SVN: <svn_id>
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
        Config::write('SITE_NAME', 'Marketing Evolution');
        Config::write('SITE_URL',  'http://marketingevolution.com');
        
        //invoke the rest object.
        $rest = new \kiss\Rest();
        
        //Return response to context
        $this->response = $rest->response();
       
    }

}