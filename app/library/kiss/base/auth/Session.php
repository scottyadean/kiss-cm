<?php

namespace kiss\base\auth;

class Session{
    
     public static function Destroy() {
        
        if(isset($_SESSION['role']))
            unset($_SESSION['role']);
        
        if(isset($_SESSION['-u']))
            unset($_SESSION['-u']);
        
        if ( isset( $_COOKIE[session_name()] ) )
            setcookie( session_name(), "", time()-3600, "/" );
            
        $_SESSION = array();
        session_destroy();
        
     }

}