<?php
/**
* REST Template html
**
* @category  API
* @package   lib/Template.php
* @output    A formatted a HTTP response replace html elements with parsed values.
* @author    Scott Dean <scott.dean@marketingevolution.com>
* @copyright 2014 Good Carrot History: 09/05/2014 - Created
* @license   <http://www.marketingevolution.com> Closed
* @version   SVN: <svn_id>
* @tutorial
* 
*/
namespace kiss;

class Template {
    
    //template file name to render
    public $template;
    
    //updated output from the template parse.
    public $output;
    
    //default template extention
    public $ext =".phtml" ;
    
    //default views path    
    public $viewPath = "/views/";

    //default layout path
    public $layoutPath = "/layouts/";
    
    //default controller path
    public $controllerPath = "/controllers/";
    
    //location of the template files
    public $templatePath = APP_PATH;    
    
    /**
    * pub parse
    * @param $context <array> file controller or tags to update in the file.
    * @param $template <string> file name with in the path to render.
    * @return <string html, json, xml>
    **/
    public function parse($context=array(), $template="index") {
        
        if( isset($context['controller']) ) {     
            $this->_parseController($context);
        
        }else{
            
            $this->template = $this->templatePath.$this->viewPath.$template.$this->ext;
            $this->output = is_file($this->template) ? file_get_contents($this->template) : 'Error: Template file '.$this->template.' not found!';
            $this->parseTags($context);
        
        }
        
        return $this;
    }
        
    /**
    * pub display
    * @return <string html, json, xml> return the output
    **/
	public function display() {
	
	    return $this->output;
	}
	
    /**
    * pub getLayout
    * @param $context <string> the route context info.
    * @return <string uri>  path to the the template file
    **/    
    public function getLayout($context) {
            
        $layout = isset($context['layout']) ? $context['layout'] : "_index";
        
        if(is_file($this->templatePath.$this->layoutPath.$layout.$this->ext)){
            return $this->templatePath.$this->layoutPath.$layout.$this->ext;
        }
        
        return false;
    }
	
    /**
    * Replace tags with ruby style args in parsed template
    * @param $tags <array> array of tags to parse key => value
    * @return<void>
    **/
	public function parseTags($tags) {
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
        
        $controller->content = $this->file($this->templatePath.$this->viewPath.$context['view'].$this->ext, $controller->getMvc());
        
        if(isset($controller->disable_layout) && !empty($controller->disable_layout)) {
            return  $controller->content;
        }
        
        return $this->file($this->getLayout($context), $controller->getMvc());
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
        require_once($path);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
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
        
        include(APP_PATH."/views".$template);
        $ret = ob_get_contents();
        ob_end_clean();
        return $ret;
    }
    

}
