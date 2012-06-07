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
	Initial Functions
---------------------------------------------*/

/**
 * Set Error Reporting
 */
function set_reporting() {

	if(DEVELOPMENT_ENVIRONMENT == true) {
		error_reporting(E_ALL);
		ini_set('display_errors', 'On');
	} else {
		error_reporting(E_ALL);
		ini_set('display_errors', 'Off');
		ini_set('log_errors', 'On');
		ini_set('error_log', BASE_PATH . 'tmp/logs/error.log');
	}

}

/**
 * Unregister Globals
 */
function unregister_globals() {

	if(ini_get('register_globals')) {
		
		$array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
		
		foreach($array as $value) {
			foreach($GLOBALS[$value] as $key => $var){
				if($var === $GLOBALS[$key]) {
					unset($GLOBALS[$key]);
				}
			}
		}
	}

}

/**
 * Custom URL Routing Processing
 */
function route($url) {
	
	global $routing;
	
	// Check all registered routes
	foreach ( $routing as $pattern => $result ) {
		// check url for the pattern
		if ( preg_match( $pattern, $url ) ) {
			// If matched, change result
			return preg_replace( $pattern, $result, $url );
		}
	}
	
	// return rebuilt url
	return ($url);
	
}

/*-------------------------------------------    
	Call Functions
---------------------------------------------*/

/**   
 * Main Call Function
 */
function call_hook() {
	
	global $url;
	global $default;
	
	$query_string = array();
	
	// Set action and controller
	if(!isset($url)) {
		// Defaults if URL is empty
		$controller = $default['controller'];
		$action = $default['action'];
	} else {
		
		// Get special url routing
		$url = route($url);
		
		// Create array from url var
		$url_array = array();
		$url_array = explode('/', $url);
	
		// Assign arrays to vars
		$controller = $url_array[0];
		array_shift($url_array);	
		
		// load default action if no action is provided
		if(isset($url_array[0]) && ($url_array[0] != '')) {
			$action = $url_array[0];
			array_shift($url_array);
		} else {
			$action = $default['action'];
		}
		
		// Save the remaining array
		$query_string = $url_array;
		
	}

	// Build controller name
	$controller_name = ucfirst($controller) . 'Controller';
	
	// Check if class exists
	if(!class_exists($controller_name)) {
		// Check if we are set to development mode
		if(DEVELOPMENT_ENVIRONMENT == true) {
			// Since we are in development mode, let's display a helpful view
			// so that you know the controller you're calling doesn't exist
			echo 'The conroller youre calling does not exist';
			return;
		} else {
			// Since we are in production mode, let's instead display the default
			// controller and error action. This is essentially a 404 error.
			$controller_name = ucfirst($default['controller']) . 'Controller';
			$action = $default['error'];
		}
	}
	
	// Check if method exists
	if((int)method_exists($controller_name, $action)) {
	
		// Instantiate Application Object
		$dispatch = new $controller_name($controller, $action, $query_string);
		dispatch($dispatch, $action, $query_string);

	} else {
		
		// Instantiate Default Application Object
		$dispatch = new $controller_name($default['controller'], $default['error'], $query_string);
		dispatch($dispatch, $default['error'], $query_string);
				
	}
}

/**   
 * Initial Dispatch Method Calls
 */
function dispatch($controller, $action, $query) {

	call_user_func_array(array($controller, 'before_filter'), $query);
	
	if((int)method_exists($controller, 'before_action')) {
		call_user_func_array(array($controller, 'before_action'), $query);
	}
	
	call_user_func_array(array($controller, $action), $query);
	
	if((int)method_exists($controller, 'after_action')) {
		call_user_func_array(array($controller, 'after_action'), $query);
	}
	
	call_user_func_array(array($controller, 'after_filter'), $query);
}

/**   
 * Secondary Call Function
 */
function perform_action($controller, $action, $query_string = null, $render = false) {
	
	$controller_name = ucfirst($controller).'Controller';
	$dispatch = new $controller_name($controller, $action);
	$dispatch->render = $render;
	return call_user_func_array(array($dispatch, $action), $query_string);

}

/**   
 * Redirect Function
 */
function redirect_action($controller = null, $action = null, $query_string = null, $return_url = null) {
		
	global $default;
			
	if($controller == null) $controller = $default['controller'];
	if($action == null) $action = $default['action'];
	
	if($query_string != null) $_SESSION['flash'] = $query_string;
	
	header('Location: '. BASE_URL . $controller . '/' . $action . '/');
	exit;
}

/** 
 * Class Auto Load Function
 *
 * This is where all classes are loaded automatically by PHP
 */
function __autoload($class_name) {
		
	$class_name = strtolower($class_name);
		
	$paths = array(
		'base' => BASE_PATH . 'baseapp/' . $class_name . '.class.php',
		'core' => BASE_PATH . 'baseapp/core.' . $class_name . '.class.php',
		'controller' => BASE_PATH . 'app/controllers/' . str_replace('controller', '_controller', $class_name) . '.php',
		'model' => BASE_PATH . 'app/models/' . $class_name . '.php',
		'plugin-custom' => BASE_PATH . 'plugins/custom/' . $class_name . '.plugin.php',
		'plugin-core' => BASE_PATH . 'plugins/core/' . $class_name . '.plugin.php',
	);
		
	foreach($paths as $path) {
		if (file_exists($path)) {
			require_once($path);
		}
	}

}

/*-------------------------------------------    
	Application Plugins Loader
---------------------------------------------*/

/**
 * This function loads all the plugins included 
 * in this application. It will look in the plugins
 * directory for files with the extension: .plugins.php
 */

// build array for all directories to include files from
// $include_directory_files = array(
// 	'plugins' => BASE_PATH . 'plugins',
// );
// 
// function plugins($dirs) {
// 	
// 	foreach ($dirs as $dir) {
// 	
// 	    // create a handler for the directory
// 	    $handler = opendir($dir);
// 
// 	    // keep going until all files in directory have been read
// 	    while ($file = readdir($handler)) {
// 			
// 				// check that the extension is correct
// 				if(preg_match('/\.plugin\.php$/i', $file)) {
// 					// inlcude the file
// 					require_once( $dir . '/' .$file );
// 				}
// 	    }
// 	    // tidy up: close the handler
// 	    closedir($handler);
// 	}
// 
// }

/*-------------------------------------------    
	Boot the Application
---------------------------------------------*/

/** 
 * Create instance of Inflection Class
 */
$inflect = new Inflections();

/** 
 * Initiate Functions
 */
set_reporting();
unregister_globals();
	
/** 
 * Process Request
 */
call_hook();

/*-------------------------------------------*/