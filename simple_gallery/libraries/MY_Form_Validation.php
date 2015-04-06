<?php

/**
 * 
 */
class MY_Form_Validation extends CI_Form_validation
{
	/**
	 * Constructor
	 */
	function __construct ($rules = array())
	{
		$this->_error_prefix = '<div class="form_error">';
		$this->_error_suffix = '</div>';
		
		parent::CI_Form_validation($rules);
	}
}
