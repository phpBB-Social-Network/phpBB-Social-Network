<?php
/**
 *
 * @package phpBB Social Network
 * @version 0.7.0
 * @copyright (c) phpBB Social Network Team 2010-2012 http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

/**
 * @ignore
 */
define('UMIL_AUTO', true);
/**
 * @ignore
 */
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include_once($phpbb_root_path . 'common.' . $phpEx);

$user->session_begin();
$auth->acl($user->data);
$user->setup();

if (!file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx))
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

// The name of the mod to be displayed during installation.
$mod_name = 'phpBB Social Network update';

/**
 * The name of the config variable which will hold the currently installed version
 * You do not need to set this yourself, UMIL will handle setting and updating the version itself.
 */
$version_config_name = 'version_socialNet';

/**
 * The language file which will be included when installing
 * Language entries that should exist in the language file for UMIL (replace $mod_name with the mod's name you set to $mod_name above)
 * $mod_name
 * 'INSTALL_' . $mod_name
 * 'INSTALL_' . $mod_name . '_CONFIRM'
 * 'UPDATE_' . $mod_name
 * 'UPDATE_' . $mod_name . '_CONFIRM'
 * 'UNINSTALL_' . $mod_name
 * 'UNINSTALL_' . $mod_name . '_CONFIRM'
 */
$language_file = array('ucp', 'mods/socialnet', 'mods/socialnet_acp');

/**
 * Load default constants for extend phpBB constants
 */
include_once($phpbb_root_path . 'socialnet/includes/constants.' . $phpEx);

/*
 * Options to display to the user (this is purely optional, if you do not need the options you do not have to set up this variable at all)
 * Uses the acp_board style of outputting information, with some extras (such as the 'default' and 'select_user' options)
 */

/*
 * Optionally we may specify our own logo image to show in the upper corner instead of the default logo.
 * $phpbb_root_path will get prepended to the path specified
 * Image height should be 50px to prevent cut-off or stretching.
 */
//$logo_img = 'styles/prosilver/imageset/site_logo.gif';

/*
 * The array of versions and actions within each.
 * You do not need to order it a specific way (it will be sorted automatically), however, you must enter every version, even if no actions are done for it.
 *
 * You must use correct version numbering.  Unless you know exactly what you can use, only use X.X.X (replacing X with an integer).
 * The version numbering must otherwise be compatible with the version_compare function - http://php.net/manual/en/function.version-compare.php
 */
$versions = array(
	'0.4.9.9'	 => array(
		'custom' => 'phpBB_SN_rename_table',
	),

	'0.5.0'		 => array(
		'table_add'			 => array(
			// GLOBAL TABLES
			array(SN_CONFIG_TABLE, array(
				'COLUMNS'		 => array(
					'config_name'	 => array('VCHAR', ''),
					'config_value'	 => array('VCHAR', ''),
					'is_dynamic'	 => array('BOOL', 0),
				),
				'PRIMARY_KEY'	 => array('config_name'),
				'KEYS'			 => array(
					'a'	 => array('INDEX', array('is_dynamic')),
				),
			)),

			array(SN_USERS_TABLE, array(
				'COLUMNS'		 => array(
					'user_id'					 => array('UINT', 0),
					'user_status'				 => array('TEXT', ''),
					'user_im_online'			 => array('BOOL', 1),
					'user_zebra_alert_friend'	 => array('BOOL', 1),
					'user_note'					 => array('TEXT', ''),
				),
				'PRIMARY_KEY'	 => array('user_id'),
			)),

			// INSTANT MESSENGER TABLES
			array(SN_IM_TABLE, array(
				'COLUMNS'	 => array(
					'uid_from'			 => array('UINT', 0),
					'uid_to'			 => array('UINT', 0),
					'message'			 => array('TEXT', ''),
					'sent'				 => array('UINT:11', 0),
					'recd'				 => array('BOOL', 0),
					'bbcode_bitfield'	 => array('VCHAR:255', ''),
					'bbcode_uid'		 => array('VCHAR:8', ''),
				),
				'KEYS'		 => array(
					'a'	 => array('INDEX', array('sent')),
				),
			)),
			array(SN_IM_CHATBOXES_TABLE, array(
				'COLUMNS'	 => array(
					'uid_from'		 => array('UINT', 0),
					'uid_to'		 => array('UINT', 0),
					'username_to'	 => array('VCHAR:255', ''),
					'starttime'		 => array('UINT:11', 0),
				),
				'KEYS'		 => array(
					'a'	 => array('UNIQUE', array('uid_from', 'uid_to')),
				),
			)),

			// USER STATUS TABLES
			array(SN_STATUS_TABLE, array(
				'COLUMNS'		 => array(
					'status_id'			 => array('UINT', NULL, 'auto_increment'),
					'poster_id'			 => array('UINT', 0),
					'status_time'		 => array('UINT:11', 0),
					'status_text'		 => array('TEXT', ''),
					'bbcode_bitfield'	 => array('VCHAR:255', ''),
					'bbcode_uid'		 => array('VCHAR:8', ''),
				),
				'PRIMARY_KEY'	 => array('status_id'),
			)),

			array(SN_STATUS_COMMENTS_TABLE, array(
				'COLUMNS'		 => array(
					'comment_id'		 => array('UINT', NULL, 'auto_increment'),
					'status_id'			 => array('UINT', 0),
					'poster_id'			 => array('UINT', 0),
					'comment_time'		 => array('UINT:11', 0),
					'comment_text'		 => array('TEXT', ''),
					'bbcode_bitfield'	 => array('VCHAR:255', ''),
					'bbcode_uid'		 => array('VCHAR:8', ''),
				),
				'PRIMARY_KEY'	 => array('comment_id'),
			)),

			array(SN_ENTRIES_TABLE, array(
				'COLUMNS'		 => array(
					'entry_id'		 => array('UINT', NULL, 'auto_increment'),
					'user_id'		 => array('UINT', 0),
					'entry_target'	 => array('UINT', 0),
					'entry_type'	 => array('UINT:11', 0),
					'entry_time'	 => array('UINT:11', 0),
				),
				'PRIMARY_KEY'	 => array('entry_id'),
			)),

		),

		'table_column_add'	 => array(
			array(ZEBRA_TABLE, 'approval', array('BOOL', 0)),
		),

		'table_row_insert'	 => array(
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_global_enable', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'module_im', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'module_userstatus', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'module_approval', 'config_value' => '0')),
			array(SN_CONFIG_TABLE, array('config_name' => 'module_mainpage', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'im_only_friends', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'im_allow_sound', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'im_url_new_window', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'fas_alert_friend_pm', 'config_value' => '0')),
			array(SN_USERS_TABLE, array('user_id' => ANONYMOUS, 'user_status' => '', 'user_im_online' => 1, 'user_zebra_alert_friend' => 0, 'user_note' => '')),
			array(SN_CONFIG_TABLE, array('config_name' => 'userstatus_comments_load_last', 'config_value' => '1', 'is_dynamic' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_cb_enable', 'config_value' => '1', 'is_dynamic' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_cb_resize', 'config_value' => '0', 'is_dynamic' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_cb_draggable', 'config_value' => '0', 'is_dynamic' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_cb_modal', 'config_value' => '1', 'is_dynamic' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_cb_width', 'config_value' => '400', 'is_dynamic' => 0)),
		),

		'permission_add'	 => array(
			array('a_sn_settings', true),
			array('u_sn_userstatus', true),
		),

		'module_add'		 => array(
			array('acp', 0, 'ACP_CAT_SOCIALNET'),
			array('acp', 'ACP_CAT_SOCIALNET', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'ACP_SN_MAIN',
				'module_mode'		 => 'main',
				'module_auth'		 => 'acl_a_sn_settings',
			)),
			array('acp', 'ACP_CAT_SOCIALNET', 'ACP_SN_CONFIGURATION'),
			array('acp', 'ACP_SN_CONFIGURATION', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'ACP_SN_AVAILABLE_MODULES',
				'module_mode'		 => 'sett_modules',
				'module_auth'		 => 'acl_a_sn_settings'
			)),
			array('acp', 'ACP_SN_CONFIGURATION', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'ACP_SN_CONFIRMBOX_SETTINGS',
				'module_mode'		 => 'sett_confirmBox',
				'module_auth'		 => 'acl_a_sn_settings'
			)),
			array('acp', 'ACP_CAT_SOCIALNET', 'ACP_SN_MODULES_CONFIGURATION'),
			array('acp', 'ACP_SN_MODULES_CONFIGURATION', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'ACP_SN_IM_SETTINGS',
				'module_mode'		 => 'module_im',
				'module_auth'		 => 'acl_a_sn_settings'
			)),
			array('acp', 'ACP_SN_MODULES_CONFIGURATION', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'ACP_SN_USERSTATUS_SETTINGS',
				'module_mode'		 => 'module_userstatus',
				'module_auth'		 => 'acl_a_sn_settings'
			)),
			array('acp', 'ACP_SN_MODULES_CONFIGURATION', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'ACP_SN_APPROVAL_SETTINGS',
				'module_mode'		 => 'module_approval',
				'module_auth'		 => 'acl_a_sn_settings'
			)),
			array('acp', 'ACP_SN_MODULES_CONFIGURATION', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'ACP_SN_MAINPAGE_SETTINGS',
				'module_mode'		 => 'module_mainpage',
				'module_auth'		 => 'acl_a_sn_settings'
			)),
			array('ucp', 0, 'UCP_SOCIALNET'),
			array('ucp', 'UCP_SOCIALNET', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'UCP_ZEBRA_FRIENDS',
				'module_mode'		 => 'module_approval_friends',
				'module_auth'		 => ''
			)),
			array('ucp', 'UCP_SOCIALNET', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'UCP_SN_IM',
				'module_mode'		 => 'module_im',
				'module_auth'		 => '',
			)),
		),

		'custom'			 => 'phpbb_SN_umil_auto',
	),
	'0.5.0.9'	 => array(
		'custom' => 'phpBB_SN_rename_table',
	),

	'0.5.1'		 => array(
		'table_column_add'	 => array(
			array(SN_USERS_TABLE, 'user_im_sound', array('TINT:1', '1')),
		),

		'table_row_insert'	 => array(
			array(SN_CONFIG_TABLE, array('config_name' => 'mp_show_new_friendships', 'config_value' => '1')),
		),

		'custom'			 => 'phpbb_SN_umil_auto',
	),

	'0.5.1.9'	 => array(
		'custom' => 'phpBB_SN_rename_table',
	),
	'0.5.2'		 => array(
		'table_row_insert'	 => array(
			array(SN_CONFIG_TABLE, array('config_name' => 'im_msg_purged_time', 'config_value' => '0')),
			array(SN_CONFIG_TABLE, array('config_name' => 'fas_friendlist_limit', 'config_value' => '20')),
			array(SN_CONFIG_TABLE, array('config_name' => 'mp_num_last_topics', 'config_value' => '10')),
		),

		'module_add'		 => array(
			array('ucp', 'UCP_SOCIALNET', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'UCP_SN_IM_HISTORY',
				'module_mode'		 => 'module_im_history',
				'module_auth'		 => '',
			)),
		),

		'table_column_add'	 => array(
			array(SN_USERS_TABLE, 'user_im_soundname', array('VCHAR:255', 'IM_New-message-1.mp3')),
		),

		'custom'			 => 'phpbb_SN_umil_auto',
	),

	'0.5.9.9'	 => array(
		'custom' => 'phpBB_SN_rename_table',
	),

	'0.6.0'		 => array(
		'table_index_add'	 => array(
			array(SN_IM_TABLE, 'b', array('uid_to', 'recd')),
			array(ZEBRA_TABLE, 'c', array('user_id', 'zebra_id', 'approval')),
			array(SN_IM_CHATBOXES_TABLE, 'b', array('uid_from', 'uid_to', 'starttime')),
			array(SN_ENTRIES_TABLE, 'a', array('user_id', 'entry_target', 'entry_type', 'entry_time')),
			array(SN_STATUS_TABLE, 'b', array('poster_id', 'status_time')),
			array(SN_STATUS_COMMENTS_TABLE, 'a', array('status_id', 'poster_id', 'comment_time')),
		),

		'table_add'			 => array(
			array(SN_NOTIFY_TABLE, array(
				'COLUMNS'		 => array(
					'ntf_id'	 => array('UINT:11', NULL, 'auto_increment'),
					'ntf_time'	 => array('UINT:11', 0),
					'ntf_type'	 => array('USINT', 0),
					'ntf_user'	 => array('UINT', 0),
					'ntf_poster' => array('UINT', 0),
					'ntf_read'	 => array('USINT', 0),
					'ntf_change' => array('UINT:11', 0),
					'ntf_data'	 => array('TEXT', ''),
				),
				'PRIMARY_KEY'	 => array('ntf_id'),
				'KEYS'			 => array(
					'a'	 => array('INDEX', array('ntf_read', 'ntf_user')),
					'b'	 => array('INDEX', array('ntf_read', 'ntf_time')),
					'c'	 => array('INDEX', array('ntf_read', 'ntf_change')),
				),
			)),
		),

		'table_column_add'	 => array(
			array(SN_STATUS_TABLE, 'page_data', array('TEXT', NULL)),
			array(SN_STATUS_TABLE, 'wall_id', array('UINT:8', 0)),
		),

		'module_add'		 => array(
			array('acp', 'ACP_SN_MODULES_CONFIGURATION', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'ACP_SN_NOTIFY_SETTINGS',
				'module_mode'		 => 'module_notify',
				'module_auth'		 => 'acl_a_sn_settings'
			)),
		),

		'table_row_insert'	 => array(
			array(SN_CONFIG_TABLE, array('config_name' => 'module_notify', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'ntf_theme', 'config_value' => 'default')),
			array(SN_CONFIG_TABLE, array('config_name' => 'mp_display_welcome', 'config_value' => '0', 'is_dynamic' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'im_msg_purged_automatic_time', 'config_value' => '0')),
		),
	),

	'0.6.0.9'	 => array(
		'custom' => 'phpBB_SN_rename_table',
	),

	'0.6.1'		 => array(
		'table_add'			 => array(
			array(SN_REPORTS_TABLE, array(
				'COLUMNS'		 => array(
					'report_id'		 => array('UINT', NULL, 'auto_increment'),
					'reason_id'		 => array('USINT', 0),
					'report_text'	 => array('TEXT', ''),
					'user_id'		 => array('UINT', 0),
					'reporter'		 => array('UINT', 0),
					'report_closed'	 => array('TINT:1', '0'),
				),
				'PRIMARY_KEY'	 => array('report_id'),
			)),
			array(SN_REPORTS_REASONS_TABLE, array(
				'COLUMNS'		 => array(
					'reason_id'		 => array('USINT', NULL, 'auto_increment'),
					'reason_text'	 => array('TEXT', ''),
				),
				'PRIMARY_KEY'	 => array('reason_id'),
			)),
			array(SN_MENU_TABLE, array(
				'COLUMNS'		 => array(
					'button_id'				 => array('UINT', NULL, 'auto_increment'),
					'button_url'			 => array('TEXT', ''),
					'button_name'			 => array('VCHAR', ''),
					'button_external'		 => array('BOOL', 0),
					'button_display'		 => array('BOOL', 1),
					'button_only_registered' => array('BOOL', 0),
					'button_only_guest'		 => array('BOOL', 0),
					'left_id'				 => array('UINT', 0),
					'right_id'				 => array('UINT', 0),
					'parent_id'				 => array('UINT', 0),
				),
				'PRIMARY_KEY'	 => array('button_id'),
				'KEY'			 => array(
					'a'	 => array('left_id'),
					'b'	 => array('right_id'),
					'c'	 => array('parent_id'),
					'd'	 => array('parent_id', 'left_id'),
				),
			)),
			array(SN_FAMILY_TABLE, array(
				'COLUMNS'		 => array(
					'id'				 => array('UINT', NULL, 'auto_increment'),
					'user_id'			 => array('UINT', '0'),
					'relative_user_id'	 => array('UINT', '0'),
					'status_id'			 => array('UINT', '0'),
					'approved'			 => array('TINT:1', '0'),
					'anniversary'		 => array('VCHAR:10', ''),
					'family'			 => array('TINT:1', '0'),
					'name'				 => array('VCHAR:255', ''),
				),
				'PRIMARY_KEY'	 => array('id'),
				'KEY'			 => array(
					'a'	 => array('user_id'),
					'b'	 => array('relative_user_id'),
					'c'	 => array('status_id'),
					'd'	 => array('approved'),
				),
			)),
			array(SN_PROFILE_VISITORS_TABLE, array(
				'COLUMNS'	 => array(
					'profile_uid'	 => array('UINT', '0'),
					'visitor_uid'	 => array('UINT', '0'),
					'visit_time'	 => array('UINT:11', '0'),
				),
				'KEY'		 => array(
					'a'	 => array('profile_uid'),
					'b'	 => array('visitor_uid'),
					'c'	 => array('visit_time')
				),
			)),
			array(SN_FMS_GROUPS_TABLE, array(
				'COLUMNS'		 => array(
					'fms_gid'	 => array('UINT', NULL, 'auto_increment'),
					'user_id'	 => array('UINT', '0'),
					'fms_name'	 => array('VCHAR:255', ''),
					'fms_clean'	 => array('VCHAR:255', ''),
				),
				'PRIMARY_KEY'	 => array('user_id', 'fms_clean'),
				'KEYS'			 => array(
					'a'	 => array('UNIQUE', array('user_id', 'fms_name')),
					'b'	 => array('INDEX', array('fms_gid', 'user_id')),
					'c'	 => array('INDEX', array('user_id')),
				),
			)),
			array(SN_FMS_USERS_GROUP_TABLE, array(
				'COLUMNS'		 => array(
					'fms_gid'	 => array('UINT', '0'),
					'user_id'	 => array('UINT', '0'),
				),
				'PRIMARY_KEY'	 => array('fms_gid', 'user_id'),
				'KEYS'			 => array(
					'a'	 => array('INDEX', array('user_id')),
					'b'	 => array('INDEX', array('fms_gid')),
				),
			)),
		),

		'table_column_add'	 => array(
			array(SN_USERS_TABLE, 'hometown', array('VCHAR:255', '')),
			array(SN_USERS_TABLE, 'sex', array('TINT:1', 0)),
			array(SN_USERS_TABLE, 'interested_in', array('TINT:1', 0)),
			array(SN_USERS_TABLE, 'languages', array('TEXT', '')),
			array(SN_USERS_TABLE, 'about_me', array('TEXT', '')),
			array(SN_USERS_TABLE, 'employer', array('TEXT', '')),
			array(SN_USERS_TABLE, 'university', array('TEXT', '')),
			array(SN_USERS_TABLE, 'high_school', array('TEXT', '')),
			array(SN_USERS_TABLE, 'religion', array('TEXT', '')),
			array(SN_USERS_TABLE, 'political_views', array('TEXT', '')),
			array(SN_USERS_TABLE, 'quotations', array('TEXT', '')),
			array(SN_USERS_TABLE, 'music', array('TEXT', '')),
			array(SN_USERS_TABLE, 'books', array('TEXT', '')),
			array(SN_USERS_TABLE, 'movies', array('TEXT', '')),
			array(SN_USERS_TABLE, 'games', array('TEXT', '')),
			array(SN_USERS_TABLE, 'foods', array('TEXT', '')),
			array(SN_USERS_TABLE, 'sports', array('TEXT', '')),
			array(SN_USERS_TABLE, 'sport_teams', array('TEXT', '')),
			array(SN_USERS_TABLE, 'activities', array('TEXT', '')),
			array(SN_USERS_TABLE, 'skype', array('VCHAR:32', '')),
			array(SN_USERS_TABLE, 'facebook', array('VCHAR:255', '')),
			array(SN_USERS_TABLE, 'twitter', array('VCHAR:255', '')),
			array(SN_USERS_TABLE, 'youtube', array('VCHAR:255', '')),
			array(SN_USERS_TABLE, 'profile_views', array('UINT:11', 0)),
			array(SN_USERS_TABLE, 'profile_last_change', array('UINT:11', 0)),
			array(SN_ENTRIES_TABLE, 'entry_additionals', array('TEXT', '')),
		),

		'table_row_insert'	 => array(
			array(SN_CONFIG_TABLE, array('config_name' => 'mp_hide_for_guest', 'config_value' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'im_colour_username', 'config_value' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'us_colour_username', 'config_value' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'fas_colour_username', 'config_value' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'mp_colour_username', 'config_value' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'ntf_colour_username', 'config_value' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'report_user_enable', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_block_myprofile', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_block_menu', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_block_search', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_block_friends_suggestions', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_block_friend_requests', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_block_birthday', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_block_recent_discussions', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_block_statistics', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_block_online_users', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'block_uo_all_users', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'block_uo_check_every', 'config_value' => '10')),
			array(SN_CONFIG_TABLE, array('config_name' => 'module_profile', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'up_enable_report', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'mp_show_profile_updated', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'mp_show_new_family', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'mp_show_new_relationship', 'config_value' => '1')),
			array(SN_REPORTS_REASONS_TABLE, array('reason_text' => 'This person is annoying me')),
			array(SN_REPORTS_REASONS_TABLE, array('reason_text' => 'This profile is pretending to be someone or is fake')),
			array(SN_REPORTS_REASONS_TABLE, array('reason_text' => 'Inappropriate profile photo')),
			array(SN_REPORTS_REASONS_TABLE, array('reason_text' => 'This person is bullying or harassing me')),
			array(SN_REPORTS_REASONS_TABLE, array('reason_text' => 'Inappropriate Wall post')),
			array(SN_REPORTS_REASONS_TABLE, array('reason_text' => 'Other')),
			array(SN_CONFIG_TABLE, array('config_name' => 'im_checkTime_min', 'config_value' => 1)),
			array(SN_CONFIG_TABLE, array('config_name' => 'im_checkTime_max', 'config_value' => 60)),
			array(SN_CONFIG_TABLE, array('config_name' => 'mp_num_last_posts', 'config_value' => 10)),
			array(SN_CONFIG_TABLE, array('config_name' => 'mp_max_profile_value', 'config_value' => 60)),
			array(SN_CONFIG_TABLE, array('config_name' => 'up_enable_subscriptions', 'config_value' => 1)),
			array(SN_CONFIG_TABLE, array('config_name' => 'up_alert_relation_pm', 'config_value' => '0')),
		),

		'permission_add'	 => array(
			array('m_sn_close_reports', true),
		),

		'module_add'		 => array(
			array('acp', 'ACP_SN_CONFIGURATION', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'ACP_SN_BLOCKS_ENABLE',
				'module_mode'		 => 'blocks_enable',
				'module_auth'		 => 'acl_a_sn_settings'
			)),
			array('acp', 'ACP_SN_CONFIGURATION', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'ACP_SN_BLOCKS_CONFIGURATION',
				'module_mode'		 => 'blocks_config',
				'module_auth'		 => 'acl_a_sn_settings'
			)),
			array('ucp', 'UCP_SOCIALNET', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'UCP_SN_APPROVAL_UFG',
				'module_mode'		 => 'module_approval_ufg',
				'module_auth'		 => ''
			)),
			array('ucp', 'UCP_SOCIALNET', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'UCP_SN_PROFILE',
				'module_mode'		 => 'module_profile',
				'module_auth'		 => ''
			)),
			array('ucp', 'UCP_SOCIALNET', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'UCP_SN_PROFILE_RELATIONS',
				'module_mode'		 => 'module_profile_relations',
				'module_auth'		 => '',
			)),
			array('ucp', 'UCP_PROFILE', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'UCP_SN_PROFILE',
				'module_mode'		 => 'module_profile',
				'module_auth'		 => ''
			)),
			array('ucp', 'UCP_PROFILE', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'UCP_SN_PROFILE_RELATIONS',
				'module_mode'		 => 'module_profile_relations',
				'module_auth'		 => '',
			)),
			array('acp', 'ACP_SN_MODULES_CONFIGURATION', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'ACP_SN_PROFILE_SETTINGS',
				'module_mode'		 => 'module_profile',
				'module_auth'		 => 'acl_a_sn_settings'
			)),
			array('mcp', 0, 'MCP_SOCIALNET'),
			array('mcp', 'MCP_SOCIALNET', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'MCP_SN_REPORTUSER',
				'module_mode'		 => 'module_reportuser',
				'module_auth'		 => 'acl_m_sn_close_reports'
			)),
		),

		'custom'			 => 'phpbb_SN_umil_auto',
	),

	'0.6.2'		 => array(
		'custom' => 'phpbb_SN_umil_auto',
	),

	'0.7.0'		 => array(
		'table_add'				 => array(
			array(SN_COMMENTS_MODULES_TABLE, array(
				'COLUMNS'		 => array(
					'cmtmd_id'	 => array('UINT', NULL, 'auto_increment'),
					'cmtmd_name' => array('VCHAR:255', ''),
				),
				'PRIMARY_KEY'	 => array('cmtmd_id', 'cmtmd_name'),
				'KEYS'			 => array(
					'a'	 => array('UNIQUE', array('cmtmd_name')),
				),
			)),
			array(SN_COMMENTS_TABLE, array(
				'COLUMNS'		 => array(
					'cmt_id'			 => array('UINT', NULL, 'auto_increment'),
					'cmt_module'		 => array('UINT', 0),
					'cmt_time'			 => array('UINT:11', 0),
					'cmt_mid'			 => array('UINT', 0),
					'cmt_poster'		 => array('UINT', 0),
					'cmt_text'			 => array('TEXT', ''),
					'bbcode_bitfield'	 => array('VCHAR:255', ''),
					'bbcode_uid'		 => array('VCHAR:8', ''),
				),
				'PRIMARY_KEY'	 => array('cmt_id', 'cmt_module', 'cmt_mid'),
				'KEYS'			 => array(
					'a'	 => array('INDEX', array('cmt_module')),
					'b'	 => array('INDEX', array('cmt_time')),
					'c'	 => array('INDEX', array('cmt_module', 'cmt_mid')),
					'd'	 => array('INDEX', array('cmt_module', 'cmt_mid', 'cmt_time')),
					'e'	 => array('INDEX', array('cmt_module', 'cmt_mid', 'cmt_time', 'cmt_poster')),
				),
			)),
			array(SN_EMOTES_TABLE, array(
				'COLUMNS'		 => array(
					'emote_id'		 => array('UINT:8', NULL, 'auto_increment'),
					'emote_name'	 => array('VCHAR:255', ''),
					'emote_image'	 => array('VCHAR:255', ''),
					'emote_order'	 => array('UINT:8', 0),
				),
				'PRIMARY_KEY'	 => array('emote_id'),
				'KEYS'			 => array(
					'u'	 => array('UNIQUE', array('emote_name')),
					'a'	 => array('INDEX', array('emote_name', 'emote_order')),
					'b'	 => array('INDEX', array('emote_order')),
				)
			)),
			array(SN_ADDONS_PLACEHOLDER_TABLE, array(
				'COLUMNS'		 => array(
					'ph_id'		 => array('UINT:8', null, 'auto_increment'),
					'ph_script'	 => array('VCHAR:64', ''),
					'ph_block'	 => array('VCHAR:16', ''),
				),
				'PRIMARY_KEY'	 => array('ph_id'),
				'KEYS'			 => array(
					'u'	 => array('UNIQUE', array('ph_script', 'ph_block')),
					'a'	 => array('INDEX', array('ph_script')),
					'b'	 => array('INDEX', array('ph_block')),
				),
			)),
			array(SN_ADDONS_TABLE, array(
				'COLUMNS'		 => array(
					'addon_id'			 => array('UINT:8', NULL, 'auto_increment'),
					'addon_placeholder'	 => array('UINT:8', 0),
					'addon_name'		 => array('VCHAR:64', ''),
					'addon_php'			 => array('VCHAR:32', ''),
					'addon_function'	 => array('VCHAR:32', ''),
					'addon_active'		 => array('USINT', 0),
					'addon_order'		 => array('UINT:8', 0)
				),
				'PRIMARY_KEY'	 => array('addon_id'),
				'KEYS'			 => array(
					'u'	 => array('UNIQUE', array('addon_placeholder', 'addon_name', 'addon_php', 'addon_function')),
					'a'	 => array('INDEX', array('addon_name', 'addon_php', 'addon_active')),
					'b'	 => array('INDEX', array('addon_order')),
				),
			)),
			array(SN_SMILIES_TABLE, array(
				'COLUMNS'		 => array(
					'smiley_id'		 => array('UINT:8', 0),
					'smiley_allowed' => array('TINT:1', 0),
				),
				'PRIMARY_KEY'	 => array('smiley_id'),
			)),

		),

		'table_column_add'		 => array(
			array(SN_FMS_GROUPS_TABLE, 'fms_collapse', array('BOOL', 0)),
			array(SN_FMS_USERS_GROUP_TABLE, 'owner_id', array('UINT:11', 0)),
		),

		'table_column_update'	 => array(
			array(SN_IM_TABLE, 'sent', array('PDECIMAL:20', 0)),
	//		array(SN_FMS_GROUPS_TABLE, 'fms_gid', array('UINT', NULL)), <= Already UINT
		),

		'table_index_add'		 => array(
			array(SN_FMS_GROUPS_TABLE, 'd', array('fms_gid', 'user_id', 'fms_clean')),
			array(SN_FMS_GROUPS_TABLE, 'e', array('fms_gid', 'user_id', 'fms_clean', 'fms_collapse')),
			array(SN_FMS_USERS_GROUP_TABLE, 'c', array('fms_gid', 'owner_id')),
		),

		'table_row_insert'		 => array(
			array(SN_CONFIG_TABLE, array('config_name' => 'ntf_life', 'config_value' => 10)),
			array(SN_CONFIG_TABLE, array('config_name' => 'ntf_checktime', 'config_value' => 10)),

			array(SN_EMOTES_TABLE, array('emote_name' => 'Poke', 'emote_image' => '', 'emote_order' => 1)),
			array(SN_EMOTES_TABLE, array('emote_name' => 'Hug', 'emote_image' => '', 'emote_order' => 2)),
			array(SN_EMOTES_TABLE, array('emote_name' => 'High five', 'emote_image' => '', 'emote_order' => 3)),
			array(SN_EMOTES_TABLE, array('emote_name' => 'Kiss', 'emote_image' => '', 'emote_order' => 4)),
			array(SN_EMOTES_TABLE, array('emote_name' => 'Cuddle', 'emote_image' => '', 'emote_order' => 5)),
			array(SN_EMOTES_TABLE, array('emote_name' => 'Flirt', 'emote_image' => '', 'emote_order' => 6)),
			array(SN_EMOTES_TABLE, array('emote_name' => 'Love', 'emote_image' => '', 'emote_order' => 7)),
			array(SN_EMOTES_TABLE, array('emote_name' => 'Sorry', 'emote_image' => '', 'emote_order' => 8)),
			array(SN_EMOTES_TABLE, array('emote_name' => 'Applaud', 'emote_image' => '', 'emote_order' => 9)),
			array(SN_EMOTES_TABLE, array('emote_name' => 'Beer', 'emote_image' => '', 'emote_order' => 10)),
			array(SN_EMOTES_TABLE, array('emote_name' => 'Slap', 'emote_image' => '', 'emote_order' => 11)),
			array(SN_EMOTES_TABLE, array('emote_name' => 'Boo', 'emote_image' => '', 'emote_order' => 12)),
			array(SN_ADDONS_PLACEHOLDER_TABLE, array('ph_script' => 'mainpage', 'ph_block' => 'header')),
			array(SN_ADDONS_PLACEHOLDER_TABLE, array('ph_script' => 'mainpage', 'ph_block' => 'leftcolumn')),
			array(SN_ADDONS_PLACEHOLDER_TABLE, array('ph_script' => 'mainpage', 'ph_block' => 'rightcolumn')),
			array(SN_ADDONS_PLACEHOLDER_TABLE, array('ph_script' => 'profile', 'ph_block' => 'tab statistics')),
			array(SN_ADDONS_PLACEHOLDER_TABLE, array('ph_script' => 'profile', 'ph_block' => 'tab info')),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_dialog_browseroutdated', 'config_value' => '0')),
		),
		'table_row_remove'		 => array(
			array(SN_CONFIG_TABLE, array('config_name' => 'up_alert_relation_pm')),
			array(SN_CONFIG_TABLE, array('config_name' => 'fas_alert_friend_pm')),
		),
		'module_add'			 => array(
			array('acp', 'ACP_SN_CONFIGURATION', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'ACP_SN_ADDONS_HOOK_CONFIGURATION',
				'module_mode'		 => 'addons',
				'module_auth'		 => 'acl_a_sn_settings'
			)),
		),

		'custom'				 => array(
			'phpbb_SN_umil_0_6_2_4',
			'phpbb_SN_umil_0_6_2_6',
			'phpbb_SN_umil_0_6_2_8',
			'phpbb_SN_umil_0_6_2_16',
			'phpbb_SN_umil_send'
		),
		'cache_purge'			 => array(
			'imageset',
			'template',
			'theme',
			'cache',
		),
	),

);

if (!defined('DEBUG_EXTRA'))
{
	define('DEBUG_EXTRA', true);
}
// Include the UMIF Auto file and everything else will be handled automatically.
/**
 * @ignore
 */
include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);

/**
 * Here is our custom function that will be called.
 *
 * @access public
 * @param string $action The action (install|update|uninstall) will be sent through this.
 * @param string $version The version this is being run for will be sent through this.
 */
function phpbb_SN_umil_auto($action, $version)
{
	global $db, $umil;

	if ($action == 'uninstall')
	{
		// Run this when uninstalling
	}
	else if ($action == 'install')
	{
		// Run this when installing
		$umil->permission_set('REGISTERED', 'u_sn_userstatus', 'group');
	}
	else
	{
		// Run this when updating
		if ($version == '0.6.0')
		{
			$db->sql_query("UPDATE " . SN_STATUS_TABLE . " SET wall_id = poster_id");
		}
	}
}

function phpbb_SN_umil_0_6_1($action, $version)
{
	global $umil;
	phpBB_SN_rename_table($action, $version);

	$umil->permission_set('ROLE_MOD_STANDARD', 'm_sn_close_reports', 'role', $action != 'uninstall');
	$umil->permission_set('ROLE_MOD_FULL', 'm_sn_close_reports', 'role', $action != 'uninstall');
	$umil->permission_set('ROLE_MOD_QUEUE', 'm_sn_close_reports', 'role', false);
	$umil->permission_set('ROLE_MOD_SIMPLE', 'm_sn_close_reports', 'role', false);
}

function phpbb_SN_umil_0_6_2_4($action, $version)
{
	global $db, $umil, $versions;

	$module_name = 'userstatus';
	$rs = $db->sql_query('SELECT * FROM ' . SN_COMMENTS_MODULES_TABLE . " WHERE cmtmd_name = '{$module_name}'");
	$module_id = $db->sql_fetchfield('cmtmd_id');

	$return_status = '';
	if ($umil->table_exists(SN_COMMENTS_TABLE) && $umil->table_exists(SN_STATUS_COMMENTS_TABLE) && $action != 'uninstall')
	{
		if ((int) $module_id == 0)
		{
			$db->sql_query("INSERT INTO " . SN_COMMENTS_MODULES_TABLE . " (cmtmd_name) VALUE ('userstatus')");
			$rs = $db->sql_query('SELECT * FROM ' . SN_COMMENTS_MODULES_TABLE . " WHERE cmtmd_name = 'userstatus'");
			$module_id = $db->sql_fetchfield('cmtmd_id');
		}

		$sql = "INSERT INTO " . SN_COMMENTS_TABLE . " (cmt_module, cmt_time, cmt_mid, cmt_poster, cmt_text, bbcode_bitfield, bbcode_uid)
				SELECT '{$module_id}' AS cmt_module, comment_time AS cmt_time, status_id AS cmt_mid, poster_id AS cmt_poster, comment_text AS cmt_text, bbcode_bitfield, bbcode_uid
						FROM " . SN_STATUS_COMMENTS_TABLE . "";

		$db->sql_query($sql);

		$umil->table_remove(SN_STATUS_COMMENTS_TABLE);

		$return_status = "- updated";
	}
	else if ($action == 'uninstall')
	{
		if (!$umil->table_exists(SN_STATUS_COMMENTS_TABLE))
		{
			$umil->table_add(SN_STATUS_COMMENTS_TABLE, $versions['0.5.0']['table_add'][5][1]);
		}

		$sql = "INSERT INTO " . SN_STATUS_COMMENTS_TABLE . " (comment_time, status_id, poster_id, comment_text, bbcode_bitfield, bbcode_uid)
							SELECT cmt_time AS comment_time, cmt_mid AS status_id, cmt_poster AS poster_id, cmt_text AS comment_text, bbcode_bitfield, bbcode_uid
								FROM " . SN_COMMENTS_TABLE . "
									WHERE cmt_module = '{$module_id}'";
		$db->sql_query($sql);
		$return_status = "- reverted back";
	}

	return 'Social Network::Comments system ' . $return_status;
}

function phpbb_SN_umil_0_6_2_6($action, $version)
{
	global $db;

	$return_status = '';
	if ($action != 'uninstall')
	{
		$sql = "SELECT fms_gid, user_id FROM " . SN_FMS_GROUPS_TABLE . " WHERE fms_gid > 0 ORDER BY user_id, fms_gid";

		$rs = $db->sql_query($sql);

		$rowset = $db->sql_fetchrowset($rs);

		$c_user = 0;
		$c_counter = 1;
		for ($i = 0; isset($rowset[$i]); $i++)
		{
			$r_gid = $rowset[$i]['fms_gid'];
			$r_uid = $rowset[$i]['user_id'];
			if ($c_user != $r_uid)
			{
				$c_user = $r_uid;
				$c_counter = 1;
			}

			$sql = "UPDATE " . SN_FMS_GROUPS_TABLE . " SET fms_gid = {$c_counter} WHERE fms_gid = {$r_gid} AND user_id = {$r_uid}";
			$db->sql_query($sql);

			$sql = "UPDATE " . SN_FMS_USERS_GROUP_TABLE . " SET fms_gid = {$c_counter}, owner_id = {$c_user} WHERE fms_gid = {$r_gid} AND owner_id = 0";
			$db->sql_query($sql);

			$c_counter++;
		}

		$sql = "SELECT user_id FROM " . USERS_TABLE . " WHERE user_type <> 2";
		$rs = $db->sql_query($sql);
		$rowset = $db->sql_fetchrowset($rs);
		if ($action == 'update')
		{
			$db->sql_return_on_error(true);
			for ($i = 0; isset($rowset[$i]); $i++)
			{
				$sql = "INSERT INTO " . SN_FMS_GROUPS_TABLE . " (fms_gid,user_id,fms_name,fms_clean,fms_collapse) VALUES (0,{$rowset[$i]['user_id']}, '---','---',0)";
				$db->sql_query($sql);
			}
			$db->sql_return_on_error(false);
		}

		$sql = "SELECT COUNT(*) FROM " . SN_FMS_USERS_GROUP_TABLE . " WHERE owner_id = 0";
		$rs = $db->sql_query($sql);
		if ($db->sql_affectedrows($rs))
		{
			$return_status = '- There are friends to be included into groups. Use SQL manager to repair.';
		}

		$db->sql_return_on_error(true);
		$db->sql_query('ALTER TABLE ' . SN_FMS_USERS_GROUP_TABLE . ' DROP PRIMARY KEY');
		$db->sql_query('ALTER TABLE ' . SN_FMS_USERS_GROUP_TABLE . ' ADD PRIMARY KEY (fms_gid, user_id, owner_id)');
		$db->Sql_return_on_error(false);
	}
	return 'Social Network::FMS Groups updated' . $return_status;
}

function phpbb_SN_umil_0_6_2_8($action, $version)
{
	global $db;

	$db->sql_query('UPDATE ' . SN_CONFIG_TABLE . ' SET config_name = "ap_show_new_friendships" WHERE config_name = "mp_show_new_friendships"');
	$db->sql_query('UPDATE ' . SN_CONFIG_TABLE . ' SET config_name = "ap_num_last_topics" WHERE config_name = "mp_num_last_topics"');
	$db->sql_query('UPDATE ' . SN_CONFIG_TABLE . ' SET config_name = "ap_display_welcome" WHERE config_name = "mp_display_welcome"');
	$db->sql_query('UPDATE ' . SN_CONFIG_TABLE . ' SET config_name = "ap_hide_for_guest" WHERE config_name = "mp_hide_for_guest"');
	$db->sql_query('UPDATE ' . SN_CONFIG_TABLE . ' SET config_name = "ap_colour_username" WHERE config_name = "mp_colour_username"');
	$db->sql_query('UPDATE ' . SN_CONFIG_TABLE . ' SET config_name = "ap_show_profile_updated" WHERE config_name = "mp_show_profile_updated"');
	$db->sql_query('UPDATE ' . SN_CONFIG_TABLE . ' SET config_name = "ap_show_new_family" WHERE config_name = "mp_show_new_family"');
	$db->sql_query('UPDATE ' . SN_CONFIG_TABLE . ' SET config_name = "ap_show_new_relationship" WHERE config_name = "mp_show_new_relationship"');
	$db->sql_query('UPDATE ' . SN_CONFIG_TABLE . ' SET config_name = "ap_num_last_posts" WHERE config_name = "mp_num_last_posts"');
	$db->sql_query('UPDATE ' . SN_CONFIG_TABLE . ' SET config_name = "ap_max_profile_value" WHERE config_name = "mp_max_profile_value"');
	$db->sql_query('UPDATE ' . SN_CONFIG_TABLE . ' SET config_name = "module_activitypage" WHERE config_name = "module_mainpage"');

	$db->sql_query('UPDATE ' . SN_ADDONS_PLACEHOLDER_TABLE . ' SET ph_script = "activitypage" WHERE ph_id = 1');
	$db->sql_query('UPDATE ' . SN_ADDONS_PLACEHOLDER_TABLE . ' SET ph_script = "activitypage" WHERE ph_id = 2');
	$db->sql_query('UPDATE ' . SN_ADDONS_PLACEHOLDER_TABLE . ' SET ph_script = "activitypage" WHERE ph_id = 3');

	$db->sql_query('UPDATE ' . MODULES_TABLE . ' SET module_langname = "ACP_SN_ACTIVITYPAGE_SETTINGS", module_mode = "module_activitypage" WHERE module_mode = "module_mainpage"');
}

function phpbb_SN_umil_0_6_2_16($action, $version)
{
	global $db;

	if ($action == 'install' || $action == 'update')
	{
		$sql = "SELECT smiley_id FROM " . SMILIES_TABLE;
		$rs = $db->sql_query($sql);

		$db->sql_return_on_error(true);
		while ($row = $db->sql_fetchrow($rs))
		{
			$sql = "INSERT INTO " . SN_SMILIES_TABLE . " (smiley_id, smiley_allowed) VALUES ({$row['smiley_id']},1)";
			$db->sql_query($sql);
		}
		$db->sql_return_on_error(false);

		$db->sql_freeresult($rs);

		return 'Social Network::IM smilies default settings added';
	}
	else
	{
		return 'Social Network::IM smilies default settings untouched';
	}
}

function phpbb_SN_umil_send($action, $version)
{
	global $version_config_name, $config, $user;

	$data = array(
		'a'	 => $action,
		'o'	 => isset($config[$version_config_name]) ? $config[$version_config_name] : '0.0.0',
		'n'	 => ($action == 'uninstall') ? $_POST['version_select'] : $version,
		's'	 => $config['server_name'],
		'p'	 => $config['script_path'],
		't'	 => time(),
		'u'	 => $user->data['username']
	);
	$query = http_build_query(array('q' => base64_encode(serialize($data))));

	$host = "update.phpbb3hacks.com";
	$directory = '/socialnet';
	$filename = 'update_sn.php';
	$port = 80;
	$errno = 0;
	$errstr = '';
	$timeout = 6;

	$file_info = '';
	if ($fsock = @fsockopen($host, $port, $errno, $errstr, $timeout))
	{
		@fputs($fsock, "POST $directory/$filename HTTP/1.1\r\n");
		@fputs($fsock, "Host: $host\r\n");
		@fputs($fsock, "Referer: {$_SERVER['HTTP_REFERER']}\r\n");
		@fputs($fsock, "Content-type: application/x-www-form-urlencoded\r\n");
		@fputs($fsock, "Content-length: " . strlen($query) . "\r\n");
		@fputs($fsock, "Connection: close\r\n\r\n");
		@fputs($fsock, $query);

		$timer_stop = time() + $timeout;
		@stream_set_timeout($fsock, $timeout);

		$get_info = false;

		while (!@feof($fsock))
		{
			if ($get_info)
			{
				$file_info .= @fread($fsock, 1024);
			}
			else
			{
				$line = @fgets($fsock, 1024);
				if ($line == "\r\n")
				{
					$get_info = true;
				}
				else if (stripos($line, '404 not found') !== false)
				{
					$errstr = $user->lang['FILE_NOT_FOUND'] . ': ' . $filename;
					return false;
				}
			}

			$stream_meta_data = stream_get_meta_data($fsock);

			if (!empty($stream_meta_data['timed_out']) || time() >= $timer_stop)
			{
				return false;
			}
		}
		$file_info = explode("\r\n", trim($file_info));
		$file_info = $file_info[1];
		@fclose($fsock);
	}
	else
	{
		$file_info = strtoupper($action) . '_FAILED';
	}

	return "Social Network: {$action} is completed";
}
/**
 * Function for table rename by install/update
 */
function phpBB_SN_rename_table($action, $version)
{
	$constants = get_defined_constants();
	foreach ($constants as $key => $value)
	{
		if (preg_match('/^SN_.*_TABLE$/', $key))
		{
			sql_rename_table($value);
		}
	}
}

/**
 * Function for catch old table name
 */
function sql_old_table_name($new_name)
{
	global $table_prefix, $table_prefix_socialnet;
	return preg_replace('/^' . $table_prefix_socialnet . '/', $table_prefix . 'socialnet_', $new_name);
}

/**
 * Function for rename SQL table for any Layer
 */
function sql_rename_table($new_name)
{
	global $db, $umil, $dbms;

	$old_name = sql_old_table_name($new_name);
	if ($umil->table_exists($old_name) && !$umil->table_exists($new_name))
	{
		switch ($db->sql_layer)
		{
			case 'firebird':
			case 'postgres':
			case 'oracle':
			case 'sqlite':
			case 'mysql_40':
			case 'mysql_41':
			case 'mysqli':
			case 'mysql':
			case 'mysql4':
				$sql = "ALTER TABLE {$old_name} RENAME TO {$new_name}";
				break;

			case 'mssql':
			case 'mssqlnative':
				$sql = "EXEC sp_rename '{$old_name}', '{$new_name}'";
				break;
		}

		$db->sql_query($sql);
	}
}

?>