<?php

namespace base; 

class Routes {
    
    //methods allowed to be called by the service.   
    public $routes;
    
    //the template to render on the request.
    public $template;
    
    public $method = "GET";
    
    //the route resource.
    public $controller;
    
    //the route action
    public $action;
    
    //http status code
    public $status = 200;

    //response formate html, xml, json 
    public $format = 'html';
    
    public $errors;
    
    //The request object passed from the front controller.
    protected $_params;
     
    // Define HTTP responses
    public $http_response_code = array(200 => 'OK',
                                       400 => 'Bad Request',
                                       401 => 'Unauthorized',
                                       403 => 'Forbidden',
                                       404 => 'Not Found');
   
 
   /**
    * public call
    * Match a resoure to a method in this class route
    * @param $this->action,
            $this->controller,
            $this->params,
            $this->method,
            $this->format
    * @return<mixed> content from the resoure return var.
    */      
    public function call($action, $controller, $params = array(), $verb="GET", $format = "html") {
         
        
        //set the controller name 
        $this->controller = trim($controller);
        
        if(empty($this->controller)){
            $this->controller = 'index';
        }
        
        //set the action name 
        $this->action = trim($action);
        
        if(empty($this->action)){
            $this->action = 'index';
        }
        
        //the request method from the rest verb ( GET, POST, PUT, DELETE )
        $this->method = $verb;
        
        //set the formate the user is requesting.
        $this->format = $format;
        
        //if the method is in the method list.
        if(!array_key_exists($this->controller, $this->routes)) {
            $this->controller = 'fileNotFound';
            $this->errors = array("Controller (".htmlentities($this->controller).") not found");
        }else {
           
            if(!isset($this->routes[$this->controller]['actions'])
               || !in_array($this->action, $this->routes[$this->controller]['actions'])) {
                $this->controller = 'fileNotFound';
                $this->errors = array("Action (".htmlentities($this->action).")  not found.");
            }
            
        }
        
        
        //collect the request get / post args.
        $this->_params = $params;
        
        //call the resource by name and pass the args.
        return call_user_func_array(array($this, $this->controller), array("args"=>$params));  
    }
    
    public function fileNotFound($args){
       //set the header and stats code.
       $this->_setHeader(404, 'html');
       
      return $this->template->parseController( array( 'controller' => 'Error',
                                                      'action'=>'index',
                                                      'view'=>'error/index',
                                                      'scope'=>array("title"      =>"File Not Found",
                                                                     'error'      => $this->errors,
                                                                     "controller" => $this->controller,
                                                                     "action"     => $this->action,
                                                                     "methods"    => array_keys($this->routes)),
                                                      'params'=>$this->_params));    
    }
  
  
   /**
    * Return the correct content type
    * @param $type <string> content type
    * @return <string>
    */
    public function getContentType($type) {
        switch($type) {

            case"json":
            case"jsonp":
                $type = 'application/json';
            break;
            
            case"xml":
            case"xul":
            case"rss":
            case"xslt":
                $type = 'application/xml';
            break;
            
            case"html":
            case"htm":
            case"phtml":
            case"html.rb":
                $type = 'text/html';
            break;

            case"txt":
            case"text":
                $type = 'text/plain';
            break;

            case"csv":
                $type = 'application/octet-stream';
            break;

            default:
                $type = 'application/json';
            break;

        };

        return $type;
    }
    
   
    /**
    * Set HTTP Response Header based on the resources response var.
    * and Set HTTP Response Content Type
    * @param $status <int> status code
    * @param $content_type <string> content type
    * @param $charset <string> defaults to utf-8
    * @return <void>
    */
    public function _setHeader($status, $content_type = "html", $charset = 'utf-8') {
        
        header('HTTP/1.1 '.$status.' '.$this->http_response_code[$status]);
        
        if($content_type == 'csv') {
            header("Content-Disposition: attachment; filename={$this->resource}.csv"); 
        }
        
        $content_type = $this->getContentType($content_type);
        
        header("Content-Type: {$content_type}; charset={$charset}");
        
    }
    
    
    
    public function _isXhr()  {
        
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            
            return true;
        }
        
        return false;

    }
    
    
    
    
    
}