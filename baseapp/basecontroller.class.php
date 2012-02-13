<?php 

class BaseController {

	protected $_controller;
	protected $_action;
	protected $_view;
		
	protected $_query;
	protected $_request;
		
	public $ajax_call;
	public $custom_layout;
	public $custom_template;
	public $render;
						
	function __construct($controller, $action, $query_string = false) {
		
		global $inflect;
		global $url;
		
		$this->_request = $url;
		
		// Set controller and action
		$this->_controller = ucfirst($controller);
		$this->_action = $action;
		
		// The Model Object
		$model = ucfirst($inflect->singularize($controller));
		if(class_exists($model)) {
			$this->$model = new $model;
		}
		
		// set query
		$this->_query = $query_string;
						
		// Load without layout : default = false
		$this->ajax_call = (IS_AJAX) ? true : false;
		
		// Load custom layout : default = false
		$this->custom_layout = false;
		// Load custom template : default = false
		$this->custom_template = false;
		
		// Render a template : default = true
		$this->render = true;
		
		// The View Object
		$this->_view = new BaseView($controller, $action);
		
		// Load Plugin Objects
		include_once (BASE_PATH . 'config/plugins.php');
		
	}
	
	function set($name, $value) {
		$this->_view->set($name, $value);
	}
	
	function __destruct() {
		if($this->render) {
		  
		  // Call builder methods
		  $this->html->assets();
		  $this->html->menu();
		  
		  // Set global template variables
			$this->set('html', $this->html);
			$this->set('custom_layout', $this->custom_layout);
			$this->set('custom_template', $this->custom_template);
			
			// Render template
			$this->_view->render($this->ajax_call);
		}
	}

}