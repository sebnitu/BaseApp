<?php
/*
*************************************************

BASEAPP
Application Boot

Created by Sebastian Nitu
http://www.sebnitu.com

*************************************************
*/

/*-------------------------------------------    
	Define Custom Objects
---------------------------------------------*/

/**
 * Plugins Array
 *
 * Loads both inner-arrays, custom and core
 * @syntax = 'ClassName' => 'Alias',
 */
$plugins = array(
  
  
  'custom' => array(
      'ShortURL' => 'shorturl',
			'Favicon' => 'favicon',
    ),
  
  'core' => array(
      'Sessions' => 'sessions',
      'Cookies' => 'cookies',
      'HTML' => 'html',
			'Debug' => 'debug',
      'FormValidation' => 'val',
      'Pagination' => 'pagin',
      'PHPMailer' => 'mail',
      'LogAction' => 'log',
    ),

);

/*-------------------------------------------    
	Load Custom Objects
---------------------------------------------*/

foreach ($plugins['custom'] as $class => $alias) {
  $this->$alias = new $class;
}

/*-------------------------------------------    
	Load Core Objects
---------------------------------------------*/

foreach ($plugins['core'] as $class => $alias) {
  $this->$alias = new $class;
}

/*-------------------------------------------    
	Fin
---------------------------------------------*/