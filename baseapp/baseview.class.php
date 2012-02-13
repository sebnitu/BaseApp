<?php

class BaseView {

	protected $variables = array();
	protected $_controller;
	protected $_action;
	
	function __construct($controller, $action) {
		$this->_controller = $controller;
		$this->_action = $action;		
	}

	function set($name, $value) {
		$this->variables[$name] = $value;
	}
	
	function render($ajax_call) {
		extract($this->variables);
				
		if ($ajax_call == false) {
			if ($custom_layout) { 
				if (file_exists(TEMPLATE_PATH . 'layouts' . DS . $custom_layout . '.php')) {
					include (TEMPLATE_PATH . 'layouts' . DS . $custom_layout . '.php');
				}
			} elseif (file_exists(TEMPLATE_PATH . 'layouts' . DS . 'application.' . $this->_controller .'.'. $this->_action . '.php')) {
				include (TEMPLATE_PATH . 'layouts' . DS . 'application.' . $this->_controller .'.'. $this->_action . '.php');
			} elseif (file_exists(TEMPLATE_PATH . 'layouts' . DS . 'application.' . $this->_controller . '.php')) {
				include (TEMPLATE_PATH . 'layouts' . DS . 'application.' . $this->_controller . '.php');
			} else {
				include (TEMPLATE_PATH . 'layouts' . DS . 'application.php');
			}
		} else {
			$this->yield();
		}
	}
    
	function yield($controller = '', $action = '') {
		extract($this->variables);
		
		if ($controller == '') {
			$controller = $this->_controller;
		}
		
		if ($action == '') {
			$action = $this->_action;
		}
		
		if ($custom_template) { 
			if (file_exists(TEMPLATE_PATH . $controller . DS . $custom_template . '.php')) {
				include (TEMPLATE_PATH . $controller . DS . $custom_template . '.php');
			}
		} elseif (IS_AJAX && file_exists(TEMPLATE_PATH . $controller . DS . $action . '.js.php')) {
			include (TEMPLATE_PATH . $controller . DS . $action . '.js.php');
		} elseif (file_exists(TEMPLATE_PATH . $controller . DS . $action . '.php')) {
			include (TEMPLATE_PATH . $controller . DS . $action . '.php');
		} else {
			global $default;
			include (TEMPLATE_PATH . $default['controller'] . DS . $default['error'] . '.php');
		}
	}

}