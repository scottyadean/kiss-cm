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
* Settings::write("MY_CONFIG", "Hello World");
* print Settings::read("MY_CONFIG");
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
    *  - set up your db models.
    *  - invoke the rest object
    *  - return the response body.
    *
    **/
    public function __construct($path) {
        //ini and session
        $this->_initIni();
        
        //route shout down error display
        $this->_initErrors();
        
        //always run me first.
        $this->_initSettings($path);
        
        //set up the db.
        $this->_initDb();
        
        //set up the routes    
        $this->_initRest();
         
        //add any additional _init function here
        
    }
    
    
    protected function _initIni() {
        
        ini_set('session.cookie_lifetime',  10800);
         
       //start the session.
       session_start();
       
       ini_set ("display_errors" , "Off" );
      
    }
    
    protected function _initSettings($path) {

        // Define path to application directory
        Settings::write('BASE_PATH', $path);
        Settings::write('APP_PATH' , $path . '/../app');
        Settings::write('SITE_NAME', 'GDH Kiss');
        Settings::write('SITE_URL',  'http://graphicdesignhouse.com');
    }
    
    protected function _initErrors() {
      
    register_shutdown_function(function() {     
         
         $err = error_get_last();
         
         if(!empty($err)){
            Errors::Error($err);
        }
    });
    }

    
    protected function _initDb() {
        
        $path = Settings::read("APP_PATH");
        require_once( $path."/config/database.php" );
        
        if(isset($dbcfg)){
            Settings::write("DB", $dbcfg);
        }
    }
   
    
    protected function _initRest() {
 
        //invoke the rest object.
        $rest = new \kiss\Rest();
        
        //Return response to context
        $this->response = $rest->response();
               
    }
    
}