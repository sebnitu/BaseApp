<?php

class Sessions {
	
	function __construct() {
		$this->start_session();
	}
	
	function create($key, $data) {
		$_SESSION[$key] = $data;
	}
	
	function delete($key) {
		unset($_SESSION[$key]);
	}
	
	function get_session($key) {
		if(isset($_SESSION[$key])) {
			return $_SESSION[$key];
		} else {
			return false;
		}
	}
	
	function start_session() {
		session_name(APP_NAME);
		session_start();
	}

}