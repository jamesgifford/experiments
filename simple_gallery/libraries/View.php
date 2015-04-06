<?php

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
class View
{
	/* User Configuration 
	 * (will be overwritten by corresponding values in the config file)
	-----------------------------------------------------------------------------*/
	
	// Directories
	var $view_dir = 		'';						// root directory for view files
	var $template_dir = 	''; 					// root directory for template files
	var $css_dir =			'css';					// root directory for stylesheet files
	var $js_dir =			'js';					// root directory for JavaScript files
	
	// Variables
	var $title_var = 		'title'; 				// special variable for the page title
	var $meta_var = 		'meta';					// special variable for meta data
	var $css_var = 			'css'; 					// special variable for style definitions
	var $js_var = 			'js'; 					// special variable for JavaScript data
	var $link_var =			'links';				// special variable for other header links (eg: favicons)
	
	// Defaults
	var $template = 		'default'; 				// The template to load for the page
	var $vars =				array();				// View variables and values
	var $partials = 		array(); 				// Partial views to load into the page
	var $meta =				array();				// Meta data
	var $css =				array();				// Stylesheet includes (not style definitions)
	var $js =				array();				// JavaScript files (not actual code)
	var $links =			array();				// Header links
	
	/* End user configuration
	-----------------------------------------------------------------------------*/
	
	
	/**
	 * Constructor
	 */
	function View ()
	{
		$this->ci =& get_instance();
		$this->ci->load->helper('url');
		
		// Load the view config file and initialize with any pre_load settings
		$this->ci->config->load('view', FALSE, TRUE);
		$this->initialize($this->ci->config->item('view_pre_load'));
		
		// Make sure that special variables are initialized
		foreach (array($this->title_var, $this->meta_var, $this->css_var, $this->js_var) as $var)
			$this->data[$var] = isset($this->data[$var]) ? $this->data[$var] : '';
	}
	
	/**
	 * Configure class data
	 * 
	 * @access 	public
	 * @param 	array 	$config 	array of settings
	 * @return 	void
	 */
	function initialize ($config = array())
	{
		if (!is_array($config))
			return;
		
		foreach ($config as $index => $value)
		{
			switch ($index)
			{
				case 'view_dir':
					$this->set_view_dir($value);
					break;
				
				case 'template_dir':
					$this->set_template_dir($value);
					break;
				
				case 'css_dir':
					$this->set_css_dir($value);
					break;
				
				case 'js_dir':
					$this->set_js_dir($value);
					break;
				
				case 'title_var':
					$this->title_var = $value;
					break;
				
				case 'meta_var':
					$this->meta_var = $value;
					break;
				
				case 'css_var':
					$this->css_var = $value;
					break;
				
				case 'js_var':
					$this->js_var = $value;
					break;
				
				case 'template':
					$this->set_template($value);
					break;
				
				case 'vars':
					foreach (is_array($value) ? $value : array($value) as $var => $value)
						$this->set($var, $value);
					break;
				
				case 'partials':
					foreach (is_array($value) ? $value : array($value) as $var => $partial)
						foreach (is_array($partial) ? $partial : array($partial) as $view)
							$this->add_part($var, $view);
					break;
				
				case 'meta':
					foreach (is_array($value) ? $value : array($value) as $var => $value)
						$this->add_meta_array($value);
					break;
				
				case 'css':
					foreach (is_array($value) ? $value : array($value) as $var => $value)
						$this->add_css_file($value);
					break;
				
				case 'js':
					foreach (is_array($value) ? $value : array($value) as $var => $value)
						$this->add_js_file($value);
					break;
			}
		}
	}
	
	/**
	 * Display the page
	 *
	 * @access	public
	 * @param 	string	$template	optional template file
	 * @param	array 	$data		optional data to pass to the template
	 * @param 	bool 	$return 	whether to return the output or send it to the browser
	 * @return 	void
	 */
	function load ($template = '', $data = array(), $return = FALSE)
	{
		$this->initialize($this->ci->config->item('view_post_load'));
		
		if ($template)
			$this->set_template($template);
		
		$this->set($data);
		
		foreach ($this->partials as $var => $partial)
			foreach ($partial as $view)
				$this->add_part($var, $view['part'], $view['data'], TRUE);
		
		return $this->ci->load->view(trim($this->view_dir, '/') . '/' . trim($this->template_dir, '/') . "/$this->template", $this->data, $return);
	}
	
	/**
	 * Wrapper class for the load function for displaying the page
	 *
	 * @access 	public
  	 * @param 	string	$template	optional template file
	 * @param	array 	$data		optional data to pass to the template
	 * @return 	void
	 */
	function display ($template = '', $data = array())
	{
		$this->load($template, $data);
	}
	
	/**
	 * Wrapper class for the load function for returning the page
	 *
	 * @access 	public
  	 * @param 	string	$template	optional template file
	 * @param	array 	$data		optional data to pass to the template
	 * @return 	void
	 */
	function show ($template = '', $data = array())
	{
		return $this->load($template, $data, TRUE);
	}
	
	/**
	 * Display a single partial view
	 * 
	 * @access 	public
 	 * @param 	string 	$part 		the relative path of the view file
	 * @param	array 	$data 		(optional) data to pass to the partial view
	 * @param 	bool 	$return 	whether to return the output or send it to the browser
	 * @return 	string
	 */
	function load_part ($part, $data = array(), $return = FALSE)
	{
		$this->set($data);
		
		return $this->ci->load->view(trim($this->view_dir, '/') . "/$part", $this->data, $return);
	}
	
	/**
	 * Wrapper class for the load_part function for displaying the partial view
	 * 
	 * @access 	public
 	 * @param 	string 	$part 		the relative path of the view file
	 * @param	array 	$data 		(optional) data to pass to the partial view
	 * @return 	string
	 */
	function disaply_part ($part, $data = array())
	{
		$this->load_part($part, $data);
	}
	
	/**
	 * Wrapper class for the load function for returning the partial view
	 * 
	 * @access 	public
 	 * @param 	string 	$part 		the relative path of the view file
	 * @param	array 	$data 		(optional) data to pass to the partial view
	 * @return 	string
	 */
	function show_part ($part, $data = array())
	{
		return $this->load_part($part, $data, TRUE);
	}
	
	/**
	 * Get the value of a variable
	 * 
	 * @access 	public
	 * @param	string	$var	the name of the variable
	 * @return 	mixed
	 */
	function get ($var)
	{
		return (isset($this->data[$var])) ? $this->data[$var] : FALSE;
	}
	
	/**
	 * Set variables
	 * 
	 * @access	public
	 * @param	mixed	$data	either a single variable name or an array of variable data
	 * @param	mixed	$value	the value for the variable
	 * @return 	void
	 */
	function set ($data, $value = NULL)
	{
		$data = is_array($data) ? $data : array($data => $value);
		
		foreach ($data as $var => $value)
			$this->data[$var] = $value;
	}
	
	/**
	 * Unset variables
	 * 
	 * @access	public
	 * @param	mixed	$data	either a single variable name or an array of variable data
	 * @return 	void
	 */
	function clear ($data)
	{
		$data = is_array($data) ? $data : array($data);
		
		foreach ($data as $var => $value)
			unset($this->data[$var]);
	}
	
	/**
	 * Append/prepend a value to existing template variables (strings or arrays)
	 * 
	 * @access 	public
	 * @param	mixed	$data		either a single variable name or an array of variable data
	 * @param 	mixed	$value		the value for the variable
	 * @param 	bool	$prepend	(optional) whether to prepend the new data or append it
	 * @return 	void
	 */
	function add ($data, $value = NULL, $prepend = FALSE)
	{
		return $prepend ? $this->prepend($data, $value) : $this->append($data, $value);
	}
	
	/**
	 * Add data to the end of a existing values (strings and arrays only)
	 * 
	 * @access 	public
	 * @param 	mixed 	$data 	either a single variable name or an array of variable data
	 * @param 	mixed 	$value 	the value to append to the variable
	 * @return 	void
	 */
	function append ($data, $value = NULL)
	{
		$data = is_array($data) ? $data : array($data => $value);
		
		foreach ($data as $var => $value)
		{
			if (!$this->get($var))
				$this->set($var, $value);
			else if (is_array($this->get($var)))
				$this->set($var, (is_array($value)) ? array_merge($this->get($var), $value) : array_merge($this->get($var), array($value)));
			else
				$this->set($var, $this->get($var) . $value);
		}
	}
	
	/**
	 * Add data to the front of existing values (strings and arrays only)
	 * 
	 * @access 	public
	 * @param 	mixed 	$data 	either a single variable name or an array of variable data
	 * @param 	mixed 	$value 	the value to prepend to the variable
	 * @return 	void
	 */
	function prepend ($data, $value = NULL)
	{
		$data = is_array($data) ? $data : array($data => $value);
		
		foreach ($data as $var => $value)
		{
			if (!$this->get($var))
				$this->set($var, $value);
			else if (is_array($this->get($var)))
				$this->set($var, (is_array($value)) ? array_merge($value, $this->get($var)) : array_merge(array($value), $this->get($var)));
			else
				$this->set($var, $value . $this->get($var));
		}
	}
	
	/**
	 * Set a partial view to the template
	 * 
	 * @access 	public
	 * @param 	string 	$var 		the name of the variable to assign the partial
	 * @param 	string 	$part 		the relative path of the view file
	 * @param	array 	$data 		(optional) data to pass to the partial view
	 * @param	bool 	$render 	(optional) whether or not to render the partial view immediately
	 * @return 	void
	 */
	function set_part ($var, $part, $data = '', $render = FALSE)
	{
		$data = (is_array($data)) ? $data : array();
		
		if ($render)
			$this->set($var, "\n" . $this->show_part($part, array_merge($this->data, $data)));
		else
			$this->partials[$var] = array(array('part' => $part, 'data' => $data));
	}
	
	/**
	 * Unset any partial views from a template variable
	 * 
	 * @access 	public
	 * @param 	string	$var 	the name of the variable
	 * @return 	void
	 */
	function clear_part ($var)
	{
		unset($this->partials[$var]);
		$this->unset($var);
	}
	
	/**
	 * Add a partial view to the template
	 * 
	 * @access 	public
	 * @param 	string 	$var 		the name of the variable to assign the partial
	 * @param 	string 	$part 		the relative path of the view file
	 * @param	array 	$data 		(optional) data to pass to the partial view
	 * @param	bool 	$render 	(optional) whether or not to render the partial view immediately
	 * @param 	bool 	$prepend	(optional) whether to prepend the new data or append it
	 * @return 	void
	 */
	function add_part ($var, $part, $data = '', $render = FALSE, $prepend = FALSE)
	{		
		if ($var && $part)
			return ($prepend) ? $this->prepend_part($var, $part, $data, $render) : $this->append_part($var, $part, $data, $render);
	}
	
	/**
	 * Append a partial view after existing partials
	 * 
	 * @access 	public
	 * @param 	string 	$var 		the name of the variable to assign the partial
	 * @param 	string 	$part 		the relative path of the view file
	 * @param	array 	$data 		(optional) data to pass to the partial view
	 * @param	bool 	$render 	(optional) whether or not to render the partial view immediately
	 * @return 	void
	 */
	function append_part ($var, $part, $data = '', $render = FALSE)
	{
		$data = (is_array($data)) ? $data : array();
		
		if ($render)
			$this->add($var, "\n" . $this->show_part($part, array_merge($this->data, $data)), FALSE);
		else
			$this->partials[$var][] = array('part' => $part, 'data' => $data);
	}
	
	/**
	 * Append a partial view before existing partials
	 * 
	 * @access 	public
	 * @param 	string 	$var 		the name of the variable to assign the partial
	 * @param 	string 	$part 		the relative path of the view file
	 * @param	array 	$data 		(optional) data to pass to the partial view
	 * @param	bool 	$render 	(optional) whether or not to render the partial view immediately
	 * @return 	void
	 */
	function prepend_part ($var, $part, $data = '', $render = FALSE)
	{
		$data = (is_array($data)) ? $data : array();
		
		if ($render)
			$this->add($var, "\n" . $this->show_part($part, array_merge($this->data, $data)), TRUE);
		else
			$this->partials[$var] = array_merge(array(array('part' => $part, 'data' => $data)), (isset($this->partials[$var])) ? $this->partials[$var] : array());
	}
	
	/**
	 * Set a directory
	 * 
	 * @access 	public
	 * @param	string 	$var 	the directory variable
	 * @param 	string 	$dir 	the directory path
	 * @return 	void
	 */
	function set_directory ($var, $dir)
	{
		$this->$var = (strpos($dir, '/') === (strlen($dir) - 1) || !$dir) ? $dir : "$dir/";
	}
	
	/**
	 * Set the view directory
	 * 
	 * @access 	public
	 * @param 	string 	$dir 	the directory path
	 * @return 	void
	 */
	function set_view_dir ($dir)
	{
		$this->set_directory('view_dir', $dir);
	}
	
	/**
	 * Set the template directory
	 * 
	 * @access 	public
	 * @param 	string 	$dir 	the directory path
	 * @return 	void
	 */
	function set_template_dir ($dir)
	{
		$this->set_directory('template_dir', $dir);
	}
	
	/**
	 * Set the stylesheet files directory
	 * 
	 * @access 	public
	 * @param 	string 	$dir 	the directory path
	 * @return 	void
	 */
	function set_css_dir ($dir)
	{
		$this->set_directory('css_dir', $dir);
	}
	
	/**
	 * Set the JavaScript files directory
	 * 
	 * @access 	public
	 * @param 	string 	$dir 	the directory path
	 * @return 	void
	 */
	function set_js_dir ($dir)
	{
		$this->set_directory('js_dir', $dir);
	}
	
	/**
	 * Set the page template
	 * 
	 * @access 	public
	 * @param 	string 	$template
	 * @return 	void
	 */
	function set_template ($template)
	{
		$this->template = $template;
	}
	
	/**
	 * Set the page title
	 * 
	 * @access 	public
	 * @param 	string 	$title 	the title of the page
	 * @return 	void
	 */
	function set_title ($title)
	{
		$this->set($this->title_var, $title);
	}
	
	/**
	 * Add to the page title
	 * 
	 * @access 	public
	 * @param 	string 	$title 	the additional title of the page
	 * @return 	void
	 */
	function add_title ($title)
	{
		$this->add($this->title_var, $title);
	}
	
	/**
	 * Add a meta tag to the page
	 * 
	 * @access	public
	 * @param 	string	$name		(optional) the name property of the meta tag
	 * @param 	string	$content 	(optional) the content property of the meta tag
	 * @param 	string	$http_equiv	(optional) the http-equiv property of the meta tag
	 * @param 	bool 	$prepend	(optional) whether to prepend the new data or append it
	 * @return 	void
	 */
	function add_meta ($name = '', $content = '', $http_equiv = '', $prepend = FALSE)
	{
		$tag = '';
		$tag .= ($name) ? 'name="' . $name . '" ' : '';
		$tag .= ($content) ? 'content="' . $content . '" ' : '';
		$tag .= ($http_equiv) ? 'http-equiv="' . $http_equiv . '" ' : '';
		
		$this->add($this->meta_var, "<meta $tag/>\n", $prepend);
	}
	
	/**
	 * Add a meta tag to the page from an array of properties
	 * 
	 * @access	public
	 * @param 	array 	$meta 		array of meta tag properties
	 * @param 	bool 	$prepend	(optional) whether to prepend the new data or append it
	 * @return 	void
	 */
	function add_meta_array ($meta, $prepend = FALSE)
	{
		if (!is_array($meta[0]))
			$meta = array($meta);
		
		foreach ($meta as $item) {
			
			if (!$item || !is_array($item))
				continue;
			
			$tag = '';
			
			foreach ($item as $property => $value)
				$tag .= "$property=\"$value\" ";
			
			$this->add($this->meta_var, "<meta $tag/>\n", $prepend);
		}
	}
	
	/**
	 * Add CSS links to the page
	 * 
	 * @access 	public
	 * @param 	mixed 	$css 		one (string) or more (array) stylesheets to add
	 * @param 	string 	$media 		(optional) the media property of the tag
	 * @param 	string 	$rel 		(optional) the rel property of the tag
	 * @param 	bool 	$prepend	(optional) whether to prepend the new data or append it
	 * @return 	void
	 */
	function add_css_file ($css, $media = 'screen', $rel = 'stylesheet', $prepend = FALSE)
	{
		if (!$css)
			return;
		
		$css = is_array($css) ? $css : array($css);
		
		foreach ($css as $val)
		{
			$tag = '<link rel="' . $rel . '" type="text/css" href="' . base_url() . trim($this->css_dir, '/') . "/$val.css?".time()."\" media=\"$media\" />\n";
			
			$this->add($this->css_var, $tag, $prepend);
		}
	}
	
	/**
	 * Add multiple stylesheets from an array
	 * 
	 * @access	public
	 * @param 	array 	$stylesheets 	an array of stylesheets
	 * @param 	bool 	$prepend		(optional) whether to prepend the new data or append it
	 * @return 	void
	 */
	function add_css_array ($stylesheets, $prepend = FALSE)
	{
		if (!is_array($stylesheets[0]))
			$stylesheets = array($stylesheets);
		
		foreach ($stylesheets as $stylesheet) {
			
			if (!$stylesheet || !is_array($stylesheet))
				continue;
			
			$this->add_css_file($stylesheet['href'], isset($stylesheet['media']) ? $stylesheet['media'] : 'screen', 'stylesheet', $prepend);
		}
	}
	
	/**
	 * Add a CSS import to the page
	 * 
	 * @access 	public
	 * @param 	mixed 	$css 		one (string) or more (array) stylesheets to add
	 * @param 	string 	$media 		(optional) the media property of the tag
	 * @param 	bool 	$prepend	(optional) whether to prepend the new data or append it
	 * @return 	void
	 */
	function add_css_import ($css, $media = 'screen')
	{
		if (!$css)
			return;
		
		$css = is_array($css) ? $css : array($css);
		$tag = '<style type="text/css" media="' . $media . '">' . "\n";
		
		foreach ($css as $val)
			$tag .= '@import url(' . base_url() . trim($this->css_dir, '/') . "/$val.css);\n";
		
		$tag .= "</style>\n";
		
		$this->add($this->css_var, $tag, $prepend);
	}
	
	/**
	 * Add CSS code to the page
	 * 
	 * @access 	public
	 * @param 	mixed 	$css 		one (string) or more (array) sections of css code to add
	 * @param 	string 	$media 		(optional) the media property of the tag
	 * @param 	bool 	$prepend	whether to prepend the new data or append it
	 * @return 	void
	 */
	function add_css_inline ($css, $media = 'screen', $prepend = FALSE)
	{
		if (!$css)
			return;
		
		$css = is_array($css) ? $css : array($css);
		$tag = '<style type="text/css" media="' . $media . '">' . "\n";
		
		foreach ($css as $val)
			$tag .= $val . "\n";
		
		$tag .= "</style>\n";
		
		$this->add($this->css_var, $tag, $prepend);
	}
	
	/**
	 * Add a JavaScript file to the page
	 * 
	 * @access 	public
	 * @param 	mixed 	$js 		one (string) or more (array) JavaScript files to add
	 * @param 	bool 	$prepend	(optional) whether to prepend the new data or append it
	 * @return 	void
	 */
	function add_js_file ($js, $prepend = FALSE)
	{
		if (!$js)
			return;
		
		$js = is_array($js) ? $js : array($js);
		
		foreach ($js as $val)
		{
			$tag = '<script type="text/javascript" language="javascript" src="' . base_url() . trim($this->js_dir, '/') . "/$val.js?".time()."\"></script>\n";
			
			$this->add($this->js_var, $tag, $prepend);
		}
	}
	
	/**
	 * Add JavaScript code to the page
	 * 
	 * @access 	public
	 * @param 	mixed 	$js 		one (string) or more (array) JavaScript code segments to add
	 * @param 	bool 	$prepend	(optional) whether to prepend the new data or append it
	 * @return 	void
	 */
	function add_js_inline ($js, $prepend = FALSE)
	{
		if (!$js)
			return;
		
		$js = is_array($js) ? $js : array($js);
		$tag = '<script type="text/javascript" language="javascript">' . "\n";
		//$tag .= "<![CDATA[\n";
		
		foreach ($js as $val)
			$tag .= $val . "\n";
		
		//$tag .= "]]>\n";
		$tag .= "</script>\n";
		
		$this->add($this->js_var, $tag, $prepend);
	}
	
	/**
	 * Add a header link to the page
	 * 
	 * @access	public
	 * @param 	string	$href		(optional) the href property of the link
	 * @param 	string	$rel 		(optional) the rel property of the link
	 * @param 	bool 	$prepend	(optional) whether to prepend the new data or append it
	 * @return 	void
	 */
	function add_link ($href = '', $rel = '', $prepend = FALSE)
	{
		$tag = '';
		$tag .= ($href) ? 'href="' . $href . '" ' : '';
		$tag .= ($rel) ? 'rel="' . $rel . '" ' : '';
				
		$this->add($this->link_var, "<link $tag/>\n", $prepend);
	}
	
	/**
	 * Add multiple header links
	 * 
	 * @access 	public
	 * @param 	string 	$links 		one (string) or more (array) sections of link code to add
	 * @param 	bool 	$prepend	whether to prepend the new data or append it
	 * @return 	void
	 */
	function add_link_array ($links, $prepend = FALSE)
	{
		if (!is_array($links[0]))
			$links = array($links);
		
		foreach ($links as $link) {
			
			if (!$link || !is_array($link))
				continue;
			
			$tag = '';
			
			foreach ($link as $property => $value)
				$tag .= "$property=\"$value\" ";
			
			$this->add($this->link_var, "<link $tag/>\n", $prepend);
		}
	}
}

/* End of file View.php */
/* Location: ./libraries/View.php */