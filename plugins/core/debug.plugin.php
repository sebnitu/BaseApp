<?php
/**
 * Debug Class
 *
 * Class for debugging your application
 */
class Debug {

	function expose($array) {
		
		echo '<div class="debug">';
		// echo '<div class="btns"><a href="#" class="close">Close debug box</a></div>';
		echo '<pre>';
		print_r($array);
		echo '</pre>';
		echo '</div>';
		
		echo '<style type="text/css">
			.debug {
				background: #fff;
				padding: 30px;
			}
			.debug .btns {
				margin-bottom: 10px;
			}
		</style>';
		
		echo '<script type="text/javascript">
			$(document).ready(function() {
				$(".debug .btns .close").click(function() {
					$(".debug").fadeOut().remove();
					return false;
				});
			}
		</script>';
				
	}

}