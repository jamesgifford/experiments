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
 * Content controller
 * 
 * An asset manager to allow normally public files (stylesheets, scripts, and images) 
 * to be stored and accessed from within the application
 * 
 * @package 	CSSDB
 * @category 	Controllers
 * @author 		James Gifford <james@jamesgifford.com>
 */
class Content extends Public_Controller
{
	/**
	 * Output the contents of a stylesheet file
	 * 
	 * @access 	public
	 * @param 	string 	$file_name 	the stylesheet file name
	 * @return 	string
	 */
	function css ($file_name = '')
	{
		header('Content-Type: text/css');
		
		return print(read_file(APPPATH . "content/css/$file_name.css"));
	}
	
	/**
	 * Output an image
	 * 
	 * @access 	public
	 * @param 	string 	$file_name 	the image file name
	 * @param 	string 	$file_ext 	the extension of the file
	 * @param 	string 	$sub_dir 	(optional) subdirectory of the images directory
	 * @return 	string
	 */
	function images ($file_name = '', $file_ext = '', $sub_dir = '')
	{
		header("Content-Type: image/$file_ext");
		
		$path = trim((APPPATH . "content/images/$sub_dir"), '/') . "/$file_name.$file_ext";
		
		return print(read_file($path));
	}
	
	/**
	 * Output a screenshot image
	 * 
	 * @access 	public
	 * @param 	string 	$file_name 	the image file name
	 * @param 	string 	$file_ext 	the extension of the file
	 * @return 	string
	 */
	function screenshots ($file_name = '', $file_ext = '')
	{
		return $this->images($file_name, $file_ext, 'screenshots');
	}
	
	/**
	 * Output the contents of a JavaScript file
	 * 
	 * @access 	public
	 * @param 	string 	$file_name 	the JavaScript file name
	 * @return 	string
	 */
	function js ($file_name = '')
	{
		header('Content-Type: text/javascript');
		
		return print(read_file(APPPATH . "content/js/$file_name.js"));
	}
}


/* End of file content.php */
/* Location: ./controllers/content.php */