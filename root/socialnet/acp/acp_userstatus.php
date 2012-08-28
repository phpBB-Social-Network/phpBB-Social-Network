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

class acp_userstatus extends socialnet
{
	var $p_master;

	function acp_userstatus(&$p_master)
	{
		$this->p_master =& $p_master;
	}

	function main($id)
	{
		global $config, $db, $user, $auth, $template;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx, $socialnet_root_path;

		$display_vars = array(
			'title'	 => 'ACP_SN_USERSTATUS_SETTINGS',
			'vars'	 => array(
				'legend1'						 							=> 'ACP_SN_USERSTATUS_SETTINGS',
				'userstatus_comments_load_last'		=> array('lang' => 'US_LOAD_LAST_USERSTATUS_COMMENTS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'userstatus_override_cfg'		 			=> array('lang' => 'OVERRIDE_USER_SETTINGS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'us_colour_username'			 				=> array('lang' => 'SN_COLOUR_NAME', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
			)
		);

		$this->p_master->_settings($id, 'sn_userstatus', $display_vars);

		$user_tools = array(
			'delete_statuses'	 => 'SN_US_DELETE_ALL_USER_STATUSES',
			'delete_comments'	 => 'SN_US_DELETE_ALL_USER_COMMENTS',
			'user_deleted'		 => 'SN_USER_DELETE_STATUS_COMMENTS_DELETED_USERS',
		);

		$submit = isset($_POST['sn_basictools_submit']) ? true : false;

		$action = request_var('action', '');
		if ($submit)
		{
			$username_ = request_var('username', '', true);
			$username = utf8_normalize_nfc($username_);

			if (in_array($action, array_keys($user_tools)) && !empty($username))
			{
				$sql = "SELECT user_id FROM " . USERS_TABLE . " WHERE username_clean = '{$username}'";
				$rs = $db->sql_query($sql);
				$user_id = $db->sql_fetchfield('user_id');
				$db->sql_freeresult($rs);

				switch ($action)
				{
					case 'delete_comments':
					
						$sql = "DELETE FROM " . SN_STATUS_COMMENTS_TABLE . " WHERE poster_id = '{$user_id}'";
						$db->sql_query($sql);
						
					break;
					
					case 'delete_statuses':
					
						$sql = "SELECT status_id FROM " . SN_STATUS_TABLE . " WHERE poster_id = '{$user_id}'";
						$rs = $db->sql_query($sql);
						$rowset = $db->sql_fetchrowset($rs);
						$db->sql_freeresult($rs);

						$status_ids = array();
						for ($i = 0; isset($rowset[$i]); $i++)
						{
							$status_ids[] = $rowset[$i]['status_id'];
						}

						if (empty($status_ids))
						{
							trigger_error('SN_NO_USER_STATUS_TO DELETE');
						}

						$sql_in_set = $db->sql_in_set('status_id', $status_ids);
						$commen_in_set = str_replace('status_id', 'entry_target', $sql_in_set);

						$sql = "DELETE FROM " . SN_ENTRIES_TABLE . "
								WHERE entry_type IN (" . SN_TYPE_NEW_STATUS . ", " . SN_TYPE_NEW_STATUS_COMMENT . ") AND {$commen_in_set}";
						$db->sql_query($sql);

						$sql = "DELETE FROM " . SN_STATUS_COMMENTS_TABLE . " WHERE $sql_in_set";
						$db->sql_query($sql);

						$sql = "DELETE FROM " . SN_STATUS_TABLE . " WHERE $sql_in_set";
						$db->sql_query($sql);
						
					break;
				}

				add_log('admin', 'LOG_CONFIG_SN_USERSTATUS_BASICTOOLS_' . strtoupper($action), $username_);
				trigger_error($user->lang[$user_tools[$action]]);
			}
			else if ($action == 'user_deleted')
			{
				$sql = "SELECT DISTINCT entry_target FROM " . SN_ENTRIES_TABLE . " WHERE user_id NOT IN (SELECT user_id FROM " . USERS_TABLE . ")";
				$rs = $db->sql_query($sql);
				$rowset = $db->sql_fetchrowset($rs);
				$db->sql_freeresult($rs);

				$status_ids = array();
				for ($i = 0; isset($rowset[$i]); $i++)
				{
					$status_ids[] = $rowset[$i]['entry_target'];
				}

				if (empty($status_ids))
				{
					trigger_error('SN_NO_USER_STATUS_TO DELETE');
				}

				$sql_in_set = $db->sql_in_set('status_id', $status_ids);
				$commen_in_set = str_replace('status_id', 'entry_target', $sql_in_set);

				$sql = "DELETE FROM " . SN_ENTRIES_TABLE . "
									WHERE entry_type IN (" . SN_TYPE_NEW_STATUS . ", " . SN_TYPE_NEW_STATUS_COMMENT . ") AND {$commen_in_set}";
				$db->sql_query($sql);

				$sql = "DELETE FROM " . SN_STATUS_COMMENTS_TABLE . " WHERE $sql_in_set";
				$db->sql_query($sql);

				$sql = "DELETE FROM " . SN_STATUS_TABLE . " WHERE $sql_in_set";
				$db->sql_query($sql);

				add_log('admin', 'LOG_CONFIG_SN_USERSTATUS_BASICTOOLS_' . strtoupper($action));
				trigger_error($user->lang[$user_tools[$action]]);
			}
		}

		foreach ($user_tools as $key => $lang)
		{
			$template->assign_block_vars('sn_basictools', array(
				'MODE'	 => $key,
				'NAME'	 => isset($user->lang[$lang]) ? $user->lang[$lang] : "{ $lang }",
				'SELECTED' => $action == $key,
			));
		}

		$template->assign_var('SN_BASICTOOLS_NEED_USERNAME', true);
	}
}

?>