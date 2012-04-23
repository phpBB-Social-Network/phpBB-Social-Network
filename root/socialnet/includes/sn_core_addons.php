<?php
/**
 *
 * @package phpBB Social Network
 * @version 0.6.3
 * @copyright (c) phpBB Social Network Team 2010-2012 http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

if (!defined('SOCIALNET_INSTALLED') || !defined('IN_PHPBB'))
{
	return;
}

class sn_core_addons
{
	var $p_master = null;

	/**
	 * Constructor
	 * - load existing modules using cache
	 */
	public function sn_core_addons(&$p_master)
	{
		global $cache, $template, $phpbb_root_path, $user, $config;

		$this->p_master = $p_master;
	}

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
									AND ph.ph_script = '{$script}' {$sql_add}
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

			if ( $addon['addon_active'] == 0)
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

			if ( !file_exists("{$phpbb_root_path}styles/{$user->theme['template_path']}/template/socialnet/addons/{$addonTemplate}"))
			{
				continue;
			}
			
			include_once("{$phpbb_root_path}socialnet/addons/{$addon['addon_php']}.{$phpEx}");

			$addonClass = new $addon['addon_php']($this->p_master);

			$addonClass->$addon['addon_function']();

			$template->set_filenames(array($addonTemplate => 'socialnet/addons/' . $addonTemplate));
			$content[$placeHolder] .= $this->p_master->get_page($addonTemplate);
		}

		$template->assign_vars($content);
	}

	public function get_placeholder_name($script, $block)
	{
		$script = strtoupper($script);
		$block = strtoupper($block);

		$script = preg_replace('/[^A-Z0-9]/si', '_', $script);
		$block = preg_replace('/[^A-Z0-9]/si', '_', $block);

		return sprintf(SN_ADDONS_PLACEHOLDER_CONTENT, $script, $block);
	}

	public function get_template_name($file, $function, $script, $block = null)
	{
		if ($block == null && strrpos($script, '::') !== false)
		{
			$ph = explode('::', $script);
			$script = $ph[0];
			$block = $ph[1];
		}
		$implode = array(preg_replace('/^addon_/si', '', $file), $function, $script, $block);
		$template = implode('_', $implode) . '.html';
		
		return $template;
	}
}
?>