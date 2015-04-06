<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * View management class for CodeIgniter
 *
 * Handles data and functionality for the view portion of MVC such as 
 * loading view and partial view files and passing data to these files 
 * including data for meta tags, stylesheets and JavaScript code.
 *
 * @package			View Library
 * @author			James Gifford
 * @link			http://jamesgifford.com
 * @copyright		Copyright (c) James Gifford
 * @version 		2.0
 * @filesource
 */

/**
 * Configuration file for the View library
 *
 * Optional config file for setting up common values for the View library.
 *
 * @package			View Library
 * @category 		Libraries
 * @author			James Gifford
 * @link			http://jamesgifford.com
 */

/*
|--------------------------------------------------------------------------
| View configuration settings
|--------------------------------------------------------------------------
| Here are the available configuration options for the view class:
| 	template			the view template file to use for the current page
|   template_dir		the directory containing view template files
|	styles_dir			the directory containing stylesheet files
|	scripts_dir			the directory containing JavaScript files
|	title_var			special variable name used for the page title
|	meta_var			special variable name used for meta tags
|	styles_var			special variable name used for style info
|	scripts_var			special variable name used for JavaScript data
|	var_prefix			prefix applied to all template variables
|	var_suffix			suffix applied to all template variables
|	vars				associative array of variable name/value pairs
|	partials			associative array of partial view variable name/value pairs
|	meta				associative array of arrays of meta tag info
|	styles				array of stylesheet files
|	scripts				array of JavaScript files
*/

/*
|--------------------------------------------------------------------------
| Pre-load config settings
|--------------------------------------------------------------------------
| The view class will be updated with these settings during initialization.
|
*/
$config['view_pre_load'] = array(
	//'template'			=> 'template',
	//'vars'				=> array('title' => '', 'header' => '', 'content' => '', 'sidebar' => '', 'footer' => ''),
	//'meta'				=> array(array('http_equiv' => 'Content-Type', 'content' => 'text/html; charset=utf-8')),
	//'css'				=> array('farbtastic', 'style'),
	//'js'				=> array('jquery', 'jquery-ui', 'farbtastic', 'scripts'),
	//'partials' 			=> array('content' => array('header'))
);

/*
|--------------------------------------------------------------------------
| Post-load config settings
|--------------------------------------------------------------------------
| The view class will be updated with these settings immediately before loading.
|
*/
$config['view_post_load'] = array(
	//'partials'			=> array('content' => array('common/user_navigation'))
);

/* End of file view.php */
/* Location: ./config/view.php */