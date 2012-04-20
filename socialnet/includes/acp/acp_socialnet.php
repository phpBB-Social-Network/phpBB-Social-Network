<?php
/**
 *
 * @package phpBB Social Network
 * @version 0.6.3
 * @copyright (c) 2010-2012 Kamahl & Culprit http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
 * @package Socialnet
 * @subpackage ACP
 */
class acp_socialnet extends AddOnsHookSystem
{
	/**
	 * @var string $mode Default phpBB variable called from $p_master
	 */
	var $mode = '';
	/**
	 * @var string $u_action Kompletni ACP URL pro odesilani formularu
	 */
	var $u_action;
	/**
	 * @var string $acpPanel_title Zakladni nadpis ACP panelu
	 */
	var $acpPanel_title = '';
	/**
	 * @var string $acpPanel_explain Zakladni popis panelu pod titulkem
	 */
	var $acpPanel_explain = '';
	/**
	 * @var string $motivationPicture URL motivacniho obrazku
	 */
	var $motivationPicture = '';
	/**
	 * @var array $new_config Pole pro nove nastaveni
	 */
	var $new_config = array();

	function main($id, $mode)
	{
		global $config, $db, $user, $auth, $template;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx, $socialnet_root_path;

		$user->add_lang(array('acp/common', 'acp/board', 'mods/socialnet_acp', 'acp/users'));

		$this->tpl_name = 'acp_socialnet';
		$this->page_title = 'ACP_CAT_SOCIALNET';
		$this->acpPanel_title = $user->lang[$this->page_title];

		// Default call function
		$call_module = 'sett_main';
		$block_match = $module_match = array();

		// Prepare function call for ACP panel
		if (preg_match('/^module_(.+)/i', $mode, $module_match))
		{
			$call_module = 'module';
			$module = $module_match[1];
		}
		else if ($mode != 'main')
		{
			$call_module = $mode;
			$module = $mode;
		}
		else
		{
			$module = $mode;
			$mode = 'socialNet';
		}
		// Call specific function of this module dependend on $mode
		$this->$call_module($id, $module);

		// Prepare motivation picture
		$this->motivationPicture = $socialnet_root_path . 'styles/images/' . $mode . '.png';

		$u_action_start = append_sid($phpbb_admin_path . 'index.' . $phpEx, "i={$id}&amp;mode={$mode}");

		$template->assign_vars(array(
			'L_TITLE'					 => $this->acpPanel_title,
			'L_TITLE_EXPLAIN'			 => $this->acpPanel_explain,
			'T_SOCIALNET_IMAGES_PATH'	 => $socialnet_root_path . 'styles/images',
			'S_MOTIONPICTURE'			 => file_exists($this->motivationPicture) ? true : false,
			'S_MOTIONPICTURE_IMG_URL'	 => $this->motivationPicture,
			'S_MODE'					 => $mode,
			'U_FIND_USERNAME'			 => append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=searchuser&amp;form=acp_socialnet_sn_basictools&amp;field=username&amp;select_single=true'),
			'U_ACTION_START'			 => $u_action_start,
			'U_ACTION'					 => $this->u_action
		));
	}

	/**
	 * Loads configuration panel for any module defined in acp
	 * @access public
	 * @param integer $id phpBB variable
	 * @param string $module  Which acp panel module should be loaded into phpBB ACP
	 * @return void
	 */
	function module($id, $module)
	{
		global $config, $user, $auth, $template;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx, $socialnet_root_path;

		$access_module = true;
		$error = array();

		// IS module enabled?
		if ($config['module_' . $module] == 0 || $config['sn_global_enable'] == 0)
		{
			$error[] = $user->lang['SN_MODULE_DISABLED'];
		}

		$module_acp_filename = "{$socialnet_root_path}acp/acp_{$module}.{$phpEx}";
		$module_filename = "{$socialnet_root_path}{$module}.{$phpEx}";

		// Exists ACP function file for this module?
		$s_sn_module = isset($user->lang['SN_MODULE_' . strtoupper($module)]) ? $user->lang['SN_MODULE_' . strtoupper($module)] : '{ SN_MODULE_' . strtoupper($module) . ' }';
		if (!file_exists($module_acp_filename))
		{
			$module_acp_filename = str_replace($phpbb_root_path, '/', $module_acp_filename);
			$access_module = false;
			$error[] = sprintf($user->lang['SN_ACP_MODULE_NOT_ACCESSIBLE'], $s_sn_module, $module_acp_filename);
		}
		// Exists module file for board?
		if (!file_exists($module_filename))
		{
			$module_filename = str_replace($phpbb_root_path, '/', $module_filename);
			$error[] = sprintf($user->lang['SN_MODULE_NOT_ACCESSIBLE'], $s_sn_module, $module_filename);
		}
		else
		{
			include($module_filename);
			if (!class_exists('socialnet_' . $module))
			{
				$error[] = sprintf($user->lang['SN_MODULE_NOT_EXISTS'], $module, $module_filename);
			}
		}

		// IF exist ACP function module try to load
		if ($access_module)
		{
			include($module_acp_filename);
			$acp_module = 'acp_' . $module;
			if (!class_exists($acp_module))
			{
				$error[] = $user->lang['ACP_PANEL_NOT_ACCESSIBLE'];
			}
			else
			{
				$modul = new $acp_module($this);
				$modul->main($id);
			}
		}
		else
		{
			$template->assign_vars(array(
				'S_ERROR'	 => (sizeof($error)) ? true : false,
				'ERROR_MSG'	 => implode('<br />', $error),
			));
		}

		$template->assign_var('B_ACP_SN_' . strtoupper($module), true);

		if ($this->acpPanel_title == '')
		{
			$this->acpPanel_title = isset($user->lang['ACP_SN_' . strtoupper($module) . '_SETTINGS']) ? $user->lang['ACP_SN_' . strtoupper($module) . '_SETTINGS'] : '{ ACP_SN_' . strtoupper($module) . '_SETTINGS }';
		}
		if ($this->acpPanel_explain == '')
		{
			$this->acpPanel_explain = sprintf($user->lang['ACP_SN_MODULE_SETTINGS_EXPLAIN'], isset($user->lang['SN_MODULE_' . strtoupper($module)]) ? $user->lang['SN_MODULE_' . strtoupper($module)] : '{ SN_MODULE_' . strtoupper($module) . ' }');
		}

	}

	/**
	 * Turn on/off global use of Social Network
	 *
	 * Globální zapnutí či vypnutí Social Network<br />
	 * Pokud je potřeba v rámci jednotlivých modulů něco donastavit, bude zavolána funkce v rámci modulu <acp_modul>::acp_sett_main()
	 *
	 * @param integer $id phpBB variable
	 * @return void
	 */
	function sett_main($id)
	{
		global $db, $user, $template, $config, $cache, $socialnet_root_path, $phpEx, $socialnet;

		$display_vars = array(
			'title'	 => 'SETTINGS',
			'vars'	 => array(
				'legend1'			 => 'SETTINGS',
				'sn_global_enable'	 => array('lang' => 'SN_GLOBAL_ENABLE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
			),
		);

		$cfg_array = request_var('config', array('' => ''), true);

		$enabled = isset($cfg_array['sn_global_enable']) ? $cfg_array['sn_global_enable'] : $config['sn_global_enable'];

		$new_value = $enabled ? '1' : '0';

		$cache->purge('modules_acp');
		$cache->purge('modules_ucp');

		$sql = "UPDATE " . MODULES_TABLE . "
				SET module_enabled = '{$new_value}'
				WHERE module_basename = 'socialnet'
					AND module_mode LIKE 'module_%'";
		$db->sql_query($sql);

		// Spustit dodatecne upravy, jednotlivych modulu
		$sql = "SELECT module_mode
				FROM " . MODULES_TABLE . "
				WHERE module_basename = 'socialnet'
					AND module_class = 'acp'
					AND module_mode LIKE 'module_%'";
		$rs = $db->sql_query($sql);
		$modules = $db->sql_fetchrowset($rs);
		$db->sql_freeresult($rs);

		for ($i = 0; $i < count($modules) && isset($modules[$i]); $i++)
		{
			$module = preg_replace('/^module_/si', '', $modules[$i]['module_mode']);
			$acp_filename = "{$socialnet_root_path}acp/acp_{$module}.{$phpEx}";
			if (file_exists($acp_filename))
			{
				include_once($acp_filename);
				$acp_moduleclass = 'acp_' . $module;
				if (method_exists($acp_moduleclass, 'acp_sett_main'))
				{
					eval("$acp_moduleclass::acp_sett_main();");
				}
			}
		}

		$this->_settings($id, 'sn_main', $display_vars);
		$user->add_lang('install');

		if ($config['sn_global_enable'] == 0)
		{
			$user->data['user_im_sound'] = 0;
			$user->data['user_im_soundname'] = 'undefined';
			$socialnet = new socialnet();
		}

		$template->assign_vars(array(
			'B_ACP_SN_MAIN'	 => true
		));

		$socialnet->_version_checker(array('host' => 'update.phpbb3hacks.com', 'directory' => '/socialnet', 'filename' => 'sn_modules.xml'));
		$this->acpPanel_explain = $user->lang['ACP_SN_WELCOME_TEXT'];
	}

	/**
	 * Configure which module of Social Network will be used
	 *
	 * Globální zapnutí či vypnutí jednotlivých modulů Social Network<br />
	 * Pokud je potřeba v rámci jednotlivých modulů něco donastavit, bude zavolána funkce v rámci modulu <acp_modul>::acp_sett_modules()
	 *
	 * @desc function take list available modules from SN_CONFIG_TABLE where config_name LIKE 'module_%'
	 * @param integer $id phpBB variable
	 * @return void
	 */
	function sett_modules($id)
	{
		global $config, $db, $user, $cache, $template, $socialnet_root_path, $phpEx;

		$sql = "SELECT * FROM " . SN_CONFIG_TABLE . " WHERE config_name LIKE 'module_%' ORDER BY config_name";

		$rs = $db->sql_query($sql);

		$modules = array();
		$modules['legend1'] = 'ACP_SN_AVAILABLE_MODULES';
		$module_current = array();
		while ($row = $db->sql_fetchrow($rs))
		{
			//$module_lang = 'SN_' . strtoupper(preg_replace( '/_([0-9])+_/si', '_', $row['config_name'])); // IF config_name of module is with number
			$module_current[$row['config_name']] = $row['config_value'];
			$module_lang = 'SN_' . strtoupper($row['config_name']) . '_NAME';
			$module_lang = isset($user->lang[$module_lang]) ? $module_lang : 'SN_' . strtoupper($row['config_name']);
			$modules[$row['config_name']] = array('lang' => $module_lang, 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true);
			$user->lang[$module_lang . '_EXPLAIN'] = sprintf($user->lang['SN_MODULE_EXPLAIN'], isset($user->lang[$module_lang]) ? $user->lang[$module_lang] : $module_lang);
		}

		$display_vars = array(
			'title'	 => 'ACP_BOARD_SETTINGS',
			'vars'	 => $modules
		);

		$cfg_array = request_var('config', array('' => ''), true);

		$cache->purge('modules_acp');
		$cache->purge('modules_ucp');

		foreach ($cfg_array as $module => $enabled)
		{
			$sql = "UPDATE " . MODULES_TABLE . "
							SET module_display = '{$enabled}'
							WHERE module_basename = 'socialnet'
								AND module_mode LIKE '{$module}%'";
			$db->sql_query($sql);

			if ($module_current[$module] != $enabled)
			{
				// Spustit dodatecne upravy, jednotlivych modulu pri zmene nastaveni
				$acp_moduleclass = 'acp_' . preg_replace('/^module_/si', '', $module);
				if (file_exists("{$socialnet_root_path}acp/{$acp_moduleclass}.{$phpEx}"))
				{
					include_once("{$socialnet_root_path}acp/{$acp_moduleclass}.{$phpEx}");
					if (method_exists($acp_moduleclass, 'acp_sett_modules'))
					{
						$module = new $acp_moduleclass($this);
						$module->acp_sett_modules($enabled);
					}
				}
			}
		}

		// We go through the display_vars to make sure no one is trying to set variables he/she is not allowed to...
		$this->_settings($id, 'sn_modules', $display_vars);

		$this->acpPanel_title = $user->lang['ACP_SN_AVAILABLE_MODULES'] . ' ' . $user->lang['SETTINGS'];
		$this->acpPanel_explain = $user->lang['ACP_SN_AVAILABLE_MODULES_EXPLAIN'];
	}

	/**
	 * Nastavení pro Potvrzovací Box
	 *
	 * Potvrzovací Box je nedílnou součástí všech stránek při použití Social Network
	 *
	 * @param integer $id phpBB variable
	 * @return void
	 */
	function sett_confirmBox($id)
	{
		global $user;

		$this->acpPanel_title = $user->lang['ACP_SN_CONFIRMBOX_SETTINGS'];
		$this->acpPanel_explain = $user->lang['ACP_SN_CONFIRMBOX_SETTINGS_EXPLAIN'];

		$display_vars = array(
			'title'	 => 'ACP_SN_CONFIRMBOX_SETTINGS',
			'vars'	 => array(
				'legend1'			 => 'ACP_SN_CONFIRMBOX_SETTINGS',
				'sn_cb_enable'		 => array('lang' => 'SN_CB_ENABLE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'sn_cb_resizable'	 => array('lang' => 'SN_CB_RESIZABLE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'sn_cb_draggable'	 => array('lang' => 'SN_CB_DRAGGABLE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'sn_cb_modal'		 => array('lang' => 'SN_CB_MODAL', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'sn_cb_width'		 => array('lang' => 'SN_CB_WIDTH', 'validate' => 'string', 'type' => 'text:4:6', 'explain' => true),
			)
		);

		$this->_settings($id, 'sn_cb', $display_vars);
	}

	function blocks_enable($id)
	{
		global $user, $config;

		$blocks = $this->_array_filter_key($config, 'catch_blocks');

		$block_vars = array();
		if (!empty($blocks))
		{
			foreach ($blocks as $key => $value)
			{
				$lang_key = strtoupper($key);
				$user->lang[$lang_key . '_EXPLAIN'] = isset($user->lang['SN_BLOCK_ENABLE_EXPLAIN']) ? $user->lang['SN_BLOCK_ENABLE_EXPLAIN'] : '{ SN_BLOCK_ENABLE_EXPLAIN } %1$s';
				$user->lang[$lang_key . '_EXPLAIN'] = sprintf($user->lang[$lang_key . '_EXPLAIN'], $user->lang[$lang_key]);
				$block_vars[$key] = array('lang' => $lang_key, 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true);
			}
		}

		asort($block_vars);

		$display_vars = array(
			'title'	 => 'SETTINGS',
			'vars'	 => array_merge(array(
				'legend1' => 'SETTINGS', ),
			$block_vars));

		$this->_settings($id, 'sn_blocks', $display_vars);
		$this->acpPanel_title = $user->lang['ACP_SN_BLOCKS_ENABLE'];
		$this->acpPanel_explain = sprintf($user->lang['ACP_SN_BLOCKS_ENABLE_EXPLAIN'], $user->lang['ACP_SN_BLOCKS_CONFIGURATION']);

	}

	function blocks_config($id, $block)
	{
		global $config, $phpbb_root_path, $phpEx, $template, $user;

		$avail_blocks = $this->_array_filter_key($config, 'catch_blocks');
		asort($avail_blocks);
		$block_settings = array();
		$block_first = '';

		foreach ($avail_blocks as $block_name => $enable)
		{
			$blockName = substr($block_name, 3);
			$block_className = 'acp_socialnet_' . $blockName;
			$block_acpfile = "{$phpbb_root_path}socialnet/acp/{$blockName}.{$phpEx}";

			if (!file_exists($block_acpfile))
			{
				continue;
			}
			include($block_acpfile);
			if (!class_exists($block_className))
			{
				continue;
			}

			$block_settings[$blockName] = $block_className;
			if ($block_first == '')
			{
				$block_first = $blockName;
			}

		}

		if (count($block_settings) != 0)
		{

			$current_block = request_var('block', @$_COOKIE[$config['cookie_name'] . '_sn_acp_block']);

			if ($current_block == '')
			{
				$current_block = $block_first;
			}

			$user->set_cookie('sn_acp_block', $current_block, time() + 86400);

			foreach ($block_settings as $blockName => $blockClass)
			{
				$template->assign_block_vars('block_tabs', array(
					'BLOCK'		 => $blockName,
					'TITLE'		 => $user->lang[strtoupper('sn_' . $blockName)],
					'S_SELECTED' => $blockName == $current_block,
				));

				$template->assign_block_vars('sn_tabs', array(
					'HREF'		 => $this->u_action . '&amp;block=' . $blockName,
					'NAME'		 => $user->lang[strtoupper('sn_' . $blockName)],
					'SELECTED'	 => $blockName == $current_block,
				));
			}

			$template->assign_var('SN_CURRENT_BLOCK', $current_block);
			$template->assign_var('U_ACTION', $this->u_action);

			$o_block = new $block_settings[$current_block]($this);
			$o_block->main($id);

			$this->tpl_name = 'acp_socialnet_blocks';
			$this->acpPanel_title = $user->lang['ACP_SN_BLOCKS_CONFIGURATION'];
			$this->acpPanel_explain = $user->lang['ACP_SN_BLOCKS_CONFIGURATION_EXPLAIN'];
		}
	}

	function addons_config($id)
	{
		global $config, $db, $user, $cache, $template, $socialnet_root_path, $phpEx, $phpbb_root_path, $p_master;

		$action = request_var('action', '');
		$addon_location = request_var('addon_location', 0);
		$addon_id = request_var('addon_id', 0);

		$this->acpPanel_title = $user->lang['ACP_SN_ADDONS_CONFIGURATION'];
		$this->acpPanel_explain = $user->lang['ACP_SN_ADDONS_CONFIGURATION_EXPLAIN'];

		switch ($action)
		{
		case "delete":

			if (confirm_box(true))
			{
				$sql = "SELECT addon_name
							FROM " . SN_ADDONS_TABLE . "
							WHERE addon_id = {$addon_id}";
				$rs = $db->sql_query($sql);
				$addon_name = $db->sql_fetchfield($rs);
				$db->sql_freeresult($rs);

				$sql = 'DELETE FROM ' . SN_ADDONS_TABLE . '
					 				  WHERE addon_id = ' . $addon_id;
				$db->sql_query($sql);
				add_log('admin', 'LOG_CONFIG_SN_ADDONS_' . strtoupper($action), 'aaa' . $addon_name);
				redirect($this->u_action);
			}
			else
			{
				confirm_box(false, $user->lang['SN_ADDONS_DELETE_ADDON_CONFIRM']);
				redirect($this->u_action);
			}

			break;

		case "add_addon":

			$addon_name = request_var('addon_name', '', true);

			$template->assign_vars(array(
				'S_NAME'				 => $addon_name,
				'S_ADDONS_CREATE_ADDON'	 => true,
			));

			// Load phps for select
			$dir = $socialnet_root_path . 'addons/';
			$options = '<option value=""></option>';
			if ($dh = opendir($dir))
			{
				while (($file = readdir($dh)) !== false)
				{
					if (strlen($file) >= 3 && (strpos($file, '.php', 1)))
					{
						$template->assign_block_vars('addonphps', array(
							'NAME'	 => $file
						));
					}
				}
				closedir($dh);
			}

			// Load htmls for select
			$dir = $phpbb_root_path . 'styles/' . $user->theme['template_path'] . '/template/socialnet/addons/';
			$options = '<option value=""></option>';
			if ($dh = opendir($dir))
			{
				while (($file = readdir($dh)) !== false)
				{
					if (strlen($file) >= 4 && (strpos($file, '.html', 1)))
					{
						$template->assign_block_vars('addonhtmls', array(
							'NAME'	 => $file
						));
					}
				}
				closedir($dh);
			}

			// Set locations for select
			for ($i = 1; $i <= 6; $i++)
			{
				$template->assign_block_vars('addonlocations', array(
					'ID'	 => $i,
					'NAME'	 => $user->lang['SN_ADDONS_LOCATIONS_' . $i],
				));
			}

			$submit = (isset($_POST['submit'])) ? true : false;

			if ($submit)
			{
				$addon_name = request_var('addon_name', '', true);
				$addon_php = request_var('addon_php', '', true);
				$addon_html = request_var('addon_html', '', true);
				$addon_location = request_var('addon_location', 0);
				$addon_active = request_var('addon_active', 0);

				if (empty($addon_php) && empty($addon_html))
				{
					$message = $user->lang['SN_ADDONS_NO_FILE'] . adm_back_link($this->u_action);
					trigger_error($message, E_USER_WARNING);
				}
				else if (empty($addon_location))
				{
					$message = $user->lang['SN_ADDONS_NO_LOCATION'] . adm_back_link($this->u_action);
					trigger_error($message, E_USER_WARNING);
				}
				else
				{
					$sql = 'SELECT MAX(right_id) AS right_id
											FROM ' . SN_ADDONS_TABLE;
					$result = $db->sql_query($sql);
					$row = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);

					$left_id = $row['right_id'] + 1;
					$right_id = $row['right_id'] + 2;

					$sql = 'INSERT INTO ' . SN_ADDONS_TABLE . ' (addon_php, addon_html, addon_name, addon_location, addon_active, left_id, right_id)
						          VALUES ("' . $addon_php . '","' . $addon_html . '", "' . $addon_name . '", ' . $addon_location . ', ' . $addon_active . ', ' . $left_id . ', ' . $right_id . ')';
					$db->sql_query($sql);
					add_log('admin', 'LOG_CONFIG_SN_ADDONS_' . strtoupper($action), $addon_name);

					$message = sprintf($user->lang['SN_ADDONS_ADDON_ADDED'], $addon_html, $user->lang['SN_ADDONS_LOCATIONS_' . $addon_location]) . adm_back_link($this->u_action);
					trigger_error($message);
				}
			}

			break;

		case "edit_addon":

			// Load phps for select
			$dir = $socialnet_root_path . 'addons/';
			$options = '<option value=""></option>';
			if ($dh = opendir($dir))
			{
				while (($file = readdir($dh)) !== false)
				{
					if (strlen($file) >= 3 && (strpos($file, '.php', 1)))
					{
						$template->assign_block_vars('addonphps', array(
							'NAME'	 => $file
						));
					}
				}
				closedir($dh);
			}

			// Load htmls for select
			$dir = $phpbb_root_path . 'styles/' . $user->theme['template_path'] . '/template/socialnet/addons/';
			$options = '<option value=""></option>';
			if ($dh = opendir($dir))
			{
				while (($file = readdir($dh)) !== false)
				{
					if (strlen($file) >= 4 && (strpos($file, '.html', 1)))
					{
						$template->assign_block_vars('addonhtmls', array(
							'NAME'	 => $file
						));
					}
				}
				closedir($dh);
			}

			// Set locations for select
			for ($i = 1; $i <= 6; $i++)
			{
				$template->assign_block_vars('addonlocations', array(
					'ID'	 => $i,
					'NAME'	 => $user->lang['SN_ADDONS_LOCATIONS_' . $i],
				));
			}

			$sql = 'SELECT *
                  FROM ' . SN_ADDONS_TABLE . '
                    WHERE addon_id = ' . $addon_id;
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);

			$template->assign_vars(array(
				'S_ADDON_ID'			 => $row['addon_id'],
				'S_NAME'				 => $row['addon_name'],
				'S_PHP'					 => $row['addon_php'],
				'S_HTML'				 => $row['addon_html'],
				'S_LOCATION'			 => $row['addon_location'],
				'S_ACTIVE'				 => $row['addon_active'],
				'S_ADDONS_EDIT_ADDON'	 => true,
			));
			$db->sql_freeresult($result);

			$submit = (isset($_POST['submit'])) ? true : false;

			if ($submit)
			{
				$addon_name = request_var('addon_name', '', true);
				$addon_php = request_var('addon_php', '', true);
				$addon_html = request_var('addon_html', '', true);
				$addon_location = request_var('addon_location', 0);
				$addon_active = request_var('addon_active', 0);

				if (empty($addon_php) && empty($addon_html))
				{
					trigger_error($user->lang['SN_ADDONS_NO_FILE'] . adm_back_link($this->u_action), E_USER_WARNING);
				}
				else if (empty($addon_location))
				{
					trigger_error($user->lang['SN_ADDONS_NO_LOCATION'] . adm_back_link($this->u_action), E_USER_WARNING);
				}
				else
				{
					$sql = 'UPDATE ' . SN_ADDONS_TABLE . '
						          SET addon_name = "' . $addon_name . '", addon_php = "' . $addon_php . '", addon_html = "' . $addon_html . '", addon_location = ' . $addon_location . ', addon_active = ' . $addon_active . '
						            WHERE addon_id = ' . $addon_id;
					$db->sql_query($sql);
					add_log('admin', 'LOG_CONFIG_SN_ADDONS_' . strtoupper($action), $addon_name);
					$message = $user->lang['SN_ADDONS_ADDON_EDITED'] . adm_back_link($this->u_action);
					trigger_error($message);
				}
			}

			break;

		case 'move_up':
		case 'move_down':

			$sql = 'SELECT left_id, right_id, addon_name
                  FROM ' . SN_ADDONS_TABLE . '
                    WHERE addon_id = ' . $addon_id;
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);

			$addon_moved_by = acp_move_addon($row, $action);

			add_log('admin', 'LOG_CONFIG_SN_ADDONS_' . strtoupper($action), $row['addon_name'], $addon_moved_by);
			redirect($this->u_action);

			break;

		case "enable_addon":

			$sql = 'SELECT addon_name
                  FROM ' . SN_ADDONS_TABLE . '
                    WHERE addon_id = ' . $addon_id;
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);

			$addon_name = $row['addon_name'];
			$db->sql_freeresult($result);

			$sql = 'UPDATE ' . SN_ADDONS_TABLE . '
                  SET addon_active = 1
                    WHERE addon_id = ' . $addon_id;
			$db->sql_query($sql);

			add_log('admin', 'LOG_CONFIG_SN_ADDONS_' . strtoupper($action), $addon_name);
			$message = $user->lang['SN_ADDONS_ADDON_ENABLED'] . adm_back_link($this->u_action);
			trigger_error($message);

			break;

		case "disable_addon":

			$sql = 'SELECT addon_name
                  FROM ' . SN_ADDONS_TABLE . '
                    WHERE addon_id = ' . $addon_id;
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$addon_name = $row['addon_name'];
			$db->sql_freeresult($result);

			$sql = 'UPDATE ' . SN_ADDONS_TABLE . '
                  SET addon_active = 0
                    WHERE addon_id = ' . $addon_id;
			$db->sql_query($sql);
			add_log('admin', 'LOG_CONFIG_SN_ADDONS_' . strtoupper($action), $addon_name);

			$message = $user->lang['SN_ADDONS_ADDON_DISABLED'] . adm_back_link($this->u_action);
			trigger_error($message);

			break;

		default:

			$sql = 'SELECT *
                  FROM ' . SN_ADDONS_TABLE . '
                      ORDER BY left_id';
			$result = $db->sql_query($sql);

			while ($row = $db->sql_fetchrow($result))
			{
				$template->assign_block_vars('addons', array(
					'ID'			 => $row['addon_id'],
					'NAME'			 => $row['addon_name'],
					'PHP'			 => $row['addon_php'],
					'HTML'			 => $row['addon_html'],
					'U_ACTIVATE'	 => ($row['addon_active'] == 1) ? $this->u_action . '&amp;action=disable_addon&amp;addon_id=' . $row['addon_id'] : $this->u_action . '&amp;action=enable_addon&amp;addon_id=' . $row['addon_id'],
					'ACTIVE'		 => ($row['addon_active'] == 1) ? $user->lang['DISABLE'] : $user->lang['ENABLE'],
					'LOCATION'		 => $user->lang['SN_ADDONS_LOCATIONS_' . $row['addon_location']],
					'U_DELETE'		 => $this->u_action . '&amp;action=delete&amp;addon_id=' . $row['addon_id'],
					'U_EDIT'		 => $this->u_action . '&amp;action=edit_addon&amp;addon_id=' . $row['addon_id'],
					'U_MOVE_UP'		 => $this->u_action . '&amp;action=move_up&amp;addon_id=' . $row['addon_id'],
					'U_MOVE_DOWN'	 => $this->u_action . '&amp;action=move_down&amp;addon_id=' . $row['addon_id'],
				));
			}
			$db->sql_freeresult($result);

			$submit = (isset($_POST['submit'])) ? true : false;

			if ($submit)
			{
				$addon_name = request_var('addon_name', '', true);
				redirect($this->u_action . '&amp;action=add_addon&amp;addon_name=' . $addon_name);
			}

			$template->assign_vars(array(
				'S_ADDONS_LIST'	 => true,
			));
		}

		$template->assign_vars(array(
			'U_ACTION'			 => $this->u_action,
			'B_ACP_SN_ADDONHOOK' => true
		));
	}

	/**
	 * Displays all admin settings for each acp panel
	 * @param mixed $id phpBB $id value
	 * @param string $mode Neccessary for log changes
	 * @param array $display_vars Standard $display_vars array to display configuration options
	 */
	function _settings($id, $mode, $display_vars = array())
	{
		global $db, $user, $auth, $template;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;
		global $cache;

		$form_key = 'acp_' . $mode . '_settings';
		add_form_key($form_key);

		$submit = (isset($_POST['submit'])) ? true : false;

		$display_vars = array_merge(array('title' => 'ACP_IM_SETTINGS'), $display_vars);

		if (isset($display_vars['lang']))
		{
			$user->add_lang($display_vars['lang']);
		}

		$this->new_config = $config;
		$cfg_array = (isset($_REQUEST['config'])) ? utf8_normalize_nfc(request_var('config', array('' => ''), true)) : $this->new_config;
		$error = array();

		// We validate the complete config if whished
		if (sizeOf($display_vars['vars']))
		{
			validate_config_vars($display_vars['vars'], $cfg_array, $error);
		}

		if ($submit && !check_form_key($form_key))
		{
			$error[] = $user->lang['FORM_INVALID'];
		}
		// Do not write values if there is an error
		if (sizeof($error))
		{
			$submit = false;
		}

		// We go through the display_vars to make sure no one is trying to set variables he/she is not allowed to...
		if (sizeOf($display_vars['vars']))
		{
			foreach ($display_vars['vars'] as $config_name => $null)
			{
				if (!isset($cfg_array[$config_name]) || strpos($config_name, 'legend') !== false)
				{
					continue;
				}

				$this->new_config[$config_name] = $config_value = $cfg_array[$config_name];

				if ($submit)
				{
					$this->_set_config($config_name, $config_value);
				}
			}
		}

		if ($submit)
		{
			add_log('admin', 'LOG_CONFIG_' . strtoupper($mode));

			trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}

		$template->assign_vars(array(
			//'S_SETTINGS' => true,
			'S_ERROR'	 => (sizeof($error)) ? true : false,
			'ERROR_MSG'	 => implode('<br />', $error),

			'S_MODE'	 => $this->mode,
			'S_FOUNDER'	 => ($user->data['user_type'] == USER_FOUNDER) ? true : false,

			'U_ACTION'	 => $this->u_action
		));

		// Output relevant page
		if (sizeOf($display_vars['vars']))
		{
			foreach ($display_vars['vars'] as $config_key => $vars)
			{
				if (!is_array($vars) && strpos($config_key, 'legend') === false)
				{
					continue;
				}

				if (strpos($config_key, 'legend') !== false)
				{
					$template->assign_block_vars('options', array(
						'S_LEGEND'	 => true,
						'LEGEND'	 => (isset($user->lang[$vars])) ? $user->lang[$vars] : $vars));

					continue;
				}

				$type = explode(':', $vars['type']);

				$l_explain = '';
				if ($vars['explain'] && isset($vars['lang_explain']))
				{
					$l_explain = (isset($user->lang[$vars['lang_explain']])) ? $user->lang[$vars['lang_explain']] : $vars['lang_explain'];
				}
				else if ($vars['explain'])
				{
					$l_explain = (isset($user->lang[$vars['lang'] . '_EXPLAIN'])) ? $user->lang[$vars['lang'] . '_EXPLAIN'] : '';
				}

				$content = build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars);

				if (empty($content))
				{
					continue;
				}

				$template->assign_block_vars('options', array(
					'KEY'			 => $config_key,
					'TITLE'			 => (isset($user->lang[$vars['lang']])) ? $user->lang[$vars['lang']] : $vars['lang'],
					'S_EXPLAIN'		 => $vars['explain'],
					'TITLE_EXPLAIN'	 => $l_explain,
					'CONTENT'		 => $content,
				));

				unset($display_vars['vars'][$config_key]);
			}
		}

	}

	function _array_filter_key($input, $callback)
	{
		if (!is_array($input))
		{
			trigger_error('array_filter_key() expects parameter 1 to be array, ' . gettype($input) . ' given', E_USER_WARNING);
			return array();
		}

		if (empty($input))
		{
			return $input;
		}

		$filteredKeys = array_filter(array_keys($input), $callback);
		if (empty($filteredKeys))
		{
			return array();
		}

		$_input = array_intersect_key(array_flip($filteredKeys), $input);

		foreach ($_input as $key => $value)
		{
			$_input[$key] = $input[$key];
		}

		return $_input;
	}

	/**
	 * Save configuration values into SOCIALNET CONFIG TABLE (SN_CONFIG_TABLE)
	 * @param string $config_name Config name
	 * @param mixed $config_value Configuration value
	 * @param boolean $is_dynamic Yes if dynamic configuration
	 * @return void
	 */
	function _set_config($config_name, $config_value, $is_dynamic = false)
	{
		global $db, $cache, $config;

		$sql = 'UPDATE ' . SN_CONFIG_TABLE . "
			SET config_value = '" . $db->sql_escape($config_value) . "'
			WHERE config_name = '" . $db->sql_escape($config_name) . "'";
		$db->sql_query($sql);

		if (!$db->sql_affectedrows() && !isset($config[$config_name]))
		{
			$sql = 'INSERT INTO ' . SN_CONFIG_TABLE . ' ' . $db->sql_build_array('INSERT', array(
				'config_name'	 => $config_name,
				'config_value'	 => $config_value,
				'is_dynamic'	 => ($is_dynamic) ? 1 : 0));
			$db->sql_query($sql);
		}

		$config[$config_name] = $config_value;

		if (!$is_dynamic)
		{
			$cache->destroy('config');
		}

	}

}

function catch_blocks($elem)
{
	$filter = '/^sn_block/si';
	return preg_match($filter, $elem);
}

function acp_move_addon($addon_row, $action = 'move_up')
{
	global $db;

	$sql_extend = ($action == 'move_up') ? "right_id < {$addon_row['right_id']} ORDER BY right_id DESC" : "left_id > {$addon_row['left_id']} ORDER BY left_id ASC";

	$sql = 'SELECT *
						FROM ' . SN_ADDONS_TABLE . '
						  WHERE ' . $sql_extend;
	$result = $db->sql_query_limit($sql, 1);

	$target = array();
	while ($row = $db->sql_fetchrow($result))
	{
		$target = $row;
	}
	$db->sql_freeresult($result);

	if (!sizeof($target))
	{
		// The addon is already on top or bottom
		return false;
	}

	/**
	 * $left_id and $right_id define the scope of the nodes that are affected by the move.
	 * $diff_up and $diff_down are the values to substract or add to each node's left_id
	 * and right_id in order to move them up or down.
	 * $move_up_left and $move_up_right define the scope of the nodes that are moving
	 * up. Other nodes in the scope of ($left_id, $right_id) are considered to move down.
	 */
	if ($action == 'move_up')
	{
		$left_id = $target['left_id'];
		$right_id = $addon_row['right_id'];

		$diff_up = $addon_row['left_id'] - $target['left_id'];
		$diff_down = $addon_row['right_id'] + 1 - $addon_row['left_id'];

		$move_up_left = $addon_row['left_id'];
		$move_up_right = $addon_row['right_id'];

	}
	else
	{
		$left_id = $addon_row['left_id'];
		$right_id = $target['right_id'];

		$diff_up = $addon_row['right_id'] + 1 - $addon_row['left_id'];
		$diff_down = $target['right_id'] - $addon_row['right_id'];

		$move_up_left = $addon_row['right_id'] + 1;
		$move_up_right = $target['right_id'];
	}

	$sql = 'UPDATE ' . SN_ADDONS_TABLE . "
						SET left_id = left_id + CASE
							WHEN left_id BETWEEN {$move_up_left} AND {$move_up_right} THEN -{$diff_up}
							ELSE {$diff_down}
						END,
						right_id = right_id + CASE
							WHEN right_id BETWEEN {$move_up_left} AND {$move_up_right} THEN -{$diff_up}
							ELSE {$diff_down}
						END
						WHERE
							left_id BETWEEN {$left_id} AND {$right_id}
							AND right_id BETWEEN {$left_id} AND {$right_id}";
	$db->sql_query($sql);

	return $target['addon_name'];
}

class AddOnsHookSystem
{
	var $aoh_mode = 'addons';
	var $aoh_sub = '';
	var $aoh_u_action = '';

	var $aoh_tabs = array(
		array(
			'sub'		 => 'addon',
			'name'		 => 'SN_ADDONS_ADDONS_MANAGEMENT',
			'visible'	 => true,
		),
		array(
			'sub'		 => 'placeholder',
			'name'		 => 'SN_ADDONS_PLACEHOLDER_MANAGEMENT',
			'visible'	 => true,
		),
		array(
			'sub'		 => 'editaddon',
			'name'		 => 'SN_ADDONS_EDITADDON',
			'visible'	 => false,
		),
		array(
			'sub'		 => 'editplaceholder',
			'name'		 => 'SN_ADDONS_EDITHOLDER',
			'visible'	 => false,
		),
	);

	function addons($id)
	{
		global $template, $user;

		$this->aoh_sub = request_var('sub', 'addon');

		$ph_id = (int) request_var('ph_id', 0);
		$error = array();

		$this->aoh_u_action = $this->u_action . '&amp;sub=' . $this->aoh_sub;
		$aohmode = 'sub_' . $this->aoh_sub;
		$this->$aohmode($id, $error);

		foreach ($this->aoh_tabs as $idx => $tab)
		{
			if (!$tab['visible'] && $this->aoh_sub != $tab['sub'])
			{
				continue;
			}
			$template->assign_block_vars('sn_tabs', array(
				'HREF'		 => $this->u_action . '&amp;sub=' . $tab['sub'] . '&amp;ph_id=' . $ph_id,
				'NAME'		 => isset($user->lang[$tab['name']]) ? $user->lang[$tab['name']] : "{ {$tab['name']} }",
				'SELECTED'	 => $this->aoh_sub == $tab['sub'] ? true : false,
			));

		}

		$template->assign_vars(array(
			'B_SN_ACP_ADDONS_HOOK_MANAGEMENT'	 => true,
			'SN_ADDONS_HOOK_SUBMODE'			 => $this->aoh_sub,
			'U_ACTION_ADDON_HOOK'				 => $this->aoh_u_action,
			'S_ERROR'							 => (sizeof($error)) ? true : false,
			'ERROR_MSG'							 => implode('<br />', $error),
		));

	}

	function sub_addon($id, &$error)
	{
		global $db, $template, $phpEx, $socialnet;

		$socialnet->addons = new sn_core_addons($socialnet);
		//
		// PLACEHOLDRES
		//
		$placeholder = $this->_available_placeholders();

		$templates = $this->_templates();
		//
		// ADD ONS IN CURRENT PLACEHOLDER
		//
		$ph_id = $placeholder[0];
		$sql = "SELECT *
				FROM " . SN_ADDONS_TABLE . "
				WHERE addon_placeholder = {$ph_id}
				ORDER BY addon_order";

		$rs = $db->sql_query($sql);
		$rowset = $db->sql_fetchrowset($rs);
		$db->sql_freeresult($rs);

		for ($i = 0; isset($rowset[$i]); $i++)
		{
			$tpl = $socialnet->addons->get_template_name($rowset[$i]['addon_php'], $rowset[$i]['addon_function'], $placeholder[1]);

			$template->assign_block_vars('addon', array(
				'ROW'			 => $i % 2 == 0 ? 1 : 2,
				'ID'			 => $rowset[$i]['addon_id'],
				'NAME'			 => $rowset[$i]['addon_name'],
				'FUNCTION'		 => $rowset[$i]['addon_function'],
				'SCRIPT'		 => $rowset[$i]['addon_php'],
				'B_ACTIVE'		 => $rowset[$i]['addon_active'],
				'TEMPLATE'		 => $tpl,
				'B_TEMPLATE'	 => in_array($tpl, $templates),

				'U_EDIT'		 => $this->u_action . '&amp;sub=editaddon&amp;ph_id=' . $ph_id . '&amp;ad_id=' . $rowset[$i]['addon_id'],
				'U_DELETE'		 => $this->u_action . '&amp;sub=deleteaddon&amp;ph_id=' . $ph_id . '&amp;ad_id=' . $rowset[$i]['addon_id'],
				'U_DISABLE'		 => $this->u_action . '&amp;sub=enableaddon&amp;ad_en=0&amp;ph_id=' . $ph_id . '&amp;ad_id=' . $rowset[$i]['addon_id'],
				'U_ENABLE'		 => $this->u_action . '&amp;sub=enableaddon&amp;ad_en=1&amp;ph_id=' . $ph_id . '&amp;ad_id=' . $rowset[$i]['addon_id'],
				'U_MOVE_DOWN'	 => $this->u_action . '&amp;sub=moveaddon&amp;ad_old=' . ($rowset[$i]['addon_order']) . '&amp;ad_new=' . ($rowset[$i]['addon_order'] + 1) . '&amp;ph_id=' . $ph_id . '&amp;ad_id=' . $rowset[$i]['addon_id'],
				'U_MOVE_UP'		 => $this->u_action . '&amp;sub=moveaddon&amp;ad_old=' . ($rowset[$i]['addon_order']) . '&amp;ad_new=' . ($rowset[$i]['addon_order'] - 1) . '&amp;ph_id=' . $ph_id . '&amp;ad_id=' . $rowset[$i]['addon_id'],
			));
		}
		$template->assign_var('PHPEX', $phpEx);
		//
		// AVAILABLE ADD ONS
		//
		$this->_available_addons();

	}

	function sub_editaddon($id, &$error)
	{
		global $template, $db, $user;

		$ad_id = (int) request_var('ad_id', 0);

		$submit = (request_var('submit', '', true) == '') ? false : true;

		if ($submit)
		{
			$name = request_var('addon_name', '', true);
			$ph_id = (int) request_var('ph_id', 0);
			$enable = (int) request_var('addon_enable', 0);

			$struct = explode('::', $name);

			$sql_ary = array(
				'addon_placeholder'	 => $ph_id,
				'addon_name'		 => $struct[0],
				'addon_php'			 => $struct[1],
				'addon_function'	 => $struct[2],
				'addon_active'		 => $enable,
			);

			if ($ad_id == 0)
			{
				$sql = "SELECT MAX(addon_order) AS max
						FROM " . SN_ADDONS_TABLE . " WHERE addon_placeholder = {$ph_id}";
				$rs = $db->sql_query($sql);
				$ad_order = (int) $db->sql_fetchfield('max') + 1;
				$db->sql_freeresult($rs);
				$sql = "INSERT INTO " . SN_ADDONS_TABLE . " %1\$s";
				$method = 'INSERT';
				$message = 'ADDED';
				$sql_ary['addon_order'] = $ad_order;
			}
			else
			{
				$sql = "UPDATE " . SN_ADDONS_TABLE . " SET %1\$s WHERE addon_id = {$ad_id}";
				$method = 'UPDATE';
				$message = "EDITED";
			}

			$sql_b = $db->sql_build_array($method, $sql_ary);

			$db->sql_return_on_error(true);
			$success = $db->sql_query(sprintf($sql, $sql_b));
			if ($success)
			{
				trigger_error($user->lang['SN_ADDONS_ADDON_' . $message] . adm_back_link($this->u_action . '&amp;sub=addon&amp;ph_id=' . $ph_id));
			}
			$db->sql_return_on_error(false);
			$error[] = $user->lang['SN_ADDONS_ADDON_' . $message . '_ERROR'];
		}

		if ($ad_id == 0)
		{
			$template->alter_block_array('sn_tabs', array('NAME' => $user->lang['SN_ADDONS_ADDADDON']), true, 'change');
			$s_add = '';
		}
		else
		{
			$sql = "SELECT * FROM " . SN_ADDONS_TABLE . " WHERE addon_id = {$ad_id}";
			$rs = $db->sql_query($sql);
			$row = $db->sql_fetchrow($rs);
			$db->sql_freeresult($rs);
			$s_add = $row['addon_name'] . '::' . $row['addon_php'] . '::' . $row['addon_function'];
			$template->assign_vars(array(
				'SN_ADDON_ENABLED'	 => $row['addon_active'],
				'SN_ADDON_ID'		 => $row['addon_id'],
			));
		}

		$addon = $this->_available_addons($s_add);
		$placeholder = $this->_available_placeholders();

		$template_exists = $this->_templates();

	}

	function sub_moveaddon($id, &$error)
	{
		global $db, $user;

		$ph_id = (int) request_var('ph_id', 0);
		$or_nw = (int) request_var('ad_new', -1);
		$or_ld = (int) request_var('ad_old', -1);
		$ad_id = (int) request_var('ad_id', 0);

		if ($ph_id != 0 && $or_nw != - 1 && $ad_id != 0 && $or_ld != - 1)
		{
			$sql = "UPDATE " . SN_ADDONS_TABLE . " SET addon_order = {$or_ld} WHERE addon_placeholder = $ph_id AND addon_order = {$or_nw}";
			$db->sql_query($sql);
			$sql = "UPDATE " . SN_ADDONS_TABLE . " SET addon_order = {$or_nw} WHERE addon_placeholder = $ph_id AND addon_id = {$ad_id}";
			$db->sql_query($sql);
		}
		$this->aoh_sub = 'addon';
		$this->sub_addon($id, $error);
	}

	function sub_enableaddon($id, &$error)
	{
		global $db;

		$ad_id = (int) request_var('ad_id', 0);
		$ad_en = (int) request_var('ad_en', -1);

		if ($ad_id != 0 && $ad_en != - 1)
		{
			$sql = "UPDATE " . SN_ADDONS_TABLE . " SET addon_active = '{$ad_en}' WHERE addon_id = '{$ad_id}'";
			$db->sql_query($sql);

		}

		$this->aoh_sub = 'addon';
		$this->sub_addon($id, $error);
	}

	function sub_deleteaddon($id, &$error)
	{
		global $db, $user;

		$ph_id = (int) request_var('ph_id', 0);
		$ad_id = (int) request_var('ad_id', 0);

		if ($ph_id == 0 || $ad_id == 0)
		{
			$this->sub_addon($id, $error);
			return;
		}

		if (confirm_box(true))
		{
			$order = (int) request_var('ad_order', 0);

			$name = request_var('name', '', true);
			$script = request_var('script', '');
			$block = request_var('block', '');

			$sql = "UPDATE " . SN_ADDONS_TABLE . " SET addon_order = addon_order - 1 WHERE addon_order > '{$order}' AND addon_placeholder = '{$ph_id}'";
			$db->sql_query($sql);

			$sql = "DELETE FROM " . SN_ADDONS_TABLE . " WHERE addon_id = '{$ad_id}'";
			$db->sql_query($sql);

			trigger_error(sprintf($user->lang['SN_ADDONS_ADDON_DELETED'], $name, $script, $block) . adm_back_link($this->u_action . '&amp;sub=addon&amp;ph_id=' . $ph_id));
		}

		$sql = "SELECT *
		FROM " . SN_ADDONS_TABLE . ", " . SN_ADDONS_PLACEHOLDER_TABLE . "
		WHERE addon_id = '{$ad_id}' AND ph_id = '{$ph_id}'";
		$rs = $db->sql_query($sql);
		$row = $db->sql_fetchrow($rs);
		$db->sql_freeresult($rs);

		$data = array(
			'name'		 => $row['addon_name'],
			'script'	 => $row['ph_script'],
			'block'		 => $row['ph_block'],
			'sub'		 => 'deleteaddon',
			'ph_id'		 => $ph_id,
			'ad_id'		 => $ad_id,
			'ad_order'	 => $row['addon_order'],
		);

		confirm_box(false, vsprintf($user->lang['SN_ADDONS_ADDON_DELETE_CONFIRM'], $data), build_hidden_fields($data));

	}

	function sub_placeholder($id, &$error)
	{
		global $template, $user, $db, $socialnet;

		$socialnet->addons = new sn_core_addons($socialnet);

		$sql = "SELECT *
		FROM " . SN_ADDONS_PLACEHOLDER_TABLE . "
		ORDER BY ph_script, ph_block";
		$rs = $db->sql_query($sql);
		$rowset = $db->sql_fetchrowset($rs);
		$db->sql_freeresult($rs);

		for ($i = 0; isset($rowset[$i]); $i++)
		{
			$template->assign_block_vars('placeholder', array(
				'ROW'			 => $i % 2 == 0 ? '1' : '2',
				'ID'			 => $rowset[$i]['ph_id'],
				'SCRIPT'		 => $rowset[$i]['ph_script'],
				'BLOCK'			 => $rowset[$i]['ph_block'],
				'PLACEHOLDER'	 => $socialnet->addons->get_placeholder_name($rowset[$i]['ph_script'], $rowset[$i]['ph_block']),
				'U_EDIT'		 => $this->u_action . '&amp;sub=editplaceholder&amp;ph_id=' . $rowset[$i]['ph_id'],
				'U_DELETE'		 => $this->u_action . '&amp;sub=deleteplaceholder&amp;ph_id=' . $rowset[$i]['ph_id'],
			));
		}

	}

	function sub_editplaceholder($id, &$error)
	{
		global $template, $db, $user;

		$ph_id = (int) request_var('ph_id', 0);
		$script_name = request_var('script_name', '');
		$block = request_var('block', '');

		$submit = (request_var('submit', '', true) == '') ? false : true;

		if ($submit)
		{
			if (empty($script_name) || empty($block))
			{
				$error[] = $user->lang['SN_ADDONS_PLACEHOLDER_EMPTY_FIELD'];
			}
			else
			{
				$sql_ary = array(
					'ph_script'	 => $script_name,
					'ph_block'	 => $block
				);
				if ($ph_id == 0)
				{
					$sql = "INSERT INTO " . SN_ADDONS_PLACEHOLDER_TABLE . " " . $db->sql_build_array('INSERT', $sql_ary);
					$message = $user->lang['SN_ADDONS_PLACEHOLDER_ADDED'];
					$error[] = $user->lang['SN_ADDONS_PLACEHOLDER_DUPLICATE'];
				}
				else
				{
					$sql = "UPDATE " . SN_ADDONS_PLACEHOLDER_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_ary) . " WHERE ph_id = {$ph_id}";
					$message = $user->lang['SN_ADDONS_PLACEHOLDER_EDITED'];
					$error[] = $user->lang['SN_ADDONS_PLACEHOLDER_ERREDIT'];
				}

				$db->sql_return_on_error(true);
				$success = $db->sql_query($sql);

				if ($success)
				{
					trigger_error($message . adm_back_link($this->u_action . '&amp;sub=placeholder'));
				}

			}
		}

		if ($ph_id == 0)
		{
			$template->alter_block_array('sn_tabs', array('NAME' => $user->lang['SN_ADDONS_ADDHOLDER']), true, 'change');
		}
		else
		{
			$sql = "SELECT ph_script, ph_block
					FROM " . SN_ADDONS_PLACEHOLDER_TABLE . "
					WHERE ph_id = {$ph_id}";
			$rs = $db->sql_query($sql);
			$row = $db->sql_fetchrow($rs);
			$db->sql_freeresult($rs);
			$script_name = $row['ph_script'];
			$block = $row['ph_block'];
		}

		$template->assign_vars(array(
			'PLACEHOLDER_ID'			 => $ph_id,
			'PLACEHOLDER_SCRIPT_NAME'	 => $script_name,
			'PLACEHOLDER_BLOCK'			 => $block,
		));

		//die('NOT implemented yet<br />' . __FILE__ . ' ' . __LINE__);

	}

	function sub_deleteplaceholder($id, &$error)
	{
		global $db, $user;

		$ph_id = (int) request_var('ph_id', 0);

		if ($ph_id == 0)
		{
			$this->sub_placeholder($id, $error);
			return;
		}

		if (confirm_box(true))
		{

			$sql = "DELETE FROM " . SN_ADDONS_PLACEHOLDER_TABLE . " WHERE ph_id = '{$ph_id}'";

			$db->sql_query($sql);

			trigger_error($user->lang['SN_ADDONS_PLACEHOLDER_DELETED'] . adm_back_link($this->u_action . '&amp;sub=placeholder'));
		}

		$data = array(
			'sub'	 => 'deleteplaceholder',
			'ph_id'	 => $ph_id,
		);

		$sql = "SELECT ph_script, ph_block
				FROM " . SN_ADDONS_PLACEHOLDER_TABLE . "
				WHERE ph_id = '{$ph_id}'";
		$rs = $db->sql_query($sql);
		$row = $db->sql_fetchrow($rs);
		$db->sql_freeresult($rs);

		confirm_box(false, vsprintf($user->lang['SN_ADDONS_PLACEHOLDER_DELETE_CONFIRM'], $row), build_hidden_fields($data));

	}

	function _available_placeholders($ph_id = 0)
	{
		global $template, $db, $user;

		if ($ph_id == 0)
		{
			$ph_id = (int) request_var('ph_id', 0);
		}

		$sql = "SELECT * FROM " . SN_ADDONS_PLACEHOLDER_TABLE . " AS ph
		ORDER BY ph.ph_script, ph.ph_block";

		$rs = $db->sql_query($sql);
		$rowset = $db->sql_fetchrowset($rs);
		$db->sql_freeresult($rs);

		$ph_script = '';
		$return = '';
		for ($i = 0; isset($rowset[$i]); $i++)
		{
			$row = $rowset[$i];
			if ($ph_id == 0)
			{
				$ph_id = $row['ph_id'];
			}

			if ($row['ph_id'] == $ph_id)
			{
				$return = $row['ph_script'] . '::' . $row['ph_block'];
			}

			if ($ph_script != $row['ph_script'])
			{
				$template->assign_block_vars('ph_script', array(
					'SCRIPT' => $row['ph_script']
				));
				$ph_script = $row['ph_script'];
			}
			$template->assign_block_vars('ph_script.ph_block', array(
				'SCRIPT'	 => $row['ph_script'],
				'BLOCK'		 => $row['ph_block'],
				'ID'		 => $row['ph_id'],
				'SELECTED'	 => $row['ph_id'] == $ph_id,
				'U_LINK'	 => $this->u_action . '&amp;ph_id=' . $row['ph_id']
			));
		}

		$template->assign_var('SN_ADDON_CURRENT_PLACEHOLDER', $ph_id);
		return array($ph_id, $return);
	}

	function _available_addons($s_add = '')
	{
		global $template, $phpbb_root_path, $phpEx;

		$addon_folder = "{$phpbb_root_path}socialnet/addons/";
		$dir = opendir($addon_folder);

		$av_addon = array();
		while (($file = readdir($dir)) !== false)
		{
			if (preg_match("/^addon_(.+)\.{$phpEx}$/i", $file, $match))
			{
				include_once($addon_folder . $file);

				$className = 'addon_' . $match[1];

				if (class_exists($className) && method_exists($className, 'install'))
				{
					$class = new $className(null);
					$addons = $class->install();

					$av_addon[$addons['name'] . '::addon_' . $match[1]] = $addons;
				}
			}
		}

		closedir($dir);

		ksort($av_addon);

		if ($s_add == '')
		{
			$s_add = request_var('addon_name', '', true);
		}

		if (!empty($av_addon))
		{
			foreach ($av_addon as $idx => $addons)
			{
				$name = explode('::', $idx);
				$template->assign_block_vars('av_file', array(
					'NAME'	 => $name[0],
					'FILE'	 => $name[1],
				));
				if (!empty($addons['addon']))
				{
					foreach ($addons['addon'] as $fnc => $title)
					{
						$template->assign_block_vars('av_file.av_fnc', array(
							'FUNCTION'	 => $fnc,
							'NAME'		 => $title,
							'SELECTED'	 => $s_add == "{$title}::{$name[1]}::{$fnc}" ? true : false,
						));
					}
				}
			}
		}

		return $s_add;
	}

	function _templates()
	{
		global $template, $phpbb_root_path, $user;

		$template_short_dir = 'socialnet/addons/';
		$template_dir = "{$phpbb_root_path}styles/{$user->theme['template_path']}/template/{$template_short_dir}";
		$dir = @opendir($template_dir);
		$available_template_dir = false;
		if (!$dir)
		{
			$template_dir = "{$phpbb_root_path}styles/prosilver/template/{$template_short_dir}";
			$dir = opendir($template_dir);
			$available_template_dir = true;
		}

		$templates = array();
		while (($file = readdir($dir)) !== false)
		{
			if ($file == '.' || $file == '..')
			{
				continue;
			}
			$templates[] = $file;
			$template->assign_block_vars('template', array('FILE' => $file));
		}
		closedir($dir);

		$template->assign_vars(array(
			'SN_ADDON_TEMPLATE_FOLDER'				 => $template_dir,
			'SN_ADDON_TEMPLATE_SH_FOLDER'			 => $template_short_dir,
			'B_SN_ADDON_TEPLATE_FOLDER_NOT_EXIST'	 => $available_template_dir,
			'L_SN_ADDON_TEMPLATE_FOLDER_NOT_EXIST'	 => sprintf($user->lang['SN_ADDON_TEMPLATE_FOLDER_NOT_EXIST'], $user->theme['theme_name']),
		));

		return $templates;
	}

}

?>