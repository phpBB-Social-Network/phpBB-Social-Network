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
	define('SN_FMS', true);
	$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
	$phpEx = substr(strrchr(__FILE__, '.'), 1);
	include_once($phpbb_root_path . 'common.' . $phpEx);
	include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);
	
	// Start session management
	$user->session_begin(false);
	$auth->acl($user->data);
	$user->setup('viewforum');
}

if (isset($socialnet) && defined('SN_FMS'))
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

	$mode = request_var('mode', 'friend');
	$user_id = request_var('usr', 0);
	$params = array(
		'mode'		 => $mode,
		'fmsf'		 => request_var('fmsf', 0),
		'limit'		 => request_var('flim', 0),
		'user_id'	 => $user_id,
		'checkbox'	 => request_var('chkbx', ''),
		'slider'	 => request_var('sl', 0) == 1,
		'ajax_load'	 => true,
		'profile_link' => request_var('pl',0) == 1,
	);

	$params = array_merge($params, $socialnet->fms_users_sqls($mode, $user_id));

	if ($mode == 'friendgroup')
	{
		$gid = request_var( 'gid',0);
		$params = array_merge($params, array(
			'limit'			 => - 1,
			'user_id_field'	 => 'user_id',
			'sql_content'	 => "SELECT u.user_id, u.username, u.username_clean, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height, u.user_colour
													FROM " . SN_FMS_USERS_GROUP_TABLE . " fms_g, " . USERS_TABLE . " u
														WHERE fms_g.user_id = u.user_id
															AND fms_g.owner_id = {$user->data['user_id']}
															AND fms_g.fms_gid = {$gid}
													ORDER BY u.username_clean ASC",
		));
	}
	else if ( $mode == 'friendprofiletab')
	{
		$params['mode_short'] = 'friend';
		$params = array_merge($params, $socialnet->fms_users_sqls('friend', $user_id));
	}
	else if ( $mode == 'suggestionfull')
	{
		$params = array_merge( $params, array(
			'mode_short'		 => 'suggestion',
			'add_friend_link'	 => true,
		));
		$params = array_merge( $params, $socialnet->fms_users_sqls('suggestion', $user->data['user_id']));
		
		$rowset = $params['rowset'];
		for( $i=0;$i<$params['fmsf'];$i++)
		{
			array_shift($rowset);
		}
		$params['rowset'] = $rowset;
	}
	
	$data = $socialnet->fms_users($params);

	header('Content-type: application/json');
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
	die(json_encode($data));
}
?>