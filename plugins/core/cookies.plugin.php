<?php

class Cookies {
	
	function create($key, $data, $time = 3600, $path = '/') {
		setcookie($key, $data, time() + $time, $path);
	}
	
	function delete($key, $path = '/') {
		setcookie($key, '', time() - 3600, $path);
	}

	function get_cookie($key) {
				
		$array = str_replace(']', '', $key);
		$array = explode('[', $array);
		
		$cookie = $_COOKIE;
		foreach ($array as $value) {
			if(isset($cookie[$value])) {
				$cookie = $cookie[$value];
			}
		}
			
		return (is_array($cookie)) ? false : $cookie;
	}
	
}