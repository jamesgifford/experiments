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
 * Parent controller
 * 
 * Parent controller class for all controllers
 *
 * @package 	CSSDB
 * @category 	Libraries
 */
class Parent_controller extends Controller
{
	/**
	 * Constructor
	 */
	function __construct ()
	{
		parent::Controller();
		
		date_default_timezone_set('America/Los_Angeles');
	}
}

/**
 * Public controller
 * 
 * Parent controller for all public controllers
 *
 * @package 	CSSDB
 * @category 	Libraries
 */
class Public_controller extends Parent_Controller
{
	/**
	 * Constructor
	 */
	function __construct ()
	{
		parent::__construct();
		
		$this->lang->load('public');
	}
}

/**
 * User controller
 * 
 * Parent controller for all user controllers
 *
 * @package 	CSSDB
 * @category 	Libraries
 */
class User_controller extends Parent_Controller
{
	/**
	 * Constructor
	 * 
	 * @param 	bool 	$is_exempt 	whether the current page is exempt from login checks
	 */
	function __construct ($is_exempt = FALSE)
	{
		parent::__construct();
		
		$this->lang->load('user');
		
		$this->view->set_template_dir('user');
		$this->view->set_template('template');
		
		if ( ! $is_exempt && ! $this->auth->is_logged_in())
		{
			redirect('user/login');
		}
	}
}

/**
 * Admin controller
 * 
 * Parent controller for all admin controllers
 *
 * @package 	CSSDB
 * @category 	Libraries
 */
class Admin_controller extends Parent_Controller
{
	/**
	 * Constructor
	 * 
	 * @param 	bool 	$is_exempt 	whether the current page is exempt from login checks
	 */
	function __construct ($is_exempt = FALSE)
	{
		parent::__construct();
		
		$this->lang->load('admin');
		
		$this->view->set_view_dir('admin');
		$this->view->set_template('template');
		
		if ( ! $is_exempt && ! $this->auth->is_logged_in() && ! $this->auth->has_role('admin'))
		{
			redirect('admin/login');
		}
	}
}

/* End of file MY_Controller.php */
/* Location: libraries/MY_Controller.php */
