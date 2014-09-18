<?php
/**
* REST Template html
*
* @category  Kiss
* @package   lib/Template.php
* @output    Format a HTTP response replace html elements with parsed values.
* @author    Scott Dean <scott.dean@graphicdesignhouse.com>
*/
namespace kiss\base;

class Template {
    
    /* @var string template file name to render*/
    public $template;
    
    /* @var string updated output from the template parse.*/
    public $output;
    
    /* @var string default template extention */
    public $ext =".phtml" ;
    
    /* @var string default views path */   
    public $viewPath = "/views/";

    /* @var string default layout path */
    public $layoutPath = "/layouts/";
    
    /* @var string default controller path */
    public $controllerPath = "/controllers/";
    
    /* @var string location of the template files */
    public $templatePath;    
    
    /**
    * Set up the base template path
    */
    public function __construct() {
        
        $this->templatePath = Settings::read("APP_PATH");    
        
    }
    
    /**
    * pub display
    * @return <string html, json, xml> return the output
    **/
    public function display() {
        return $this->output;
    }
	
	
    /**
    * Replace tags with ruby style args in parsed template
    * @param $tags <array> array of tags to parse key => value
    * @return<void>
    **/
    public function tags($tags) {
        foreach($tags as $tag=>$data){	
          $this->output = str_replace("<%= ".$tag." %>", $data, $this->output);
        }
    }
    
    /**
     * pub mvc
    * Render mvc controller 
    * @param $context <string> the route context info.
    * @return <string html, json, xml> return the output
    **/
    public function mvc($context) {
          
        require_once($this->templatePath.$this->controllerPath.ucwords($context['controller']).".php");
        
        $controller = new $context['controller'];
        $controller->initProps($context);
        $controller->init();
         
        $action = $controller->action."Action";
        $controller->$action(); 
       
        if(isset($controller->disable_view) && !empty($controller->disable_view)) {
            return;
        }
        
        $controller->content = $this->file($this->templatePath.$this->viewPath.$controller->view.$this->ext, $controller->getMvc());
               
        if(isset($controller->disable_layout) && !empty($controller->disable_layout)) {
            return  $controller->content;
        }
        
        return $this->file($this->templatePath.$this->layoutPath.$controller->getLayout().$this->ext, $controller->getMvc());
    }
    
    /** 
    * pub static render
    * Render file contents
    * @param $path <string url> path to file to parse 
    * @param $context <string> the route context info.
    * @return <string html, json, xml> return the output
    **/
    public static function render($template, array $params = array()){
        ob_start();
        
        if(!empty($params))
            extract($params, EXTR_SKIP);
        
        require(Settings::read("APP_PATH")."/views".$template);
        $ret = ob_get_contents();
        ob_end_clean();
        return $ret;
    }
    
    /**
    * private file
    * Render file contents
    * @param $path <string url> path to file to parse 
    * @param $context <string> the route context info.
    * @return <string html, json, xml> return the output
    **/
    private function file($path, $context) {
        ob_start();
        
        $mvc = $context;
        require($path);
        $content = ob_get_contents();
        ob_end_clean();
        
        return $content;
    }
    
    
    public function __destruct() {
        
        $this->output = null;    
        
    }
}
