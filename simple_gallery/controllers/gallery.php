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
 * Gallery controller
 *
 * @package 	CSSDB
 * @category 	Controllers
 */
class Gallery extends Public_Controller
{
	/**
	 * Default action
	 * 
	 * @access 	public
	 * @return 	void
	 */
	function index ()
	{
		$designs = $this->design_model->get_designs();
		$tags_with_category = $this->design_model->get_tags_with_category();
		$tags_with_count = $this->design_model->get_tags_with_count();
		
		$this->view->set('designs', $designs);
		$this->view->set('tags_with_category', $tags_with_category);
		$this->view->set('tags_with_count', $tags_with_count);
		$this->view->display('gallery/template');
	}
	
	/**
	 * Retrieve gallery contents for an AJAX request
	 * 
	 * @access 	public
	 * @return 	void
	 */
	function gallery_ajax ()
	{
		// Prevent futher access if this is not an AJAX request
		if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest')
		{
			redirect();
		}
		
		$filters = $_POST;
		
		// Clean/remove any post vars here
		
		$designs = $this->design_model->get_designs($filters);
		
		echo $this->view->show_part('gallery/' . ($designs ? 'gallery' : 'empty'), array('designs' => $designs));
	}
}

/* End of file gallery.php */
/* Location: controllers/gallery.php */
