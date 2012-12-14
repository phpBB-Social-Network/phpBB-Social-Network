<?php
/**
 *
 * @package phpBB Social Network
 * @version 0.7.0
 * @copyright (c) phpBB Social Network Team 2010-2012 http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

if (!defined('SOCIALNET_INSTALLED'))
{
	/**
	 * @ignore
	 */
	define('IN_PHPBB', true);
	/**
	 * @ignore
	 */
	define('SN_LOADER', 'notify');
	define('SN_NOTIFY', true);
	$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
	$phpEx = substr(strrchr(__FILE__, '.'), 1);
	/**
	 * @ignore
	 */
	include_once($phpbb_root_path . 'common.' . $phpEx);

	// Start session management
	$user->session_begin(false);
	$auth->acl($user->data);
	$user->setup();

}

if (!class_exists('socialnet_notify'))
{
	/**
	 * class notify
	 */
	class socialnet_notify
	{
		var $p_master = null;
		var $time = null;
		var $time_new = 300;
		var $time_read = 86400;

		/**
		 * socialnet_notify constructor
		 *
		 * @param object $p_master Reference to parent object
		 * @access public
		 * @return void
		 */
		function socialnet_notify(&$p_master)
		{
			$this->p_master =& $p_master;
		}

		function init()
		{
			global $user, $db, $phpbb_root_path, $phpEx, $template, $config;
			$this->time = time();

			$this->ntf_delete();

			$this->ntf_mark(SN_NTF_STATUS_UNREAD);

			$this->ntf_check_MARK();

			$this->ntf_ap_show();

			$template->assign_vars(array(
				'U_VIEW_NOTIFY'				 			=> append_sid("{$phpbb_root_path}activitypage.$phpEx", 'mode=notify'),
				'S_SN_USER_UNREAD_NOTIFY'		=> $this->ntf_notify_count(),
				'S_SN_NTF_THEME'			 			=> $config['ntf_theme'],
				'SN_NTF_LIFE'				 				=> $config['ntf_life'] * 1000,
				'SN_NTF_CHECKTIME'			 		=> $config['ntf_checktime'] * 1000,
			));
		}

		/**
		 * socialnet_norify::load
		 * Function is called by ajax function from page.
		 * Main function for generating the popups
		 * @access public
		 * @return void
		 */
		function load()
		{
			global $user, $db, $phpbb_root_path, $phpEx;

			/**
			 * @ignore
			 */
			include_once($phpbb_root_path . 'includes/functions.' . $phpEx);
			/**
			 * @ignore
			 */

			$ntf_type = request_var('type', 'check');
			$ntf_id = request_var('nid', 0);
			if ($ntf_type == 'delete' && $ntf_id != 0)
			{
				/**
				 * DELETE NOTIFY - Not Implemented in PAGE
				 */
				$sql = "DELETE FROM " . SN_NOTIFY_TABLE . "
									WHERE ntf_user = '{$user->data['user_id']}'
										AND ntf_id = '{$ntf_id}'";

				$db->sql_return_on_error(true);
				$return = array();
				if ($db->sql_query($sql))
				{
					$return['del'] = true;
				}
				else
				{
					$return['del'] = false;
				}
				die(json_encode($return));
			}

			if ($ntf_type == 'markRead' && $ntf_id != 0)
			{
				$this->ntf_markID(SN_NTF_STATUS_READ, SN_NTF_STATUS_READ, $ntf_id);
			}

			if ($ntf_type == 'check')
			{
				/**
				 * CHECK NOTIFY - Check each if zavolánom if there is a new notification to view
				 */
				$sql_where = array(
					"ntf_user = {$user->data['user_id']}",
					"ntf_read = " . SN_NTF_STATUS_NEW,
					"ntf_time > " . ($this->time - $this->time_new),
				);

				$sql = "SELECT ntf.*, user_avatar, user_avatar_type, user_avatar_width, user_avatar_height, u.user_colour
									FROM " . SN_NOTIFY_TABLE . " AS ntf, " . USERS_TABLE . " AS u
										WHERE " . implode(" AND ", $sql_where) . "
											AND ntf.ntf_poster = u.user_id
									ORDER BY ntf.ntf_time DESC";
				$rs = $db->sql_query($sql);
				$rowset = $db->sql_fetchrowset($rs);
				$db->sql_freeresult($rs);

				$sql = "UPDATE " . SN_NOTIFY_TABLE . "
									SET ntf_read = " . SN_NTF_STATUS_DISPLAYED . ", ntf_change = {$this->time}
										WHERE " . implode(" AND ", $sql_where);
				$db->sql_query($sql);

				$ntf_return = array();
				$ntf_return['message'] = array();
				for ($i = 0; isset($rowset[$i]); $i++)
				{
					$ntf = unserialize($rowset[$i]['ntf_data']);
					$ntf_text = $user->lang[$ntf['text']];
					unset($ntf['text']);

					if (!empty($ntf['link']))
					{
						$ntf_link = explode('?', $ntf['link']);

						$ntf_link[1] = preg_replace('/(#socialnet_us)?$/i', '&amp;ntfMark=' . $rowset[$i]['ntf_id'] . '\1', $ntf_link[1]);
						$ntf_link[1] = preg_replace('/#socialnet_us.*$/i', '#socialnet_us', $ntf_link[1]);

						$ntf['link'] = generate_board_url() . '/' . append_sid($ntf_link[0], $ntf_link[1]);
					}

					$avatar = $this->p_master->get_user_avatar_resized($rowset[$i]['user_avatar'], $rowset[$i]['user_avatar_type'], $rowset[$i]['user_avatar_width'], $rowset[$i]['user_avatar_height'], 42);
					$avatar = $this->p_master->absolutePath($avatar);

					if (isset($ntf['user']))
					{
						$ntf['user'] = $this->p_master->get_username_string($this->p_master->config['ntf_colour_username'], 'full', $rowset[$i]['ntf_poster'], $ntf['user'], $rowset[$i]['user_colour']);
					}

					$ntf_return['message'][] = $avatar . vsprintf($ntf_text, $ntf);
				}

				$ntf_return['cnt'] = $this->ntf_notify_count();
				die(json_encode($ntf_return));
			}
		}

		/**
		 * socialnet_notify::ntf_check_FAMILY
		 * The function is called when creating a module, check and create the appropriate notification to the user.
		 * Notification relating to "user added as a family member"
		 * @access private
		 * @return void
		 */
		function ntf_check_FAMILY($relation_id, $relative_user_id, $status_id)
		{
			global $db, $user, $phpbb_root_path, $phpEx, $config;

			$mode = request_var('mode', '');
			$module = request_var('i', '');

			$link = "ucp.{$phpEx}?i=socialnet&amp;mode=module_profile_relations&amp;action=approve_relation&amp;id={$relation_id}";
			$status = $this->p_master->family_status($status_id);

			$this->ntf_generate(SN_NTF_FAMILY, $relative_user_id, array(
				'text'	 => 'SN_NTF_APPROVE_FAMILY',
				'user'	 => $user->data['username'],
				'status' => $status,
				'link'	 => $link,
			));
		}

		/**
		 * socialnet_notify::ntf_check_RELATIONSHIP
		 * The function is called when creating a module, check and create the appropriate notification to the user.
		 * Notification relating to "relationship has been created"
		 * @access private
		 * @return void
		 */
		function ntf_check_RELATIONSHIP($relation_id, $relative_user_id)
		{
			global $db, $user, $phpbb_root_path, $phpEx, $config;

			$mode = request_var('mode', '');
			$module = request_var('i', '');

			$link = "ucp.{$phpEx}?i=socialnet&amp;mode=module_profile_relations&amp;action=approve_relation&amp;id={$relation_id}";

			$this->ntf_generate(SN_NTF_REALTION, $relative_user_id, array(
				'text'	 => 'SN_NTF_APPROVE_RELATIONSHIP',
				'user'	 => $user->data['username'],
				'link'	 => $link,
			));
		}

		/**
		 * socialnet_notify::ntf_ap_show
		 * The function is called when creating a module for displaying user notifications on the Activity page.
		 * @access private
		 * @return void
		 */
		function ntf_ap_show()
		{
			global $db, $phpbb_root_path, $phpEx, $template, $user;

			if ($this->p_master->script_name == 'activitypage')
			{
				$mode = request_var('mode', '');
				if ($mode == 'notify')
				{
					// delete notifications from deleted users
					$users_ids = array();

					$sql = 'SELECT user_id
							FROM ' . USERS_TABLE;
					$result = $db->sql_query($sql);
					while ( $row = $db->sql_fetchrow($result) )
					{
						$users_ids[] = $row['user_id'];
					}
					$sql = 'DELETE FROM ' . SN_NOTIFY_TABLE . ' WHERE ntf_poster NOT IN (' . implode(',', $users_ids) . ')';
					$db->sql_query($sql);

					// select notifications
					$sql = "SELECT ntf.*, user_avatar, user_avatar_type, user_avatar_width, user_avatar_height, u.user_colour
										FROM " . SN_NOTIFY_TABLE . " AS ntf, " . USERS_TABLE . " AS u
											WHERE ntf_user = {$user->data['user_id']}
												AND ntf_poster = user_id
										ORDER BY ntf_time DESC";
					$rs = $db->sql_query($sql);
					$rowset = $db->sql_fetchrowset($rs);
					$db->sql_freeresult($rs);

					for ($i = 0; isset($rowset[$i]); $i++)
					{
						$row = $rowset[$i];
						$data = unserialize($row['ntf_data']);
						$text = $data['text'];
						unset($data['text']);

						$poster_avatar = $this->p_master->get_user_avatar_resized($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height'], 50);
						$ntf_link = explode('?', $data['link']);

						if (isset($data['user']))
						{
							$data['user'] = $this->p_master->get_username_string($this->p_master->config['ntf_colour_username'], 'full', $row['ntf_poster'], $data['user'], $row['user_colour']);
						}

						$ntf_link[1] = 'ntfMark=' . $row['ntf_id'] . (isset($ntf_link[1]) ? '&amp;' . $ntf_link[1] : '');

						$data['link'] = append_sid($phpbb_root_path . $ntf_link[0], $ntf_link[1]);

						$template->assign_block_vars('ap_notify', array(
							'NTF_ID'			 				=> $row['ntf_id'],
							'DATA'				 				=> @vsprintf($user->lang[$text], $data),
							'POSTER_AVATAR'		 		=> $poster_avatar,
							'B_UNREAD'			 			=> $row['ntf_read'] > SN_NTF_STATUS_READ,
							'U_POSTER_PROFILE'		=> $this->p_master->get_username_string($this->p_master->config['ntf_colour_username'], 'profile', $row['ntf_poster'], $data['user'], $row['user_colour']),
						));
					}

					// Mark as read for SN_NTF_EMOTE;
					$this->ntf_mark(SN_NTF_STATUS_READ, SN_NTF_STATUS_UNREAD, $user->data['user_id'], 'SN_NTF_EMOTE');
				}
			}
		}

		/**
		 * socialnet_notify::mtf_mark
		 * Change notification status from defined notification status to new notification status
		 * @param integer $status New status of notfication
		 * @param integer $from_status Old status of notofication. Default SN_NTF_STATUS_NEW
		 * @param integer $user ID user, which belongs the notifications, 0 all users
		 * @param string $for string that contain field ntf_data, almost NTF_TEXT string
		 */
		function ntf_mark($status, $from_status = SN_NTF_STATUS_NEW, $user = 0, $for = false)
		{
			global $db;

			$sql_where = "ntf_time > " . ($this->time + $this->time_new);
			if ($user != 0)
			{
				$sql_where = "ntf_user = " . $user;
			}

			if ($for != false)
			{
				$sql_where .= " AND ntf_data LIKE '%{$for}%'";
			}

			$sql = "UPDATE " . SN_NOTIFY_TABLE . "
								SET ntf_read = {$status}, ntf_change = '{$this->time}'
									WHERE ntf_read >= {$from_status}
										AND " . $sql_where;
			$db->sql_query($sql);
		}

		/**
		 * socialnet_notify::mtf_mark
		 * Change notification status from defined notification status to new notification status
		 * @param integer $status New status of notfication
		 * @param integer $from_status Old status of notofication. Default SN_NTF_STATUS_NEW
		 * @param integer $user ID user, which belongs the notifications, 0 all users
		 */
		function ntf_markID($status, $from_status = SN_NTF_STATUS_NEW, $ntf = 0)
		{
			global $db;

			$sql = "UPDATE " . SN_NOTIFY_TABLE . "
								SET ntf_read = {$status}, ntf_change = '{$this->time}'
									WHERE ntf_read >= {$from_status}
										AND ntf_id = {$ntf}";
			$db->sql_query($sql);
		}

		function ntf_check_MARK()
		{
			global $db, $user;
			$ntf_mark = request_var('ntfMark', 0);

			if ($ntf_mark == 0)
			{
				return;
			}

			$sql = "UPDATE " . SN_NOTIFY_TABLE . "
								SET ntf_read = " . SN_NTF_STATUS_READ . "
									WHERE ntf_id = {$ntf_mark}
										AND ntf_user = {$user->data['user_id']}";
			$db->sql_query($sql);
		}

		/**
		 * socialnet_notify::ntf_delete
		 * Delete user notification
		 * @param integer $ntf_id ID of notification to be deleted. 0 delete all readed notification readed older than 1 day.
		 */
		function ntf_delete($ntf_id = 0)
		{
			global $db, $user;

			if ($ntf_id == 0)
			{
				$sql_where = "ntf_read = " . SN_NTF_STATUS_READ . " AND ntf_change < " . ($this->time - $this->time_read);
			}
			else
			{
				$sql_where = "ntf_id = {$ntf_id}";
			}

			$db->sql_query("DELETE FROM " . SN_NOTIFY_TABLE . " WHERE " . $sql_where);
		}

		/**
		 * socialnet_notify::ntf_notify_count
		 * Get count of new unread notification for current user
		 * @access private
		 * @param integer $status lowest ID notification status to be counted
		 * @return integer Count of notifications with greater or equal ID status than $status
		 */
		function ntf_notify_count($status = SN_NTF_STATUS_UNREAD)
		{
			global $user, $db;
			$sql_where = array(
				"ntf_user = {$user->data['user_id']}",
				"ntf_read >= " . $status,
			);

			$sql = "SELECT count(*) AS computed
								FROM " . SN_NOTIFY_TABLE . "
									WHERE " . implode(" AND ", $sql_where);
			$rs = $db->sql_query($sql);
			$row = $db->sql_fetchrow($rs);
			$db->sql_freeresult($rs);
			return $row['computed'];
		}

	}
}

if (isset($socialnet) && defined('SN_NOTIFY'))
{
	if ($user->data['user_type'] == USER_IGNORE || $config['board_disable'] == 1)
	{
		$ann_data = array(
			'user_id'	 => 'ANONYMOUS',
			'del'		 => false,
			'cnt'		 => 0,
			'message'	 => array(),
		);

		header('Content-type: application/json');
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		die(json_encode($ann_data));
		return;
	}

	$socialnet->modules_obj['notify']->load();
}

?>