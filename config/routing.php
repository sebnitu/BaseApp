<?php 
/*
*************************************************

BASEAPP
Application Routing File

Created by Sebastian Nitu
http://www.sebnitu.com

*************************************************
*/

/*-------------------------------------------    
	Get the URL Request
---------------------------------------------*/

if (isset($_GET['url'])) {
	$url = $_GET['url'];
}

/*-------------------------------------------    
	Custom Routing
---------------------------------------------*/

/**
 * Build Custom Routing
 *
 * Store special case routing 
 * in array for later use. 
 */
$routing = array(
	'/admin\/(.*?)\/(.*?)\/(.*)/' => 'admin/\1_\2/\3',
);

/**
 * Set Defaults Routing
 *
 * Set the default controller
 * action and error action 
 */
$default = array(
	'controller' 	=> 'page',
	'action' 			=> 'index',
	'error' 			=> 'error',
);

/*-------------------------------------------*/