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
 * Admin dashboard controller
 *
 * @author 		James Gifford
 * @copyright 	Copyright (c) 2009, James Gifford
 * @link 		http://jamesgifford.com
 * @filesource
 */
class Dashboard extends Admin_Controller
{
	/**
	 * Constructor
	 */
	function __construct ()
	{
		parent::__construct();
		
		$this->view->add_part('content', 'menu');
	}
	
	/**
	 * 
	 * 
	 * @access 	public
	 * @return 	void
	 */
	function index ()
	{
		$this->view->display();
	}
}

/* End of file dashboard.php */
/* Location: controllers/admin/dashboard.php */