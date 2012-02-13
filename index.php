<?php
/*
*************************************************

BASEAPP
Application Front

Created by Sebastian Nitu
http://www.sebnitu.com

*************************************************
*/

/**
 * This file is the gateway to our application
 *
 * It loads all the configurations and settings 
 * to make sure they are available throughout 
 * the app. Every page or process will be 
 * rendered by going through this file.
 */
	
/*-------------------------------------------
	Load Our Settings
---------------------------------------------*/

	require_once('config/config.php');

/*-------------------------------------------
	Boot the Application
---------------------------------------------*/

	require_once('config/boot.php');

/*-------------------------------------------*/