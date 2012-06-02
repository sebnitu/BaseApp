<?php
/**
 * Debug Class
 *
 * Class for clean output for debugging variables or arrays
 */
class Debug {

	function expose($array) {
		
		echo '<div class="debug">';
		echo '<pre>';
		print_r($array);
		echo '</pre>';
		echo '</div>';
		
		echo '<style type="text/css">
			.debug {
				background: #efefef;
				padding: 10px 20px;
				border: 1px solid red;
			}
			.debug .btns {
				margin-bottom: 10px;
			}
		</style>';
				
	}

}