<?php
namespace kiss\base;

class Headers {
    
    /*@var array allowed HTTP request format*/
    public static $http_request_format = array('html', 'xml', 'csv', 'json', 'txt');
    
    
   /*@var array HTTP responses*/ 
   public static $http_response_code = array(200 => 'OK', 400 => 'Bad Request', 401 => 'Unauthorized',
                                             403 => 'Forbidden', 404 => 'Not Found');
    
  /**
    * Set HTTP Response Header based on the resources response var.
    * and Set HTTP Response Content Type
    * @param $status <int> status code
    * @param $content_type <string> content type
    * @param $charset <string> defaults to utf-8
    * @return <void>
   */
    public static function Set($status, $content_type = "html", $filename = false, $charset = 'utf-8') {
        
        header('HTTP/1.1 '.$status.' '.self::$http_response_code[$status]);
        
        if($filename) {
            header("Content-Disposition: attachment; filename={$filename}.csv"); 
        }
        
        $content_type = static::GetContentType($content_type);
        header("Content-Type: {$content_type}; charset={$charset}");
    }


    /**
     * Get all request headers
     * @return array The request headers
     */
    public static function Get() {

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
    * Define the current relative URI
    * @return string
    */
    public static function GetRequestUri() {

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
    * get the controller and action on the param string using a reg exp. pattern
    * @return<array>
    */    
    public static function GetRegExParams($matches) {
        
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
     * Set the return format (html, txt, json, xml, csv)
     * @return string
     */
    public static function GetRequestFormat() {
        
        $format = isset($params['api']) ? trim(strtolower($params['api'])) : false;
        
        return isset($format) && in_array($format, self::$http_request_format) ?  $format : 'html';
    }

    /**
     * Get the request method used, taking overrides into account
     * @return string The Request method to handle
     */
    public static function GetRequestMethod() {

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
                    $headers = static::Get();
                    if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], array('PUT', 'DELETE', 'PATCH'))) {
                            $method = $headers['X-HTTP-Method-Override'];
                    }
            }

            return $method;
    }

    /**
    * Return the correct content type
    * @param $type <string> content type
    * @return <string>
    */
    public static function GetContentType($type) {
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


    
}
