<?php 
/**
* REST
**
* @category  API
* @package   lib
* @output    A formatted HTTP response 
* @author    Scott Dean <scott.dean@marketingevolution.com>
* @copyright 2014 Good Carrot History: 09/05/2014 - Created
* @license   <http://www.marketingevolution.com> Closed
* @version   SVN: <svn_id>
* @tutorial
* This script provides a simple RESTful API interface to connect to sass
*/
class Rest {
    
    // the http raw route.
    public $route;
    
    
    // The http response content type ['json', 'xml', 'html']
    public $format = "html";
    
    // Default method controller to call
    public $controller = "index";
    
    //default method action to call if invalid default to help 
    public $method = "GET";

    //request params
    public $params = array();
    
    // Class Container for avl. resource methods.
    public $router;
                   
    
    // Define whether an HTTPS connection is required
    public $HTTPS_required = false;
    
    // Define whether user authentication is required
    public $authentication_required = false;

    /** 
    * Rest object setup
    * @param $resources <object> List of Rest Avl. Resources 
    */
    public function __construct($router) {
            
         $this->router  = $router;
         $this->route   = explode("/", $_SERVER["REQUEST_URI"] );
          
          
         $this->action     = isset($this->route[2]) && !empty($this->route[2]) ? trim(strtolower($this->route[2])) : 'index';
         $this->controller = isset($this->route[1]) && !empty($this->route[1]) ? trim(strtolower($this->route[1])) : 'index';
         
         
         $this->format     = isset($_REQUEST['format']) ? trim(strtolower($_REQUEST['format'])) : 'html';
         $this->method     = isset($_SERVER['REQUEST_METHOD']) ? trim(strtoupper($_SERVER['REQUEST_METHOD'])) : 'GET';      
         $this->params     = $this->params($this->route);
         
    }
    
    /**
     * Deliver HTTP Response
     * @param string $api_response The desired HTTP response data
     * @return void <http context, headers>
     **/ 
    function response() {
        
        //call the resource.
        return $this->router->call($this->action,
                                   $this->controller,
                                   $this->params,
                                   $this->method,
                                   $this->format);
    }
    
    function params($p) {
       
       $result = array();
        
        if(isset($p[0]))
            unset($p[0]);
        
        if(isset($p[1]))
            unset($p[1]);
        
        if(isset($p[2]))
            unset($p[2]);
              
        while (count($p)) {
            list($key,$value) = array_splice($p, 0, 2);
            $result[$key] = $value;
        }
       
        array_merge($result, $_REQUEST);
               
      return $result;   
         
    }
    
    
}
