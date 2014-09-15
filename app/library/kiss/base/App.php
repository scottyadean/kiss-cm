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
    *  - set up your db models.
    *  - invoke the rest object
    *  - return the response body.
    *
    **/
    public function __construct($path) {
        
        //always run me first.
        $this->_initSettings($path);

        //set up the db.
        $this->_initDb();
        
        //set up the routes    
        $this->_initRest();
         
        //add any additional _init function here
        
    }
    
    
    protected function _initSettings($path) {
        
        // Define path to application directory
        Settings::write('BASE_PATH', $path);
        Settings::write('APP_PATH' , $path . '/../app');
        Settings::write('SITE_NAME', 'GDH Kiss');
        Settings::write('SITE_URL',  'http://graphicdesignhouse.com');
    }
    
    
    protected function _initDb() {
        
        //Propel is a awesome full featured orm that is suitable for mid-sized to large projects
        //Propel set up is a bit more difficult check out the docs at (http://propelorm.org/)
        //\Propel\Runtime\Propel::init(Settings::read('APP_PATH').'/config/propel.php');
                
        //ActiveRecord is a simple orm that is great for smaller projects.
        //To start using phpactiveRecord create your db, update your db creds below and add your models to the /models dir.
        // -- To Use ActiveRecord in your controllers:
        //      $user = models\User::find_by_username('mr user');
        //      var_dump($user->username);
        $dbcfg = \ActiveRecord\Config::instance();
        $dbcfg->set_model_directory(Settings::read('APP_PATH').'/models');
        $dbcfg->set_connections( array('development' =>'mysql://username:password@localhost/dbname') );
        
    }
   
    
    protected function _initRest() {
 
        //invoke the rest object.
        $rest = new \kiss\Rest();
        
        //Return response to context
        $this->response = $rest->response();
               
    }
    
    
}