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
 * Admin login controller
 *
 * @author 		James Gifford
 * @copyright 	Copyright (c) 2009, James Gifford
 * @link 		http://jamesgifford.com
 * @filesource
 */
class Login extends Admin_Controller
{
	/**
	 * Constructor
	 */
	function __construct ()
	{
		parent::__construct(TRUE);
	}
	
	/**
	 * Allow a user to login to the admin area
	 * 
	 * @access 	public
	 * @return 	void
	 */
	function index ()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules(array(
			array('field' => 'username', 'label' => lang('login_label_username'), 'rules' => 'trim|required'),
			array('field' => 'password', 'label' => lang('login_label_password'), 'rules' => 'trim|required|callback__check_login')
		));
		
		if ($this->form_validation->run())
		{
			redirect('admin');
		}
		
		$this->view->add_part('content', 'login/login_form');
		$this->view->display();
	}
	
	/**
	 * Log the user out
	 * 
	 * @access 	public
	 * @return 	void
	 */
	function logout ()
	{
		$this->auth->logout();
		
		redirect('admin');
	}
	
	
	/* Private methods
	-----------------------------------------------------------------------------*/
	
	/**
	 * Authenticate the login by checking the values against the database
	 * 
	 * @access 	private
	 * @param 	string 	$password 	the password supplied by the user
	 * @return 	bool
	 */
	function _check_login ($password)
	{
		$this->form_validation->set_message('_check_login', 'Invalid login.');
		
		return $this->auth->login($this->input->post('username'), $password);
	}
}

/* End of file login.php */
/* Location: controllers/admin/login.php */