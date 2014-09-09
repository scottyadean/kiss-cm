<?php

class Sync extends base\Controller { 

    public function indexAction() {
        $this->disable_layout = true;
        $this->disable_view = true;
        print $this->_asJson($this->params);
    }
    
   
 }
