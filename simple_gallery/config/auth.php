<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Authentication library
 * 
 * Basic user authentication/verification library
 * 
 * @package 		Auth Library
 * @author			James Gifford
 * @link			http://jamesgifford.com
 * @copyright		Copyright (c) James Gifford
 * @version 		1.4
 * @filesource
 */

/*
|--------------------------------------------------------------------------
| Auth library config settings
|--------------------------------------------------------------------------
| users_table_name 		name of the users database table
| roles_table_name 		name of the user roles database table
| link_table_name 		name of the database table linking users to roles
|
*/
$config['auth_config'] = array(
	'users_table_name' 	=> 'users',
	'roles_table_name' 	=> 'users_roles',
	'link_table_name' 	=> 'users_roles_link'
);


/* End of file auth.php */
/* Location: ./config/auth.php */