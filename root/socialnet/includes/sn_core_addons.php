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
	 * @param 	mixed 	$script	script name
	 * @param 	mixed 	$block 	specifies placeholder's block
	 *
	 * @global	object	$template		phpBB template object
	 * @global	object	$user			phpBB user object
	 * @global	object	$db			phpBB database object
	 * @global	string	$phpbb_root_path	phpBB root path string
	 * @global	string	$phpEx			php extension
	 */
	public function get($script = null, $block = null)
	{
		global $template, $user, $db, $phpbb_root_path, $phpEx;

		$sql_add = '';

		if ($script == null)
		{
			$script = $this->p_master->script_name;
		}

		if ($block != null && is_string($block))
		{
			$sql_add = "AND ph.ph_block = '{$block}'";
		}

		$sql = "SELECT ad.*, ph.ph_script, ph.ph_block
							FROM " . SN_ADDONS_TABLE . " AS ad, " . SN_ADDONS_PLACEHOLDER_TABLE . " AS ph
								WHERE ad.addon_placeholder = ph.ph_id
									AND ( ph.ph_script = '{$script}' OR ph.ph_script = 'allpages' ) {$sql_add}
							ORDER BY ph.ph_block, ad.addon_order";
		$rs = $db->sql_query($sql);
		$rowset = $db->sql_fetchrowset($rs);
		$db->sql_freeresult($rs);

		$content = array();
		$blockName = '';
		$placeHolder = '';
		for ($ad_i = 0; isset($rowset[$ad_i]); $ad_i++)
		{
			$addon = $rowset[$ad_i];

			if ($addon['addon_active'] == 0)
			{
				continue;
			}

			if ($blockName != $addon['ph_block'])
			{
				$placeHolder = $this->get_placeholder_name($addon['ph_script'], $addon['ph_block']);

				if (!isset($content[$placeHolder]))
				{
					$content[$placeHolder] = '';
				}
			}
			$addonTemplate = $this->get_template_name($addon['addon_php'], $addon['addon_function'], $addon['ph_script'], $addon['ph_block']);

			include_once("{$phpbb_root_path}socialnet/addons/{$addon['addon_php']}.{$phpEx}");

			$addonClass = new $addon['addon_php']($this->p_master);

			$addonClass->$addon['addon_function']($addon['ph_script'], $addon['ph_block']);

			$template->set_filenames(array($addonTemplate => 'socialnet/addons/' . $addonTemplate));

			$tpl_script = $this->get_namefortemplate($addon['ph_script']);
			$tpl_block = $this->get_namefortemplate($addon['ph_block']);

			$template->assign_vars(array(
				'SN_ADDONS_CURRENT_SCRIPT'		 => $tpl_script,
				'SN_ADDONS_CURRENT_BLOCK'		 => $tpl_block,
				'SN_ADDONS_CURRENT_PLACEHOLDER'	 => "{$tpl_script}_{$tpl_block}",
			));

			$content[$placeHolder] .= $this->p_master->get_page($addonTemplate);
		}

		$template->assign_vars($content);
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