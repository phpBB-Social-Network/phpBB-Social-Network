<?php
/**
 *
 * @package phpBB Social Network
 * @version 0.6.3
 * @copyright (c) phpBB Social Network Team 2010-2012 http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

if (!defined('SOCIALNET_INSTALLED') && !defined('IN_PHPBB'))
{
	return;
}

class acp_im extends socialnet
{
	var $p_master = null;

	function acp_im(&$p_master)
	{
		$this->p_master =& $p_master;
	}

	function main($id)
	{
		global $user, $db, $template, $config;

		$manage = request_var('manage', 'settings');
		$template->assign_block_vars('sn_tabs', array(
			'HREF'		 => $this->p_master->u_action,
			'SELECTED'	 => $manage == 'settings' ? true : false,
			'NAME'		 => $user->lang['SETTINGS']
		));

		$template->assign_block_vars('sn_tabs', array(
			'HREF'		 => $this->p_master->u_action . '&amp;manage=smiley',
			'SELECTED'	 => $manage == 'smiley' ? true : false,
			'NAME'		 => isset($user->lang['SN_IM_MANAGE_SMILIES']) ? $user->lang['SN_IM_MANAGE_SMILIES'] : '{ SN_IM_MANAGE_SMILIES }'
		));

		$template->assign_var('S_ACP_SN_IM_TAB', $manage);

		switch ($manage)
		{
			case 'settings':
				$this->main_settings($id);
				break;
			case 'smiley':
				$this->main_smilies($id);
				break;
		}

	}

	function main_settings($id)
	{
		global $user, $db, $template, $config;

		$display_vars = array(
			'title'	 => 'ACP_IM_SETTINGS',
			'vars'	 => array(
				'legend1'						 => 'ACP_SN_IM_SETTINGS',
				'im_only_friends'				 => array('lang' => 'IM_ONLY_FRIENDS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'im_url_new_window'				 => array('lang' => 'IM_URL_IN_NEW_WINDOW', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'im_msg_purged_automatic_time'	 => array('lang' => 'IM_AUTOMATIC_PURGING_MESSAGES', 'validate' => 'int:0', 'type' => 'text:3:4', 'append' => ' ' . $user->lang['DAYS'], 'explain' => true),
				'im_colour_username'			 => array('lang' => 'SN_COLOUR_NAME', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'legend2'						 => 'SN_IM_CHECKTIMES',
				'im_checkTime_min'				 => array('lang' => 'IM_CHECK_TIME_MIN', 'validate' => 'int:2:30', 'type' => 'text:3:4', 'append' => ' ' . $user->lang['SECONDS'], 'explain' => true),
				'im_checkTime_max'				 => array('lang' => 'IM_CHECK_TIME_MAX', 'validate' => 'int:60', 'type' => 'text:3:4', 'append' => ' ' . $user->lang['SECONDS'], 'explain' => true),
			));

		$this->p_master->_settings($id, 'sn_im', $display_vars);

		$sn_im_purge_days = (int) request_var('sn_im_purge_days', 1);
		$sn_im_close_days = (int) request_var('sn_im_close_days', 1);

		$sn_im_purge_days = ($sn_im_purge_days < 1) ? 1 : $sn_im_purge_days;
		$sn_im_close_days = ($sn_im_close_days < 1) ? 1 : $sn_im_close_days;

		$template->assign_vars(array(
			'SN_IM_PURGE_DAYS'	 => $sn_im_purge_days,
			'SN_IM_CLOSE_DAYS'	 => $sn_im_close_days,
			'SN_IM_AUTO_PURGE'	 => $config['im_msg_purged_automatic_time'] != 0 ? true : false,
		));

		// PURGE ALL DELIVERED MESSAGES
		$purge_all_messages = request_var('sn_im_purge_all_msg', '');
		if ($purge_all_messages != '')
		{
			$time = $sn_im_purge_days * 24 * 3600;
			$sql = "DELETE FROM " . SN_IM_TABLE . " WHERE recd = 1 AND sent < " . (time() - $time);
			$db->sql_query($sql);

			$sql = "UPDATE " . SN_CONFIG_TABLE . " SET config_value = '" . (time() - $time) . "' WHERE config_name = 'im_msg_purged_time'";
			$db->sql_query($sql);

			add_log('admin', 'LOG_CONFIG_SN_IM_MSG_PURGED');
			trigger_error($user->lang['IM_PURGE_ALL_MSG_SUCCESS'] . adm_back_link($this->p_master->u_action));
		}

		// PURGE ALL OPEN CHATBOXES
		$purge_all_chatboxes = request_var('sn_im_close_all_chatbox', '');
		if ($purge_all_chatboxes != '')
		{
			$time = $sn_im_close_days * 24 * 3600;
			$sql = "DELETE FROM " . SN_IM_CHATBOXES_TABLE . " WHERE starttime < " . (time() - $time);
			$db->sql_query($sql);

			add_log('admin', 'LOG_CONFIG_SN_IM_CHATBOXES_CLOSED');
			trigger_error($user->lang['IM_PURGE_ALL_CHATBOX_SUCCESS'] . adm_back_link($this->p_master->u_action));
		}
	}

	function main_smilies($id)
	{
		global $user, $db, $template, $config, $phpbb_root_path;

		$user->add_lang('acp/posting');

		$config_name = 'sn_im_smilies_not_allowed';
		$sql = 'SELECT *
					FROM ' . SMILIES_TABLE . '
					ORDER BY smiley_order';
		$result = $db->sql_query($sql, 3600);

		$smilies = $smilies_ids = array();
		while ($row = $db->sql_fetchrow($result))
		{
			if (empty($smilies[$row['smiley_url']]))
			{
				$smilies[$row['smiley_url']] = $row;
				$smilies_ids[] = $row['smiley_id'];
			}
		}
		$db->sql_freeresult($result);

		$submit = (request_var('submit', '', true) == '') ? false : true;

		if ($submit)
		{
			$allowed = request_var('sn_im_smiley', array(0 => 0));

			$array_diff = array_diff($smilies_ids, $allowed);
			array_unshift($array_diff, sizeOf($smilies_ids) == sizeOf($allowed) ? 'X' : 'Y');
			$config[$config_name] = implode(',', $array_diff);
			$sql = "UPDATE " . SN_CONFIG_TABLE . " SET config_value = '{$config[$config_name]}' WHERE config_name = '{$config_name}'";
			$rs = $db->sql_query($sql);
			if ($db->sql_affectedrows($rs) == 0)
			{
				$sql = "INSERT INTO " . SN_CONFIG_TABLE . " (config_name,config_value) VALUES ('{$config_name}', '{$config[$config_name]}')";
				$db->sql_query($sql);
			}
		}

		if (!isset($config[$config_name]) || empty($config[$config_name]))
		{
			$config[$config_name] = implode(',', array());
		}

		$smiley_allowed = explode(',', $config[$config_name]);

		$root_path = (defined('PHPBB_USE_BOARD_URL_PATH') && PHPBB_USE_BOARD_URL_PATH) ? generate_board_url() . '/' : $phpbb_root_path;

		foreach ($smilies as $smiley_url => $row)
		{
			$is_allowed = !in_array($row['smiley_id'], $smiley_allowed);
			$template->assign_block_vars('sn_smiley', array(
				'ID'		 => $row['smiley_id'],
				'CODE'		 => $row['code'],
				'IMAGE'		 => $root_path . $config['smilies_path'] . '/' . $row['smiley_url'],
				'EMOTION'	 => $row['emotion'],
				'WIDTH'		 => $row['smiley_width'],
				'HEIGHT'	 => $row['smiley_height'],
				'ALLOWED'	 => $is_allowed,
			));
		}
		$db->sql_freeresult($result);

	}
}

?>