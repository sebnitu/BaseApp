<?php
/*
*************************************************

BASEAPP
Form Validation Class

Created by Sebastian Nitu
http://www.sebnitu.com

*************************************************
*/
/**
 * Class for validating input form fields
 * Includes error reporting
 */

class FormValidation {
	
	protected $_error_list;
	protected $_method;
	protected $_model;
	
	function __construct() {
		$this->reset_error();
		$this->set_method('POST');
	}
	
	/**
	 * Get Value
	 *
	 * Method to get the value of a variable (field)
	 */
	protected function _get_value($field) {
		if ($this->_method == 'POST') {
			if (isset($_POST[$field])) {
				$value = $_POST[$field];
			}
		} elseif ($this->_method == 'GET') {
			if (isset($_POST[$field])) {
				$value = $_GET[$field];
			}
		}
		if (!isset($value)) {
			// global ${$field};
			$value = $field;
		}
		return $value;
	}
	
	/*-------------------------------------------    
		Utility Methods
	---------------------------------------------*/
	
	/**
	 * Set Form Method
	 */
	function set_method($method) {
		if ($method == 'POST' || $method == 'post' || $method == 'Post') {
			$this->_method = 'POST';
		} elseif ($method == 'GET' || $method == 'get' || $method == 'Get') {
			$this->_method = 'GET';
		}
	}
	
	/**
	 * Set Model
	 */
	function set_model($model) {
		$this->_model = $model;
	}
	
	/**
	 * Do Errors Exist
	 */
	function has_error() {
		if (sizeof($this->_error_list) > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Get Error
	 */
	function get_error() {
		return $this->_error_list;
	}
	
	/** 
	 * Add Error
	 */
	function add_error($field, $msg) {
		$value = $this->_get_value($field);
		$this->_error_list[] = array('field' => $field, 'value' => $value, 'msg' => $msg);
	}
	
	/**
	 * Reset Error
	 */
	function reset_error() {
		$this->_error_list = array();
	}
	
	/*-------------------------------------------    
		General Validations
	---------------------------------------------*/
	
	/**
	 * Custom Regular Expression
	 */
	function custom($field, $pattern, $msg) {
		$value = $this->_get_value($field);
		if (preg_match($pattern, $value)) {
			return true;
		} else {
			$this->_error_list[] = array('field' => $field, 'value' => $value, 'msg' => $msg);
			return false;
		}
	}
	
	/**
	 * Is Empty
	 */
	function is_empty($field, $msg) {
		$value = $this->_get_value($field);
		if (trim($value) == '') {
			$this->_error_list[] = array('field' => $field, 'value' => $value, 'msg' => $msg);
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * Is Duplicate
	 */
	function is_duplicate($fields, $msg) {
		if (is_array($fields)) {
			foreach ($fields as $key => $field) {
				$value[$key] = $this->_get_value($field);
				$this->_model->where($field, $value[$key]);
			}
		} else {
			$value = $this->_get_value($fields);
			$this->_model->where($fields, $value);
		}
		$result = $this->_model->search();
		if ($result) {
			$this->_error_list[] = array('field' => $fields, 'value' => $value, 'msg' => $msg);
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * Is Match
	 */
	function is_match($fields, $msg) {
		if (is_array($fields)) {
			foreach ($fields as $key => $field) {
				$value[$key] = $this->_get_value($field);
				$this->_model->where($field, $value[$key]);
			}
		} else {
			$value = $this->_get_value($fields);
			$this->_model->where($fields, $value);
		}
		$result = $this->_model->search();
		if (count($result) == 1) {
			return $result;
		} else {
			$this->_error_list[] = array('field' => $fields, 'value' => $value, 'msg' => $msg);
			return false;
		}
	}

	/*-------------------------------------------    
		String Validations
	---------------------------------------------*/
	
	/**
	 * Is String
	 */
	function is_string($field, $msg) {
		$value = $this->_get_value($field);
		if (!is_string($value)) {
			$this->_error_list[] = array('field' => $field, 'value' => $value, 'msg' => $msg);
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * Is Alphabetic
	 */
	function is_alpha($field, $msg) {
		$value = $this->_get_value($field);
		$pattern = '/^[a-zA-Z]+$/';
		if (preg_match($pattern, $value)) {
			return true;
		} else {
			$this->_error_list[] = array('field' => $field, 'value' => $value, 'msg' => $msg);
			return false;
		}
	}
	
	/**
	 * Is Alphanumeric
	 */
	function is_alnum($field, $msg) {
		$value = $this->_get_value($field);
		$pattern = '/^[A-Za-z0-9]+$/';
		if (preg_match($pattern, $value)) {
			return true;
		} else {
			$this->_error_list[] = array('field' => $field, 'value' => $value, 'msg' => $msg);
			return false;
		}
	}

	/**
	 * Is Alphanumeric and dashes
	 */
	function is_alnum_dash($field, $msg) {
		$value = $this->_get_value($field);
		$pattern = '/^[A-Za-z0-9\-]+$/';
		if (preg_match($pattern, $value)) {
			return true;
		} else {
			$this->_error_list[] = array('field' => $field, 'value' => $value, 'msg' => $msg);
			return false;
		}
	}
	
	/*-------------------------------------------    
		Number Validations
	---------------------------------------------*/
	
	/**
	 * Is Number
	 */
	function is_number($field, $msg) {
		$value = $this->_get_value($field);
		if (!is_numeric($value)) {
			$this->_error_list[] = array('field' => $field, 'value' => $value, 'msg' => $msg);
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * Is Integer
	 */
	function is_integer($field, $msg) {
		$value = $this->_get_value($field);
		if (!is_integer($value)) {
			$this->_error_list[] = array('field' => $field, 'value' => $value, 'msg' => $msg);
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * Is Float
	 */	
	function is_float($field, $msg) {
		$value = $this->_get_value($field);
		if (!is_float($value)) {
			$this->_error_list[] = array('field' => $field, 'value' => $value, 'msg' => $msg);
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * Is within a Range
	 */
	function is_range($field, $msg, $min, $max) {
		$value = $this->_get_value($field);
		if(!is_numeric($value) || $value < $min || $value > $max) {
			$this->_error_list[] = array('field' => $field, 'value' => $value, 'msg' => $msg);
			return false;
		} else {
			return true;
		}
	}
	
	/*-------------------------------------------    
		Specific Data Validations
	---------------------------------------------*/
	
	/**
	 * Is Email Address
	 */
	function is_email($field, $msg) {
		$value = $this->_get_value($field);
		$pattern = '/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/';
		if (preg_match($pattern, $value)) {
			return true;
		} else {
			$this->_error_list[] = array('field' => $field, 'value' => $value, 'msg' => $msg);
			return false;
		}
	}
	
	/**
	 * Is Valid URL
	 */
	function is_url($field, $msg) {
		$value = $this->_get_value($field);
		$pattern = '/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i';
		if (preg_match($pattern, $value)) {
			return true;
		} else {
			$this->_error_list[] = array('field' => $field, 'value' => $value, 'msg' => $msg);
			return false;
		}		
	}
	
	/*-------------------------------------------    
		Utility Functions
	---------------------------------------------*/
	
	/** 
	 * Random String Generator
	 */
	function rand_string($minlength, $maxlength = false, $useupper = true, $usespecial = true, $usenumbers = true) {
		
		if ($maxlength == false) $maxlength = $minlength;
		
		$key = '';
		$charset = "abcdefghijklmnopqrstuvwxyz";
		if ($useupper) $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		if ($usenumbers) $charset .= "0123456789";
		if ($usespecial) $charset .= "~@#$%^*()_+-={}|]["; // Note: using all special characters this reads: "~!@#$%^&*()_+`-={}|\\]?[\":;'><,./";
		if ($minlength > $maxlength) $length = mt_rand ($maxlength, $minlength);
		else $length = mt_rand ($minlength, $maxlength);
		for ($i=0; $i<$length; $i++) $key .= $charset[(mt_rand(0,(strlen($charset)-1)))];
		return $key;
	
	}

}