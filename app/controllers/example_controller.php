<?php 

class ExampleController extends AppController {
	
	/**
	 * Before & After Filters
	 */
	function before_action() { }
	
	function after_action() { }
	
	/**
	 * Template Methods
	 */
	function example() {
		
		$our_title = 'Test';
		
		$this->set('title', $our_title);
		
	}
	
}