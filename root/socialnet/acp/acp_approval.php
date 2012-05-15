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

class acp_approval extends socialnet
{
	var $p_master = null;

	function acp_approval(&$p_master)
	{
		$this->p_master =& $p_master;
	}

	function main($id)
	{
		global $user, $template, $db;

		$display_vars = array(
			'title'	 => 'ACP_FMS_SETTINGS',
			'vars'	 => array(
				'legend1'				 => 'ACP_SN_APPROVAL_SETTINGS',
				'fas_friendlist_limit'	 => array('lang' => 'SN_FAS_FRIENDS_PER_PAGE', 'validate' => 'int:10', 'type' => 'text:3:5', 'explain' => true),
				'fas_colour_username'	 => array('lang' => 'SN_COLOUR_NAME', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
			)
		);

		$this->p_master->_settings($id, 'sn_fms', $display_vars);

		$user_tools = array(
			'deleted_user'	 => 'SN_FMS_PURGE_ALL_FRIENDS_DELETED_USERS',
		);

		$submit = isset($_POST['sn_basictools_submit']) ? true : false;

		$action = request_var('action', '');
		if ($submit)
		{
			$username = utf8_normalize_nfc(request_var('username', '', true));

			if ($action == 'deleted_user')
			{
				$sql = "SELECT user_id FROM " . USERS_TABLE;
				$rs = $db->sql_query($sql);
				$rowset = $db->sql_fetchrowset($rs);
				$db->sql_freeresult($rs);

				$user_ids = array();
				foreach ($rowset as $idx => $user_d)
				{
					$user_ids[] = $user_d['user_id'];
				}
				unset($rowset);

				$sql_deleted_users = $db->sql_in_set('user_id', $user_ids, true);
				$sql = "DELETE FROM " . ZEBRA_TABLE . " WHERE " . $db->sql_in_set('zebra_id', $user_ids, true) . " OR " . $sql_deleted_users;

				$db->sql_query($sql);

				$sql = "SELECT fms_gid FROM " . SN_FMS_GROUPS_TABLE . " WHERE " . $sql_deleted_users;

				$rs = $db->sql_query($sql);
				$rowset = $db->sql_fetchrowset($rs);
				$group_ids = array();
				foreach ($rowset as $idx => $group)
				{
					$group_ids[] = $group['fms_gid'];
				}

				if (!empty($group_ids))
				{
					$sql_delete_groups = $db->sql_in_set('fms_gid', $group_ids);

					$sql = "DELETE FROM " . SN_FMS_USERS_GROUP_TABLE . " WHERE " . $sql_delete_groups;
					$db->sql_query($sql);

					$sql = "DELETE FROM " . SN_FMS_GROUPS_TABLE . " WHERE " . $sql_delete_groups;
					$db->sql_query($sql);
				}
				
				add_log( 'admin', 'LOG_CONFIG_SN_FMS_BASICTOOLS_DELETED_USER');
				trigger_error($user->lang[$user_tools[$action]]);
			}
		}

		foreach ($user_tools as $key => $lang)
		{
			$template->assign_block_vars('sn_basictools', array(
				'MODE'	 => $key,
				'NAME'	 => isset($user->lang[$lang]) ? $user->lang[$lang] : "{ $lang }",
			));
		}
	}

	/**
	 * Dodatečné změny při globalního nastavení
	 *
	 * @access private
	 * @return @void
	 */
	function acp_sett_main()
	{
		global $db;

		$sql = "SELECT module_enabled, module_display
							FROM " . MODULES_TABLE . "
								WHERE module_basename = 'socialnet'
									AND module_mode = 'module_approval_friends'";
		$rs = $db->sql_query($sql);
		$row = $db->sql_fetchrow($rs);

		$sql = "UPDATE " . MODULES_TABLE . "
							SET module_display = " . ($row['module_enabled'] && $row['module_display'] ? '0' : '1') . "
								WHERE module_basename = 'zebra'
									AND module_mode = 'friends'";
		$db->sql_query($sql);
	}

	/**
	 * Dodatečné změny při povolení, či zakázání modulu
	 *
	 * @access private
	 * @param boolean $enabled Zda je modul povolen či nepovolen
	 * @return void
	 */
	function acp_sett_modules($enabled)
	{
		global $db, $phpbb_root_path, $phpEx;
		$sql = "UPDATE " . MODULES_TABLE . "
							SET module_display = " . ($enabled == 1 ? '0' : '1') . "
								WHERE module_basename = 'zebra'
									AND module_mode = 'friends'";
		$db->sql_query($sql);

		if ($enabled)
		{
			include_once("{$phpbb_root_path}/includes/functions_privmsgs.{$phpEx}");

			$sql = "SELECT user_id, zebra_id
								FROM " . ZEBRA_TABLE . "
									WHERE friend = 1";
			$rs = $db->sql_query($sql);
			$zebra_1 = $db->sql_fetchrowset($rs);

			$sql = "SELECT z1.user_id , z1.zebra_id
								FROM " . ZEBRA_TABLE . " AS z1, " . ZEBRA_TABLE . " AS z2
									WHERE z1.friend = 1
										AND z1.zebra_id = z2.user_id
										AND z1.user_id = z2.zebra_id";
			$rs = $db->sql_query($sql);
			$zebra_2 = $db->sql_fetchrowset($rs);

			if (!empty($zebra_1))
			{
				foreach ($zebra_1 as $idx => $zebra)
				{
					if (!in_array($zebra, $zebra_2))
					{
						$sql = "UPDATE " . ZEBRA_TABLE . " SET friend = 0, approval = 1 WHERE " . $db->sql_build_array('SELECT', $zebra);
						$db->sql_query($sql);
						$this->send_pm($zebra['user_id'], $zebra['zebra_id']);
					}
				}
			}
		}
		else
		{
			$sql = "UPDATE " . ZEBRA_TABLE . " SET friend =1, approval = 0 WHERE approval = 1";
			$db->sql_query($sql);
		}
	}

	/**
	 * Send PMs when FMS is enabled
	 *
	 * @param integer $send_from
	 * @param integer $send_to
	 * @return void
	 */
	function send_pm($send_from, $send_to)
	{
		global $db, $config, $phpbb_root_path, $phpEx, $user;

		$sql = "SELECT u.username, u1.user_lang
							FROM " . USERS_TABLE . " AS u, " . USERS_TABLE . " AS u1
								WHERE u.user_id = {$send_from}
									AND u1.user_id = {$send_to}" ;
		$rs = $db->sql_query($sql);
		$row = $db->sql_fetchrow($rs);
		$username = $row['username'];
		$user_lang = $row['user_lang'] != '' ? $row['user_lang'] : $config['default_lang'];
		$lang = array();

		include("{$phpbb_root_path}language/{$user_lang}/ucp.{$phpEx}");
		include("{$phpbb_root_path}language/{$user_lang}/mods/socialnet.{$phpEx}");

		$my_subject = sprintf($lang['SN_FAS_REQUEST_ADDED'], $username);
		$message = sprintf($lang['SN_FAS_REQUEST_ADDED_MESSAGE'], $username, append_sid("./ucp.{$phpEx}?i=socialnet&amp;mode=module_approval_friends"), $lang['UCP_ZEBRA_FRIENDS']);

		$poll = $uid = $bitfield = $options = '';
		generate_text_for_storage($my_subject, $uid, $bitfield, $options, false, false, false);
		generate_text_for_storage($message, $uid, $bitfield, $options, true, true, true);

		$data = array(
			'address_list'		 => array('u' => array($send_to => 'to')),
			'from_user_id'		 => $send_from,
			'from_username'		 => $config['sitename'],
			'icon_id'			 => 0,
			'from_user_ip'		 => $user->ip,
			'enable_bbcode'		 => true,
			'enable_smilies'	 => true,
			'enable_urls'		 => true,
			'enable_sig'		 => true,
			'message'			 => $message,
			'bbcode_bitfield'	 => $bitfield,
			'bbcode_uid'		 => $uid,
		);
		submit_pm('post', $my_subject, $data, false);
	}
}

?>