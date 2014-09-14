<?php
/**
| | (_)
| | __ _ ___ ___
| |/ /| |/ __|/ __|
| < | |\__ \\__ \
|_|\_\|_||___/|___/
+++++++++++++++++++++++++++++++++++
* Main
* @category  base
* @package   kiss\base\App
* @author    Scott Dean <scott.dean@graphicdesignhouse.com>
* @tutorial
* Add config data here like so:
* Config::write("MY_CONFIG", "Hello World");
* print Config::read("MY_CONFIG");
*  >> Hello World
**/
namespace kiss\base;
         
class App  {
    
    /*
     * @var string HTTP Content Retuned from the route
    */
    public $response;
    
    /**
    *
    * Init kiss app
    *  - set base config settings
    *  - invoke the rest object
    *  - return the response body.
    *
    **/
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