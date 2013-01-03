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
	 * Addon directory
	 *
	 * @var	string	$addon_directory
	 */
	var $addon_directory = '';

	/**
	 * Constructor
	 *
	 * Registers hooks
	 *
	 * @access public
	 *
	 * @param	object	$socialnet	socialnet instance
	 */
	function laughing_copyright($socialnet)
	{
		$this->socialnet =& $socialnet;
		$this->addon_directory = $this->socialnet->get_addon_directory('laughing_copyright');

		$socialnet->hook->add_action('sn.copyright_append', array($this, 'make_copyright_smile'));
	}

	/**
	 * Direct access
	 *
	 * @access public
	 *
	 */
	function direct_access()
	{
		die('Never enought of smile? :)');
	}

	/**
	 * Installs addon
	 *
	 * @access public
	 *
	 * @return
	 */
	function install()
	{
	}

	/**
	 * Uninstalls addon
	 *
	 * @access public
	 *
	 * @return
	 */
	function uninstall()
	{
	}

	/**
	 * Tells name of the addon
	 *
	 * @access static
	 *
	 * @return	string	name of the addon
	 */
	function addon_name()
	{
		return 'Laughing Copyright';
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