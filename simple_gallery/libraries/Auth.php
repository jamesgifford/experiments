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
 * Authentication library
 * 
 * Basic user authentication/verification library
 * 
 * @package 		CSSDB
 * @category 		Libraries
 */
class Auth
{
	/**
	 * The user's database record
	 * 
	 * @access 	private
	 * @var 	array
	 */
	private $user;
	
	/**
	 * The roles the user belongs to
	 * 
	 * @access 	private
	 * @var 	array
	 */
	private $roles;
	
	/**
	 * Constructor
	 */
	function __construct ()
	{
		// Get an instance of the CodeIgniter object
		$this->ci =& get_instance();
		$this->ci->load->model('auth_model');
		$this->ci->load->library('encrypt');
		
		$this->user = $this->roles = array();
				
		// Try to get the user's user_id from the session
		if ($user_id = $this->ci->session->userdata('user_id'))
		{		
			if ( ! ($this->user = $this->ci->auth_model->get_user_by_id($this->ci->encrypt->decode($user_id))))
				show_error('Invalid user id');
			
			// Retrieve the user's user role info
			$this->roles = $this->ci->auth_model->get_roles_by_user_id($user_id);
		}
	}
	
	/**
	 * Attempt to log a user in with their username and password
	 * 
	 * @access 	public
	 * @param 	string 	$username 	the user's username
	 * @param 	string 	$password 	the user's password
	 * @return 	bool
	 */
	function login ($username, $password)
	{
		// Find the user's database record by the username
		if ( ! ($this->user = $this->ci->auth_model->get_user_by_name($username)))
			return FALSE;
		
		// If the provided password matches the database record, log the user in
		if ($this->ci->encrypt->decode($this->user['password']) == $password)
		{
			// Store the encrypted user id in the session
			$this->ci->session->set_userdata('user_id', $this->ci->encrypt->encode($this->user_id()));
			
			// Retrieve the user's user role info
			$this->roles = $this->ci->auth_model->get_roles_by_user_id($this->user_id());
			
			// Log the details of this login
			$this->ci->auth_model->log_login($this->user_id());
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Clear stored data to log a user out
	 * 
	 * @access 	public
	 * @return 	void
	 */
	function logout ()
	{
		$this->user = $this->roles = array();
		$this->ci->session->unset_userdata('user_id');
	}
	
	/**
	 * Check to see if the user is logged in
	 * 
	 * @access 	public
	 * @return 	bool
	 */
	function is_logged_in ()
	{
		return isset($this->user) && is_array($this->user) && count($this->user);
	}
	
	/**
	 * Check if the user belongs to the specifed role
	 * 
	 * @access 	public
	 * @param 	mixed 	$name_or_id 	either the name or the id of the role
	 * @return 	bool
	 */
	function has_role ($name_or_id)
	{
		return is_int($name_or_id) ? isset($this->roles[$name_or_id]) : in_array($name_or_id, $this->roles);
	}
	
	/**
	 * Return a field from the user's database record
	 * 
	 * @access 	public
	 * @param 	string 	$field 	the name of the field to return
	 * @return 	mixed
	 */
	function userdata ($field)
	{
		return isset($this->user[$field]) ? $this->user[$field] : FALSE;
	}
	
	/**
	 * Convenience function for getting the user's id
	 * 
	 * @access 	public
	 * @return 	int
	 */
	function user_id ()
	{
		return $this->userdata('id');
	}
	
	/**
	 * Convenience function for getting the user's name
	 * 
	 * @access 	public
	 * @return 	int
	 */
	function name ()
	{
		return $this->userdata('name');
	}
	
	/**
	 * Convenience function for getting the user's title
	 * 
	 * @access 	public
	 * @return 	int
	 */
	function title ()
	{
		return $this->userdata('title');
	}
}

/* End of file Auth.php */
/* Location: ./libraries/Auth.php */
