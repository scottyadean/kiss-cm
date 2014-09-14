<?php

namespace kiss\base;

class Routes {
    
    public $uri;
    public $title;
    public $params;
    public $routes;
    
    public $status =  200;
    public $format = 'html';
    public $method = "GET";
    public $headers;
    
    /*@var string the controller route resource.*/
    public $controller;
    
    /*@var string error controller route resource*/
    public $errorController = 'error';
    
    /*@var string defaul controller route resource*/
    public $defaultController = 'index';
    
    /*@var string route resource*/
    public $action;
    
    /*@var array route info plucked from the routes array*/
    public $currentRoute;
    
    /*@var string error action route resource*/
    public $errorAction = 'error';
    
    /*@var string default action route resource*/
    public $defaultAction = 'index';
    
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
    
    /*@var array HTTP responses*/
    public $http_response_code = array(200 => 'OK', 400 => 'Bad Request', 401 => 'Unauthorized',
                                       403 => 'Forbidden', 404 => 'Not Found');
   
    /*@var array allowed HTTP request format*/
    public $http_request_format = array('html', 'xml', 'csv', 'json', 'txt');
   
   /**
    * public add
    *  add route resource to a list
    *  @param $route <string> name of the route controller.
    *  @param $actions<array> list of avl. route actions.
    *  @param $callback <closure> the function to render instead of mvc.
    * @return<void> 
    */        
    public function add($route, $pattern = null, array $actions=array(), $callback = false) {
        
        $this->routes[$route] = array('actions' => $actions ,
                                      'pattern' => $pattern,
                                      'callback'=> $callback);
        
        
    }
    
   /**
    * public map
    * Match a resoure to a method in this class route
    * @return<void>
    */       
    public function map() {
        
        $this->uri      = $this->getRequestUri();
        $this->method   = $this->getRequestMethod();
        $this->format   = $this->getRequestFormat();
        $this->params   = $this->getRequestParams();
        $this->headers  = $this->getRequestHeaders();
        
        return $this;
        
    }
     
   /**
    * public route
    * Match a resoure to a method in this class route
    * @return<mixed> content from the resoure return var.
    */      
    public function route() {
       
       $this->routeCheck();
       $this->setHeader(200, $this->format);
       
       if( !empty($this->currentRoute) && isset($this->currentRoute['callback']) ) {
        
          if( is_callable($this->currentRoute['callback'])){
            $this->response = call_user_func_array($this->currentRoute['callback'] , $this->params);
            return;
           }
       }
       
       $this->response = $this->template->mvc($this->getMvcConfig());
        
    }
    
    /*
    * Route Missing controllers and actions to the Error Controller.
    * @param $args<array> $_REQUEST object.
    */
    public function error($args){
        
        // set the header and stats code.
        $this->setHeader(404, $this->$format);
        $this->title = "File Not Found";
        return $this->template->mvc($this->getMvcConfig());    
    }
  
   /**
    * Define the current relative URI
    * @return string
    */
    public function getRequestUri() {

            // Get the current Request URI and remove rewrite basepath from it (= allows one to run the router in a subfolder)
            $basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
            $uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));

            // Don't take query params into account on the URL
            if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));

            // Remove trailing slash + enforce a slash at the start
            $uri = '/' . trim($uri, '/');

            return $uri;

    }
    
    /**
     * Set the return format (html, txt, json, xml, csv)
     * @return string
     */
    public function getRequestFormat() {
        
        $format = isset($params['api']) ? trim(strtolower($params['api'])) : false;
        
        return isset($format) && in_array($format, $this->http_request_format) ?  $format : 'html';
    }
  
    /**
     * Get all request headers
     * @return array The request headers
     */
    public function getRequestHeaders() {

            // getallheaders available, use that
            if (function_exists('getallheaders')) return getallheaders();

            // getallheaders not available: manually extract 'm
            $headers = array();
            foreach ($_SERVER as $name => $value) {
                    if ((substr($name, 0, 5) == 'HTTP_') || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
                            $headers[str_replace(array(' ', 'Http'), array('-', 'HTTP'), ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                    }
            }
            return $headers;

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
                $type = 'text/html';
            break;

        };

        return $type;
    }    
    

    /**
     * Get the request method used, taking overrides into account
     * @return string The Request method to handle
     */
    public function getRequestMethod() {

            // Take the method as found in $_SERVER
            $method = $_SERVER['REQUEST_METHOD'];

            // If it's a HEAD request override it to being GET and prevent any output, as per HTTP Specification
            // @url http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.4
            if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
                    ob_start();
                    $method = 'GET';
            }

            // If it's a POST request, check for a method override header
            else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $headers = $this->getRequestHeaders();
                    if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], array('PUT', 'DELETE', 'PATCH'))) {
                            $method = $headers['X-HTTP-Method-Override'];
                    }
            }

            return $method;
    }
    
   /**
    * Return the controller parse default config array.
    * @return <array>
    */
    public function getMvcConfig() {
        
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
    * Get the / param list form the uri
    * ie(  /controller/action/param/sample ) >> array( 'controller' => 'action', 'param' => 'sample' ); 
    * @param $result <array> array join of parsed params from both the standard $_REQUEST object and the /route/params  
    * @return <array>
    */
    public function getRequestParams($result = array()) {
        
        $p = explode("/", rtrim( $_REQUEST["_route"], "/"));
        
        foreach ($this->routes as $k=>$r) {
            if (preg_match_all('#^' . $r['pattern'] . '$#', $this->uri, $matches, PREG_OFFSET_CAPTURE)) {
                   $this->controller = $k;
                   $this->action = array_shift($this->getRegExParams(array_slice($matches, 1)));
                  
                   break;  
            }
        }
        
        //if we do not have a reg exp. passed in on the uri get the 1st param as the controller.
        if(empty($this->controller) && isset($p[0]) && trim($p[0]) != "") {
            $this->controller = str_replace(".php", "", trim($p[0]));
        }
        //if we do not have a reg exp. passed in on the uri get the 2nd pram as the action.
        if(empty($this->action) && isset($p[1]) && trim($p[1]) != ""){
           $this->action = str_replace(".php", "", trim($p[1]));
        }
        
        //if we have params parse the /slash/sep/params into an array.
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
    * get the controller and action on the param string using a reg exp. pattern
    * @return<array>
    */    
    public function getRegExParams($matches) {
        
       $params =  array_map(function($match, $index) use ($matches) {
                    
                    // We have a following parameter: take the substring from the current param position until the next one's position (thank you PREG_OFFSET_CAPTURE)
                    if (isset($matches[$index+1]) && isset($matches[$index+1][0]) && is_array($matches[$index+1][0])) {
                            return trim(substr($match[0][0], 0, $matches[$index+1][0][1] - $match[0][1]), '/');
                    }
                    
                    // We have no following paramete: return the whole lot
                    else {
                            return (isset($match[0][0]) ? trim($match[0][0], '/') : null);
                    }
                    
                }, $matches, array_keys($matches));
        
        
        return $params;
    }  
  
    /**
    * Set HTTP Response Header based on the resources response var.
    * and Set HTTP Response Content Type
    * @param $status <int> status code
    * @param $content_type <string> content type
    * @param $charset <string> defaults to utf-8
    * @return <void>
    */
    public function setHeader($status, $content_type = "html", $filename = false, $charset = 'utf-8') {
        
        header('HTTP/1.1 '.$status.' '.$this->http_response_code[$status]);
        
        if($filename) {
            header("Content-Disposition: attachment; filename={$filename}.csv"); 
        }
        
        $content_type = $this->getContentType($content_type);
        header("Content-Type: {$content_type}; charset={$charset}");
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
            $uri = $this->getRequestUri();

            // Variables in the URL
            $urlvars = array();

            // Loop all routes
            foreach ($routes as $route) {

                    // we have a match!
                    if (preg_match_all('#^' . $route['pattern'] . '$#', $uri, $matches, PREG_OFFSET_CAPTURE)) {

                            // Extract the matched URL parameters (and only the parameters)
                            $params = $this->getRegExParams(array_slice($matches, 1));

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