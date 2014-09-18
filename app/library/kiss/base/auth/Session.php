<?php
namespace kiss\base\auth;

class Session{
     
     public static function Invoke($usr) {
        
        $_SESSION['role'] = $usr->role;
        $_SESSION['-u']   = array('id'=>$usr->id,
                                  'username'=>$usr->username,
                                  'email'=>$usr->email,
                                  'role'=>$usr->role);        
     }
     
     
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