<?php

//Propel is a awesome full featured orm that is suitable for mid-sized to large projects
//Propel set up is a bit more difficult check out the docs at (http://propelorm.org/)

// setup the autoloading
/*   
   $serviceContainer = \Propel\Runtime\Propel::getServiceContainer();
   $serviceContainer->setAdapterClass('kiss', 'mysql');
   $dbcfg = new \Propel\Runtime\Connection\ConnectionManagerSingle();
   $dbcfg->setConfiguration(array (
     'dsn'      => 'mysql:host=localhost;dbname=propel',
     'user'     => 'root',
     'password' => 'password',
   ));
   
   $serviceContainer->setConnectionManager('kiss', $dbcfg);  
*/ 
       
//ActiveRecord is a simple orm that is great for smaller projects.
//To start using phpactiveRecord create your db, update your db creds below and add your models to the /models dir.
// -- To Use ActiveRecord in your controllers:
//      $user = models\User::find_by_username('mr user');
//      var_dump($user->username);

$dbcfg = \ActiveRecord\Config::instance();
$dbcfg->set_model_directory($path.'/models/activerecord');
$dbcfg->set_connections( array('development' =>'mysql://root:!bigworms!@localhost/kiss') );
