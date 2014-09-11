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

    // Class Container for avl. resource method.
    public $router;
    
    // Request Params
    public $params;
                   
    // Define whether an https connection is required
    public $https_required = false;
    
    // Define whether user authentication is required
    public $authentication_required = false;

    /** 
    * Rest object setup
    * @param $resources <object> List of Rest Avl. Resources 
    */
    public function __construct() {

         $this->router  = new Routes();
         $this->params  = $this->router->map();
         
    }
    
    /**
     * Deliver HTTP Response
     * @param string $api_response The desired HTTP response data
     * @return void <http context, headers>
     **/ 
    function response() {
        
        //call the resource.
        return $this->router->route();
    }
}   
