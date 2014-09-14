<?php 
/**
* REST
**
* @category  Kiss
* @package   kiss
* @output    return a formatted HTTP response 
* @author    Scott Dean <scott.dean@graphicdesignhouse.com>
* This script provides a simple RESTful API interface to connect to sass
*/
namespace kiss;

class Rest {

    // Class Container for avl. resource method.
    public $router;
    
    // Request Params
    public $params;
                   
    // Define whether an https connection is required
    private $_https_required = false;
    
    // Define whether user authentication is required
    private $_authentication_required = false;

    /** 
    * Rest object setup
    * @return<void>
    */
    public function __construct() {
        
         $this->router = new Router();
         $this->params = $this->router->params;
         
    }
    
    /**
     * Deliver HTTP Response
     * @param string $api_response The desired HTTP response data
     * @return void <http context, headers>
     **/ 
    function response() {
        
        return $this->router->response;
    }
}   