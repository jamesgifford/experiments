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
 * Auth model
 * 
 * Database model used by the Auth library
 *
 * @package 	CSSDB
 * @category 	Models
 */
class Auth_model extends Model
{
	/**
	 * Constructor
	 */
	function __construct ()
	{
		parent::Model();
		
		// Define the list of tables used in this model
		$this->tables = array(
			'users' 			=> 'users',
			'roles' 			=> 'roles',
			'users_to_roles'	=> 'users_to_roles',
			'logins' 			=> 'logins');
	}
	
	/* Selects
	-----------------------------------------------------------------------------*/
	
	/**
	 * Get a user's database record by their user id
	 * 
	 * @access 	public
	 * @param 	int 	$user_id 	the id of the user
	 * @return 	array
	 */
	function get_user_by_id ($user_id)
	{
		$this->db->where('id', $user_id);
		$query = $this->db->get($this->tables['users']);
		
		return $query->num_rows() ? $query->row_array() : FALSE;
	}
	
	/**
	 * Get a user's database record b their username
	 * 
	 * @access 	public
	 * @param 	string 	$user_name 	the user's username
	 * @return 	array
	 */
	function get_user_by_name ($user_name)
	{
		$this->db->where('name', $user_name);
		$query = $this->db->get($this->tables['users']);
		
		return $query->num_rows() ? $query->row_array() : FALSE;
	}
	
	/**
	 * Get a list of the roles a user belongs to
	 * 
	 * @access 	public
	 * @param 	int 	$user_id 	the id of the user
	 * @return 	array
	 */
	function get_roles_by_user_id ($user_id)
	{
		$this->db->select('roles.id, roles.name');
		$this->db->join($this->tables['users_to_roles'].' link', 'roles.id = link.role_id');
		$this->db->where('link.user_id', $user_id);
		$query = $this->db->get($this->tables['roles'].' roles');
		
		$result = array();
		foreach ($query->result_array() as $row)
			$result[$row['id']] = $row['name'];
		
		return $result;
	}
	
	/* Inserts
	-----------------------------------------------------------------------------*/
	
	/**
	 * Record a user's login
	 * 
	 * @access 	public
	 * @param 	int 	$user_id 	the id of the user
	 * @return 	void
	 */
	function log_login ($user_id)
	{
		$this->db->insert($this->tables['logins'], array(
			'user_id' 		=> $user_id, 
			'ip_address' 	=> $this->input->ip_address(),
			'login_date' 	=> date('Y-m-d H:i:s')
		));
	}
	
	/* Updates
	-----------------------------------------------------------------------------*/
	
	/* Deletes
	-----------------------------------------------------------------------------*/
	
}

/* End of file Auth_model.php */
/* Location: libraries/Auth_model.php */
