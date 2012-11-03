<?php
/**
 *
 * @package phpBB Social Network
 * @version 0.7.0
 * @copyright (c) phpBB Social Network Team 2010-2012 http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

if (!defined('SOCIALNET_INSTALLED') || !defined('IN_PHPBB'))
{
	/**
	 * @ignore
	 */
	define('IN_PHPBB', true);
	/**
	 * @ignore
	 */
	define('SN_LOADER', 'activitypage');
	define('SN_AP', true);
	$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
	$phpEx = substr(strrchr(__FILE__, '.'), 1);
	include_once($phpbb_root_path . 'common.' . $phpEx);
	include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);

	// Start session management
	$user->session_begin(false);
	$auth->acl($user->data);
	$user->setup('viewforum');
}

if (!class_exists('socialnet_activitypage'))
{
	/**
	 * socialnet_activity
	 */
	class socialnet_activitypage
	{
		var $p_master = null;
		var $friends_entry = array();

		function socialnet_activitypage(&$p_master = null)
		{
			$this->p_master =& $p_master;
		}

		function init()
		{
			global $phpEx, $user, $template, $phpbb_root_path, $db, $socialnet;

			$template_vars = array();

			$on_login = false;
			if ($this->p_master->script_name == 'activitypage')
			{
				$on_login = $this->p_master->block('login');
			}

			if ($this->p_master->script_name == 'activitypage' && !$on_login)
			{
				$mode = request_var('mode', 'view_main');

				switch ($mode)
				{
					case 'view_suggestions':

						$this->p_master->fms_users(array_merge(array(
							'mode'				 => 'suggestionfull',
							'mode_short'		 => 'suggestion',
							'slider'			 => false,
							'user_id'			 => $user->data['user_id'],
							'limit'				 => 50,
							'fmsf'				 => 0,
							'avatar_size'		 => 50,
							'add_friend_link'	 => true
						), $this->p_master->fms_users_sqls('suggestion', $user->data['user_id'])));

						break;

					case 'view_main':

						$last_entry_time = request_var('lEntryTime', 0);

						//$a_ap_entries = $this->ap_load_entries($last_entry_time, 15);
						$a_ap_entries = $this->p_master->entry->get($last_entry_time, 15);
						foreach ($a_ap_entries['entries'] as $idx => $a_ap_entry)
						{
							$template->assign_block_vars('ap_entries', $a_ap_entry);
						}

						$template_vars = array_merge($template_vars, array(
							'B_SN_AP_MORE_ENTRIES'	 => $a_ap_entries['more'],
						));

						break;

					case 'search':

						$username = request_var('username', '', true);
						$username_clean = utf8_clean_string($username);

						$db_username = $db->sql_escape($username);
						$db_username_clean = $db->sql_escape($username_clean);

						function sn_ap_cmp_username($expr)
						{
							global $db;
							$sql = "SELECT user_id, username_clean, username
								FROM " . USERS_TABLE . "
								WHERE {$expr} AND user_type <> 2";
							$result = $db->sql_query($sql);
							return $db->sql_fetchfield('user_id');
						}

						$search_user_id = sn_ap_cmp_username("username = '{$db_username}'");
						if ($search_user_id == 0)
						{
							$search_user_id = sn_ap_cmp_username("username_clean = '{$db_username_clean}'");
						}
						if ($search_user_id == 0)
						{
							$search_user_id = sn_ap_cmp_username("username_clean " . $db->sql_like_expression($db_username_clean . $db->any_char));
						}
						if ($search_user_id == 0)
						{
							$search_user_id = sn_ap_cmp_username("username_clean " . $db->sql_like_expression($db->any_char . $db_username_clean . $db->any_char));
						}

						if ($search_user_id != 0)
						{
							$redirect = append_sid("{$phpbb_root_path}profile.$phpEx", 'u=' . $search_user_id);
						}
						else
						{
							$redirect = append_sid("{$phpbb_root_path}activitypage.{$phpEx}", "search={$username}");
						}
						redirect($redirect);

						break;
				}

				$template_vars = array_merge($template_vars, array(
					'S_MY_USERNAME'		 => $user->data['username'],
					'S_MY_USER_AVATAR'	 => $this->p_master->get_user_avatar_resized($user->data['user_avatar'], $user->data['user_avatar_type'], $user->data['user_avatar_width'], $user->data['user_avatar_height'], 50),
					'U_VIEW_SUGGESTIONS' => append_sid("activitypage.$phpEx", 'mode=view_suggestions'),
					'U_MANAGE_FRIEND'	 => append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=zebra'),
					'U_ADD_FRIEND'		 => append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=zebra'),
					'U_EDIT_MY_PROFILE'	 => append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=profile'),
					'U_MY_USERNAME_LINK' => append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $user->data['user_id']),
					'S_SN_AP_' . strtoupper($mode)					 => true,
					'USER_ID'			 => $user->data['user_id'],
				));
			}

			$ap_enabled = true;
			// Generate Activity page Comments counts.
			if ($user->data['is_registered'])
			{
				$my_friends = $this->p_master->friends['user_id'];

				if ($my_friends)
				{
					$sql = 'SELECT COUNT(entry_id) as count
			              FROM ' . SN_ENTRIES_TABLE . '
			                WHERE entry_time > ' . $user->data['user_lastvisit'] . '
												AND entry_type = ' . SN_TYPE_NEW_STATUS_COMMENT . '
			                	AND ' . $db->sql_in_set('user_id', $my_friends);
					$result = $db->sql_query($sql);

					$template_vars = array_merge($template_vars, array(
						'SN_AP_NEW_COMMENTS_COUNT'	 => $db->sql_fetchfield('count', false, $result),
					));
					$db->sql_freeresult($result);
				}
			}
			else
			{
				$ap_enabled = !$this->p_master->config['ap_hide_for_guest'];
			}

			$template_vars = array_merge($template_vars, array(
				'SN_MODULE_ACTIVITYPAGE_ENABLED' => $ap_enabled,
			));

			$template->assign_vars($template_vars);
		}

		function load($mode)
		{
			global $socialnet_root_path, $phpEx, $socialnet, $template, $phpbb_root_path;

			switch ($mode)
			{
				case 'users_autocomplete':

					$socialnet->users_autocomplete();

					break;
				case 'onlineUsers':

					$this->p_master->online_users(true);

					break;

				case 'snApOlderEntries':

					$last_entry_time = request_var('lEntryTime', 0);

					//$a_ap_entries = $this->ap_load_entries($last_entry_time, 15);
					$a_ap_entries = $this->p_master->entry->get($last_entry_time, 15);

					foreach ($a_ap_entries['entries'] as $idx => $a_ap_entry)
					{
						$template->assign_block_vars('ap_entries', $a_ap_entry);
					}

					$return = array();
					$return['more'] = $a_ap_entries['more'];

					$template->assign_vars(array(
						'B_SN_AP_MORE_ENTRIES'	 => $a_ap_entries['more'],
						'B_SN_AP_MORE_LOAD'		 => true,
					));

					$template->set_filenames(array('body' => 'socialnet/activitypage_body_entries.html'));

					$return['content'] = $this->p_master->get_page();

					header('Content-type: application/json');
					header("Cache-Control: no-cache, must-revalidate");
					header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
					die(json_encode($return));

					break;

				case 'snApNewestEntries':

					$last_entry_time = request_var('lEntryTime', 0);

					//$a_ap_entries = $this->ap_load_entries($last_entry_time, 15, false);
					$a_ap_entries = $this->p_master->entry->get($last_entry_time, 15, false);

					foreach ($a_ap_entries['entries'] as $idx => $a_ap_entry)
					{
						$template->assign_block_vars('ap_entries', $a_ap_entry);
					}

					$return = array();
					$return['more'] = $a_ap_entries['more'];

					$template->assign_vars(array(
						'B_SN_AP_MORE_ENTRIES'	 => $a_ap_entries['more'],
						'B_SN_AP_MORE_LOAD'		 => true,
						'B_SN_ONLY_ONE'			 => true
					));

					$template->set_filenames(array('body' => 'socialnet/activitypage_body_entries.html'));

					$return['content'] = $this->p_master->get_page();

					header('Content-type: application/json');
					header("Cache-Control: no-cache, must-revalidate");
					header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
					die(json_encode($return));

					break;
			}

		}

	}
}

if (isset($socialnet) && defined('SN_AP'))
{
	if ($user->data['user_type'] == USER_IGNORE || $config['board_disable'] == 1)
	{
		$ann_data = array(
			'user_id'		 => 'ANONYMOUS',
			'more'			 => false,
			'onlineCount'	 => 0,
		);

		header('Content-type: application/json');
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		die(json_encode($ann_data));
	}

	$s_mode = request_var('mode', 'startAP');

	$socialnet->modules_obj['activitypage']->load($s_mode);
}

?>