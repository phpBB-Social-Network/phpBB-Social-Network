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
	define('SN_LOADER', 'approval');
	define('SN_FAS', true);
	$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
	$phpEx = substr(strrchr(__FILE__, '.'), 1);
	include_once($phpbb_root_path . 'common.' . $phpEx);
	include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);

	// Start session management
	$user->session_begin(false);
	$auth->acl($user->data);
	$user->setup('viewforum');
}

if (!class_exists('socialnet_approval'))
{
	class socialnet_approval
	{
		var $p_master = null;
		var $script_name = '';

		function socialnet_approval(&$p_master)
		{
			$this->p_master =& $p_master;
		}

		function init()
		{
			global $config, $user, $phpEx, $db, $template, $phpbb_root_path;

			$this->script_name = $this->p_master->script_name;
			$mode = request_var('mode', '', true);

			if ($this->script_name == 'memberlist' && $mode == 'viewprofile')
			{
				$user_id = request_var('u', ANONYMOUS);
				$username = request_var('un', '', true);

				if ($user_id == ANONYMOUS && !$username)
				{
					if ($user->data['is_registered'])
					{
						header('Location: ' . append_sid($phpbb_root_path . 'profile.' . $phpEx . '?u=' . $user->data['user_id']));
						die();
					}
					else
					{
						trigger_error('NO_USER');
					}
				}

				// Get user
				$sql = 'SELECT *
  								FROM ' . USERS_TABLE . '
  									WHERE ' . (($username) ? "username_clean = '" . $db->sql_escape(utf8_clean_string($username)) . "'" : "user_id = $user_id");
				$result = $db->sql_query($sql);
				$member = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if (!$member)
				{
					trigger_error('NO_USER');
				}
				$user_id = $member['user_id'];
				$username = $member['username'];

				if ($this->script_name == 'memberlist')
				{
					$this->p_master->fms_users(array_merge(array(
						'mode'				 => 'friend',
						'slider'			 => false,
						'user_id'			 => $user_id,
						'limit'				 => 12,
						'add_friend_link'	 => true,
					), $this->p_master->fms_users_sqls('friend', $user_id)));
				}
			}
		}

		function load()
		{
			global $template;
			$mode = request_var('mode', '');

			$this->$mode();

			switch ($mode)
			{
			case 'memberlist':

				$template->set_filenames(array('body' => "socialnet/memberlist_viewprofile_friends.html"));

				break;

			case 'mutual':

				$template->set_filenames(array('body' => "socialnet/memberlist_viewprofile_mutual.html"));

				break;

			default:

				$template->set_filenames(array('body' => "socialnet/ucp_approval_block_{$mode}.html"));
			}

			$page = $this->p_master->get_page();
			header('Content-type: text/html; charset=UTF-8');
			die($page);
		}

		function group()
		{
			global $db, $user;

			$gid = request_var('gid', 0);
			$uid = request_var('uid', 0);
			$sub = request_var('sub', '');
			$tid = request_var('tid', '', true);

			header('Content-type: application/json');
			header("Cache-Control: no-cache, must-revalidate");
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

			if (($gid == 0 && $tid == '') || $uid == 0 || $sub == '')
			{
				die(json_encode(array('error' => 1, 'text' => "some user problem occured\nReload page using F5 or CTRL+F5")));
			}

			switch ($sub)
			{
			case 'create':

				$sql = "SELECT MAX(fms_gid) AS max_id FROM " . SN_FMS_GROUPS_TABLE . " WHERE user_id = {$user->data['user_id']}";
				$rs = $db->sql_query($sql);
				$max_id = (int) $db->sql_fetchfield('max_id');
				$gid = $max_id + 1;

				$sql_ary = array(
					'fms_gid'		 => $gid,
					'user_id'		 => $user->data['user_id'],
					'fms_name'		 => $db->sql_escape($tid),
					'fms_clean'		 => utf8_clean_string($tid),
					'fms_collapse'	 => 0,
				);
				$db->sql_return_on_error(true);

				$sql = "INSERT INTO " . SN_FMS_GROUPS_TABLE . $db->sql_build_array('INSERT', $sql_ary);
				$db->sql_query($sql);
				$is_error =  $db->sql_error_sql;
				$db->sql_return_on_error(false);
				if ($is_error != '')
				{
					unset($sql_ary['fms_gid']);
					$sql = "SELECT fms_gid FROM " . SN_FMS_GROUPS_TABLE ." WHERE " .$db->sql_build_array('SELECT', $sql_ary);
					$rs = $db->sql_query($sql);
					$gid = $db->sql_fetchfield('fms_gid');
				}

			case 'add':

				$sql = "INSERT INTO " . SN_FMS_USERS_GROUP_TABLE . " (fms_gid,user_id,owner_id) VALUES ({$gid},{$uid},{$user->data['user_id']})";

				break;

			case 'remove':

				$sql = "DELETE FROM " . SN_FMS_USERS_GROUP_TABLE . " WHERE fms_gid = {$gid} AND user_id = {$uid}";

				break;

			case 'delete':

				$sql = "DELETE FROM " . SN_FMS_USERS_GROUP_TABLE . " WHERE fms_gid = {$gid} AND owner_id = {$user->data['user_id']}";
				$db->sql_query($sql);
				$sql = "DELETE FROM " . SN_FMS_GROUPS_TABLE . " WHERE fms_gid = {$gid} AND user_id = {$user->data['user_id']}";

				break;
			}

			$db->sql_return_on_error(true);
			$db->sql_query($sql);
			$db->sql_return_on_error(false);
				
			$this->p_master->reload_groups();

			die(json_encode(array('error' => 0, 'gid' => $gid, 'uid' => $uid, 'sub' => $sub, 'user' => $user->data['user_id'])));
		}

		function _group_load()
		{
			global $template, $user, $db, $config, $phpbb_root_path, $phpEx;

			$html = '';
			$gid = request_var('gid', 0);

			$sql = "SELECT u.user_id, u.username, u.username_clean, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height, u.user_colour
								FROM " . SN_FMS_USERS_GROUP_TABLE . " fms_g, " . USERS_TABLE . " u
									WHERE fms_g.user_id = u.user_id
										AND fms_g.fms_gid = {$gid}
										AND fms_g.user_id = {$user->data['user_id']}
								ORDER BY u.username_clean ASC";
			$rs = $db->sql_query($sql);
			$rowset = $db->sql_fetchrowset($rs);
			$db->sql_freeresult($rs);

			$template->set_filenames(array('body' => 'socialnet/ucp_approval_block_ufg.html'));
			$template->assign_var('B_UFG_FRIENDS_ONLY', true);
			$i_avatar_maxHeight = 48;
			$user_id_field = 'user_id';

			for ($i = 0; isset($rowset[$i]) && $row = $rowset[$i]; $i++)
			{
				if (!empty($row['user_avatar']) && function_exists('get_user_avatar'))
				{
					$img_avatar = $this->p_master->get_user_avatar_resized($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height'], $i_avatar_maxHeight);
				}
				else
				{
					$img_avatar = '<img src="./socialnet/styles/images/im_no_avatar_50.png" width="' . $i_avatar_maxHeight . '" height="' . $i_avatar_maxHeight . '" />';
				}

				$template->assign_block_vars('fas_friend', array(
					'USER_ID'			 => $row[$user_id_field],
					'USERNAME'			 => $this->p_master->get_username_string($config['fas_colour_username'], 'no_profile', $row[$user_id_field], $row['username'], $row['user_colour']),
					'USER_PROFILE'		 => $this->p_master->get_username_string($config['fas_colour_username'], 'full', $row[$user_id_field], $row['username'], $row['user_colour']),
					'USERNAME_NO_COLOR'	 => $row['username'],
					'U_PROFILE'			 => append_sid("{$phpbb_root_path}memberlist.{$phpEx}?mode=viewprofile&amp;u={$row[$user_id_field]}"),
					'AVATAR'			 => $img_avatar,
				));
			}

			$html = $this->p_master->get_page();
			header('Content-type: application/json');
			header("Cache-Control: no-cache, must-revalidate");
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
			die(json_encode(array('html' => $html)));
		}

		function hook_template()
		{
			global $template, $phpbb_root_path, $phpEx;

			if (!is_object($template) || !method_exists($template, 'assign_vars'))
			{
				return;
			}

			array_walk_recursive($template->_tpldata, 'hook_template_approval_array_callback');
		}
	}
}

if (!function_exists('hook_template_approval_array_callback'))
{
	function hook_template_approval_array_callback(&$item, $key)
	{
		global $phpEx;

		$preg_match_profile = '/ucp\.' . $phpEx . '\?i=zebra([^"\']*?)/si';

		if (preg_match($preg_match_profile, $item) && !preg_match('/mode=foes/si', $item))
		{
			$item = preg_replace($preg_match_profile, 'ucp.' . $phpEx . '?i=socialnet&amp;mode=module_approval_friends\2', $item);
		}
	}
}

if (isset($socialnet) && defined('SN_FAS'))
{
	if ($user->data['user_type'] == USER_IGNORE || $config['board_disable'] == 1)
	{
		$ann_data = array('user_id'		 => 'ANONYMOUS',
			'onlineUsers'	 => array(),
			'chatBoxes'		 => array(),
			'message'		 => array(),
			'user_online'	 => 0,
			'message'		 => array(),
			'onlineCount'	 => 0,
		);

		header('Content-type: application/json');
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		die(json_encode($ann_data));
	}

	$socialnet->modules_obj['approval']->load();
}

?>