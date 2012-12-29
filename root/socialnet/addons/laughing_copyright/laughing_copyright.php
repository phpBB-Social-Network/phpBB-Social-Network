<?php

/**
 * Laughing copyright addon
 *
 * @package	phpBB-Social-Network
 * @author	Senky
 * @version 1.0.0
 * @access public
 */
class laughing_copyright
{
	/**
	 * Socialnet instance
	 *
	 * @var	object	$socialnet
	 */
	var $socialnet = null;

	/**
	 * Constructor
	 *
	 * Registers hooks
	 *
	 * @param	object	$socialnet	socialnet instance
	 */
	function laughing_copyright($socialnet)
	{
		$this->socialnet = $socialnet;

		$socialnet->hook->add_action('sn.copyright_append', array($this, 'make_copyright_smile'));
	}

	/**
	 * Direct access
	 *
	 */
	function direct_access()
	{
	}

	/**
	 * Installs addon
	 *
	 * @return
	 */
	function install()
	{
	}

	/**
	 * Uninstalls addon
	 *
	 * @return
	 */
	function uninstall()
	{
	}


	/**
	 * Makes copyright smile
	 *
	 * @param 	string	$string	additional_copy string
	 * @return	array 	additional_copy string with smiley
	 */
	function make_copyright_smile($string)
	{
		return array(
	 		'additional_copy'	=> $string . ' :)',
		);
	}
}