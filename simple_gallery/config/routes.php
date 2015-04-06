<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| 	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['scaffolding_trigger'] = 'scaffolding';
|
| This route lets you set a "secret" word that will trigger the
| scaffolding feature for added security. Note: Scaffolding must be
| enabled in the controller in which you intend to use it.   The reserved 
| routes must come before any wildcard or regular expression routes.
|
*/

$uri_array = explode('/', $_SERVER['REQUEST_URI']);
$is_user = isset($uri_array[1]) && strtolower($uri_array[1]) === 'user';
$is_admin = isset($uri_array[1]) && strtolower($uri_array[1]) === 'admin';

$route['default_controller'] = $is_user ? 'dashboard' : ($is_admin ? 'dashboard' : 'gallery');

// Define routes for user requests
if ($is_user)
{
	
}
// Define routes for admin requests
else if ($is_admin)
{
	$route['admin/logout'] = 'admin/login/logout';
}
// Define routes for public requests
else
{
	
}

// Define routes for any request
$route['content/(:any).(css)'] = 'content/css/$1';
$route['content/screenshots/(:any).(png|jpg|gif)'] = 'content/screenshots/$1/$2';
$route['content/(:any).(png|jpg|gif)'] = 'content/images/$1/$2';


$route['scaffolding_trigger'] = '';

/* End of file routes.php */
/* Location: ./system/application/config/routes.php */
