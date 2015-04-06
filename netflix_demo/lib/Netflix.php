<?php

// Include the OAuthSimple library for oauth calls
include_once('OAuthSimple.php');

/**
 * A simple, sample Netflix library
 */
class Netflix
{
	/* Public properties
	-----------------------------------------------------------------------------*/
	
	/**
	 * The Netflix user's user id
	 * @access 	public
	 * @var 	string
	 */
	public $user_id;
	
	/**
	 * Information from the last loaded movie
	 * @access 	public
	 * @var 	object
	 */
	public $movie;
	
	/**
	 * The last title searched for
	 * @access 	public
	 * @var 	string
	 */
	public $search_terms = '';
	
	/**
	 * The current offset of the search results
	 * @access 	public
	 * @var 	int
	 */
	public $search_offset = 0;
	
	
	/* Private properties
	-----------------------------------------------------------------------------*/
	
	/**
	 * The Netflix api key
	 * @access 	private
	 * @var 	string
	 */
	private $_api_key;
	
	/**
	 * The Netflix api shared secret
	 * @access 	private
	 * @var 	string
	 */
	private $_api_secret;
	
	/**
	 * The base uri for api calls
	 * @access 	private
	 * @var 	string
	 */
	private $_api_uri_base = 'http://api.netflix.com/';
	
	/**
	 * Selection of URIs used in the library
	 * @access 	private
	 * @var 	array
	 */
	private $_uris = array(
		'access' 	=> 'oauth/access_token',
		'request' 	=> 'oauth/request_token',
		'titles'	=> 'catalog/titles'
	);
	
	
	/* "Magic" methods
	-----------------------------------------------------------------------------*/
	
	/**
	 * Constructor
	 * @access 	public
	 * @return 	void
	 */
	public function __construct ($params = array())
	{
		if (isset($params['api_key']))
		{
			$this->set_api_key($params['api_key']);
		}
		
		if (isset($params['api_secret']))
		{
			$this->set_api_secret($params['api_secret']);
		}
	}
	
	
	/* Public methods
	-----------------------------------------------------------------------------*/
	
	/**
	 * Search for movies by title
	 * @access 	public
	 * @param 	string 	$terms 		the search terms
	 * @param 	int 	$offset 	(optional) the number of results to skip over
	 * @return 	void
	 */
	public function search_by_title ($terms, $offset = 0)
	{
		$terms = $terms ? $terms : $this->search_terms;
		$offset = $offset ? $offset : $this->search_offset;
		
		$this->search_terms = $terms;
		$this->search_offset = $offset;
		
		$arguments = array(
			'term'			=> $this->search_terms,
			'expand'		=> 'formats,synopsis',
			'start_index' 	=> $this->search_offset,
			'max_results'	=> '1',
			'output'		=> 'json'
		);
		
		$oauth = new OAuthSimple();
		$signed = $oauth->sign(array(
			'path' 			=> $this->_api_uri_base . $this->_get_uri('titles'),
			'parameters'	=> $arguments,
			'signatures'	=> array(
				'consumer_key'	=> $this->_api_key,
				'shared_secret'	=> $this->_api_secret
			)
		));
				
		$result = $this->_do_curl($signed['signed_url']);
		
		$this->movie = $result->catalog_titles->catalog_title;
	}
	
	/**
	 * Get the next movie in the search results
	 * @access 	public
	 * @return 	void
	 */
	public function next_movie ()
	{
		$this->search_by_title(NULL, ++$this->search_offset);
	}
	
	/**
	 * Get the previous movie in the search results
	 * @access 	public
	 * @return 	void
	 */
	public function prev_movie ()
	{
		$this->search_by_title(NULL, --$this->search_offset);
	}
	
	/**
	 * Get the current movie's title
	 * @access 	public
	 * @param 	bool 	$short 	whether to get the short or regular title
	 * @return 	string
	 */
	public function get_movie_title ($short = FALSE)
	{
		if ( ! isset($this->movie) || ! $this->movie)
		{
			return FALSE;
		}
		
		return $short ? $this->movie->title->short : $this->movie->title->regular;
	}
	
	/**
	 * Get the current movie's average rating
	 * @access 	public
	 * @return 	float
	 */
	public function get_movie_rating ()
	{
		if ( ! isset($this->movie) || ! $this->movie)
		{
			return FALSE;
		}
		
		return $this->movie->average_rating;
	}
	
	/**
	 * Get the Netflix user id
	 * @access 	public
	 * @return 	string
	 */
	public function get_user_id ()
	{
		return $this->user_id;
	}
	
	/**
	 * Set the Netflix user id
	 * @access 	public
	 * @param 	string 	$user_id 	the user id of the Netflix user
	 * @return 	void
	 */
	public function set_user_id ($user_id)
	{
		$this->user_id = $user_id;
	}
	
	/**
	 * Set the Netflix api key
	 * @access 	public
	 * @param 	string 	$key 	the api key given by Netflix
	 * @return 	void
	 */
	public function set_key ($key)
	{
		$this->_api_key = $key;
	}
	
	/**
	 * Set the Netflix api ahared secret
	 * @access 	public
	 * @param 	string 	$secret 	the api shared secret given by Netflix
	 * @return 	void
	 */
	public function set_secret ($secret)
	{
		$this->_api_secret = $secret;
	}
	
	
	/* Private methods
	-----------------------------------------------------------------------------*/
	
	/**
	 * Get a specific uri
	 * @access 	private
	 * @param 	string 	$name 	the name of the uri
	 * @return 	string
	 */
	private function _get_uri ($name)
	{
		return isset($this->_uris[$name]) ? $this->_uris[$name] : FALSE;
	}
	
	/**
	 * Execute a curl request
	 * @access 	private
	 * @param 	string 	$url 	the url of the request
	 * @return	string
	 */
	private function _do_curl ($url)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt($curl, CURLOPT_SETTIMEOUT, 2);
		
		$buffer = curl_exec($curl);
				
		if (curl_errno($curl))
		{
			die ("<div id=\"error\">A connection error occurred. Please try again.</div>");
		}
		
		return json_decode($buffer);
	}
}
