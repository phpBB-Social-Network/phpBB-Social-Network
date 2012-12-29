<?php
/**
 * SN Core Addons
 *
 * @author		Culprit <jankalach@gmail.com>
 * @author		Kamahl <kamahl19@gmail.com>
 *
 * @package		phpBB Social Network
 * @version		0.7.0
 * @since		0.6.2
 * @copyright		(c) phpBB Social Network Team 2010-2012 http://phpbbsocialnetwork.com
 * @license		http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

/**
 * @ignore
 */
if (!defined('SOCIALNET_INSTALLED') || !defined('IN_PHPBB'))
{
	return;
}

/**
 * Core Addons class
 *
 * @package phpBB-Social-Network
 */
class sn_core_addons
{
	/**
	 * $p_master
	 *
	 * @todo add proper description
	 *
	 * @var object $p_master
	 */
	var $p_master = null;

	/**
	 * Constructor
	 *
	 * Loads existing modules using cache
	 *
	 * @todo remove globals, they are not needed at all
	 *
	 * @access	public
	 *
	 * @param 	mixed	$p_master
	 */
	public function sn_core_addons(&$p_master)
	{
		global $cache, $template, $phpbb_root_path, $user, $config;

		$this->p_master = $p_master;
	}

	/**
	 * Loads specified addons
	 *
	 * @access	public
	 *
	 * @global	object	$db			phpBB database object
	 * @global	string	$phpEx			php extension
	 * @global	string	$socialnet_root_path	socialnet root path string
	 */
	public function get()
	{
		global $db, $phpEx, $socialnet_root_path;

		// load only enabled addons
		$sql = 'SELECT addon_name
				FROM ' . SN_ADDONS_TABLE . '
				WHERE addon_active = 1';
		$result = $db->sql_query($sql);

		while( $row = $db->sql_fetchrow($result) )
		{
			$addon_name = $row['addon_name'];

			// addon file myst be located in /socialnet/addons/<addon_name>/<addon_name>.php
			$addon_file = $socialnet_root_path . 'addons/' . $addon_name . '/' . $addon_name . '.' . $phpEx;

			if ( file_exists($addon_file) )
			{
				include($addon_file);

				if ( class_exists($addon_name) )
				{
					// add addon to $socialnet->addon, creating new instance sending $socialnet as parametre
					$this->p_master->addon->$addon_name = new $addon_name($this->p_master);
				}
			}
		}
		$db->sql_freeresult($result);
	}

	/**
	 * Returns template name for specified placeholder
	 *
	 * @access public
	 *
	 * @param 	string	$script	script name
	 * @param 	string	$block 	block name
	 *
	 * @return	string	generated placeholder name
	 */
	public function get_placeholder_name($script, $block)
	{
		$script = strtoupper($script);
		$block = strtoupper($block);

		$script = $this->get_namefortemplate($script);
		$block = $this->get_namefortemplate($block);

		return sprintf(SN_ADDONS_PLACEHOLDER_CONTENT, $script, $block);
	}

	/**
	 * Generates string to fit template variable pattern
	 *
	 * Template variables can be only in form [A-Z0-9_], therefore we need to replace all other characters
	 * by "_"
	 *
	 * @access	public
	 *
	 * @param 	string	$name	string to be modified
	 *
	 * @return	string	generated string to fit template variable pattern
	 */
	public function get_namefortemplate($name)
	{
		return preg_replace('/[^A-Z0-9]/si', '_', $name);
	}

	/**
	 * Returns name of template file
	 *
	 * @todo verify description for $block
	 *
	 * @access	public
	 *
	 * @param 	string	$file    	addon's php filename
	 * @param 	string	$function	addon function to be called
	 * @param 	string	$script  	script name on which addon should be called
	 * @param 	string	$block   	placeholder's block
	 *
	 * @return	string	template filename (appended by .html)
	 */
	public function get_template_name($file, $function, $script, $block = null)
	{
		if ($block == null && strrpos($script, '::') !== false)
		{
			$ph = explode('::', $script);
			$script = $ph[0];
			$block = $ph[1];
		}
		//$implode = array(preg_replace('/^addon_/si', '', $file), $function, $script, $block); // Long template file name
		$implode = array(preg_replace('/^addon_/si', '', $file), $function); //Short template file name
		$template = implode('_', $implode) . '.html';

		return $template;
	}
}
?>