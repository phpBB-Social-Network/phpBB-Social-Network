<?php
/**
 *
 * @package phpBB Social Network
 * @version 0.7.0
 * @copyright (c) phpBB Social Network Team 2010-2012 http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('memberlist');

$user_id = (int) request_var('u', ANONYMOUS);
$username = request_var('un', '', true);
$action = request_var('action', '');

if ((int) $user_id == 0)
{
	$user_id = $user->data['user_id'];
}

if (!$config['module_profile'])
{
	redirect(append_sid("{$phpbb_root_path}memberlist.{$phpEx}", 'mode=viewprofile&amp;u=' . $user_id));
}

// Can this user view profiles/memberlist?
if (!$auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'))
{
	if ($user->data['user_id'] != ANONYMOUS)
	{
		trigger_error('NO_VIEW_USERS');
	}

	login_box('', ((isset($user->lang['LOGIN_EXPLAIN_' . strtoupper('viewprofile')])) ? $user->lang['LOGIN_EXPLAIN_' . strtoupper('viewprofile')] : $user->lang['LOGIN_EXPLAIN_MEMBERLIST']));
}

$template->set_filenames(array(
	'body'	 => 'socialnet/user_profile_body.html',
));

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

// Select data for left column
$sql = 'SELECT user_id, username, user_type, user_colour, user_inactive_reason, user_avatar, user_avatar_type, user_avatar_width, user_avatar_height, user_allow_pm
              FROM ' . USERS_TABLE . '
                WHERE ' . (($username) ? "username_clean = '" . $db->sql_escape(utf8_clean_string($username)) . "'" : "user_id = $user_id");
$result = $db->sql_query($sql);
$member = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

if (!$member)
{
	trigger_error('NO_USER');
}

// a_user admins and founder are able to view inactive users and bots to be able to manage them more easily
// Normal users are able to see at least users having only changed their profile settings but not yet reactivated.
if (!$auth->acl_get('a_user') && $user->data['user_type'] != USER_FOUNDER)
{
	if (($member['user_type'] == USER_IGNORE) || ($member['user_type'] == USER_INACTIVE && $member['user_inactive_reason'] != INACTIVE_PROFILE))
	{
		trigger_error('NO_USER');
	}
}

$user_id = (int) $member['user_id'];

// What colour is the zebra
$sql_app = ($socialnet->is_enabled('approval')) ? ', approval' : '';

$sql = "SELECT friend, foe {$sql_app}
              FROM " . ZEBRA_TABLE . "
                WHERE zebra_id = $user_id
                  AND user_id = {$user->data['user_id']}";
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$foe = ($row['foe']) ? true : false;
$friend = ($row['friend']) ? true : false;
$request = isset($row['approval']) && $row['approval'] ? true : false;
$db->sql_freeresult($result);

// We need to check if the modules 'zebra' ('friends' & 'foes' mode),  'notes' ('user_notes' mode) and  'warn' ('warn_user' mode) are accessible to decide if we can display appropriate links
$zebra_enabled = $friends_enabled = $foes_enabled = false;

// Only check if the user is logged in
if ($user->data['is_registered'])
{
	if (!class_exists('p_master'))
	{
		include_once($phpbb_root_path . 'includes/functions_module.' . $phpEx);
	}
	$module = new p_master();

	$module->list_modules('ucp');
	$module->list_modules('mcp');

	$zebra_enabled = ($module->loaded('zebra')) ? true : false;
	$friends_enabled = ($module->loaded('zebra', 'friends')) ? true : false;
	$foes_enabled = ($module->loaded('zebra', 'foes')) ? true : false;

	unset($module);
}

$online = false;
if ($config['load_onlinetrack'])
{
	$sql = 'SELECT MAX(session_time) AS session_time, MIN(session_viewonline) AS session_viewonline
                FROM ' . SESSIONS_TABLE . "
                  WHERE session_user_id = " . $user_id;
	$result = $db->sql_query($sql);
	$session = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	$member['session_time'] = (isset($session['session_time'])) ? $session['session_time'] : 0;
	$member['session_viewonline'] = (isset($session['session_viewonline'])) ? $session['session_viewonline'] : 0;
	unset($session);

	$update_time = $config['load_online_time'] * 60;
	$online = (time() - $update_time < $member['session_time'] && ((isset($member['session_viewonline']) && $member['session_viewonline']) || $auth->acl_get('u_viewonline'))) ? true : false;
}

// Load number of friends
$sql = 'SELECT COUNT(z.zebra_id) AS num_friends
		FROM ' . ZEBRA_TABLE . ' z, ' . USERS_TABLE . ' u
		WHERE z.user_id = ' . $user_id . '
			AND z.friend = 1
			AND u.user_id = z.zebra_id
			AND u.user_type NOT IN (' . USER_INACTIVE . ',' . USER_IGNORE . ')';
$result = $db->sql_query($sql);
$total_friends = $db->sql_fetchfield('num_friends');
$db->sql_freeresult($result);

// Template variables for Left column
$redirect = '&amp;redirect=' . base64_encode(append_sid("{$phpbb_root_path}profile.$phpEx", 'u=' . $user_id));
$template->assign_vars(array(
	'USER_ID'				 => $user_id,
	'S_OWN_PROFILE'			 => ($user_id === (int) $user->data['user_id']) ? true : false,
	'USERNAME_FULL'			 => $socialnet->get_username_string(1, 'full', $user_id, $member['username'], $member['user_colour']),
	'USERNAME'				 => $socialnet->get_username_string(1, 'username', $user_id, $member['username'], $member['user_colour']),
	'USER_AVATAR'			 => $socialnet->get_user_avatar_resized($member['user_avatar'], $member['user_avatar_type'], $member['user_avatar_width'], $member['user_avatar_height'], 150, false),
	'U_EDIT_PROFILE'		 => ($user->data['user_id'] == $user_id) ? append_sid("{$phpbb_root_path}ucp.{$phpEx}", 'i=socialnet&amp;mode=module_profile') : '',
	'U_EDIT_FRIENDS'		 => ($user->data['user_id'] == $user_id) ? append_sid("{$phpbb_root_path}ucp.{$phpEx}", 'i=socialnet&amp;mode=module_approval_friends') : '',
	'U_EDIT_RELATIONS'		 => ($user->data['user_id'] == $user_id) ? append_sid("{$phpbb_root_path}ucp.{$phpEx}", 'i=socialnet&amp;mode=module_profile_relations') : '',
	'U_CREATE_FRIENDS_GROUP' => append_sid("{$phpbb_root_path}ucp.{$phpEx}", 'i=socialnet&amp;mode=module_approval_ufg'),
	'U_VIEW_PROFILE'		 => $socialnet->get_username_string(1, 'profile', $user_id, $member['username'], $member['user_colour']),
	'S_ZEBRA'				 => ($user->data['user_id'] != $user_id && $user->data['is_registered'] && $zebra_enabled) ? true : false,
	'U_ADD_FRIEND'			 => (!$friend && !$request && !$foe && $friends_enabled) ? append_sid("{$phpbb_root_path}ucp.{$phpEx}", 'i=zebra&amp;add=' . urlencode(htmlspecialchars_decode($member['username'])) . $redirect) : '',
	'U_ADD_FOE'				 => (!$friend && !$request && !$foe && $foes_enabled) ? append_sid("{$phpbb_root_path}ucp.{$phpEx}", 'i=zebra&amp;mode=foes&amp;add=' . urlencode(htmlspecialchars_decode($member['username'])) . $redirect) : '',
	'U_REMOVE_FRIEND'		 => ($friend && !$request && $friends_enabled) ? append_sid("{$phpbb_root_path}ucp.{$phpEx}", 'i=zebra&amp;remove=1&amp;usernames[]=' . $user_id . $redirect) : '',
	'U_REMOVE_FOE'			 => ($foe && $foes_enabled) ? append_sid("{$phpbb_root_path}ucp.{$phpEx}", 'i=zebra&amp;remove=1&amp;mode=foes&amp;usernames[]=' . $user_id . $redirect) : '',
	'U_CANCEL_REQUEST'		 => ($request && !$friend && !$foe && $friends_enabled) ? append_sid("{$phpbb_root_path}ucp.{$phpEx}", 'i=socialnet&amp;mode=module_approval_friends&amp;module=friends&amp;cancel=1&amp;cancel_request[]=' . $user_id . $redirect) : '',
	'U_SEARCH_USER'			 => ($auth->acl_get('u_search')) ? append_sid("{$phpbb_root_path}search.{$phpEx}",(($user_id === (int) $user->data['user_id']) ? 'search_id=egosearch' : "author_id=$user_id").'&amp;sr=posts') : '',
	'U_USER_ADMIN'			 => ($auth->acl_get('a_user')) ? append_sid("{$phpbb_root_path}adm/index.{$phpEx}", 'i=users&amp;mode=overview&amp;u=' . $user_id, true, $user->session_id) : '',
	'U_USER_BAN'			 => ($auth->acl_get('m_ban') && $user_id != $user->data['user_id']) ? append_sid("{$phpbb_root_path}mcp.{$phpEx}", 'i=ban&amp;mode=user&amp;u=' . $user_id, true, $user->session_id) : '',
	'U_MCP_QUEUE'			 => ($auth->acl_getf_global('m_approve')) ? append_sid("{$phpbb_root_path}mcp.{$phpEx}", 'i=queue', true, $user->session_id) : '',
	'U_SWITCH_PERMISSIONS'	 => ($auth->acl_get('a_switchperm') && $user->data['user_id'] != $user_id) ? append_sid("{$phpbb_root_path}ucp.{$phpEx}", "mode=switch_perm&amp;u={$user_id}&amp;hash=" . generate_link_hash('switchperm')) : '',
	'U_PM'					 => ($config['allow_privmsg'] && $auth->acl_get('u_sendpm') && ($member['user_allow_pm'] || $auth->acl_gets('a_', 'm_') || $auth->acl_getf_global('m_'))) ? append_sid("{$phpbb_root_path}ucp.{$phpEx}", 'i=pm&amp;mode=compose&amp;u=' . $user_id) : '',
	'ONLINE_IMG'			 => (!$config['load_onlinetrack']) ? '' : (($online) ? 'online' : 'offline'),
	'S_ONLINE'				 => ($config['load_onlinetrack'] && $online) ? true : false,
	'U_USER_REPORT'			 => ($config['up_enable_report']) ? append_sid("{$phpbb_root_path}profile.{$phpEx}", 'action=report_user&amp;u=' . $user_id) : '',
	'TOTAL_FRIENDS'			 => $total_friends,
));

// Load relationships and family
$sql = 'SELECT f.status_id, f.anniversary, f.relative_user_id, f.family, f.name, f.approved,
							 u.username, u.user_colour, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height
	        FROM ' . SN_FAMILY_TABLE . ' f
	          LEFT JOIN ' . USERS_TABLE . ' u
	            ON u.user_id = f.relative_user_id
	          WHERE f.user_id = ' . $user_id . '
	            ORDER BY f.status_id ASC';
$result = $db->sql_query($sql);

while ($relation = $db->sql_fetchrow($result))
{
	$avatar_img = $socialnet->get_user_avatar_resized($relation['user_avatar'], $relation['user_avatar_type'], $relation['user_avatar_width'], $relation['user_avatar_height'], 50);
	$username = ($relation['relative_user_id']) ? $socialnet->get_username_string($socialnet->config['us_colour_username'], 'full', $relation['relative_user_id'], $relation['username'], $relation['user_colour']) : '';
	$profile_link = ($relation['relative_user_id']) ? $socialnet->get_username_string($socialnet->config['us_colour_username'], 'profile', $relation['relative_user_id'], $relation['username'], $relation['user_colour']) : '';

	if ($relation['family'])
	{
		if ($relation['approved'] != SN_RELATIONSHIP_APPROVED)
		{
			continue;
		}

		$template->assign_block_vars('family', array(
			'USER_ID'		 => $relation['relative_user_id'],
			'STATUS'		 => $socialnet->family_status($relation['status_id']),
			'U_RELATIVE'	 => ($relation['name']) ? '<strong>' . $relation['name'] . '</strong>' : $username,
			'U_PROFILE_LINK' => ($relation['name']) ? 'javascript:return false;' : $profile_link,
			'APPROVED'		 => ($relation['approved'] == SN_RELATIONSHIP_APPROVED) ? true : false,
			'REFUSED'		 => ($relation['approved'] == SN_RELATIONSHIP_REFUSED) ? true : false,
			'UNANSWERED'	 => ($relation['approved'] == SN_RELATIONSHIP_UNANSWERED) ? true : false,
			'AVATAR'		 => $avatar_img,
		));
	}
	else
	{
		if ($relation['anniversary'])
		{
			$relationship_arr = array_map('intval', explode('-', $relation['anniversary']));
			$relationship_anniversary = $user->format_date(gmmktime(0, 0, -$user->timezone, (int) $relationship_arr[1], (int) $relationship_arr[0], (int) $relationship_arr[2]), '|j. F Y|');
		}

		$template->assign_block_vars('relationship', array(
			'USER_ID'		 => $relation['relative_user_id'],
			'STATUS'		 => $socialnet->relationship_status($relation['status_id'], ($relation['approved'] == SN_RELATIONSHIP_APPROVED && $relation['relative_user_id'] != 0) ? true : false),
			'U_RELATIVE'	 => (!empty($relation['name'])) ? '<strong>' . $relation['name'] . '</strong>' : $username,
			'U_PROFILE_LINK' => (!empty($relation['name'])) ? 'javascript:return false;' : $profile_link,
			'APPROVED'		 => ($relation['approved'] == SN_RELATIONSHIP_APPROVED) ? true : false,
			'REFUSED'		 => ($relation['approved'] == SN_RELATIONSHIP_REFUSED) ? true : false,
			'UNANSWERED'	 => ($relation['approved'] == SN_RELATIONSHIP_UNANSWERED) ? true : false,
			'AVATAR'		 => $avatar_img,
			'ANNIVERSARY'	 => ($relation['anniversary']) ? $relationship_anniversary : '',
		));
	}
}
$db->sql_freeresult($result);

// Load 7 random friends
$socialnet->fms_users(array_merge(array(
	'mode'		 => 'friend',
	'user_id'	 => $user_id,
	'limit'		 => 7,
	'slider'	 => false,
	'total'		 => 7,
	'random'	 => true,
), $socialnet->fms_users_sqls('friend', $user_id)));

// Load Freind Groups
// Expression SN_MODULE_APPROVAL_ENABLED and not S_OWN_PROFILE and not U_ADD_FRIEND
if (in_array('approval', $socialnet->existing) && !($user_id === (int) $user->data['user_id']) && !(!$friend && !$foe && $friends_enabled) && !empty($socialnet->groups))
{
	foreach ($socialnet->groups as $gid => $g_data)
	{
		if ($gid == 0)
		{
			continue;
		}
		$template->assign_block_vars('sn_fms_group', array(
			'GID'				 => $gid,
			'S_GROUP_NAME'		 => $g_data['name'],
			'B_USER_IN_GROUP'	 => in_array($user_id, $g_data['users']),
		));
	}
}

// Inactive reason/account?
if ($member['user_type'] == USER_INACTIVE)
{
	$user->add_lang('acp/common');

	$inactive_reason = $user->lang['INACTIVE_REASON_UNKNOWN'];

	switch ($member['user_inactive_reason'])
	{
		case INACTIVE_REGISTER:
			$inactive_reason = $user->lang['INACTIVE_REASON_REGISTER'];
			break;

		case INACTIVE_PROFILE:
			$inactive_reason = $user->lang['INACTIVE_REASON_PROFILE'];
			break;

		case INACTIVE_MANUAL:
			$inactive_reason = $user->lang['INACTIVE_REASON_MANUAL'];
			break;

		case INACTIVE_REMIND:
			$inactive_reason = $user->lang['INACTIVE_REASON_REMIND'];
			break;
	}

	$template->assign_vars(array(
		'S_USER_INACTIVE'		 => true,
		'USER_INACTIVE_REASON'	 => $inactive_reason
	));
}

// Add visitor to profile visitors table and update profile_views
if ($user->data['is_registered'] && $user->data['user_id'] != $user_id)
{
	$sql = 'DELETE FROM ' . SN_PROFILE_VISITORS_TABLE . '
            WHERE profile_uid = ' . $user_id . '
              AND visitor_uid = ' . $user->data['user_id'];
	$db->sql_query($sql);

	$sql = 'INSERT INTO ' . SN_PROFILE_VISITORS_TABLE . ' (profile_uid, visitor_uid, visit_time)
            VALUES (' . $user_id . ', ' . $user->data['user_id'] . ', ' . time() . ')';
	$db->sql_query($sql);

	$sql = 'UPDATE ' . SN_USERS_TABLE . '
	          SET profile_views = profile_views + 1
	            WHERE user_id = ' . $user_id;
	$db->sql_query($sql);
}

$template->assign_vars(array(
	'U_UP_AJAXURL'		 => append_sid("{$socialnet_root_path}profile.{$phpEx}"),
	'U_UP_TAB'			 => append_sid("{$socialnet_root_path}profile.{$phpEx}", 'u=' . $user_id),
	'U_UP_TAB_WALL'		 => append_sid("{$socialnet_root_path}profile.{$phpEx}", 'mode=wall&amp;u=' . $user_id),
	'U_UP_TAB_INFO'		 => append_sid("{$socialnet_root_path}profile.{$phpEx}", 'mode=info&amp;u=' . $user_id),
	'U_UP_TAB_FRIENDS'	 => append_sid("{$socialnet_root_path}profile.{$phpEx}", 'mode=friends&amp;u=' . $user_id),
	'U_UP_TAB_STATS'	 => append_sid("{$socialnet_root_path}profile.{$phpEx}", 'mode=stats&amp;u=' . $user_id),
	'U_USER_REPORT'		 => ($config['up_enable_report']) ? append_sid("{$socialnet_root_path}profile.{$phpEx}", 'mode=report_user&amp;u=' . $user_id) : '',
));

page_header(sprintf($user->lang['VIEWING_PROFILE'], $member['username']));

page_footer();

?>