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
 * Admin designs controller
 *
 * @author 		James Gifford
 * @copyright 	Copyright (c) 2009, James Gifford
 * @link 		http://jamesgifford.com
 * @filesource
 */
class Designs extends Admin_Controller
{
	/**
	 * Constructor
	 */
	function __construct ()
	{
		parent::__construct();
	}
	
	/**
	 * 
	 * 
	 * @access 	public
	 * @return 	void
	 */
	function index ()
	{
		$this->add_design();
	}
	
	/**
	 * Add a new design
	 * 
	 * @access 	public
	 * @return 	void
	 */
	function add_design ()
	{
		$categories = $this->design_model->get_categories();
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules(array(
			array('field' => 'title', 		'label' => lang('add_design_label_title'), 			'rules' => 'trim|required'),
			array('field' => 'url', 		'label' => lang('add_design_label_url'), 			'rules' => 'trim|required'),
			array('field' => 'description',	'label' => lang('add_design_label_description'),	'rules' => 'trim'),
			array('field' => 'screenshot', 	'label' => lang('add_design_label_screenshot'), 	'rules' => 'trim|callback__upload_screenshot')
		));
		
		if ($this->form_validation->run())
		{
			$design = array(
				'title' 		=> $this->input->post('title'),
				'name' 			=> urlencode(str_replace(' ', '_', strtolower($this->input->post('title')))),
				'url' 			=> urlencode($this->input->post('url')),
				'description' 	=> $this->input->post('description'),
				'added_date' 	=> date('Y-m-d H:i:s')
			);
			
			// TODO: Update this error message
			if (!($design_id = $this->design_model->insert('designs', $design)))
				show_error('oops');
			
			$file_data = $this->upload->data();
			
			// Rename the uploaded file with the new design_id
			@rename(APPPATH . 'content/images/screenshots/' . $file_data['file_name'], APPPATH . 'content/images/screenshots/' . $design_id . $file_data['file_ext']);
			
			$this->design_model->set_screenshot($design_id, $design_id . $file_data['file_ext']);
			
			// Get the color data from the uploaded screenshot
			$this->_import_image($design_id, $design_id . $file_data['file_ext']);
			
			// Add tags from each category
			foreach ($categories as $category)
			{
				$tags = explode(',', $this->input->post($category['name']));
				
				foreach ($tags as $tag)
				{
					$tag = trim($tag);
					
					if (!($tag_id = $this->design_model->find_tag_by_title($tag)))
					{
						$new_tag = array(
							'category_id' 	=> $category['id'],
							'title' 		=> $tag,
							'name' 			=> urlencode(str_replace(' ', '_', strtolower($tag))),
							'added_date'	=> date('Y-m-d H:i:s')
						);
						
						// TODO: Update this error message
						if (!($tag_id = $this->design_model->insert('designs_tags', $new_tag)))
						{
							show_error('oops again');
						}
					}
					
					$link = array(
						'design_id' 	=> $design_id,
						'tag_id' 		=> $tag_id,
						'added_date' 	=> date('Y-m-d H:i:s')
					);
					
					// TODO: Update this error message
					if (!($link_id = $this->design_model->insert('designs_tags_link', $link)))
					{
						show_error('another oops');
					}
				}
			}
		}
		
		$this->view->add_part('content', 'designs/add_design_form', array('categories' => $categories));
		$this->view->display();
	}
	
	/* Private methods
	-----------------------------------------------------------------------------*/
	
	/**
	 * Upload a screenshot for a design
	 * 
	 * @access 	private
	 * @return 	bool
	 */
	function _upload_screenshot ()
	{
		$this->form_validation->set_message('_upload_screenshot', 'You must include a screenshot.');
		
		if ( ! $_FILES['screenshot']['name'])
		{
			return FALSE;
		}
		
		// TODO: ajust the error messages for 'screnshot' instead of 'file'
		$this->form_validation->set_message('_upload_screenshot', 'An error occurred while uploading the screenshot.');
		
		$this->load->library('upload', array(
			'upload_path' 		=> APPPATH . 'content/images/screenshots/',
			'allowed_types' 	=> 'gif|jpg|png',
			'max_size'			=> '1000',
			'max_width'			=> '1024',
			'max_height'		=> '768'));
		
		return $this->upload->do_upload('screenshot');
	}
	
	/**
	 * Parse an image an import its color data into the database
	 * 
	 * @access 	private
	 * @param 	int 	$design_id 	the id of the design to import the image for
	 * @param 	string 	$file 		the local path to the uploaded file
	 * @return 	void
	 */
	function _import_image ($design_id, $file)
	{
		set_time_limit(0); 	// Temporary. To be replaced once expected processing time is determined.
		
		// Load the image through ImageMagick
		$image = new Imagick(APPPATH . 'content/images/screenshots/' . $file);
		$geometry = $image->getImageGeometry();
		
		$colors = array();
		
		// Loop through each pixel in the image
		for ($x = 0; $x < $geometry['width']; $x++)
		{
			for ($y = 0; $y < $geometry['height']; $y++)
			{
				// Get the pixel's color and convert it into a hex value
				$pixel = $image->getImagePixelColor($x, $y);
				$color = $pixel->getColor();
				$color = sprintf('%02x%02x%02x', $color['r'], $color['g'], $color['b']);
				
				$colors[$color] = isset($colors[$color]) ? $colors[$color] + 1 : 1;
			}
		}
		
		arsort($colors);
		
		// Increasing the sensitivity will increase the number of colors stored per image
		$sensitivity = 0.00005;
		
		// Determine the number of pixels a color must occur in order to be stored
		$threshold = max(ceil(($geometry['width'] * $geometry['height']) * $sensitivity), 1);
		
		foreach ($colors as $color => $count)
		{
			// Don't store the color if it doesn't occur in enough pixels
			if ($count < $threshold)
			{
				break;
			}
			
			// Split the color value into red, green, and blue components
			$int_value = hexdec($color);
			$rgb_value = array("red" => 0xFF & ($int_value >> 0x10), "green" => 0xFF & ($int_value >> 0x8), "blue" => 0xFF & $int_value);
			
			// Store the color data in the database
			$this->db->set('design_id', $design_id)->set('red', $rgb_value['red'])->set('green', $rgb_value['green'])->set('blue', $rgb_value['blue']);
			$this->db->insert('colors');
		}
	}
}

/* End of file designs.php */
/* Location: controllers/admin/designs.php */
