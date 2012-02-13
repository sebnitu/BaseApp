<?php 

class BaseModel extends BaseQuery {

	protected $_model;
	
	function __construct() {
		
		global $inflect;
		
		$this->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		$this->_limit = PAGINATE_LIMIT;
		$this->_model = get_class($this);
		$this->_table = strtolower($inflect->pluralize($this->_model));
		
		/**
		 * Set $this->abstract = true to keep the system from
		 * looking for a corresponding table. This will work well
		 * for instances where we don't require a table.
		 */
		if (!isset($this->abstract)) {
			$this->_describe();
		}
		
	}

}