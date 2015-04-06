<?php

/**
 * CSSDB.com
 * 
 * A web design gallery with extensive searhcing and filtering options
 * 
 * @package 	CSSDB
 * @author 		James Gifford
 * @copyright 	Copyright (c) 2009, James Gifford <http://jamesgifford.com>
 * @link 		http://cssdb.com
 * @filesource
 */

/**
 * Parent model class
 *
 * @package 	CSSDB
 * @category 	Libraries
 */
class MY_Model extends Model
{
	/**
	 * Constructor
	 */
	function __construct ()
	{
		parent::Model();
	}
	
	/* Selects
	-----------------------------------------------------------------------------*/
	
	/**
	 * Return a single value from the first row of a result
	 * 
	 * @access 	public
	 * @param 	object 	$query 	query object
	 * @param 	string 	$name 	the name of the field to return
	 * @param 	mixed 	$empty 	what to return if there are no rows
	 * @return 	array
	 */
	function query_value ($query, $name, $empty = FALSE)
	{
		return $query->num_rows() ? $query->row()->$name : $empty;
	}
	
	/**
	 * Return a single row from a result
	 * 
	 * @access 	public
	 * @param 	object 	$query 	query object
	 * @param 	mixed 	$empty 	what to return if there are no rows
	 * @return 	array
	 */
	function query_row ($query, $empty = FALSE)
	{
		return $query->num_rows() ? $query->row_array() : $empty;
	}
	
	/**
	 * Return all rows from a result
	 * 
	 * @access 	public
	 * @param 	object 	$query 	query object
	 * @param 	mixed 	$empty 	what to return if there are no rows
	 * @return 	array
	 */
	function query_rows ($query, $empty = FALSE)
	{
		return $query->num_rows() ? $query->result_array() : $empty;
	}
	
	/* Inserts
	-----------------------------------------------------------------------------*/
	
	/**
	 * Insert a new record
	 * 
	 * @access 	public
	 * @param 	string 	$table 		the name of the table to add the record to
	 * @param 	array 	$record 	the record data
	 * @return 	mixed
	 */
	function insert ($table, $record)
	{
		$this->db->insert($table, $record);
		
		return $this->db->affected_rows() ? $this->db->insert_id() : FALSE;
	}
}

/* End of file MY_Model.php */
/* Location: libraries/MY_Model.php */
