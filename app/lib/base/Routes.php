<?php

namespace base; 

class Routes {
    
    //methods allowed to be called by the service.   
    public $routes;
    
    //The request object passed from the front controller.
    public $params;
     
    //response format html, xml, json 
    public $format = 'html';
    
    //the request method
    public $method = "GET";
    
    //the route resource.
    public $controller;
    public $defaultController = 'index';
    
    //the route action
    public $action;
    public $defaultAction = 'index';
    
    //http status code
    public $status = 200;

    //the template to render on the request.
    public $template;
    
    //default layout to use if you would like to call the default config
    public $layout = "_index";
    
    //default page title.
    public $title;
    
    //error container
    public $errors = array();
    

    // Define HTTP responses
    public $http_response_code = array(200 => 'OK',
                                       400 => 'Bad Request',
                                       401 => 'Unauthorized',
                                       403 => 'Forbidden',
                                       404 => 'Not Found');
   
   
   
   
   /**
    * public map
    * Match a resoure to a method in this class route
    * @return<mixed> content from the resoure return var.
    */       
    public function map() {
        
        $this->params   = array_merge($_REQUEST, $this->_setControllerAction(explode("/", rtrim( $_REQUEST["_route"], "/"))) );
        $this->format   = isset($params['api']) ? trim(strtolower($params['api'])) : 'html';
        $this->method   = isset($_SERVER['REQUEST_METHOD']) ? trim(strtoupper($_SERVER['REQUEST_METHOD'])) : 'GET';      
             
        return $this->params;
    }
    
   /**
    * public route
    * Match a resoure to a method in this class route
    * @return<mixed> content from the resoure return var.
    */      
    public function route() {
        
        //check the route can make sure we can find one
        $this->routeCheck();
        
        //call the resource by name and pass the args.
        return call_user_func_array(array($this, $this->controller), array("args"=>$this->params));  
    }
    
    /*
    * Route Missing controllers and actions to the Error Controller.
    * @param $args<array> $_REQUEST object.
    */
    public function fileNotFound($args){
        
        // set the header and stats code.
        $this->_setHeader(404, 'html');
       

        return $this->template->mvc( array( 'controller' => ucwords($this->errorAction),
                                            'action'     => $this->errorAction,
                                            'view'       => $this->errorController.'/'.$this->errorAction,
                                            'scope'=>array("title"      =>"File Not Found",
                                                           'error'      => $this->errors,
                                                           "controller" => $this->controller,
                                                           "action"     => $this->action,
                                                           "methods"    => array_keys($this->routes)),
                                                           'params'=>$this->params));    
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
    
    
        /**
    * Set the / param list form the uri
    * ie(  /controller/action/param/sample ) >> array( 'controller' => 'action', 'param' => 'sample' ); 
    * @param $p <array> array exploded from the _route uri appened by the rewrite
    * @return <array>
    */
    protected function _setControllerAction($p) {
        
        if(isset($p[0]) && trim($p[0]) != ""){
            $this->controller = str_replace(".php", "", trim($p[0]));
        }
        
        if(isset($p[1]) && trim($p[1]) != ""){
           $this->action = str_replace(".php", "", trim($p[1]));
        }
        
        
        $result = array(); 
        if(count($p) > 1){
        
        while (count($p)) {
            
           list($key, $value) = array_splice($p, 0, 2);

            if(trim($key) != "")
                $result[$key] = $value;
        }
        }
        
        return $result;

    }
    
    /**
    * make sure everything is good to go before we call our controller->action\
    * if we have a problem route to the default error controller.
    * @return<void>
    */
    public function routeCheck() {
        
        if(empty($this->controller)) {
             $this->controller = (!empty($this->routes[$this->controller]['defaultController']))
            ? $this->routes[$this->controller]['defaultController'] : $this->defaultController;
        }

        if(empty($this->action)){
            $this->action = (!empty($this->routes[$this->controller]['defaultAction']))
            ? $this->routes[$this->controller]['defaultAction'] : $this->defaultAction;
        }
        
        //if the controller is in the routes list.
        if(!isset($this->routes[$this->controller])) {
            $this->errors[] = "Controller (".htmlentities($this->controller).") not found";
            $this->controller = 'fileNotFound';
        }else {
           
            if(!isset($this->routes[$this->controller]['actions'])
               || !in_array($this->action, $this->routes[$this->controller]['actions'])) {
                $this->controller = 'fileNotFound';
                $this->errors[] = "Action (".htmlentities($this->action).")  not found.";
            }
            
        }
      
    }
   


   /**
    * Return the controller parse default config array.
    * @return <array>
    */
    public function defaultConfig() {
        
        $title = !empty($this->title) ? $this->title : ucwords(Strings::camelCaseSplit($this->controller));
        //set the default controller config.        
        return array( 'controller' => ucwords($this->controller),
                      'action'     => Strings::actionRouterName($this->action),
                      'layout'     => $this->layout,
                      'view'       => strtolower($this->controller).'/'.$this->action,
                      'params'     => $this->params,
                      'scope'      => array("title"  => $title,
                                            "errors" => $this->errors));
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
    
  
    
}