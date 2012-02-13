<?php
/*
*************************************************

BASEAPP
Application Configuration File

Created by Sebastian Nitu
http://www.sebnitu.com

*************************************************
*/

/*-------------------------------------------    
	Error Reporting Toggle
---------------------------------------------*/

/** Change to 'true' or 'false' **/
define('DEVELOPMENT_ENVIRONMENT', true);

/*-------------------------------------------    
	MySQL Settings
---------------------------------------------*/

/** MySQL database username **/
define('DB_USER', '');

/** MySQL database password **/
define('DB_PASS', '');

/** MySQL hostname **/
define('DB_HOST', '');

/** MySQL database name **/
define('DB_NAME', '');

/*-------------------------------------------    
	Application Information
---------------------------------------------*/

/** Application Name **/
define('APP_NAME', '');

/** Default Pagination Value **/
define('PAGINATE_LIMIT', '');

/*-------------------------------------------    
	Application Directory
---------------------------------------------*/

/**
 * Set Application Directory
 *
 * Default == ""
 * If application is not in the root directory
 * write it's directory here (e.g. 'sub/app_dir')
 * Leave off beginning and trailing slashes
 */
$app_dir = '';

/** 
 * Set Application Port
 *
 * Add port if applicable 
 * Leave blank if otherwise
 */
$port = '';

//$_SERVER['SERVER_PORT']

/*-------------------------------------------    
	Application Path and URL Builder
---------------------------------------------*/

/**
 * Directory Separator
 * ( '\' for Windows, '/' for Unix )
 */
define('DS', DIRECTORY_SEPARATOR);

/** 
 * Base URL
 * Output: /home/user/public_html/
 */
if ($app_dir) {
	$base_path = str_replace( DS . $app_dir . DS . 'config', DS . $app_dir . DS, dirname(__FILE__));
} else {
	$base_path = str_replace( DS . 'config', DS, dirname(__FILE__));
}
define('BASE_PATH', $base_path);

/** 
 * Base URL
 * Output: http://yourdomain.com/
 */
 if ($app_dir) {
	$base_url = 'http://' . $_SERVER['SERVER_NAME'] . $port . '/' .$app_dir. '/';
} else {
	$base_url = 'http://' . $_SERVER['SERVER_NAME'] . $port . '/';
}
define('BASE_URL', $base_url);

/**
 * Template Path
 * Output: /home/user/public_html/app/views/
 */
$template_path = BASE_PATH . 'app' . DS . 'views' . DS;
define('TEMPLATE_PATH', $template_path);

/*-------------------------------------------    
	Define Ajax Requests Checker
---------------------------------------------*/

define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

/*-------------------------------------------    
	Router & Inflection Settings
---------------------------------------------*/

/** Load custom irregular words **/
require_once( BASE_PATH . 'config' . DS . 'inflections.php' );

/** Load routing file and set defaults **/
require_once( BASE_PATH . 'config' . DS . 'routing.php' );

/*-------------------------------------------*/