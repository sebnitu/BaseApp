<?php
/**
 * Log Action
 *
 * This class creates log files and handles all
 * interactions with submitting entries, returning
 * file contents and clearing a log.
 */
class LogAction {
	
	public $filename;
	public $action;
	public $message;
	
	protected $delimiter = ' | ';
	protected $directory;
	
	function __construct() {
		$this->directory = BASE_PATH . 'tmp' . DS . 'logs' . DS;
	}
	
	function log_entry() {
				
		if($handle = fopen($this->directory . $this->filename, 'a')) {
						
			$content = strftime('%Y-%m-%d %H:%M:%S');
			$content .= $this->delimiter;
			$content .= $this->action .': '. $this->message;
			$content .= "\n";
					
			fwrite($handle, $content);
			
			fclose($handle);
		} else {
			echo 'Log file "'.$filename.'" could not be opened/created.';
		}
		
	}
	
	function log_output() {
		
		$fn = $this->directory . $this->filename;
		
		if(file_exists($fn) && is_readable($fn) && $handle = fopen($fn, 'r')) { // r = read				
			$content = array();
			while(!feof($handle)) {
				$entry = fgets($handle);
				if(trim($entry) != '') {
					$content[] = $entry;
				}
			}

			fclose($handle);
		}	else {
			echo 'Log file "'.$filename.'" could not be opened/created.';
		}
		
		return $content;	
	}
	
	function log_clear($content = 'Log has been cleared') {

		$content = strftime('%Y-%m-%d %H:%M:%S');
		$content .= $this->delimiter;
		$content .= $this->action .': '. $this->message;
		$content .= "\n";
		
		if($size = file_put_contents($this->directory . $this->filename, $content)) {
		}	else {
			echo 'Log file "'.$filename.'" could not be opened/created.';
		}
		
	}
	
}