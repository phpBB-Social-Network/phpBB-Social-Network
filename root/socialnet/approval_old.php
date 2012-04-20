<?php
/**
*
* @package phpBB Social Network
* @version 0.6.3
* @copyright (c) 2010-2012 Kamahl & Culprit http://phpbbsocialnetwork.com
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
	/**
	 * Socialnet_approval trida
	 *
	 * @package FriendApproval
	 * @author Culprit
	 */

	class socialnet_approval
	{
		var $p_master = null;
		var $script_name = '';

		function socialnet_approval(&$p_master)
		{
			global $config, $user, $phpEx, $db, $template, $phpbb_root_path;

			$this->p_master =& $p_master;

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

				// Get user...
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
			die($page);
		}

		function memberlist()
		{
			global $template;

			$user_id = request_var('usr', 0);
			$this->friends($user_id);
		}

		function _friends($user_id = 0, $buttons = true)
		{
			global $db, $phpbb_root_path, $phpEx, $config, $user, $template;

			$mode = $user_id == 0 ? 'friends' : 'memberlist';
			$fmsf = request_var('fmsf', 0);

			$sql_and = 'z.friend = 1';

			$user_id = $user_id == 0 ? $user->data['user_id'] : $user_id;
			//count
			$sql = 'SELECT COUNT(z.user_id) AS computed
						FROM ' . ZEBRA_TABLE . ' z
						WHERE z.user_id = ' . $user_id . "
							AND $sql_and";

			$pagination = $this->_pagination('FAS_FRIEND', $mode, $sql, $fmsf, $user_id);
			$pagination['B_FMS_BUTTONS'] = $buttons;
			$template->assign_vars($pagination);

			$sql = 'SELECT z.*, u.username, u.username_clean, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height, u.user_colour
						FROM ' . ZEBRA_TABLE . ' z, ' . USERS_TABLE . ' u
						WHERE z.user_id = ' . $user_id . "
							AND $sql_and
							AND u.user_id = z.zebra_id
						ORDER BY u.username_clean ASC";

			$this->_fill_blocks('fas_friend', $sql, $fmsf);

		}

		function _mutual()
		{
			global $template;
			$user_id = request_var('usr', 0);
			$this->mutual_($user_id);
		}

		function _mutual_($user_id)
		{
			global $db, $template, $user;

			$fmsf = request_var('fmsf', 0);

			$sql_in_set = $db->sql_in_set('z.zebra_id', $this->p_master->friends['user_id']);

			$sql = "SELECT COUNT(z.zebra_id) AS computed
					FROM " . ZEBRA_TABLE . " AS z
					WHERE z.user_id = {$user_id} AND z.friend = 1 AND {$sql_in_set}";

			$pagination = $this->_pagination('FAS_COMMON', 'mutual', $sql, $fmsf, $user_id);
			$template->assign_vars($pagination);

			$sql = "SELECT z.*, u.username, u.username_clean, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height, u.user_colour
						FROM " . ZEBRA_TABLE . " z, " . USERS_TABLE . " u
						WHERE z.user_id = {$user_id} AND z.friend = 1 AND {$sql_in_set}
						ORDER BY u.username_clean ASC";

			$this->_fill_blocks('fas_common_friend', $sql, $fmsf);
		}

		function _approve()
		{
			global $db, $phpbb_root_path, $phpEx, $config, $user, $template;

			$fmsf = request_var('fmsf', 0);
			//count
			$sql = 'SELECT COUNT(z.user_id) AS computed
						FROM ' . ZEBRA_TABLE . ' z
						WHERE z.zebra_id = ' . $user->data['user_id'] . "
							AND z.approval = 1";

			$pagination = $this->_pagination('FAS_APPROVE', 'approve', $sql, $fmsf);
			$template->assign_vars($pagination);

			$sql = 'SELECT z.*, u.username, u.username_clean, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height, u.user_colour
							FROM ' . ZEBRA_TABLE . ' z, ' . USERS_TABLE . ' u
							WHERE z.zebra_id = ' . $user->data['user_id'] . "
								AND z.approval = 1
								AND u.user_id = z.user_id
							ORDER BY u.username_clean ASC";
			$this->_fill_blocks('fas_approve', $sql, $fmsf, 'user_id');
		}

		function _cancel()
		{
			global $db, $phpbb_root_path, $phpEx, $config, $user, $template;

			$fmsf = request_var('fmsf', 0);
			//count
			$sql = 'SELECT COUNT(z.user_id) AS computed
						FROM ' . ZEBRA_TABLE . ' z
						WHERE z.user_id = ' . $user->data['user_id'] . "
							AND z.approval = 1";

			$pagination = $this->_pagination('FAS_REQUEST', 'cancel', $sql, $fmsf);
			$template->assign_vars($pagination);

			$sql = 'SELECT z.*, u.username, u.username_clean, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height, u.user_colour
							FROM ' . ZEBRA_TABLE . ' z, ' . USERS_TABLE . ' u
							WHERE z.user_id = ' . $user->data['user_id'] . "
								AND z.approval = 1
								AND u.user_id = z.zebra_id
							ORDER BY u.username_clean ASC";

			$this->_fill_blocks('fas_cancel', $sql, $fmsf);

		}

		function _ufg($user_id = 0, $buttons = false)
		{
			global $db, $phpbb_root_path, $phpEx, $config, $user, $template;

			$mode = 'ufg';
			$fmsf = request_var('fmsf', 0);

			$sql_and = 'z.friend = 1';

			$user_id = $user_id == 0 ? $user->data['user_id'] : $user_id;
			//count
			$sql = 'SELECT COUNT(z.user_id) AS computed
									FROM ' . ZEBRA_TABLE . ' z
									WHERE z.user_id = ' . $user_id . "
										AND $sql_and";

			$pagination = $this->_pagination('FAS_FRIEND', $mode, $sql, $fmsf, $user_id);
			$template->assign_vars($pagination);

			$sql = 'SELECT z.*, u.username, u.username_clean, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height, u.user_colour
									FROM ' . ZEBRA_TABLE . ' z, ' . USERS_TABLE . ' u
									WHERE z.user_id = ' . $user_id . "
										AND $sql_and
										AND u.user_id = z.zebra_id
									ORDER BY u.username_clean ASC";

			$this->_fill_blocks('fas_friend', $sql, $fmsf);

		}

		function group()
		{
			global $db, $user;

			$gid = request_var('gid', 0);
			$uid = request_var('uid', 0);
			$sub = request_var('sub', '');

			header('Content-type: application/json');
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

			if ($gid == 0 || $uid == 0 || $sub == '')
			{
				die(json_encode(array('error' => 1, 'text' => "some user problem recognized\nReload Page using F5 or CTRL+F5")));
			}

			switch ($sub)
			{
				case 'add':
					$sql = "INSERT INTO " . SN_FMS_USERS_GROUP_TABLE . " (fms_gid,user_id) VALUES ({$gid},{$uid})";
					break;
				case 'remove':
					$sql = "DELETE FROM " . SN_FMS_USERS_GROUP_TABLE . " WHERE fms_gid = {$gid} AND user_id = {$uid}";
					break;
				case 'delete':
					$sql = "DELETE FROM " . SN_FMS_USERS_GROUP_TABLE . " WHERE fms_gid = {$gid}";
					$db->sql_query($sql);
					$sql = "DELETE FROM " . SN_FMS_GROUPS_TABLE . " WHERE fms_gid = {$gid} AND user_id = {$user->data['user_id']}";

			}

			$db->sql_query($sql);

			$this->p_master->reload_groups();

			die(json_encode(array('error' => 0, 'text' => "GID:{$gid}\nUID:{$uid}\nSUB:{$sub}\nUSER:{$user->data['user_id']}")));
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
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
			die(json_encode(array('html' => $html)));

		}

		/*
		 function _fill_blocks($block, $sql, $fmsf, $user_id_field = 'zebra_id')
		 {
		 global $db, $template, $phpbb_root_path, $phpEx, $config, $user;
		
		 $result = $db->sql_query_limit($sql, $config['fas_friendlist_limit'], $fmsf);
		
		 $i_avatar_maxHeight = 48;
		
		 while ($row = $db->sql_fetchrow($result))
		 {
		 $img_avatar = $this->p_master->get_user_avatar_resized($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height'], $i_avatar_maxHeight);
		
		 $template->assign_block_vars($block, array(
		 'USER_ID'			 => $row[$user_id_field],
		 'USERNAME'			 => $this->p_master->get_username_string($config['fas_colour_username'], 'no_profile', $row[$user_id_field], $row['username'], $row['user_colour']),
		 'USER_PROFILE'		 => $this->p_master->get_username_string($config['fas_colour_username'], 'full', $row[$user_id_field], $row['username'], $row['user_colour']),
		 'USERNAME_NO_COLOR'	 => $row['username'],
		 'U_PROFILE'			 => append_sid("{$phpbb_root_path}memberlist.{$phpEx}?mode=viewprofile&amp;u={$row[$user_id_field]}"),
		 'AVATAR'			 => $img_avatar,
		 ));
		 }
		 $db->sql_freeresult($result);
		
		 }
		
		 function _pagination($block, $mode, $sql, $start, $user_id = 0)
		 {
		 global $db, $config, $user;
		
		 $rs = $db->sql_query($sql);
		 $row = $db->sql_fetchrow($rs);
		 $db->sql_freeresult($rs);
		 $total = $row['computed'];
		
		 $pagination_url = "javascript:fms_load";
		
		 $generate_pagination = generate_pagination($pagination_url, $total, $config['fas_friendlist_limit'], $start);
		
		 $pagination = preg_replace('/\?start=([0-9]{1,})/si', '(\'' . $mode . '\',\1,' . $user_id . ')', $generate_pagination);
		 $pagination = preg_replace('/' . $pagination_url . '"/si', $pagination_url . '(\'' . $mode . '\',0,' . $user_id . ')"', $pagination);
		 return array(
		 'SN_' . $block . '_PAGINATION'	 => $pagination,
		 'SN_' . $block . '_PAGE_NUMBER'	 => on_page($total, $config['fas_friendlist_limit'], $start),
		 'SN_' . $block . '_TOTAL'		 => $total == 1 ? $user->lang[$block . '_TOTAL'] : sprintf($user->lang[$block . 'S_TOTAL'], $total),
		 );
		 }
		 */
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

		if (preg_match($preg_match_profile, $item) && !preg_match('/mode=foes/si', $item)) //FOES ZATIM ZUSTAVAJI NA phpBB

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
			'onlineCount'	 => 0
		);

		header('Content-type: application/json');
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		die(json_encode($ann_data));
	}

	$socialnet->modules_obj['approval']->load();

}

?>