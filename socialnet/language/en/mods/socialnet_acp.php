<?php
/**
*
* @package phpBB Social Network
* @version 0.6.3
* @copyright (c) 2010-2012 Kamahl & Culprit http://phpbbsocialnetwork.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_CAT_SOCIALNET'						 => 'Social Network',
	'ACP_CAT_SOCIALNET_EXPLAIN'				 => '',
	'ACP_SN_WELCOME_TEXT'					 => 'phpBB3 Social Network is a modification for phpBB3 boards, which turns your board to full valued social network software. Our goal is to provide you community software solution with favourite features of all major social network websites. phpBB3 Social Network is modular based application, it means that you can turn on/off each module and it is easy to create your own new modules. If you miss some module or feature, you can look at the bottom of this page and choose which module you want to download. Feel free to visit <a href="http://phpbbsocialnetwork.com">phpbbsocialnetwork.com</a> to ask for support or ask us to create new module ',

  'ACP_SN_LIKE_US_FB'						=> 'Like phpBB Social Network on Facebook',
	'ACP_SN_LIKE_US_FB_EXPLAIN'		=> 'If you want to know all news, see new screenshots and be informed about phpBB Social Network, just Like Us on Facebook.',         
	
	'ACP_SN_CONTRIBUTE'						=> 'Contribute to phpBB Social Network',
	'ACP_SN_CONTRIBUTE_EXPLAIN'		=> 'Do you like phpBB Social Network? The easiest way to help out is to make a donation, no matter how small. You can make a donation via PayPal or a Bank Transfer (<a href="http://phpbbsocialnetwork.com/support_us.php" style="font-weight: bold;">contact us</a> for transfer details).<br /><form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="margin: 15px 0;"><input type="hidden" name="cmd" value="_donations" /><input type="hidden" name="business" value="G4NHS46RS8HTC" /><input type="hidden" name="lc" value="CZ" /><input type="hidden" name="item_name" value="phpBB Social Network" /><input type="hidden" name="currency_code" value="EUR" /><input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHosted" /><input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" style="border: 0; width: 147px; height: 47px;background: none; cursor: pointer;" name="submit" alt="PayPal" /><img style="border: 0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" alt="" /></form>There are also other ways how you can help us to develop phpBB Social Network <a href="http://phpbbsocialnetwork.com/support_us.php" style="font-weight: bold;">here</a>.',
	
	'SN_GLOBAL_ENABLE'						 => 'Enable Social Network',
	'SN_GLOBAL_ENABLE_EXPLAIN'				 => 'Enable or disable Social Network MOD',

	'ACP_SN_MAIN'							 => 'Main',

	'ACP_SN_CONFIGURATION'					 => 'Social Network Configuration',
	'ACP_SN_GLOBAL_SETTINGS'				 => 'Social Network Settings',

	'ACP_SN_AVAILABLE_MODULES'				 => 'Enable Modules',
	'ACP_SN_AVAILABLE_MODULES_EXPLAIN'		 => 'Via this panel you can enable / disable modules of Social Network',

	'MODULES'								 => 'Modules',

	'SN_AVAILABLE_MODULES'					 => 'List of phpBB Social Network modules',
	'VERSION_AVAILABLE'						 => 'Latest version',
	'VERSION_INSTALLED'						 => 'Current version',
	'DOWNLOAD_LATEST'						 => 'Download latest version',
	'NOT_INSTALLED'							 => 'not installed',
	'UPDATE_TO_LATEST'						 => 'Update to latest version',
	'BUY_HERE'								 => 'Buy here',
	'ACP_SN_VERSION_UP_TO_DATE'				 => 'Your %1$s installation is up to date. There are no updates available at this time.',
	'ACP_SN_VERSION_NOT_UP_TO_DATE'			 => 'Your %1$s installation is not up to date. You can download the latest version <a href="%2$s">here</a>.',
	'SN_VERSION_CHECK_EXPLAIN'				 => 'Checks to see if your phpBB Social Network installation is up to date.',

	// MODULES SETTINGS
	'ACP_SN_MODULES_CONFIGURATION'			 => 'Modules Configuration',

	'SN_ACP_MODULE_NOT_ACCESSIBLE'			 => 'Administration panel for <strong>%1$s</strong> does not exist <em>(%2$s)</em>',
	'SN_MODULE_NOT_ACCESSIBLE'				 => 'Module <strong>%1$s</strong> does not exist <em>(%2$s)</em>',
	'SN_MODULE_DISABLED'					 => 'This module is disabled',
	'ACP_PANEL_NOT_ACCESSIBLE'				 => 'ACP panel for module does not exist',
	'SN_MODULE_NOT_EXISTS'					 => 'Module class (socialnet_%1$s) does not exist in file %2$s',
	'SN_FILE_NOT_EXISTS'					 => 'Module file %1$s does not exist',

	'SN_MODULE_EXPLAIN'						 => 'Allow using %1$s',
	'SN_MODULE_IM'							 => 'Instant messenger',

	'SN_MODULE_USERSTATUS'					 => 'User status',

	'SN_MODULE_APPROVAL'					 => 'Friends management system',

	'SN_MODULE_MAINPAGE'					 => 'Mainpage',

	'SN_MODULE_NOTIFY'						 => 'Notifications',

	'SN_MODULE_PROFILE'						 => 'User profile',

	'ACP_SN_IM_SETTINGS'					 => 'Instant messenger settings',
	'ACP_SN_USERSTATUS_SETTINGS'			 => 'User status settings',
	'ACP_SN_APPROVAL_SETTINGS'				 => 'Friends management system settings',
	'ACP_SN_MAINPAGE_SETTINGS'				 => 'Mainpage settings',
	'ACP_SN_NOTIFY_SETTINGS'				 => 'Notifications settings',
	'ACP_SN_PROFILE_SETTINGS'				 => 'User profile settings',

	'SN_NTF_THEME'							 => 'Notification bubble color',

	'ACP_SN_MODULE_SETTINGS_EXPLAIN'		 => 'Configuration panel for %1$s',

	'OVERRIDE_USER_SETTINGS'				 => 'Override user settings',
	'OVERRIDE_USER_SETTINGS_EXPLAIN'		 => 'Replace user\'s settings of this MOD with defaults',

	'SN_COLOUR_NAME'						 => 'Colour username',
	'SN_COLOUR_NAME_EXPLAIN'				 => 'Use phpBB colour names',

	// CONFIRM BOX SETTINGS
	'ACP_SN_CONFIRMBOX_SETTINGS'			 => 'Confirm Box settings',
	'ACP_SN_CONFIRMBOX_SETTINGS_EXPLAIN'	 => 'You can configure Confirm Box via this panel',

	'SN_CB_ENABLE'							 => 'Enable Confirm Box',
	'SN_CB_ENABLE_EXPLAIN'					 => 'Enable showing of the confirm boxes',
	'SN_CB_RESIZABLE'						 => 'Enable resizable confirm box',
	'SN_CB_RESIZABLE_EXPLAIN'				 => 'Set confirm boxes resizable',
	'SN_CB_DRAGGABLE'						 => 'Enable draggable confirm box',
	'SN_CB_DRAGGABLE_EXPLAIN'				 => 'Set confirm boxes draggable',
	'SN_CB_MODAL'							 => 'Enable modal confirm box',
	'SN_CB_MODAL_EXPLAIN'					 => 'Set confirm boxes modal',
	'SN_CB_WIDTH'							 => 'Set width of confirm box',
	'SN_CB_WIDTH_EXPLAIN'					 => 'Set width of confirm box<br />You can use eg. 400 or 40%',

	// BLOCKS SETTINGS
	'ACP_SN_BLOCKS_ENABLE'					 => 'Enable Blocks',
	'ACP_SN_BLOCKS_ENABLE_EXPLAIN'			 => 'This Control Panel allows to be able to enable/disable existing blocks.<br />
	Allow to use this blocks on any page of board, on which phpBB Social Network is loaded.<br />
	Specific settings for the blocks is under <strong>%1$s</strong> panel.',

	'SN_BLOCK_ENABLE_EXPLAIN'				 => 'Display %1$s',
	'SN_BLOCK_MYPROFILE'					 => 'My Profile Block',
	'SN_BLOCK_MENU'							 => 'Menu Block',
	'SN_BLOCK_SEARCH'						 => 'Search Block',
	'SN_BLOCK_FRIENDS_SUGGESTIONS'			 => 'Friend Suggestions Block',
	'SN_BLOCK_FRIEND_REQUESTS'				 => 'Friend Requests Block',
	'SN_BLOCK_BIRTHDAY'						 => 'Birthday Block',
	'SN_BLOCK_RECENT_DISCUSSIONS'			 => 'Recent Discussions Block',
	'SN_BLOCK_STATISTICS'					 => 'Statistics Block',
	'SN_BLOCK_ONLINE_USERS'					 => 'Online Users Block',

	'ACP_SN_BLOCKS_CONFIGURATION'			 => 'Blocks configuration',
	'ACP_SN_BLOCKS_CONFIGURATION_EXPLAIN'			 => 'On the right you can select the block which you want to configure',
	'ACP_SN_BLOCK_MENU_SETTINGS'			 => 'Menu settings',
	'ACP_SN_BLOCK_ONLINE_USERS_SETTINGS'	 => 'Online Users settings',
	'ACP_SN_BLOCK_SETTINGS_EXPLAIN'			 => '%1$s settings',
	'SN_BLOCK_NOT_EXISTS'					 => 'Module class (acp_socialnet_block_%1$s) does not exist in file %2$s',
	'SELECT_BLOCK'							 => 'Select block',
	
	// BLOCK USER ONLINE SETTINGS
	'BLOCK_UO_SHOW_ALL'						 => 'Show all friends',
	'BLOCK_UO_SHOW_ALL_EXPLAIN'				 => 'Display all friends that are online',
	'BLOCK_UO_CHECK_EVERY'					 => 'Check online friends every',
	'BLOCK_UO_CHECK_EVERY_EXPLAIN'			 => 'Check online friends every X seconds.<br />0 seconds disable checking.',

	// BLOCK BUTTONS MENU SETTINGS
	'BLOCK_MENU_BUTTONS_MANAGE'				 => 'Buttons management',
	'BLOCK_MENU_CREATE_BUTTON_EXPLAIN'		 => 'Here you can create or manage buttons. There are 2 types of buttons: parrent buttons and subbuttons.',
	'BLOCK_MENU_NAV'						 => 'Menu',
	'BLOCK_MENU_EDIT_BUTTON'				 => 'Edit button',
	'BLOCK_MENU_BUTTON_NAME'				 => 'Button name',
	'BLOCK_MENU_BUTTON_URL'					 => 'Link',
	'BLOCK_MENU_BUTTON_URL_EXPLAIN'			 => 'Url address has include http://',
	'BLOCK_MENU_BUTTON_PARENT'				 => 'Parent button',
	'BLOCK_MENU_BUTTON_PARENT_EXPLAIN'		 => 'Select the parent button if you want to have dropdown menu',
	'BLOCK_MENU_BUTTON_ONLY_REGISTERED'		 => 'Display only to registered users',
	'BLOCK_MENU_BUTTON_ONLY_GUEST'			 => 'Display only to guests',
	'BLOCK_MENU_BUTTON_DISPLAY'				 => 'Display the button',
	'BLOCK_MENU_BUTTON_EXTERNAL'			 => 'The link will be opened in a new window',
	'BLOCK_MENU_DELETE_BUTTON_CONFIRM'		 => 'Are you sure you want to delete this button?',
	'BLOCK_MENU_DELETE_SUBBUTTONS_CONFIRM'	 => 'Are you sure you want to delete this button and all its subbutons?',
	'BLOCK_MENU_BUTTON_ADDED'				 => 'A new button has been added succesfully',
	'BLOCK_MENU_BUTTON_EDITED'				 => 'Button has been edited succesfully',
	'BLOCK_MENU_MOVE_BUTTON_WITH_SUBS'		 => 'This button can\'t became a subbutton because it has subbuttons.',
	'BLOCK_MENU_NO_BUTTONS'					 => 'There is no button to manage',
	'BLOCK_MENU_NO_SUBBUTTONS'				 => 'There is no subbutton to manage',
	'BLOCK_MENU_CREATE_BUTTON'				 => 'Create a new button',
));

// INSTANT MESSENGER ACP
$lang = array_merge($lang, array(
	'IM_ONLY_FRIENDS'						 => 'Allow chat only with friends',
	'IM_ONLY_FRIENDS_EXPLAIN'				 => 'This option allows you turn on/off chating only with friends',

	'IM_ALLOW_SOUND'						 => 'Play sound when receive messages',
	'IM_ALLOW_SOUND_EXPLAIN'				 => 'This option enable/disable sound by receiving new message',

	'IM_URL_IN_NEW_WINDOW'					 => 'Open links in new window',
	'IM_URL_IN_NEW_WINDOW_EXPLAIN'			 => 'This option enable/disable opening links in the new window',

	'IM_AUTOMATIC_PURGING_MESSAGES'			 => 'Auto-prune message age',
	'IM_AUTOMATIC_PURGING_MESSAGES_EXPLAIN'	 => 'Delivered messages are automatically deleted when they are older than X days.<br /><em>Insert 0 to disable auto-pruning old messages</em>',

	'PURGE'									 => 'Purging',

	'IM_PURGE_ALL_MSG'						 => 'Delete old delivered messages',
	'IM_PURGE_ALL_MSG_EXPLAIN'				 => 'All delivered messages older then X days will be deleted<br /><em>Minimal value is 1 day</em>',
	'IM_PURGE_ALL_MSG_SUCCESS'				 => 'All old delivered messages have been deleted',

	'IM_PURGE_ALL_CHATBOX'					 => 'Close all opened chatboxes',
	'IM_PURGE_ALL_CHATBOX_EXPLAIN'			 => 'All opened chatboxes older then X days will be closed<br /><em>Minimal value is 1 day</em>',
	'IM_PURGE_ALL_CHATBOX_SUCCESS'			 => 'Chatboxes have been closed',

	'SN_IM_CHECKTIMES'						 => 'Instant Messenger check times',
	'IM_CHECK_TIME_MIN'						 => 'Minimum',
	'IM_CHECK_TIME_MAX'						 => 'Maximum',

	'IM_CHECK_TIME_MIN_EXPLAIN'				 => 'Minimum time to check for new messages<br /><em>Value between 2-30</em>',
	'IM_CHECK_TIME_MAX_EXPLAIN'				 => 'Maximum time to check for new messages<br /><em>Minimum value is 60</em>',
));

// ADDONS MANAGEMENT ACP
$lang = array_merge($lang, array(
  'ACP_SN_ADDONS_CONFIGURATION'			 => 'Addons Management',
	'ACP_SN_ADDONS_CONFIGURATION_EXPLAIN'	 => 'You can manage external addons via this panel.',

  'SN_ADDONS_NO_ADDONS'					   => 'You have not added any addon yet',
  'SN_ADDONS_CREATE_ADDON'				 => 'Add a new addon',
	'SN_ADDONS_CREATE_ADDON_EXPLAIN' => 'Perform these simple steps:<br />1. create a .php file and upload it to root/socialnet/addons/<br />2. create a .html file and upload it to root/styles/your_style/template/socialnet/addons/<br />3. choose the .php file and/or .html file, point them to the location and enable them',
	'SN_ADDONS_EDIT_ADDON'				 	 => 'Edit addon',
	'SN_ADDONS_ADDON_NAME'				 	 => 'Addon name',
	'SN_ADDONS_ADDON_PHP'					   => 'Addon php file',
	'SN_ADDONS_ADDON_PHP_EXPLAIN'		 => 'a .php file in root/socialnet/addons/',
	'SN_ADDONS_ADDON_HTML'					 => 'Addon html file',
	'SN_ADDONS_ADDON_HTML_EXPLAIN'	 => 'a .html file in root/styles/yourstyle/template/socialnet/addons/',
	'SN_ADDONS_ADDON_LOCATION'			 => 'Location',
	'SN_ADDONS_ADDON_LOCATION_EXPLAIN' => 'Select the location where the addon should to be loaded',
	'SN_ADDONS_ADDON_ACTIVE'				 => 'Enable',
	'SN_ADDONS_ADDON_ACTIVE_EXPLAIN' => 'You can enable/disable the addon here',
	'SN_ADDONS_LOCATIONS_1'					 => 'User Profile -> Info',
	'SN_ADDONS_LOCATIONS_2'				 	 => 'User Profile -> Statistics',
	'SN_ADDONS_LOCATIONS_3'					 => 'Mainpage -> Header',
	'SN_ADDONS_LOCATIONS_4'					 => 'Mainpage -> Left column',
	'SN_ADDONS_LOCATIONS_5'					 => 'Mainpage -> Right column',
	'SN_ADDONS_LOCATIONS_6'					 => 'Mainpage post',
	'SN_ADDONS_DELETE_ADDON_CONFIRM' => 'Are you sure you want to delete this addon?',
	'SN_ADDONS_ADDON_ADDED'				 	 => 'Addon has been added succesfully.',
	'SN_ADDONS_ADDON_EDITED'				 => 'Addon has been edited succesfully.',
	'SN_ADDONS_ADDON_ENABLED'				 => 'Addon has been enabled succesfully.',
	'SN_ADDONS_ADDON_DISABLED'			 => 'Addon has been disabled succesfully.',
	'SN_ADDONS_NO_FILE'					 	   => 'You must choose either a .php file or a .html file or both of them.',
	'SN_ADDONS_NO_LOCATION'				   => 'Please specify the location for this addon.',
));

// USER STATUS ACP
$lang = array_merge($lang, array(
	'US_COMMENTS'									 => 'Allow other users to comment on user status',
	'US_COMMENTS_EXPLAIN'							 => 'This option allows all users to post comments to user status',

	'US_LOAD_LAST_USERSTATUS_COMMENTS'				 => 'Load last 3 comments',
	'US_LOAD_LAST_USERSTATUS_COMMENTS_EXPLAIN'		 => 'If you want to load first 3 comments and then others, choose No',

	'SN_US_DELETE_ALL_USER_STATUSES'				 => 'Delete all statuses',
	'SN_US_DELETE_ALL_USER_COMMENTS'				 => 'Delete all comments',
	'SN_USER_DELETE_STATUS_COMMENTS_DELETED_USERS'	 => 'Purge statuses and comments written by deleted users',

	'SN_NO_USER_STATUS_TO DELETE'					 => 'No statuses found',
));

// MODULE APPROVAL / FRIEND MANAGEMENT SYSTEM
$lang = array_merge($lang, array(
	'SN_FAS_ALERT_FRIEND_BY_PM'				 => 'Notify users about new request via PM',
	'SN_FAS_ALERT_FRIEND_BY_PM_EXPLAIN'		 => 'Send PM to the user about a new friend request. If you set no, then user will be notified by notification.',

	'SN_FAS_FRIENDS_PER_PAGE'				 => 'Number of friends per page on user profile',
	'SN_FAS_FRIENDS_PER_PAGE_EXPLAIN'		 => 'How many friends per page will be displayed',
	'SN_FMS_PURGE_ALL_FRIENDS_DELETED_USERS' => 'Purge all friends and friend groups deleted users',
));
// MAINPAGE ACP
$lang = array_merge($lang, array(
	'MP_NUM_LAST_POSTS'							 => 'Limit last posts displaying',
	'MP_NUM_LAST_POSTS_EXPLAIN'					 => 'Limits the number of last posts loaded for Recent discussion',
	'MP_SHOW_NEW_FRIENDSHIPS'					 => 'Display notification about new friendships',
	'MP_SHOW_NEW_FRIENDSHIPS_EXPLAIN'			 => 'Display notification about adding new friends by one of yours friends on Mainpage',
	'MP_SHOW_PROFILE_UPDATED'					 => 'Display notification about profile update',
	'MP_SHOW_PROFILE_UPDATED_EXPLAIN'			 => 'Display notification about updating the profile by one of yours friends on Mainpage',
	'MP_SHOW_NEW_FAMILY'						 => 'Display notification about new family member',
	'MP_SHOW_NEW_FAMILY_EXPLAIN'				 => 'Display notification about adding the family member by one of yours friends on Mainpage',
	'MP_SHOW_NEW_RELATIONSHIP'					 => 'Display notification about new relationship',
	'MP_SHOW_NEW_RELATIONSHIP_EXPLAIN'			 => 'Display notification about new relationship of one of yours friends on Mainpage',
	'MP_DISPLAY_WELCOME'						 => 'Display welcome block on Mainpage',
	'MP_DISPLAY_WELCOME_EXPLAIN'				 => 'Display welcome block for unregistered users on Mainpage. You can edit it following instructions below.',
	'MP_HIDE_FOR_GUEST'							 => 'Hide mainpage for guests',
	'MP_HIDE_FOR_GUEST_EXPLAIN'					 => 'Mainpage module will be redirected to index when will be accessed by guest',

	'ACP_SN_MAINPAGE_IS_MAIN'					 => 'Set Mainpage as first page',
	'ACP_SN_MAINPAGE_IS_MAIN_EXPLAIN'			 => 'If you want to have the Mainpage as the first page of your web instead of index.php page, please follow these instructions.',
	'ACP_SN_MAINPAGE_IS_MAIN_OPEN_FIND'			 => 'Open file .htaccess (located in the root of your web) and find:',
	'ACP_SN_MAINPAGE_IS_MAIN_AFTER_ADD'			 => 'After add:',
	'ACP_SN_MAINPAGE_IS_MAIN_SAVE'				 => 'Save the file and upload to your web.',
	'ACP_SN_MAINPAGE_IS_MAIN_NO_DIRECTORY_INDEX' => 'If you can not find the DirectoryIndex in your .htaccess file, go to the bottom of the file and add this line',
	'ACP_SN_MAINPAGE_NOT_MAIN'					 => 'Unset Mainpage as first page',
	'ACP_SN_MAINPAGE_NOT_MAIN_EXPLAIN'			 => 'If you dont want to have the Mainpage as the first page of your web instead of index.php page, please follow these instructions.',
	'ACP_SN_MAINPAGE_NOT_MAIN_OPEN_FIND'		 => 'Open file .htaccess (located in the root of your web) and find:',
	'ACP_SN_MAINPAGE_NOT_MAIN_DELETE'			 => 'Delete it, save the file and upload to your web.',
	'ACP_SN_MAINPAGE_WELCOME'					 => 'Edit Welcome text on Mainpage',
	'ACP_SN_MAINPAGE_WELCOME_EXPLAIN'			 => 'If you want to display the Welcome text for guests on Mainpage, you can follow these instructions.',
	'ACP_SN_MAINPAGE_WELCOME_INSTRUCTIONS'		 => 'Open file language/en/mods/socialnet.php using a <a href="http://www.pspad.com/">text editor</a> and find:',
	'ACP_SN_MAINPAGE_WELCOME_EDIT'				 => 'There you can see two lines containing the title and the text of Welcome block. Feel free to edit them and also style them using <a href="http://www.w3.org/wiki/HTML">HTML</a> and CSS.',
));

// USER PROFILE ACP
$lang = array_merge($lang, array(
	'SN_ENABLE_REPORT'					 => 'Enable reporting users',
	'SN_ENABLE_REPORT_EXPLAIN'			 => 'Users will be able to report other users',
	'SN_ENABLE_SUBSCRIPTIONS'			 => 'Enable subscribing users',
	'SN_ENABLE_SUBSCRIPTIONS_EXPLAIN'	 => 'Users will be able to subscribe other users to see their activity on Mainpage',
	'SN_MAX_PROFILE_VALUE'				 => 'Max length of value displayed on Mainpage',
	'SN_MAX_PROFILE_VALUE_EXPLAIN'		 => 'Set the maximum number of characters of values displayed on Mainpage after the profile update.',
	'SN_PROFILE_NO_REASON'				 => 'You need to create at least one report reason',
	'SN_PROFILE_REPORT_REASONS'			 => 'Report user reasons',
	'SN_PROFILE_REPORT_REASONS_EXPLAIN'	 => 'Here you can manage reasons for reporting users',
	'SN_PROFILE_ADD_REASON'				 => 'Add reason',
	'SN_PROFILE_DELETE_REASON_CONFIRM'	 => 'Are you sure you want to delete this reason?',
	'SN_PROFILE_REASON_ADDED'			 => 'Report reason has been added successfully',
	'SN_PROFILE_REASON_DELETED'			 => 'Report reason has been deleted successfully',
	'SN_PROFILE_ALERT_RELATION_BY_PM'				 => 'Notify users about new relation via PM',
	'SN_PROFILE_ALERT_RELATION_BY_PM_EXPLAIN'		 => 'Send PM to the user about a new relationship or family request. If you set no, then user will be notified by notification.',
));

// PHPBB LOG CONFIG
$lang_log_main = '<strong>Social Network &raquo</strong> ';

$lang = array_merge($lang, array(
	'LOG_CONFIG_SN_MAIN'									 => $lang_log_main . 'Global settings changed',
	'LOG_CONFIG_SN_MODULES'									 => $lang_log_main . 'Available modules changed',
	'LOG_CONFIG_SN_CB'										 => $lang_log_main . 'Confirm box settings changed',
	'LOG_CONFIG_SN_BLOCKS'									 => $lang_log_main . 'Available blocks changed',
	'LOG_CONFIG_SN_BLOCK_USERONLINE'						 => $lang_log_main . 'Settings User OnlineBlock changed',

	'LOG_CONFIG_SN_BLOCK_MENU_ADD_BUTTON'					 => $lang_log_main . 'Menu block item <strong>%1$s</strong> added',
	'LOG_CONFIG_SN_BLOCK_MENU_EDIT_BUTTON'					 => $lang_log_main . 'Menu block item <strong>%1$s</strong> changed',
	'LOG_CONFIG_SN_BLOCK_MENU_DELETE'						 => $lang_log_main . 'Menu block item <strong>%1$s</strong> deleted',
	'LOG_CONFIG_SN_BLOCK_MENU_MOVE_UP'						 => $lang_log_main . 'Menu block moved item <strong>%1$s</strong> above <strong>%2$s</strong>',
	'LOG_CONFIG_SN_BLOCK_MENU_MOVE_DOWN'					 => $lang_log_main . 'Menu block moved item <strong>%1$s</strong> below <strong>%2$s</strong>',
	
	'LOG_CONFIG_SN_ADDONS_ADD_ADDON'					 => $lang_log_main . 'Addon <strong>%1$s</strong> added',
	'LOG_CONFIG_SN_ADDONS_EDIT_ADDON'					 => $lang_log_main . 'Addon <strong>%1$s</strong> changed',
	'LOG_CONFIG_SN_ADDONS_DELETE'						 => $lang_log_main . 'Addon <strong>%1$s</strong> deleted',
	'LOG_CONFIG_SN_ADDONS_MOVE_UP'						 => $lang_log_main . 'Addon moved item <strong>%1$s</strong> above <strong>%2$s</strong>',
	'LOG_CONFIG_SN_ADDONS_MOVE_DOWN'					 => $lang_log_main . 'Addon moved item <strong>%1$s</strong> below <strong>%2$s</strong>',
	'LOG_CONFIG_SN_ADDONS_ENABLE_ADDON'					 => $lang_log_main . 'Addon <strong>%1$s</strong> enabled',
	'LOG_CONFIG_SN_ADDONS_DISABLE_ADDON'				 => $lang_log_main . 'Addon <strong>%1$s</strong> disabled',

	'LOG_CONFIG_SN_IM'										 => $lang_log_main . 'Instant Messenger module settings changed',
	'LOG_CONFIG_SN_IM_MSG_PURGED'							 => $lang_log_main . 'Instant Messenger messages purged',
	'LOG_CONFIG_SN_IM_CHATBOXES_CLOSED'						 => $lang_log_main . 'Instant Messenger chatboxes closed',

	'LOG_CONFIG_SN_USERSTATUS'								 => $lang_log_main . 'UserStatus module settings changed',
	'LOG_CONFIG_SN_USERSTATUS_BASICTOOLS_DELETE_STATUSES'	 => $lang_log_main . 'Userstatuses deleted for user %1$s',
	'LOG_CONFIG_SN_USERSTATUS_BASICTOOLS_DELETE_COMMENTS'	 => $lang_log_main . 'Userstatus comments deleted for user %1$s',
	'LOG_CONFIG_SN_USERSTATUS_BASICTOOLS_USER_DELETED'		 => $lang_log_main . 'Userstatuses and userstatus comments deleted for user deleted users',

	'LOG_CONFIG_SN_FMS'										 => $lang_log_main . 'Friend Management module settings changed',
	'LOG_CONFIG_SN_FMS_BASICTOOLS_DELETED_USER'				 => $lang_log_main . 'Purged all friends and friend groups of deleted users',
	'LOG_CONFIG_SN_MP'										 => $lang_log_main . 'Mainpage module settings changed',
	'LOG_CONFIG_SN_NTF'										 => $lang_log_main . 'Notifications module settings changed',
	'LOG_CONFIG_SN_UP'										 => $lang_log_main . 'Profile module settings changed',

));

?>