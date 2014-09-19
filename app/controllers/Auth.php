<?php
use kiss\base\Controller   as BaseController;
use models\activerecord    as ActiveModels;
use kiss\base\auth\Hash    as Hash;
use kiss\base\auth\Session as Sess;

//forms
use \models\forms\Join     as JoinForm;
use \models\forms\Login    as LoginForm;
use \models\forms\Password as PasswordForm;

class Auth extends BaseController { 

    /**
     * publc __construct
     * put init code here
    */
    public function init() {
    }
 
    /**
     * publc index Action
     * view @  /views/auth/login.phtml
    */
    public function indexAction() {
        $this->view = 'auth/login';
        $this->loginAction();
    }

    /**
     * publc join Action
     * view @  /views/auth/join.phtml
    */
    public function joinAction() {
        
        $join = new JoinForm('/auth/join');
        $usr = null;
        
        if($this->isPost() && $join->form->validate()) {
         
            $usr = ActiveModels\User::find_by_username_or_email($this->getParam('username'),
                                                                $this->getParam('email'));
            //if no user exists go ahead and create a user.            
            if(empty($usr)){
                
                $hash = Hash::SetHmac(trim($_POST['password']));
                
               $last_id = ActiveModels\User::create(array( 'username' => $this->getParam('username'),
                                                            'password' => md5(time()),
                                                            'email'    => $this->getParam('email'),
                                                            'verified' => 0,
                                                            'hash'     => $hash['hash'],
                                                            'salt'     => $hash['salt'],
                                                            'role'     => 1));
                $join->form->clearSession();
                $join->form->process();        
            
            }
        }
       
        $this->authError = empty($usr) ? false : true;
        
        $this->form = $join->getForm();
        
    }
    
    /**
     * pub login action
     * - create login from
     * - check for post
     * - validate form values
     * - check if user found
     * - if auth rediect else display login form
    */
    public function loginAction() {
        
        $login = new LoginForm;
        
        $this->authError = false;
        
        if($this->isPost() && $login->form->validate()) {
            
            $usr = ActiveModels\User::find_by_username_or_email(trim($_POST['username']),
                                                                trim($_POST['username']));
            if(!empty($usr)){
               
               $auth = Hash::GetHmac(trim($_POST['password']), $usr->salt, $usr->hash);
               
               if( $auth == true ) {
                    Sess::Invoke($usr);
                    $login->form->clearSession();
                    $login->form->process();                    
               }
            }
            
            $this->authError = true;        
        }
                
        $this->form = $login->getForm();
    }

    /**
    * pass action
    * - create form
    * - if post validate form check if the user exits.
    * - send the rest link
    **/
    public function passAction() {
        $passForm = new PasswordForm;
        $this->scope['message'] = false;
        
        if($this->isPost() && $passForm->form->validate()) {   
        
            $usr = ActiveModels\User::find_by_username_or_email(trim($_POST['username']),
                                                                trim($_POST['username']));
            if(!empty($usr)) {
                
               //add your email script here
               //mail($usr->email, "Click this reset link", "from: [site email]");
               
               $this->setFlashMessage("Please check your email for a reset link");
               $passForm->form->clearSession();
               $passForm->form->process();
               
            }
        }
        
        $this->form = $passForm->getForm();
    }
    

    /**
    * pass checkExists
    * - add any front end checks here to save the user
    * from submiting a form.
    * - return json.
    **/
    public function checkExistAction() {
       
        $success = false;
        $res     = null;
        
        if($this->isPost() && $this->isXhr()) {   
        
            $input = $this->getParam('input');
            
            if($input == 'username') 
                $usr = ActiveModels\User::find_by_username($this->getParam('check'));
            
            if($input == 'email')
                $usr = ActiveModels\User::find_by_email($this->getParam('check'));
            
            $res = !empty($usr);
            $success = true;
        }
        
        echo $this->toJson(array("found"=>$res, 'success'=>$success));
       
    }    
    
    
    /**
    * logout action
    * - destroy session.
    * - redirect to the base route
    **/
    public function logoutAction() {
        
        Sess::Destroy();
    
        $this->_redirect("/");
    
    }
}