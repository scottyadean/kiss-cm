<?php
namespace kiss\base;

class Routes {
    
    /*@var string current request uri .*/
    public $uri;
    
    /*@var string default title to pass to the controller .*/
    public $title;
    
    /*@var array contains all the request params .*/
    public $params;
    
    /*@var array contains all the routes .*/
    public $routes;
    
    /*@var array contains the acl contact [route]-[action] == [roles] .*/
    public $acl;
    
    /*@var array if set will enable acl and rediect to the contents of the array .*/
    public $aclEnabled = array('controller'=>'auth', 'action'=>'login');
    
    /*@var acl roles .*/
    public $roles = array( 0 => array('guests'),
                           1 => array('guests','users'),
                           2 => array('guests','users','admins'));
    
    
    /*@var int response code.*/
    public $status =  200;
    
    /*@var string lowwer-case response format.*/
    public $format = 'html';
    
    /*@var string upper-case header verb.*/
    public $method = "GET";
    
    /*@var array route resource headers.*/
    public $headers;
    
    /*@var string the controller route resource.*/
    public $controller;
    
    /*@var string defaul controller route resource*/
    public $defaultController = 'index';
    
    /*@var string route resource*/
    public $action;
    
    /*@var string default action route resource*/
    public $defaultAction = 'index';
    
    /*@var string error controller route resource*/
    public $errorController = 'error';
    
    /*@var string error action route resource*/
    public $errorAction = 'error';
    
    /*@var array route info plucked from the routes array*/
    public $currentRoute;
    
    /*@var string default layout template will parse when calling mvc*/
    public $layout = "_index";
    
    /*@var object template to render on the request.*/
    public $template;
    
    /*@var string template response.*/
    public $response;
    
    /*@var array error container*/
    public $errors = array();
    
    /*@var static routes*/
    private $_statics;

    /*@var array The before middleware route patterns and their handling functions*/
    private $befores = array();

    /*@var object The function to be executed when no route has been matched*/
    private $notFound;

    /*@var string Current baseroute, used for (sub)route mounting*/
    private $baseroute = '';

    
    /** 
    * Routes object setup
    * @return<void>
    */
    public function init() {
       
       //create an instance of the base template  
       $this->template = new Template();
       
       //Set default controller and action.
       $this->defaultAction  =  $this->defaultController = 'index';
       $this->errorAction    =  $this->errorController   = 'error';

    }    
    
    public function routeShutDownError($exception='') {
        Headers::Set(500, 'html');
        print $this->template->mvc($this->getMvcConfig($exception)); 
                
    }
    
   /**
    * public add
    *  add route resource to a list
    *  @param $route <string> name of the route controller.
    *  @param $actions<array> list of avl. route actions.
    *  @param $callback <closure> the function to render instead of mvc.
    * @return<void> 
    */        
    public function add($route,
                        $pattern = null,
                        array $actions=array(),
                        $alias = null,
                        $callback = false) {
        
            
        $this->acl[$route] = $actions;
        
        $this->routes[$route] = array('actions' => array_keys($actions),
                                      'pattern' => $pattern,
                                      'alias'   => $alias,
                                      'callback'=> $callback);
    }
    
   /**
    * public addRole
    * add acl role to the acl roles 
    * @return<void>
    */    
    public function addRole(array $role = array(), $index = 0) {
        
        $this->roles[$index] = $role;
    }
    
   /**
    * public map
    * Match a resoure to a method in this class route
    * @return<void>
    */       
    public function map() {
        
        $this->headers  = Headers::Get();
        $this->uri      = Headers::GetRequestUri();
        $this->method   = Headers::GetRequestMethod();
        $this->params   = $this->getRequestParams();
        $this->format   = Headers::GetRequestFormat($this->params);
        
        return $this;
        
    }
     
   /**
    * public route
    * Match a resoure to a method in this class route
    * @return<mixed> content from the resoure return var.
    */      
    public function route() {
       
       $this->routeCheck();
       $this->aclCheck(); 
       
       Headers::Set(200, $this->format);
       
       
       if( !empty($this->currentRoute) && isset($this->currentRoute['callback']) ) {
        
          if( is_callable($this->currentRoute['callback'])){
            $this->response = call_user_func_array($this->currentRoute['callback'] , array("args"=>$this->getMvcConfig()));
            return;
           }
       }
       
       $this->response = $this->template->mvc($this->getMvcConfig());
        
    }
    
    
    /*
    * Route Missing controllers and actions to the Error Controller.
    * @param $args<array> $_REQUEST object.
    */
    public function error($args=''){
        
        // set the header and stats code.
        Headers::Set(404, $this->format);
        $this->title = "File Not Found";
        return $this->template->mvc($this->getMvcConfig());    
    }

   /**
    * Return the controller parse default config array.
    * @return <array>
    */
    public function getMvcConfig($exception = false) {
        
        $title = !empty($this->title) ? $this->title : ucwords(Strings::camelCaseSplit($this->controller));
        //set the default controller config.        
        return array( 'controller' => ucwords($this->controller),
                      'action'     => Strings::actionRouterName($this->action),
                      'layout'     => $this->layout,
                      'view'       => strtolower($this->controller).'/'.$this->action,
                      'params'     => $this->params,
                      'format'     => $this->format,
                      'scope'      => array("title"     => $title,
                                            "errors"    => $this->errors,
                                            'status'    => $this->status,  
                                            "exception" => $exception));
    }    
  
  /**
    * Get the / param list form the uri
    * ie(  /controller/action/param/sample ) >> array( 'controller' => 'action', 'param' => 'sample' ); 
    * @param $result <array> array join of parsed params from both the standard $_REQUEST object and the /route/params  
    * @return <array>
    */
    public function getRequestParams($result = array()) {        
        
        //try and find the route using the reg. ex.
        foreach ($this->routes as $k=>$r) {
            if (preg_match_all('#^' . $r['pattern'] . '$#', $this->uri, $matches, PREG_OFFSET_CAPTURE)) {
                  
                   $this->controller = $k;
                   
                   if($this->aliasCheck())
                        break;
                   
                   if(count($matches) > 0)
                      $a = Headers::GetRegExParams(array_slice($matches, 1));
                   
                   $this->action = array_shift($a);
                   
                   break;  
            }
        }
        
        //init.php in the pub folder will assign all uri prams to the _route index
        $p = explode("/", rtrim( $_REQUEST["_route"], "/"));
    
        //if we do not have a reg exp. passed in on the uri get the 1st param as the controller.
        if(empty($this->controller) && isset($p[0]) && trim($p[0]) != "") {
            $this->controller = str_replace(".php", "", trim($p[0]));
        }
        
        //if we do not have a reg exp. passed in on the uri get the 2nd pram as the action.
        if(empty($this->action) && isset($p[1]) && trim($p[1]) != ""){
           $this->action = str_replace(".php", "", trim($p[1]));
        }
    
        //if we could not match a reg ex on the route check the route again for the alias
        if(empty($this->action) || empty($this->controller)){
            $this->aliasCheck();
        }
    
        //if we have params parse the /slash/separated/params/into an array
        //like this array(slash=>separated, params=>into).
        if(count($p) != 0){
            while (count($p)) {
                if(!isset($p[1])) {
                     if(isset($p[0]))
                        $result[$p[0]] = null;
                    break;    
                }
                
                list($key, $value) = array_splice($p, 0, 2);
                
                if(trim($key) != "")
                    $result[$key] = $value;
            }
        }
        
        return array_merge($_REQUEST, $result);
    }
    
    
    /**
    * make sure everything is good to go before we call our controller->action\
    * if we have a problem route to the default error controller.
    * @return<void>
    */
    public function routeCheck() {
   
        if(empty($this->controller)) {
             $this->controller = $this->defaultController;
        }

        if(empty($this->action)){
            $this->action = $this->defaultAction;
        }
        
        //if the controller is in the routes list.
        if(!isset($this->routes[$this->controller])) {
             $this->errors[]  = "404 - File not found";
             $this->errors[] = "Controller (".htmlentities($this->controller).") does not exist";
             $this->controller = $this->errorController;
             $this->action = $this->errorAction;
             
        }else {
           
            if(!isset($this->routes[$this->controller]['actions'])
               || !in_array($this->action, $this->routes[$this->controller]['actions'])) {
                $this->errors[]  = "404 - File not found";
                $this->errors[] = "Action (".htmlentities($this->action).")  does not exist.";
                $this->controller = $this->errorController;
                $this->action = $this->errorAction;
            }
            
        }
           
        if(isset($this->routes[$this->controller])) {
            $this->currentRoute = $this->routes[$this->controller];
        }
      
    }
   
   /**
     *pub  aclCheck
    * check the route for the acl permission if it exists.
    * @return bool
    */
    public function aclCheck() {
        
         if( is_array($this->aclEnabled) ) {
             
             $role    = isset($_SESSION['role']) ? (int)$_SESSION['role'] : 0;
             
             if(!in_array($this->acl[$this->controller][$this->action], $this->roles[$role])){
                $this->controller = $this->aclEnabled['controller'];
                $this->action = $this->aclEnabled['action'];
             }        
       }
        
    }
    
    /**
     *pub aliasCheck
    * check the route array and pluck the alias directive if it exists.
    * @return bool
    */
    public function aliasCheck() {
        
        if( isset($this->routes[$this->controller]['alias'])) {        
                $alias = explode('@', $this->routes[$this->controller]['alias']);
                $this->controller = $alias[0];
                $this->action     = $alias[1];  
                return true;
           }
           
          return false;
        
    }
    
    /**
     * Store a before middleware route and a handling function to be executed when accessed using one of the specified methods
     * @param string $methods Allowed methods, | delimited
     * @param string $pattern A route pattern such as /about/system
     * @param object $fn The handling function to be executed
     */
    public function before($methods, $pattern, $fn) {
    
            $pattern = $this->baseroute . '/' . trim($pattern, '/');
            $pattern = $this->baseroute ? rtrim($pattern, '/') : $pattern;
    
            foreach (explode('|', $methods) as $method) {
                    $this->befores[$method][] = array(
                            'pattern' => $pattern,
                            'fn' => $fn
                    );
            }
    }

    /**
     * Store a route and a handling function to be executed when accessed using one of the specified methods
     * @param string $methods Allowed methods, | delimited
     * @param string $pattern A route pattern such as /about/system
     * @param object $fn The handling function to be executed
     */
    public function match($methods, $pattern, $fn) {

            $pattern = $this->baseroute . '/' . trim($pattern, '/');
            $pattern = $this->baseroute ? rtrim($pattern, '/') : $pattern;

            foreach (explode('|', $methods) as $method) {
                    $this->_statics[$method][] = array(
                            'pattern' => $pattern,
                            'fn' => $fn
                    );
            }

    }

    /**
     * Shorthand for a route accessed using GET
     * @param string $pattern A route pattern such as /about/system
     * @param object $fn The handling function to be executed
     */
    public function get($pattern, $fn) {
            $this->match('GET', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using POST
     * @param string $pattern A route pattern such as /about/system
     * @param object $fn The handling function to be executed
     */
    public function post($pattern, $fn) {
            $this->match('POST', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using PATCH
     * @param string $pattern A route pattern such as /about/system
     * @param object $fn The handling function to be executed
     */
    public function patch($pattern, $fn) {
            $this->match('PATCH', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using DELETE
     * @param string $pattern A route pattern such as /about/system
     * @param object $fn The handling function to be executed
     */
    public function delete($pattern, $fn) {
            $this->match('DELETE', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using PUT
     * @param string $pattern A route pattern such as /about/system
     * @param object $fn The handling function to be executed
     */
    public function put($pattern, $fn) {
            $this->match('PUT', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using OPTIONS
     * @param string $pattern A route pattern such as /about/system
     * @param object $fn The handling function to be executed
     */
    public function options($pattern, $fn) {
            $this->match('OPTIONS', $pattern, $fn);
    }

    /**
     * Mounts a collection of callables onto a base route
     * @param string $baseroute The route subpattern to mount the callables on
     * @param callable $fn The callabled to be called
     */
    public function mount($baseroute, $fn) {

            // Track current baseroute
            $curBaseroute = $this->baseroute;

            // Build new baseroute string
            $this->baseroute .= $baseroute;

            // Call the callable
            call_user_func($fn);

            // Restore original baseroute
            $this->baseroute = $curBaseroute;
    }

    /**
     * Execute the router: Loop all defined before middlewares and routes, and execute the handling function if a mactch was found
     * @param object $callback Function to be executed after a matching route was handled (= after router middleware)
     */
    public function runStatic($callback = null) {

            // Define which method we need to handle
            $this->method = $this->getRequestMethod();

            // Handle all before middlewares
            if (isset($this->befores[$this->method]))
                    $this->handle($this->befores[$this->method]);

            // Handle all routes
            $numHandled = 0;
            if (isset($this->_statics[$this->method]))
                    $numHandled = $this->handle($this->_statics[$this->method], true);

            // If no route was handled, trigger the 404 (if any)
            if ($numHandled == 0) {
                    if ($this->notFound && is_callable($this->notFound)) call_user_func($this->notFound);
                    else header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            }
            // If a route was handled, perform the finish callback (if any)
            else {
                    if ($callback) $callback();
            }

            // If it originally was a HEAD request, clean up after ourselves by emptying the output buffer
            if ($_SERVER['REQUEST_METHOD'] == 'HEAD') ob_end_clean();
    }

    /**
     * Set the 404 handling function
     * @param object $fn The function to be executed
     */
    public function set404($fn) {
            $this->notFound = $fn;
    }

    /**
     * Handle a a set of routes: if a match is found, execute the relating handling function
     * @param array $routes Collection of route patterns and their handling functions
     * @param boolean $quitAfterRun Does the handle function need to quit after one route was matched?
     * @return int The number of routes handled
     */
    private function handle($routes, $quitAfterRun = false) {

            // Counter to keep track of the number of routes we've handled
            $numHandled = 0;

            // The current page URL
            $uri =  Headers::GetRequestUri();

            // Variables in the URL
            $urlvars = array();

            // Loop all routes
            foreach ($routes as $route) {

                    // we have a match!
                    if (preg_match_all('#^' . $route['pattern'] . '$#', $uri, $matches, PREG_OFFSET_CAPTURE)) {

                            // Extract the matched URL parameters (and only the parameters)
                            $params = Headers::GetRegExParams(array_slice($matches, 1));

                            // call the handling function with the URL parameters
                            call_user_func_array($route['fn'], $params);

                            // yay!
                            $numHandled++;

                            // If we need to quit, then quit
                            if($quitAfterRun)
                                break;

                    }
            }

            // Return the number of routes handled
            return $numHandled;
    }
}