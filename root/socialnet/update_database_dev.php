#!/usr/bin/env php
<?php
/**
 *
 * @package phpBB Social Network
 * @version 0.7.0
 * @copyright (c) phpBB Social Network Team 2010-2012 http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */
//test
function show_usage()
{
	echo "Automatically runs phpBB SocialNetwork UMIL developer database update script.\n";
	echo "\n";

	echo "Usage: [php] update_database_dev.php [OPTIONS]\n";
	echo "\n";

	echo "Options:\n";
	echo " -a action                      Action: install/update/uninstall\n";
	echo " -h                             This help text\n";

	exit(2);
}

// Handle arguments
$opts = getopt('a:h');

if (empty($opts) || isset($opts['h']))
{
	show_usage();
}

$action	= get_arg($opts, 'a', '');

// action may be only install, update, or uninstall
if ( !in_array($action, array('install', 'update', 'uninstall')) )
{
	show_usage();
}

/**
 * @ignore
 */
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : 'root/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include_once($phpbb_root_path . 'common.' . $phpEx);

if (!file_exists($phpbb_root_path . 'umil/umil.' . $phpEx))
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

if (!class_exists('umil'))
{
	include($phpbb_root_path . 'umil/umil.' . $phpEx);
}

// If you want a completely stand alone version (able to use UMIL without messing with any of the language stuff) send true, otherwise send false
$umil = new umil(true);

/**
 * The name of the config variable which will hold the currently installed version
 * You do not need to set this yourself, UMIL will handle setting and updating the version itself.
 */
$version_config_name = 'version_socialNet';

/**
 * Load default constants for extend phpBB constants
 */
include_once($phpbb_root_path . 'socialnet/includes/constants.' . $phpEx);

/*
 * The array of versions and actions within each.
 * You do not need to order it a specific way (it will be sorted automatically), however, you must enter every version, even if no actions are done for it.
 *
 * You must use correct version numbering.  Unless you know exactly what you can use, only use X.X.X (replacing X with an integer).
 * The version numbering must otherwise be compatible with the version_compare function - http://php.net/manual/en/function.version-compare.php
 */
$versions = array(
	'0.7.0'	 => array(
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
					'user_im_sound'				 => array('TINT:1', '1'),
					'user_im_soundname'			 => array('VCHAR:255', 'IM_New-message-1.mp3'),
					'hometown'					 => array('VCHAR:255', ''),
					'sex'						 => array('TINT:1', 0),
					'interested_in'				 => array('TINT:1', 0),
					'languages'					 => array('TEXT', ''),
					'about_me'					 => array('TEXT', ''),
					'employer'					 => array('TEXT', ''),
					'university'				 => array('TEXT', ''),
					'high_school'				 => array('TEXT', ''),
					'religion'					 => array('TEXT', ''),
					'political_views'			 => array('TEXT', ''),
					'quotations'				 => array('TEXT', ''),
					'music'						 => array('TEXT', ''),
					'books'						 => array('TEXT', ''),
					'movies'					 => array('TEXT', ''),
					'games'						 => array('TEXT', ''),
					'foods'						 => array('TEXT', ''),
					'sports'					 => array('TEXT', ''),
					'sport_teams'				 => array('TEXT', ''),
					'activities'				 => array('TEXT', ''),
					'skype'						 => array('VCHAR:32', ''),
					'facebook'					 => array('VCHAR:255', ''),
					'twitter'					 => array('VCHAR:255', ''),
					'youtube'					 => array('VCHAR:255', ''),
					'profile_views'				 => array('UINT:11', 0),
					'profile_last_change'		 => array('UINT:11', 0),
				),
				'PRIMARY_KEY'	 => array('user_id'),
			)),

			// INSTANT MESSENGER TABLES
			array(SN_IM_TABLE, array(
				'COLUMNS'	 => array(
					'uid_from'			 => array('UINT', 0),
					'uid_to'			 => array('UINT', 0),
					'message'			 => array('TEXT', ''),
					'sent'				 => array('PDECIMAL:20', 0),
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
					'b'	 => array('INDEX', array('uid_from', 'uid_to', 'starttime')),
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
					'page_data'			 => array('TEXT', NULL),
					'wall_id'			 => array('UINT:8', 0),
				),
				'PRIMARY_KEY'	 => array('status_id'),
				'KEYS'			 => array(
					'b' => array('INDEX', array('poster_id', 'status_time')), ),
			)),

			array(SN_ENTRIES_TABLE, array(
				'COLUMNS'		 => array(
					'entry_id'			 => array('UINT', NULL, 'auto_increment'),
					'user_id'			 => array('UINT', 0),
					'entry_target'		 => array('UINT', 0),
					'entry_type'		 => array('UINT:11', 0),
					'entry_time'		 => array('UINT:11', 0),
					'entry_additionals'	 => array('TEXT', ''),
				),
				'PRIMARY_KEY'	 => array('entry_id'),
				'KEYS'			 => array(
					'a'	 => array('INDEX', array('user_id', 'entry_target', 'entry_type', 'entry_time')),
				),
			)),

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
					'fms_gid'		 => array('UINT', NULL, 'auto_increment'),
					'user_id'		 => array('UINT', '0'),
					'fms_name'		 => array('VCHAR:255', ''),
					'fms_clean'		 => array('VCHAR:255', ''),
					'fms_collapse'	 => array('BOOL', 0),
				),
				'PRIMARY_KEY'	 => array('user_id', 'fms_clean'),
				'KEYS'			 => array(
					'a'	 => array('UNIQUE', array('user_id', 'fms_name')),
					'b'	 => array('INDEX', array('fms_gid', 'user_id')),
					'c'	 => array('INDEX', array('user_id')),
					'd'	 => array('INDEX', array('fms_gid', 'user_id', 'fms_clean')),
					'e'	 => array('INDEX', array('fms_gid', 'user_id', 'fms_clean', 'fms_collapse')),
				),
			)),
			array(SN_FMS_USERS_GROUP_TABLE, array(
				'COLUMNS'		 => array(
					'fms_gid'	 => array('UINT', '0'),
					'user_id'	 => array('UINT', '0'),
					'owner_id'	 => array('UINT:11', 0),
				),
				'PRIMARY_KEY'	 => array('fms_gid', 'user_id', 'owner_id'),
				'KEYS'			 => array(
					'a'	 => array('INDEX', array('user_id')),
					'b'	 => array('INDEX', array('fms_gid')),
					'c'	 => array('INDEX', array('fms_gid', 'owner_id')),
				),
			)),

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

		'table_column_add'	 => array(
			array(ZEBRA_TABLE, 'approval', array('BOOL', 0)),
		),

		'table_index_add'	 => array(
			array(ZEBRA_TABLE, 'c', array('user_id', 'zebra_id', 'approval')),
		),

		'table_row_insert'	 => array(
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_global_enable', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_cb_enable', 'config_value' => '1', 'is_dynamic' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_cb_resize', 'config_value' => '0', 'is_dynamic' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_cb_draggable', 'config_value' => '0', 'is_dynamic' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_cb_modal', 'config_value' => '1', 'is_dynamic' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_cb_width', 'config_value' => '400', 'is_dynamic' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_block_myprofile', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_block_menu', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_block_search', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_block_friends_suggestions', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_block_friend_requests', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_block_birthday', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_block_recent_discussions', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_block_statistics', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_block_online_users', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'sn_dialog_browseroutdated', 'config_value' => '0')),
			array(SN_CONFIG_TABLE, array('config_name' => 'module_im', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'module_userstatus', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'module_approval', 'config_value' => '0')),
			array(SN_CONFIG_TABLE, array('config_name' => 'module_activitypage', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'module_profile', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'module_notify', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'userstatus_comments_load_last', 'config_value' => '1', 'is_dynamic' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'block_uo_all_users', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'block_uo_check_every', 'config_value' => '10')),
			array(SN_CONFIG_TABLE, array('config_name' => 'im_only_friends', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'im_allow_sound', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'im_url_new_window', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'im_msg_purged_time', 'config_value' => '0')),
			array(SN_CONFIG_TABLE, array('config_name' => 'im_msg_purged_automatic_time', 'config_value' => '0')),
			array(SN_CONFIG_TABLE, array('config_name' => 'im_colour_username', 'config_value' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'im_checkTime_min', 'config_value' => 2)),
			array(SN_CONFIG_TABLE, array('config_name' => 'im_checkTime_max', 'config_value' => 60)),
			array(SN_CONFIG_TABLE, array('config_name' => 'us_colour_username', 'config_value' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'fas_friendlist_limit', 'config_value' => '20')),
			array(SN_CONFIG_TABLE, array('config_name' => 'fas_colour_username', 'config_value' => 0)),
			//array(SN_CONFIG_TABLE, array('config_name' => 'fas_alert_friend_pm', 'config_value' => '0')),
			array(SN_CONFIG_TABLE, array('config_name' => 'ntf_theme', 'config_value' => 'default')),
			array(SN_CONFIG_TABLE, array('config_name' => 'ntf_colour_username', 'config_value' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'ntf_life', 'config_value' => 10)),
			array(SN_CONFIG_TABLE, array('config_name' => 'ntf_checktime', 'config_value' => 10)),
			array(SN_CONFIG_TABLE, array('config_name' => 'report_user_enable', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'up_enable_report', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'ap_show_new_friendships', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'ap_num_last_topics', 'config_value' => '10')),
			array(SN_CONFIG_TABLE, array('config_name' => 'ap_display_welcome', 'config_value' => '0', 'is_dynamic' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'ap_hide_for_guest', 'config_value' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'ap_colour_username', 'config_value' => 0)),
			array(SN_CONFIG_TABLE, array('config_name' => 'ap_show_profile_updated', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'ap_show_new_family', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'ap_show_new_relationship', 'config_value' => '1')),
			array(SN_CONFIG_TABLE, array('config_name' => 'ap_num_last_posts', 'config_value' => 10)),
			array(SN_CONFIG_TABLE, array('config_name' => 'ap_max_profile_value', 'config_value' => 60)),
			array(SN_CONFIG_TABLE, array('config_name' => 'up_enable_subscriptions', 'config_value' => 1)),
			//array(SN_CONFIG_TABLE, array('config_name' => 'up_alert_relation_pm', 'config_value' => '0')),

			array(SN_REPORTS_REASONS_TABLE, array('reason_text' => 'This person is annoying me')),
			array(SN_REPORTS_REASONS_TABLE, array('reason_text' => 'This profile is pretending to be someone or is fake')),
			array(SN_REPORTS_REASONS_TABLE, array('reason_text' => 'Inappropriate profile photo')),
			array(SN_REPORTS_REASONS_TABLE, array('reason_text' => 'This person is bullying or harassing me')),
			array(SN_REPORTS_REASONS_TABLE, array('reason_text' => 'Inappropriate Wall post')),
			array(SN_REPORTS_REASONS_TABLE, array('reason_text' => 'Other')),

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

			array(SN_ADDONS_PLACEHOLDER_TABLE, array('ph_script' => 'activitypage', 'ph_block' => 'header')),
			array(SN_ADDONS_PLACEHOLDER_TABLE, array('ph_script' => 'activitypage', 'ph_block' => 'leftcolumn')),
			array(SN_ADDONS_PLACEHOLDER_TABLE, array('ph_script' => 'activitypage', 'ph_block' => 'rightcolumn')),
			array(SN_ADDONS_PLACEHOLDER_TABLE, array('ph_script' => 'profile', 'ph_block' => 'tab statistics')),
			array(SN_ADDONS_PLACEHOLDER_TABLE, array('ph_script' => 'profile', 'ph_block' => 'tab info')),

			array(SN_USERS_TABLE, array(
				'user_id'					 => ANONYMOUS,
				'user_status'				 => '',
				'user_im_online'			 => 1,
				'user_zebra_alert_friend'	 => 0,
				'user_note'					 => '',
				'languages'					 => '',
				'about_me'					 => '',
				'employer'					 => '',
				'university'				 => '',
				'high_school'				 => '',
				'religion'					 => '',
				'political_views'			 => '',
				'quotations'				 => '',
				'music'						 => '',
				'books'						 => '',
				'movies'					 => '',
				'games'						 => '',
				'foods'						 => '',
				'sports'					 => '',
				'sport_teams'				 => '',
				'activities'				 => '',
			)),

		),

		'permission_add'	 => array(
			array('a_sn_settings', true),
			array('u_sn_userstatus', true),
			array('m_sn_close_reports', true),
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
				'module_langname'	 => 'ACP_SN_ACTIVITYPAGE_SETTINGS',
				'module_mode'		 => 'module_activitypage',
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
			array('ucp', 'UCP_SOCIALNET', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'UCP_SN_IM_HISTORY',
				'module_mode'		 => 'module_im_history',
				'module_auth'		 => '',
			)),
			array('acp', 'ACP_SN_MODULES_CONFIGURATION', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'ACP_SN_NOTIFY_SETTINGS',
				'module_mode'		 => 'module_notify',
				'module_auth'		 => 'acl_a_sn_settings'
			)),
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
			array('acp', 'ACP_SN_CONFIGURATION', array(
				'module_basename'	 => 'socialnet',
				'module_langname'	 => 'ACP_SN_ADDONS_HOOK_CONFIGURATION',
				'module_mode'		 => 'addons',
				'module_auth'		 => 'acl_a_sn_settings'
			)),
		),

		'custom'			 => array(
			'phpbbSN_smilies_allow',
			'phpbbSN_create_fms_primarygroups',
		),

		'cache_purge'		 => array(
			'imageset',
			'template',
			'theme',
			'cache',
		),

	),
);


// install, update, do whatever! :)
$umil->run_actions($action, $versions, $version_config_name);

exit( "The database was updated successfully!\n" ); // to display this message also after developer makes pull


/*
 * console-related functions
 */
function get_arg($array, $index, $default)
{
	return isset($array[$index]) ? $array[$index] : $default;
}