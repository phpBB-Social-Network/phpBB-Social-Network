<?php
/**
 *
 * @package phpBB Social Network
 * @version 0.7.0
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
		global $user, $db, $template, $config, $phpbb_root_path, $phpEx, $cache;

		$user->add_lang('acp/posting');

		$sql = 'SELECT ps.*, ss.smiley_allowed
					FROM ' . SMILIES_TABLE . ' AS ps LEFT OUTER JOIN ' . SN_SMILIES_TABLE . ' AS ss ON ps.smiley_id = ss.smiley_id
					ORDER BY smiley_order';
		$rs = $db->sql_query($sql);

		$smilies = array();

		while ($row = $db->sql_fetchrow($rs))
		{
			$smilies[$row['smiley_id']] = $row;
		}

		$db->sql_freeresult($rs);

		$submit = (request_var('submit', '', true) == '') ? false : true;

		if ($submit)
		{
			// PURGE CACHED SMILIES
			$cached = 'SELECT ps.*, ss.smiley_allowed
					FROM ' . SMILIES_TABLE . ' AS ps, ' . SN_SMILIES_TABLE . ' AS ss
					WHERE ps.smiley_id = ss.smiley_id AND ss.smiley_allowed = 1
					ORDER BY smiley_order';
			$cache->remove_file('sql_' . md5($cached) . '.' . $phpEx);
			
			// DELETE ALL RECORDS
			$sql = "DELETE FROM " . SN_SMILIES_TABLE;
			$db->sql_query($sql);

			// INSERT ALL RECORDS
			$smileys = request_var('sn_im_smiley', array(0 => 0));

			if (!empty($smilies))
			{
				foreach ($smilies as $idx => $row)
				{
					$smilies[$idx]['smiley_allowed'] = $allowed = in_array($row['smiley_id'], $smileys) ? 1 : 0;
					$sql = "INSERT INTO " . SN_SMILIES_TABLE . " (smiley_id, smiley_allowed) VALUES ('{$row['smiley_id']}','{$allowed}')";
					$db->sql_query($sql);
				}
			}

		}

		$root_path = (defined('PHPBB_USE_BOARD_URL_PATH') && PHPBB_USE_BOARD_URL_PATH) ? generate_board_url() . '/' : $phpbb_root_path;

		foreach ($smilies as $idx => $row)
		{
			$template->assign_block_vars('sn_smiley', array(
				'ID'		 => $row['smiley_id'],
				'CODE'		 => $row['code'],
				'IMAGE'		 => $root_path . $config['smilies_path'] . '/' . $row['smiley_url'],
				'EMOTION'	 => $row['emotion'],
				'WIDTH'		 => $row['smiley_width'],
				'HEIGHT'	 => $row['smiley_height'],
				'ALLOWED'	 => $row['smiley_allowed'],
			));
		}

	}
}

?>