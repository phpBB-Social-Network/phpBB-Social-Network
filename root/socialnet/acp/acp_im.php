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

		$display_vars = array(
			'title'	 => 'ACP_IM_SETTINGS',
			'vars'	 => array(
				'legend1'						 => 'ACP_SN_IM_SETTINGS',
				'im_only_friends'				 => array('lang' => 'IM_ONLY_FRIENDS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'im_url_new_window'				 => array('lang' => 'IM_URL_IN_NEW_WINDOW', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'im_msg_purged_automatic_time'	 => array('lang' => 'IM_AUTOMATIC_PURGING_MESSAGES', 'validate' => 'int:0', 'type' => 'text:3:4', 'append' => ' ' . $user->lang['DAYS'], 'explain' => true),
				'im_colour_username'			 => array('lang' => 'SN_COLOUR_NAME', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'legend2' => 'SN_IM_CHECKTIMES',
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
}

?>