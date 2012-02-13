<?php
/**
 * Application Controller > Base Controller
 *
 * Methods and variables added to this controller apply 
 * to all controllers across the entire application.
 */ 
class AppController extends BaseController {
	
	/**
	 * Before Filter
	 * 
	 * This method is called before every 
	 * Controller / Method combination
	 */
	function before_filter() { }
	
	/**
	 * After Filter
	 * 
	 * This method is called after every 
	 * Controller / Method combination
	 */	
	function after_filter() { }
	
	/**
	 * Application wide methods
	 */
	
	/**
	 * Global Builders : Assets : Menus
	 */
	function global_assets() {
    $this->html->assets['admin'] = array(
      'css' => array(),
      'js' => array(),
    );
    $this->html->assets['public'] = array(
      'css' => array(),
      'js' => array(),
    );
	  $this->html->assets['shared'] = array(
	    'css' => array(),
	    'js' => array(),
	  );
	}
	 
	function global_menus() {
		
		// Social Menu
		/* EXAMPLE
		$this->html->menu['pages'] = array(
			'settings' => array(
				'name' => 'Sitemap',
				'type' => 'ul',
			),
			'items' => array(
				'tools' => array(
					'path' 	=> 'page/tools/',
					'title' => '',
					'text' => 'Tools',
				),
				'about' => array(
					'path' 	=> 'page/about/',
					'title' => '',
					'text' => 'About',
				),
				'contact' => array(
					'path' 	=> 'page/contact/',
					'title' => '',
					'text' => 'Contact',
				),
			)
		);
		*/
		
	}
	
}