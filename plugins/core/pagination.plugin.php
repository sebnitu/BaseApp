<?php

class Pagination {
	
	public $current_page;
	public $total_count;
	public $page_limit;
	public $returned_set;
	
	public $total_pages;
	public $range = array();
		
	function __construct() {
		$this->current_page 	= 1;
		$this->page_limit 		= PAGINATE_LIMIT;
		$this->total_count 		= 0;
		$this->returned_set		= 0;
	}
	
	function offset() {
		return ($this->current_page - 1) * $this->page_limit;
	}
	
	function total_pages() {
		return ceil($this->total_count/$this->page_limit);
	}
	
	function prev_page() {
		if(($this->current_page - 1) >= 1) {
			return $this->current_page - 1;
		} else {
			return false;
		}
	}
	
	function next_page() {
		if(($this->current_page + 1) <= $this->total_pages()) {
			return $this->current_page + 1;
		} else {
			return false;
		}
	}
	
	function get_range() {
		$this->range['min'] = ($this->current_page * $this->page_limit) - ($this->page_limit - 1);
		if(($this->current_page + 1) <= $this->total_pages()) {
			$this->range['max'] = $this->range['min'] + $this->page_limit - 1;
		} else {
			$this->range['max'] = $this->range['min'] + ($this->returned_set - 1);
		}
		return $this->range;
	}
	
}