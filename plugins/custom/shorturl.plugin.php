<?php
/**
 * ShortURL Redirect
 */
class ShortURL {
	
	protected $url; 
	protected $codeset = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	protected $base;
	
	function __construct() {
		global $url;
		$this->url = $url;
		
		// Count the characters
		$this->base = strlen($this->codeset);
	}
	
	/**
	 * The Process Method
	 */
	function process($user) {
		if (preg_match('/^[a-zA-Z0-9\-]{1,}$/', $this->url)) {
						
			$bookmark_id = $this->shorturl_algorithm_undo($this->url); 
			
			$bookmark = perform_action('bookmarks', 'get_bookmark_by_id', $bookmark_id);
			
			if ($bookmark) {
				if ($user['id'] != $bookmark[0]['Bookmark']['account_id']) {
					$visited = perform_action('bookmarks', 'save_bookmark_visit', array($bookmark[0]['Bookmark']['id'], $bookmark[0]['Bookmark']['visits']));
					$visitor_data = perform_action('visitors', 'save_visitor', $bookmark[0]['Bookmark']['id']);
				} else {
					$visited = true;
					$visitor_data = true;					
				}
				if ($visited && $visitor_data) {
					header('Location: ' . $bookmark[0]['Bookmark']['long_url']);
				}
			}
		}
	}
	
	/**
	 * ShortURL Algorithms
	 */
	public function shorturl_algorithm($shorturl_id) {

		// Save the shorturl_id
		$id = $shorturl_id;

		// create converted variable
		$converted = '';

		// Build the unique shorturl
		while ($id > 0) {
		  $converted = substr($this->codeset, ($id % $this->base), 1) . $converted;
		  $id = floor($id/$this->base);
		}

		return $converted;
	}
	
	public function shorturl_algorithm_undo($shorturl_key) {

		$converted = $shorturl_key;
		$c = 0;
		for ($i = strlen($converted); $i; $i--) {
		  $c += strpos($this->codeset, substr($converted, (-1 * ( $i - strlen($converted) )),1)) 
		        * pow($this->base, $i-1);
		}
		
		return $c;
	}
		
}