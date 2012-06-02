<?php 
/**
 * The Great BaseQuery Class
 * This class handles all database interactions
 */
class BaseQuery {

protected $_dbHandle;
protected $_result;
protected $_query;
protected $_table;
protected $_lastQuery;

protected $_describe = array();
protected $_describe_foreign = array();

protected $_orderBy;
protected $_order;
protected $_extraConditions;

protected $_hO;
protected $_hM;
protected $_hMABTM;

protected $_page;
protected $_limit;
protected $_count;
protected $_range = array();

/*-------------------------------------------    
	General DB Methods
---------------------------------------------*/

/**
 * DB Connection
 */	
function connect($address, $account, $pwd, $name) {
	$this->_dbHandle = @mysql_connect($address, $account, $pwd);
	
	if ($this->_dbHandle != 0) {
		if (mysql_select_db($name, $this->_dbHandle)) {
			return 1;
		} else {
			return 0;
		}
	} else {
		return 0;
	}
}

/** 
 * DB Close Connection
 */
function disconnect() {
	if (@mysql_close($this->_dbHandle) != 0) {
		return 1;
	}  else {
		return 0;
	}
}

/** 
 * Custom SQL Query
 */
function customQuery($query) {

	global $inflect;

	$this->_result = mysql_query($query, $this->_dbHandle);
	$this->_lastQuery = $query;
	
	$this->confirmQuery($this->_result);
	
	$result = array();
	$table = array();
	$field = array();
	$tempResults = array();

	if(substr_count(strtoupper($query),"SELECT") > 0) {
		if (mysql_num_rows($this->_result) > 0) {
			$numOfFields = mysql_num_fields($this->_result);
			for ($i = 0; $i < $numOfFields; ++$i) {
				array_push($table,mysql_field_table($this->_result, $i));
				array_push($field,mysql_field_name($this->_result, $i));
			}
			while ($row = mysql_fetch_row($this->_result)) {
				for ($i = 0;$i < $numOfFields; ++$i) {
					$table[$i] = ucfirst($inflect->singularize($table[$i]));
					$tempResults[$table[$i]][$field[$i]] = $row[$i];
				}
				array_push($result,$tempResults);
			}
		}
		mysql_free_result($this->_result);
	}	
	$this->clear();
	return($result);
}

/*-------------------------------------------    
	Relationship Methods
---------------------------------------------*/

/**
 * ShowHasOne Method
 *
 * Performs a LEFT JOIN for each hasOne
 * relationship that is set in the Model
 */
function showHasOne() {
	$this->_hO = true;
}

/**
 * ShowHasMany Method
 *
 * for each result returned by the query and for each hasMany 
 * relationship in the Model, it will find all those records 
 * in related tables which match the current result’s id. Then 
 * it will push all those results in the same array.
 */
function showHasMany() {
	$this->_hM = true;
}

/**
 * ShowHasManyAndBelongsToMany Method
 * 
 * for each result returned by the query and for each 
 * hasManyAndBelongsToMany in the Model, it will find 
 * all those records which match the current result’s id
 */
function showHMABTM() {
	$this->_hMABTM = true;
}

/*-------------------------------------------    
	Extra Conditions Methods
---------------------------------------------*/

/**
 * The Where Method
 * Appends WHERE 'fieldName' = 'value'
 */
function where($field, $value) {
	$this->_extraConditions .= '`'.$this->_model.'`.`'.$field.'` = \''.mysql_real_escape_string($value).'\' AND ';
}

/**
 * The Like Method
 * Appends WHERE 'fieldName' LIKE 'value'
 */
function like($field, $value) {
	$this->_extraConditions .= '`'.$this->_model.'`.`'.$field.'` LIKE \'%'.mysql_real_escape_string($value).'%\' AND ';
}

/**
 * Set Page Method
 * 
 * Enables pagination and display only results for 
 * the set page number.
 */
function setPage($page) {
	$this->_page = $page;
}

/**
 * Set Limit Method
 *
 * Allows you to modify the number of results per page 
 * if pageNumber is set. Its default value is the one 
 * set as PAGINATE_LIMIT in config.php (Line 45)
 */
function setLimit($limit) {
	$this->_limit = $limit;
}

/**
 * Order By Method
 * Appends ORDER BY 'fieldName' ASC/DESC
 */
function orderBy($orderBy, $order = 'ASC') {
	$this->_orderBy = $orderBy;
	$this->_order = $order;
}

/*-------------------------------------------    
	The Great Search Method
---------------------------------------------*/
/**
 * Search Method
 *
 * Performs almost any DB Query needed assuming
 * all required variables are set. if id is set, 
 * then it will return a single result (and not 
 * an array), else it will return an array.
 *
 * Each call to search() will call the clear()
 * function after returning the resulting row(s)
 * which will reset all the variables.
 */
function search($clear = true) {

	global $inflect;

	$from = '`'.$this->_table.'` as `'.$this->_model.'` ';
	$conditions = '\'1\'=\'1\' AND ';
	$conditionsChild = '';
	$fromChild = '';

	if ($this->_hO == 1 && isset($this->hasOne)) {
		
		foreach ($this->hasOne as $alias => $model) {
			$table = strtolower($inflect->pluralize($model));
			$singularAlias = strtolower($alias);
			$from .= 'LEFT JOIN `'.$table.'` as `'.$alias.'` ';
			$from .= 'ON `'.$this->_model.'`.`'.$singularAlias.'_id` = `'.$alias.'`.`id`  ';
		}
	}

	if ($this->id) {
		$conditions .= '`'.$this->_model.'`.`id` = \''.mysql_real_escape_string($this->id).'\' AND ';
	}

	if ($this->_extraConditions) {
		$conditions .= $this->_extraConditions;
	}

	$conditions = substr($conditions,0,-4);
	
	if (isset($this->_orderBy)) {
		$conditions .= ' ORDER BY `'.$this->_model.'`.`'.$this->_orderBy.'` '.$this->_order;
	}

	if (isset($this->_page)) {
		$offset = ($this->_page-1)*$this->_limit;
		$conditions .= ' LIMIT '.$this->_limit.' OFFSET '.$offset;
	}
	
	$this->_query = 'SELECT * FROM '.$from.' WHERE '.$conditions;
	$this->_result = mysql_query($this->_query, $this->_dbHandle);
	$result = array();
	$table = array();
	$field = array();
	$tempResults = array();
	$numOfFields = mysql_num_fields($this->_result);
	for ($i = 0; $i < $numOfFields; ++$i) {
	    array_push($table,mysql_field_table($this->_result, $i));
	    array_push($field,mysql_field_name($this->_result, $i));
	}
	
	if (mysql_num_rows($this->_result) > 0 ) {
		while ($row = mysql_fetch_row($this->_result)) {
			for ($i = 0;$i < $numOfFields; ++$i) {
				$tempResults[$table[$i]][$field[$i]] = $row[$i];
			}

			if ($this->_hM == 1 && isset($this->hasMany)) {
				foreach ($this->hasMany as $aliasChild => $modelChild) {
					$queryChild = '';
					$conditionsChild = '';
					$fromChild = '';

					$tableChild = strtolower($inflect->pluralize($modelChild));
					$pluralAliasChild = strtolower($inflect->pluralize($aliasChild));
					$singularAliasChild = strtolower($aliasChild);

					$fromChild .= '`'.$tableChild.'` as `'.$aliasChild.'`';
					
					$conditionsChild .= '`'.$aliasChild.'`.`'.strtolower($this->_model).'_id` = \''.$tempResults[$this->_model]['id'].'\'';

					$queryChild =  'SELECT * FROM '.$fromChild.' WHERE '.$conditionsChild;	
					$resultChild = mysql_query($queryChild, $this->_dbHandle);
			
					$tableChild = array();
					$fieldChild = array();
					$tempResultsChild = array();
					$resultsChild = array();
					
					if (mysql_num_rows($resultChild) > 0) {
						$numOfFieldsChild = mysql_num_fields($resultChild);
						for ($j = 0; $j < $numOfFieldsChild; ++$j) {
							array_push($tableChild,mysql_field_table($resultChild, $j));
							array_push($fieldChild,mysql_field_name($resultChild, $j));
						}

						while ($rowChild = mysql_fetch_row($resultChild)) {
							for ($j = 0;$j < $numOfFieldsChild; ++$j) {
								$tempResultsChild[$tableChild[$j]][$fieldChild[$j]] = $rowChild[$j];
							}
							array_push($resultsChild,$tempResultsChild);
						}
					}
					
					$tempResults[$aliasChild] = $resultsChild;
					
					mysql_free_result($resultChild);
				}
			}


			if ($this->_hMABTM == 1 && isset($this->hasManyAndBelongsToMany)) {
				foreach ($this->hasManyAndBelongsToMany as $aliasChild => $tableChild) {
					$queryChild = '';
					$conditionsChild = '';
					$fromChild = '';

					$tableChild = strtolower($inflect->pluralize($tableChild));
					$pluralAliasChild = strtolower($inflect->pluralize($aliasChild));
					$singularAliasChild = strtolower($aliasChild);

					$sortTables = array($this->_table,$pluralAliasChild);
					sort($sortTables);
					$joinTable = implode('_',$sortTables);

					$fromChild .= '`'.$tableChild.'` as `'.$aliasChild.'`,';
					$fromChild .= '`'.$joinTable.'`,';
					
					$conditionsChild .= '`'.$joinTable.'`.`'.$singularAliasChild.'_id` = `'.$aliasChild.'`.`id` AND ';
					$conditionsChild .= '`'.$joinTable.'`.`'.strtolower($this->_model).'_id` = \''.$tempResults[$this->_model]['id'].'\'';
					$fromChild = substr($fromChild,0,-1);

					$queryChild =  'SELECT * FROM '.$fromChild.' WHERE '.$conditionsChild;	
					$resultChild = mysql_query($queryChild, $this->_dbHandle);
			
					$tableChild = array();
					$fieldChild = array();
					$tempResultsChild = array();
					$resultsChild = array();
					
					if (mysql_num_rows($resultChild) > 0) {
						$numOfFieldsChild = mysql_num_fields($resultChild);
						for ($j = 0; $j < $numOfFieldsChild; ++$j) {
							array_push($tableChild,mysql_field_table($resultChild, $j));
							array_push($fieldChild,mysql_field_name($resultChild, $j));
						}

						while ($rowChild = mysql_fetch_row($resultChild)) {
							for ($j = 0;$j < $numOfFieldsChild; ++$j) {
								$tempResultsChild[$tableChild[$j]][$fieldChild[$j]] = $rowChild[$j];
							}
							array_push($resultsChild,$tempResultsChild);
						}
					}
					
					$tempResults[$aliasChild] = $resultsChild;
					mysql_free_result($resultChild);
				}
			}

			array_push($result,$tempResults);
		}

		if (mysql_num_rows($this->_result) == 1 && $this->id != null) {
			mysql_free_result($this->_result);
			$this->clear();
			return($result[0]);
		} else {
			mysql_free_result($this->_result);
			$this->clear();
			return($result);
		}
		
	} else {
		mysql_free_result($this->_result);
		
		if ($clear) {
			$this->clear();
		}
		
		return $result;
	}

}

/*-------------------------------------------    
	Save, Delete, Update and Describe Methods
---------------------------------------------*/

/**
 * The Save Method
 * 
 * If ID is set, this method will simply update
 * the provided variables in the database. Otherwise
 * it will create a new record.
 */
function save() {
	
	global $inflect;
	$query = '';
	
	// If an ID was passed, update the entry
	if (isset($this->id)) {
	
		$updates = '';
		
		foreach ($this->_describe as $field) {
			if ($this->$field) {
				$updates .= '`'.$field.'` = \''.mysql_real_escape_string($this->$field).'\',';
			}
		}

		$updates = substr($updates,0,-1);

		$query = 'UPDATE '.$this->_table.' SET '.$updates.' WHERE `id`=\''.mysql_real_escape_string($this->id).'\'';
	
	// If no ID, create new entry		
	} else {
	
		$fields = '';
		$values = '';
		
		foreach ($this->_describe as $field) {
			if ($this->$field) {
				$fields .= '`'.$field.'`,';
				$values .= '\''.mysql_real_escape_string($this->$field).'\',';
			}
		}
		
		$values = substr($values,0,-1);
		$fields = substr($fields,0,-1);

		$query = 'INSERT INTO '.$this->_table.' ('.$fields.') VALUES ('.$values.')';
	}
	
	$this->_result = mysql_query($query, $this->_dbHandle);
	
	if ($this->_result == 0) {
		return -1;
	}
	
	if ($this->_hMABTM == 1 && isset($this->hasManyAndBelongsToMany)) {
		foreach ($this->hasManyAndBelongsToMany as $aliasChild => $tableChild) {
						
			$tableChild = strtolower($inflect->pluralize($tableChild));
			
			$hMABTM_table_sort = array($tableChild, $this->_table);

			sort($hMABTM_table_sort);
			$hMABTM_table = implode('_',$hMABTM_table_sort);
			$this->_describe_foreign($hMABTM_table);
			
			$hMABTM_id = strtolower($this->_model) . '_id';
			
			if (in_array($hMABTM_id, $this->_describe_foreign)) {
			
				$fields = '';
				$values = '';
				
				foreach ($this->_describe_foreign as $field) {
					if ($this->$field) {
						if (is_array($this->$field)) {
							foreach ($this->$field as $value) {
								$fields .= '`'.$field.'`,';
								$values .= '\''.mysql_real_escape_string($value).'\',';
							}
						} else {
							$fields .= '`'.$field.'`,';
							$values .= '\''.mysql_real_escape_string($this->$field).'\',';
						}
					}
				}

				$values = substr($values,0,-1);
				$fields = substr($fields,0,-1);
			
				$query = 'INSERT INTO '.$hMABTM_table.' ('.$fields.') VALUES ('.$values.')';
				$this->_result = mysql_query($query, $this->_dbHandle);
				
				if (!$this->_result) {
					return false;
				}
				
			}
			
		}
	}

	return $this->_result;

	$this->clear();
}

/**
 * The Delete Method
 *
 * This method will delete a given record. 
 * ID needs to be set in this case.
 */
function delete() {
	
	global $inflect;
	
	if ($this->id) {
				
		$query = 'DELETE FROM ' . $this->_table . ' WHERE `id`=\'' . mysql_real_escape_string($this->id) . '\'';
		$this->_result = mysql_query($query, $this->_dbHandle);
		
		if ($this->_result) {
			if ($this->_hMABTM == true && isset($this->hasManyAndBelongsToMany)) {
				foreach ($this->hasManyAndBelongsToMany as $aliasChild => $tableChild) {
					
					$tableChild = strtolower($inflect->pluralize($tableChild));
					
					$hMABTM_table_sort = array($tableChild, $this->_table);

					sort($hMABTM_table_sort);
					$hMABTM_table = implode('_',$hMABTM_table_sort);
					$this->_describe_foreign($hMABTM_table);

					$hMABTM_id = strtolower($this->_model) . '_id';

					if (in_array($hMABTM_id, $this->_describe_foreign)) {

						$query = 'DELETE FROM ' . $hMABTM_table . ' WHERE `'. $hMABTM_id .'`=\'' . mysql_real_escape_string($this->id) . '\'';
						$this->_result = mysql_query($query, $this->_dbHandle);

					}
					
				}
				
			}
		}
		
		$this->clear();
		return ($this->_result);
		
	} else {
		
		if ($this->_hMABTM == true && isset($this->hasManyAndBelongsToMany)) {
			foreach ($this->hasManyAndBelongsToMany as $aliasChild => $tableChild) {
				
				$tableChild = strtolower($inflect->pluralize($tableChild));
				
				$hMABTM_table_sort = array($tableChild, $this->_table);

				sort($hMABTM_table_sort);
				$hMABTM_table = implode('_',$hMABTM_table_sort);
				$this->_describe_foreign($hMABTM_table);

				$hMABTM_id = strtolower($this->_model) . '_id';

				if (in_array($hMABTM_id, $this->_describe_foreign)) {
					
					$query = 'DELETE FROM ' . $hMABTM_table . ' WHERE `'. $hMABTM_id .'`=\'' . mysql_real_escape_string($this->$hMABTM_id) . '\'';
										
					$this->_result = mysql_query($query, $this->_dbHandle);

				}
				
			}
		}
		
		$this->clear();
		return ($this->_result);
		
	}
}

/**
 * Describe Protected Method
 *
 * Takes all set variables in the controller and
 * creates the appropriate $this->_describe array
 * Called by BaseModel's __construct() Method
 */
protected function _describe() {
	if (!$this->_describe) {
		$this->_describe = array();
		$query = 'DESCRIBE ' . $this->_table;
		$this->_result = mysql_query($query, $this->_dbHandle);
		while ($row = mysql_fetch_row($this->_result)) {
			array_push($this->_describe, $row[0]);
		}
		mysql_free_result($this->_result);
	}
	foreach ($this->_describe as $field) {
		$this->$field = null;
	}
}

/**
 * Describe Foreign Table
 *
 * Grabs all the fields in a foreign table
 * and saves it to the _describe_foreign var
 */
protected function _describe_foreign($foreign_table) {
	$this->_describe_foreign = array();
	$query = 'DESCRIBE ' . $foreign_table;
	$this->_result = mysql_query($query, $this->_dbHandle);
		
	while ($row = mysql_fetch_row($this->_result)) {
		array_push($this->_describe_foreign, $row[0]);
	}
	unset($this->_describe_foreign[0]);
	
	mysql_free_result($this->_result);
}

/*-------------------------------------------    
	Other DB Methods
---------------------------------------------*/

/**
 * Confirm Query
 * Confirms a returned DB result
 */
function confirmQuery($result) {
	if(!$result) {
		$output = '<p><strong>Database query failed:</strong> ' . mysql_error() . '</p>';
		$output .= '<p><strong>Last SQL query:</strong> ' . $this->_lastQuery . '</p>';
		die($output);
	}
}

/**
 * MySQL Preparation
 * Escapes a string for use in a DB Query
 */
function db_prep($value) {
	$value = mysql_real_escape_string($value);
	return $value;
}

/** 
 * Get Error Method
 * Returns any DB errors
 */
function getError() {
	return mysql_error($this->_dbHandle);
}

/** 
 * Clear Method
 * Resets all the variables
 */
function clear() {
	foreach($this->_describe as $field) {
		$this->$field = null;
	}
	$this->_orderby = null;
	$this->_extraConditions = null;
	$this->_hO = null;
	$this->_hM = null;
	$this->_hMABTM = null;
	$this->_page = null;
	$this->_order = null;
}

/** 
 * Pagination Count
 */
function totalPages() {
	if ($this->_query && $this->_limit) {
		$pattern = '/SELECT (.*?) FROM (.*)LIMIT(.*)/i';
		$replacement = 'SELECT COUNT(*) FROM $2';
		$countQuery = preg_replace($pattern, $replacement, $this->_query);
		$this->_result = mysql_query($countQuery, $this->_dbHandle);
		$count = mysql_fetch_row($this->_result);
		$this->_count = array_shift($count);
		$totalPages = ceil($this->_count/$this->_limit);
		return $totalPages;
	} else {
		return -1;
	}
}

/** 
 * Get Count
 * Returns the total number of records
 */
function getCount($field = null, $value = null) {
	if(isset($this->_count)) {
		return $this->_count;
	} else {
		if ($field && $value) {
			$countQuery = 'SELECT COUNT(*) FROM ' . $this->_table . ' WHERE '. $field . ' = ' . $value;
			$this->_result = mysql_query($countQuery, $this->_dbHandle);
			$count = mysql_fetch_row($this->_result);
			$this->_count = array_shift($count);
			return $this->_count;
		} else {
			$countQuery = 'SELECT COUNT(*) FROM ' . $this->_table;
			$this->_result = mysql_query($countQuery, $this->_dbHandle);
			$count = mysql_fetch_row($this->_result);
			$this->_count = array_shift($count);
			return $this->_count;
		}
	}
}
   
} // End class BaseQuery