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
 * Design model
 * 
 * Database model used for the gallery
 *
 * @package 	CSSDB
 * @category 	Libraries
 */
class Design_model extends MY_Model
{
	function __construct ()
	{
		parent::__construct();
		
		// Define the list of tables used in this model
		$this->tables = array(
			'designs' 			=> 'designs',
			'tags' 				=> 'tags',
			'designs_link' 		=> 'designs_to_tags',
			'categories' 		=> 'categories',
			'categories_link' 	=> 'categories_to_designs_to_tags',
			'colors' 			=> 'colors',
			'screenshots' 		=> 'screenshots');
	}
	
	/* Selects
	-----------------------------------------------------------------------------*/
	
	/**
	 * Search for designs matching a value(s) from a single category
	 * 
	 * @access 	public
	 * @param 	array 	$filters 	list of filters to apply to the results
	 * @param 	int 	$limit 		maximum number of records to return
	 * @param 	int 	$offset 	number of records to skip
	 * @return 	array
	 */
	function get_designs ($filters = NULL, $limit = NULL, $offset = NULL)
	{
		$screenshot_string = 'FALSE';
		$where_string = '';
		
		if (isset($filters['tags']))
		{
			$screenshot_string = 'screenshots.tag_id IN (' . implode(',', $filters['tags']) . ')';
			$part_one = $part_two = '';
			
			foreach ($filters['tags'] as $index => $tag_id)
			{	
				if (!$index)
				{
					$part_one .= "designs.id IN (SELECT link$index.design_id AS id FROM ".$this->tables['designs_link']." link$index";
					$part_two = " WHERE link$index.tag_id = $tag_id";
				}
				else
				{
					$part_one .= " INNER JOIN ".$this->tables['designs_link']." link$index ON link0.design_id = link$index.design_id";
					$part_two .= " AND link$index.tag_id = $tag_id";
				}
			}
			
			$where_string .= "$part_one $part_two) AND ";
		}
		
		if (isset($filters['color']))
		{
			$tolerance = 0.1;
			
			$search_int = hexdec($filters['color']);
			$search_rgb = array("red" => 0xFF & ($search_int >> 0x10), "green" => 0xFF & ($search_int >> 0x8), "blue" => 0xFF & $search_int);
			
			foreach ($search_rgb as $name => $value)
			{
				$search_rgb['upper_'.$name] = min((int)($search_rgb[$name] * (1 + $tolerance)), 255);
				$search_rgb['lower_'.$name] = max((int)($search_rgb[$name] / (1 + $tolerance)), 0);
			}
			
			$where_string .= "designs.id IN (SELECT design_id AS id FROM ".$this->tables['colors']." WHERE 
				red >= {$search_rgb['lower_red']} AND red <= {$search_rgb['upper_red']} AND 
				green >= {$search_rgb['lower_green']} AND green <= {$search_rgb['upper_green']} AND 
				blue >= {$search_rgb['lower_blue']} AND blue <= {$search_rgb['upper_blue']}) AND ";
		}
		
		if (isset($filters['search_text']))
		{
			$search = strtolower(trim($filters['search_text']));
			$where_string .= '(';
			
			if ( ! isset($filters['search_fields']) || ! is_array($filters['search_fields']))
				$filters['search_fields'][] = 'title';
			
			if ($filters['search_match'] == 'exact')
			{
				foreach ($filters['search_fields'] as $field)
					$where_string .= "designs.$field = '%$search%' AND ";
				
				$where_string = substr($where_string, 0, strrpos($where_string, ' AND')) . ') AND ';
			}
			else
			{
				foreach ($filters['search_fields'] as $field)
					foreach (explode(' ', $search) as $string)
						$where_string .= "designs.$field LIKE '%$string%' " . ($filters['search_match'] == 'any' ? 'OR' : 'AND') . ' ';
				
				$where_string = substr($where_string, 0, strrpos($where_string, ' ' . ($filters['search_match'] == 'any' ? 'OR' : 'AND'))) . ') AND ';
			}
		}
		
		$query = "
			SELECT designs.title, coalesce(screenshots.screenshot, designs.screenshot) AS screenshot 
			FROM ".$this->tables['designs_link']." link, ".$this->tables['designs']." designs 
				LEFT OUTER JOIN ".$this->tables['screenshots']." screenshots 
				ON screenshots.design_id = designs.id 
				AND ($screenshot_string) 
			WHERE $where_string designs.id = link.design_id 
			GROUP BY designs.id ";
		
		if ( ! isset($filters['sort_by']) || ! $filters['sort_by'])
		{
			$filters['sort_by'] = 'added_date';
			
			if ( ! isset($filters['sort_direction']) || ! $filters['sort_direction'])
				$filters['sort_direction'] =  'ASC';
		}
		
		$query .= "ORDER BY designs." . $filters['sort_by'] . ' ' . strtoupper($filters['sort_direction']);
		$query .= $limit ? ($offset ? " LIMIT $limit OFFSET $offset" : " LIMIT $limit") : '';
		
		return $this->query_rows($this->db->query($query));
	}
	
	/**
	 * Get the total number of designs
	 * 
	 * @access 	public
	 * @return 	int
	 */
	function count_designs ()
	{
		return $this->db->count_all_results($this->tables['designs']);
	}
	
	/**
	 * Get all tags with categories
	 * 
	 * @access 	public
	 * @return 	array
	 */
	function get_tags_with_category ()
	{
		$query = $this->db->query("
			SELECT dt.tag_id, t.name AS tag_name, t.title AS tag_title, COALESCE(cdt.category_id, 0) AS category_id, 
				COALESCE(c.name, '') AS category_name, COALESCE(c.title, '') AS category_title, COUNT(t.id) AS count, 
				(CASE WHEN COUNT(t.id) > 5 THEN 'ultra_popular' WHEN COUNT(t.id) > 4 THEN 'very_popular' WHEN COUNT(t.id) > 3 THEN 'popular' WHEN COUNT(t.id) > 2 THEN 'somewhat_popular' WHEN COUNT(t.id) > 1 THEN 'not_very_popular' ELSE 'not_popular' END) AS popularity 
			FROM tags t, designs_to_tags dt 
			LEFT OUTER JOIN categories_to_designs_to_tags cdt 
				ON dt.id = cdt.design_to_tag_id 
			LEFT OUTER JOIN categories c 
				ON COALESCE(cdt.category_id, 0) = c.id 
			WHERE dt.tag_id = t.id 
			GROUP BY t.id, cdt.category_id 
			ORDER BY c.sort DESC, c.name ASC, t.sort DESC, t.name ASC
		");
		
		return $this->query_rows($query, array());
	}
	
	/**
	 * Get all tags with counts
	 * 
	 * @access 	public
	 * @return 	array
	 */
	function get_tags_with_count ()
	{
		$query = $this->db->query("
			SELECT t.id AS tag_id, t.name AS tag_name, t.title AS tag_title, COUNT(t.id) AS count, 
				(CASE WHEN COUNT(t.id) > 5 THEN 'ultra_popular' WHEN COUNT(t.id) > 4 THEN 'very_popular' WHEN COUNT(t.id) > 3 THEN 'popular' WHEN COUNT(t.id) > 2 THEN 'somewhat_popular' WHEN COUNT(t.id) > 1 THEN 'not_very_popular' ELSE 'not_popular' END) AS popularity 
			FROM tags t, designs_to_tags dt 
			WHERE dt.tag_id = t.id 
			GROUP BY t.id  
			ORDER BY t.sort DESC, t.name ASC
		");
		
		return $this->query_rows($query, array());
	}
	
	/**
	 * Check for the existence of a tag by its name
	 * 
	 * @access 	public
	 * @param 	string 	$title 	the title of the tag to check
	 * @return 	bool
	 */
	function find_tag_by_title ($title)
	{
		$this->db->where('lower(title)', strtolower($title));
		
		return $this->query_value($this->db->get($this->tables['tags']), 'id');
	}
	
	/**
	 * Get categories
	 * 
	 * @access 	public
	 * @return 	array
	 */
	function get_categories ()
	{
		$this->db->order_by('sort', 'desc');
		
		return $this->query_rows($this->db->get($this->tables['categories']), array());
	}
	
	
	/* Updates
	-----------------------------------------------------------------------------*/
	
	/**
	 * Set the screenshot for a design
	 * 
	 * @access 	public
	 * @param 	int 	$design_id 	the id of the design
	 * @param 	string 	$file_name 	the name of the screenshot file
	 * @return 	void
	 */
	function set_screenshot ($design_id, $file_name)
	{
		$this->db->where('id', $design_id);
		$this->db->set('screenshot', $file_name);
		$this->db->update($this->tables['designs']);
	}
}

/* End of file design_model.php */
/* Location: models/design_model.php */
