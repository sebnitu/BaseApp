<?php
/*
*************************************************

BASEAPP
HTML Class

Created by Sebastian Nitu
http://www.sebnitu.com

*************************************************
*/
/**
 * Class for handling common functions in the
 * templating / views layer
 */

class HTML {

/*-------------------------------------------    
	Variables
---------------------------------------------*/

public $menu = array();
public $assets = array();

/*-------------------------------------------    
	Builder Methods
---------------------------------------------*/

/**
 * Assets Builder
 *
 * Builds the global assets that can then be
 * called in your templates via: $html->assets['group']['type'];
 *
 * @param $array
 *    Array of a defined asset groups and types
 */
function assets() {
      
  $array = $this->assets;
  
  foreach ($array as $group => $assets) {
    
    /**
     * Build Stylesheet Declarations
     */
    if (isset($assets['css'])) {
      foreach ($assets['css'] as $key => $stylesheet) {
        $path = $this->appinfo('css') . '/' . $group . '/' . $stylesheet;
        $output = '<link rel="stylesheet" type="text/css" media="all" href="' . $path . '">';
        $this->assets[$group]['css'][$key] = $output;
      }
      $this->assets[$group]['css'] = implode("\n", $this->assets[$group]['css']) . "\n";
    }
    
    /**
     * Build JavaScript Declarations
     */
    if (isset($assets['js'])) {
      foreach ($assets['js'] as $key => $script) {
       $path = $this->appinfo('js') . '/' . $group . '/' . $script;
       $output = '<script type="text/javascript" src="' . $path . '"></script>';
       $this->assets[$group]['js'][$key] = $output;
      }
      $this->assets[$group]['js'] = implode("\n", $this->assets[$group]['js']) . "\n";
    }
    
  }
}

/**
 * Menu Builder
 *
 * Builds the global menus that can then be
 * called in your templates via: $this->menu['MENU_NAME'];
 *
 * @param $array
 *   Array of a defined global menu
 */
function menu() {
	
	global $url;
	
	$array = $this->menu;
			
	foreach ($array as $menu_key => $menu) {
		
		$output = '';
		
		foreach ($menu['items'] as $item_key => $item) {
			
			// Check if path has http or https in it
			$http_link = strpos($item['path'], 'http://');
			$https_link = strpos($item['path'], 'https://');
		
			if ($http_link === false && $https_link === false) {
				$item['url'] = BASE_URL . $item['path'];
			} else {
				$item['url'] = $item['path'];
			}
			
			// Set item classes
			$item_classes = 'menu-item menu-item-' . $item_key;
			
			// Check if is on current page				
			if ($item['path'] == $url) {
				$item_classes .= ' active';
			}
							
			$link_item = "<a href='{$item['url']}' title='{$item['title']}'>{$item['text']}</a>";
			$link_item = "<li class=\"{$item_classes}\">{$link_item}</li>\n";
			$output .= $link_item;
		
		}
	
		$menu_classes = 'menu menu-' . $menu_key;
	
		if ($menu['settings']['type'] == 'ol') {
			$output = "<ol class=\"{$menu_classes}\">\n{$output}</ol>\n";
		} else {
			$output = "<ul class=\"{$menu_classes}\">\n{$output}</ul>\n";
		}
			
		$this->menu[$menu_key] = $output;
	
	}
}

/*-------------------------------------------    
  Utility Methods
---------------------------------------------*/

/** 
 * Directory Builder
 */
function appinfo($grab) {

	$locations = array(
		'url' => BASE_URL,
		'img' => BASE_URL . 'public/img',
		'css' => BASE_URL . 'public/css',
		'js' => BASE_URL . 'public/js',
	
		'name' => APP_NAME,
	);

	return $locations[$grab];
}

/**
 * String to Slug (URL Friendly)
 */
function to_slug($string, $space = '-') {
	$string = preg_replace('/[^a-zA-Z0-9 -]/', '', $string);
	$string = trim(strtolower($string));
	$string = str_replace(' ', $space, $string);
	$string = str_replace('--', '-', $string);
	return $string;
}

/*-------------------------------------------    
  Other Methods
---------------------------------------------*/

/** 
 * Link Builder
 */
function link_to($controller = NULL, $action = NULL, $text = NULL, $title = NULL) {
  
	global $url;
	global $default;
	
	if(!isset($url) && $controller == 'home') {
		$home = true;
	} else {
		$home = false;
	}

	// Set defaults if no arguments were provided
	if($controller == NULL) $controller = $default['controller'];
	if($action == NULL) $action = '';
	if($text == NULL && $action == '') {
		$text = ucwords($controller);
	} elseif($text == NULL) {
		$text = ucwords($action);
	}

	// Compare URL with Link
	$urls = array();
	$urls = explode('/', $url, 2);
	$urls = str_replace('/', '', $urls);
	$link = array($controller,$action);

	// Check if they match
	if ($urls == $link || $home == true) { $active = ' class="active" '; } else { $active = ''; }
	
	if (isset($title)) {
		$title = 'title="' . $title . '"';
	} else {
		$title = '';
	}
	
	// Build the link
	$link = BASE_URL . $controller . '/' . $action;
	
	// Print the link
	if($controller == 'home') {
		echo '<a '. $active .' href="'. BASE_URL .'" '.$title.'>'. ucwords($controller) .'</a>';
	} else {
		echo '<a '. $active .' href="'. $link .'/" '.$title.'>'. $text .'</a>';
	}
	
}

/** 
 * Is Home Function
 */
function is_home() {

	global $url;
	global $default;

	$home = '';

	$current_url = rtrim($url, '/\\');

	$default_url = $default['controller'] . '/' . $default['action'];

	if ( (empty($current_url)) ||
		($current_url == $default_url) ||
		($current_url == $default['controller'])) $home = true;

	return ($home) ? true : false;
}

/*-------------------------------------------    
  Data Output Methods
---------------------------------------------*/

/**
 * A truncate method for returning shortened data
 */
function truncate_by_words($string, $wordsreturned) {

	$retval = $string;
	$array = explode(" ", $string);
	
	if (count($array)<=$wordsreturned) {
		// Already short enough, return the whole thing
		$retval = $string;
	} else {
		// Need to chop off some words
		array_splice($array, $wordsreturned);
		$retval = implode(" ", $array)." ...";
	}
	return $retval;
}

/**
 * Text output with HTML formatting
 */
function text_to_html($data) {
	
	// Strip slashes
	$html = stripslashes($data);
	
	// Take any whitespace or new lines 
	// off the end of the string
	$html = rtrim($html);
	
	// The order these symbols are
	// replaced is very important
	$symbols = array(
		'&' 	=> '&#38;', 		# ampersand
		'---' => '&#8212;', 	# em dash
		'--' 	=> '&#8211;', 	# en dash
		'...' => '&#8230;',		# horizontal ellipsis
		'<' 	=> '&#60;', 		# less than sign
		'>' 	=> '&#62;', 		# greater than sign
		'<<' 	=> '&#171;', 		# left double angle quotes
		'>>' 	=> '&raquo;', 	# right double angle quotes
		"\n"	=> '</p><p>',		# paragraph block
		'<p></p>' => '<br>',	# line break
	);
	
	foreach ($symbols as $symbol => $code) {
		$html = str_replace($symbol, $code, $html);
	}
	
	// Replace single ticks with single quotation mark
	// if it's between a letter and/or number
	$regex = array (
		'/[a-zA-Z0-9]+[\']+[a-zA-Z0-9]/' => '&#8217;', # right single quotation mark
	);
			
	foreach ($regex as $reg => $code) {
		
		// While there is a match
		while(preg_match($reg, $html, $match)) {
			
			// Replace symbol with the code
			$new_char = str_replace('\'', $code, $match);
			
			// Replace new symbol with old match
			$html = str_replace($match, $new_char, $html);
		
		}
	}
	
	// Open the string with a paragraph tag
	$html = '<p>' . $html;
	
	// Close the string with a paragraph tag
	$html .= '</p>';
			
	return $html;
}

/**
 * Simple Text Output
 */
function t($data) {
	
	$data = stripslashes($data);
	$data = htmlentities($data);
	
	return $data;
	
}

} # End of HTML Class