<?php
/**
 *
 * @package phpBB Social Network
 * @version 0.7.0
 * @copyright (c) phpBB Social Network Team 2010-2012 http://phpbbsocialnetwork.com
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
	'ACP_SN_WELCOME_TEXT'					 => 'phpBB Social Network is a modification for phpBB boards, which turns your board into a full-valued social network software. Our goal is to provide you a community software solution with favourite features from all major social network websites. phpBB Social Network is a modular based application, which means that you can turn on/off each module and it is easy to create your own new modules. If you miss some module or feature, you can look at the bottom of this page and choose which module you want to download. Feel free to visit <a href="http://phpbbsocialnetwork.com">phpbbsocialnetwork.com</a> to ask for support or ask us to create a new module ',
	'ACP_SN_LIKE_US_FB'						 => 'Like phpBB Social Network on Facebook',
	'ACP_SN_LIKE_US_FB_EXPLAIN'				 => 'If you want to know all the news, see new screenshots and be informed about phpBB Social Network, just Like Us on Facebook.',
	'ACP_SN_CONTRIBUTE'						 => 'Contribute to phpBB Social Network',
	'ACP_SN_CONTRIBUTE_EXPLAIN'				 => 'Do you like phpBB Social Network? The easiest way to help out is to make a donation, no matter how small. You can make a donation via PayPal or a Bank Transfer (<a href="http://phpbbsocialnetwork.com/support_us.php" style="font-weight: bold;">contact us</a> for transfer details).<br /><form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="margin: 15px 0;"><p><input type="hidden" name="cmd" value="_donations" /><input type="hidden" name="business" value="G4NHS46RS8HTC" /><input type="hidden" name="lc" value="CZ" /><input type="hidden" name="item_name" value="phpBB Social Network" /><input type="hidden" name="currency_code" value="EUR" /><input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHosted" /><input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" style="border: 0; width: 147px; height: 47px;background: none; cursor: pointer;" name="submit" alt="PayPal" /><img style="border: 0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" alt="" /></p></form>There are also other ways in which you can help us to develop phpBB Social Network <a href="http://phpbbsocialnetwork.com/support_us.php" style="font-weight: bold;">here</a>.',
	'SN_GLOBAL_ENABLE'						 => 'Enable Social Network',
	'SN_GLOBAL_ENABLE_EXPLAIN'				 => 'Enable or disable Social Network MOD',
	'SN_SHOW_BROWSER_OUTDATED'				 => 'Show dialog for outdated browsers',
	'SN_SHOW_BROWSER_OUTDATED_EXPLAIN'		 => 'when browser is outdated for SN. Dialog with the message will appear',
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
	'SN_VERSION_CHECK_EXPLAIN'				 => 'Check to see if your phpBB Social Network installation is up to date.',
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
	'SN_MODULE_ACTIVITYPAGE'				 => 'Activity page',
	'SN_MODULE_NOTIFY'						 => 'Notifications',
	'SN_MODULE_PROFILE'						 => 'User profile',
	'SN_MODULE_INITIALIZING'       => 'Initializing, please wait ...<br /><br />',
	'SN_MODULE_INITIALIZING_FMS'       => 'Initializing Friends Management system, please wait - ',

	'SN_MODULE_NOTIFY_DETAIL'				 => 'If this module is disabled, then all notifications are delivered using Private messages',

	'ACP_SN_IM_SETTINGS'					 => 'Instant messenger settings',
	'ACP_SN_USERSTATUS_SETTINGS'			 => 'User status settings',
	'ACP_SN_APPROVAL_SETTINGS'				 => 'Friends management system settings',
	'ACP_SN_ACTIVITYPAGE_SETTINGS'			 => 'Activity page settings',
	'ACP_SN_NOTIFY_SETTINGS'				 => 'Notification settings',
	'ACP_SN_PROFILE_SETTINGS'				 => 'User profile settings',
	'SN_NTF_THEME'							 => 'Notification bubble color',
	'SN_NTF_LIFE'							 => 'Display time',
	'SN_NTF_LIFE_EXPLAIN'					 => 'How long should be notification displayed on page',
	'SN_NTF_CHECKTIME'						 => 'Check time',
	'SN_NTF_CHECKTIME_EXPLAIN'				 => 'How often should notifications be checked',
	'ACP_SN_MODULE_SETTINGS_EXPLAIN'		 => 'Configuration panel for %1$s',
	'OVERRIDE_USER_SETTINGS'				 => 'Override user settings',
	'OVERRIDE_USER_SETTINGS_EXPLAIN'		 => 'Replace user\'s settings of this MOD with defaults',
	'SN_COLOUR_NAME'						 => 'Colour username',
	'SN_COLOUR_NAME_EXPLAIN'				 => 'Use phpBB colour names',
	// CONFIRM BOX SETTINGS
	'ACP_SN_CONFIRMBOX_SETTINGS'			 => 'Confirmation Box settings',
	'ACP_SN_CONFIRMBOX_SETTINGS_EXPLAIN'	 => 'You can configure Confirmation Box via this panel',
	'SN_CB_ENABLE'							 => 'Enable Confirmation Box',
	'SN_CB_ENABLE_EXPLAIN'					 => 'Enable showing of the Confirmation boxes',
	'SN_CB_RESIZABLE'						 => 'Enable resizable Confirmation box',
	'SN_CB_RESIZABLE_EXPLAIN'				 => 'Set Confirmation boxes resizable',
	'SN_CB_DRAGGABLE'						 => 'Enable draggable Confirmation box',
	'SN_CB_DRAGGABLE_EXPLAIN'				 => 'Set Confirmation boxes draggable',
	'SN_CB_MODAL'							 => 'Enable modal Confirmation box',
	'SN_CB_MODAL_EXPLAIN'					 => 'Set Confirmation boxes modal',
	'SN_CB_WIDTH'							 => 'Set width of Confirmation box',
	'SN_CB_WIDTH_EXPLAIN'					 => 'Set width of Confirmation box<br />You can use eg. 400 or 40%',
	// BLOCKS SETTINGS
	'ACP_SN_BLOCKS_ENABLE'					 => 'Enable Blocks',
	'ACP_SN_BLOCKS_ENABLE_EXPLAIN'			 => 'This Control Panel allows you to be able to enable/disable existing blocks.<br />
	Allows you to use these blocks on any page of the board on which phpBB Social Network is loaded.<br />
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
	'ACP_SN_BLOCKS_CONFIGURATION_EXPLAIN'	 => 'On the right you can select the block which you want to configure',
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
	'BLOCK_MENU_CREATE_BUTTON_EXPLAIN'		 => 'Here you can create or manage buttons. There are 2 types of buttons: parent buttons and sub-buttons.',
	'BLOCK_MENU_NAV'						 => 'Menu',
	'BLOCK_MENU_EDIT_BUTTON'				 => 'Edit button',
	'BLOCK_MENU_BUTTON_NAME'				 => 'Button name',
	'BLOCK_MENU_BUTTON_URL'					 => 'Link',
	'BLOCK_MENU_BUTTON_URL_EXPLAIN'			 => 'Url address has to include http://',
	'BLOCK_MENU_BUTTON_PARENT'				 => 'Parent button',
	'BLOCK_MENU_BUTTON_PARENT_EXPLAIN'		 => 'Select the parent button if you want to have a dropdown menu',
	'BLOCK_MENU_BUTTON_ONLY_REGISTERED'		 => 'Display only to registered users',
	'BLOCK_MENU_BUTTON_ONLY_GUEST'			 => 'Display only to guests',
	'BLOCK_MENU_BUTTON_DISPLAY'				 => 'Display the button',
	'BLOCK_MENU_BUTTON_EXTERNAL'			 => 'The link will be opened in a new window',
	'BLOCK_MENU_DELETE_BUTTON_CONFIRM'		 => 'Are you sure you want to delete this button?',
	'BLOCK_MENU_DELETE_SUBBUTTONS_CONFIRM'	 => 'Are you sure you want to delete this button and all its sub-buttons?',
	'BLOCK_MENU_BUTTON_ADDED'				 => 'A new button has been added successfully',
	'BLOCK_MENU_BUTTON_EDITED'				 => 'Button has been edited successfully',
	'BLOCK_MENU_MOVE_BUTTON_WITH_SUBS'		 => 'This button can\'t became a sub-button because it has sub-buttons.',
	'BLOCK_MENU_NO_BUTTONS'					 => 'There is no button to manage',
	'BLOCK_MENU_NO_SUBBUTTONS'				 => 'There is no sub-button to manage',
	'BLOCK_MENU_CREATE_BUTTON'				 => 'Create a new button',
));

// INSTANT MESSENGER ACP
$lang = array_merge($lang, array(
	'IM_ONLY_FRIENDS'						 => 'Allow chat only with friends',
	'IM_ONLY_FRIENDS_EXPLAIN'				 => 'This option allows you turn on/off chatting only with friends',
	'IM_ALLOW_SOUND'						 => 'Play sound when receive messages',
	'IM_ALLOW_SOUND_EXPLAIN'				 => 'This option enables/disables sound when receiving a new message',
	'IM_URL_IN_NEW_WINDOW'					 => 'Open links in new window',
	'IM_URL_IN_NEW_WINDOW_EXPLAIN'			 => 'This option enables/disables opening links in the new window',
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
	'SN_IM_MANAGE_SMILIES'					 => 'Manage display smilies',
));

// ADDONS MANAGEMENT ACP
$lang = array_merge($lang, array(
	'ACP_SN_ADDONS_HOOK_CONFIGURATION'			 => 'Add-ons Hook System Management',
	'ACP_SN_ADDONS_HOOK_CONFIGURATION_EXPLAIN'	 => 'Add-on Hooks allow you to add your own code to SN that runs when specific page is loaded inside the system.',

	'SN_ADDONS_ADDONS_MANAGEMENT'				 => 'Add-ons Management',
	'SN_ADDONS_PLACEHOLDER_MANAGEMENT'			 => 'Placeholders Management',
	'SN_ADDONS_EDITHOLDER'						 => 'Edit placeholder',
	'SN_ADDONS_ADDHOLDER'						 => 'Add placeholder',
	'SN_ADDONS_PLACEHOLDER_EMPTY_FIELD'			 => 'Both fields are required',
	'SN_ADDONS_PLACEHOLDER_ADDED'				 => 'New placeholder has been added successfully',
	'SN_ADDONS_PLACEHOLDER_EDITED'				 => 'Placeholder has been edited successfully',
	'SN_ADDONS_PLACEHOLDER_DELETE_CONFIRM'		 => 'Are you sure to delete placeholder <strong>%1$s::%2$s</strong>?',
	'SN_ADDONS_PLACEHOLDER_DELETED'				 => 'Placeholder has been deleted successfully',
	'SN_ADDONS_PLACEHOLDER_DUPLICATE'			 => 'New placeholder could not be added, already exists',
	'SN_ADDONS_PLACEHOLDER_ERREDIT'				 => 'Some problems by placeholder occurs',
	'SN_ADDONS_PLACEHOLDER'						 => 'Placeholder',
	'SN_ADDONS_PLACEHOLDER_PAGE'				 => 'Script name',
	'SN_ADDONS_PLACEHOLDER_BLOCK'				 => 'Block on page',
	'SN_ADDONS_PLACEHOLDER_STRING'				 => 'Template variable',
	'SN_ADDONS_PLACEHOLDER_SCRIPT_NAME'			 => 'Script name',
	'SN_ADDONS_PLACEHOLDER_BLOCK'				 => 'Block',
	'SN_ADDONS_PLACEHOLDER_SCRIPT_NAME_EXPLAIN'	 => 'Script name for placeholder. Script name is exactly name of the page which is dipslayed on the board.<br />
		If you don\'t know what is exactly script name of the current page you can mostly find it in source code in HTML tag BODY.<br />
		<em>(eg. &lt;body id="phpbb" class="section-<strong>{script name}</strong> ..."&gt;)</em>',
	'SN_ADDONS_PLACEHOLDER_BLOCK_EXPLAIN'		 => 'Name of the block. Because you can create more than one placeholder for one script, than block name must be specified.<br />
		<em>(eg. "header", "leftcolumn", "rightcolumn")</em>',
	'SN_ADDONS_NO_PLACEHOLDER_TO_ADD_ADDON'		 => 'There is no placeholder for adding an Add-on. You may create some placeholder first.',
	'SN_ADDONS_ADDON'							 => 'Add-on',
	'SN_ADDONS_ADDADDON'						 => 'Add Add-on',
	'SN_ADDONS_EDITADDON'						 => 'Edit Add-on',

	'SN_ADDONS_TEMPLATE'						 => 'Add-on template',
	'SN_ADDONS_ADDON_ADDED_ERROR'				 => 'This add-on is already added for this placeholder',
	'SN_ADDONS_ADDON_DELETE_CONFIRM'			 => 'Are you sure you want to remove add-on <strong>%1$s</strong> from placeholder <strong>%2$s::%3$s</strong>?',
	'SN_ADDONS_ADDON_DELETED'					 => 'Add-on <strong>%1$s</strong> from placeholder <strong>%2$s::%3$s</strong> has been successfully removed.',
	'SN_ADDON_NO_ADDON_IN_PLACEHOLDER'			 => 'No Add-on assigned to this placeholder',

	'SN_ADDON_TEMPLATE_FOLDER_NOT_EXIST'		 => 'Add-on Template folder for style <strong>%1$s</strong> does not exists. Used <strong>prosilver</strong> template folder instead.',
	'SN_ADDONS_ADDON_TEMPLATE_EXIST'			 => 'Exist',
	'SN_ADDONS_ADDON_TEMPLATE_NOT_EXIST'		 => 'Not exist',
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
	'SN_FAS_FRIENDS_PER_PAGE'				 => 'Number of friends per page on user profile',
	'SN_FAS_FRIENDS_PER_PAGE_EXPLAIN'		 => 'How many friends per page will be displayed',
	'SN_FMS_PURGE_ALL_FRIENDS_DELETED_USERS' => 'Purge all friends and friend groups deleted users',
));

// ACTIVITYPAGE ACP
$lang = array_merge($lang, array(
	'AP_NUM_LAST_POSTS'								 => 'Limit last posts displaying',
	'AP_NUM_LAST_POSTS_EXPLAIN'						 => 'Limits the number of last posts loaded for Recent discussion',
	'AP_SHOW_NEW_FRIENDSHIPS'						 => 'Display notification about new friendships',
	'AP_SHOW_NEW_FRIENDSHIPS_EXPLAIN'				 => 'Display notification about adding new friends by one of your friends on Activity page',
	'AP_SHOW_PROFILE_UPDATED'						 => 'Display notification about profile update',
	'AP_SHOW_PROFILE_UPDATED_EXPLAIN'				 => 'Display notification about updating the profile by one of your friends on Activity page',
	'AP_SHOW_NEW_FAMILY'							 => 'Display notification about new family member',
	'AP_SHOW_NEW_FAMILY_EXPLAIN'					 => 'Display notification about adding the family member by one of your friends on Activity page',
	'AP_SHOW_NEW_RELATIONSHIP'						 => 'Display notification about new relationship',
	'AP_SHOW_NEW_RELATIONSHIP_EXPLAIN'				 => 'Display notification about new relationship of one of your friends on Activity page',
	'AP_DISPLAY_WELCOME'							 => 'Display welcome block on Activity page',
	'AP_DISPLAY_WELCOME_EXPLAIN'					 => 'Display welcome block for unregistered users on Activity page. You can edit it following the instructions below.',
	'AP_HIDE_FOR_GUEST'								 => 'Hide Activity page for guests',
	'AP_HIDE_FOR_GUEST_EXPLAIN'						 => 'Activity page module will be redirected to the index page when accessed by a guest',
	'ACP_SN_ACTIVITYPAGE_IS_MAIN'					 => 'Set Activity page as first page',
	'ACP_SN_ACTIVITYPAGE_IS_MAIN_EXPLAIN'			 => 'If you want to have the Activity page as the first page of your web instead of the index.php page, please follow these instructions.',
	'ACP_SN_ACTIVITYPAGE_IS_MAIN_OPEN_FIND'			 => 'Open file .htaccess (located in the root of your web) and find:',
	'ACP_SN_ACTIVITYPAGE_IS_MAIN_AFTER_ADD'			 => 'After add:',
	'ACP_SN_ACTIVITYPAGE_IS_MAIN_SAVE'				 => 'Save the file and upload it to your web.',
	'ACP_SN_ACTIVITYPAGE_IS_MAIN_NO_DIRECTORY_INDEX' => 'If you can not find the DirectoryIndex in your .htaccess file, go to the bottom of the file and add this line',
	'ACP_SN_ACTIVITYPAGE_NOT_MAIN'					 => 'Unset Activity page as first page',
	'ACP_SN_ACTIVITYPAGE_NOT_MAIN_EXPLAIN'			 => 'If you don\'t want to have the Activity page as the first page of your web instead of index.php page, please follow these instructions.',
	'ACP_SN_ACTIVITYPAGE_NOT_MAIN_OPEN_FIND'		 => 'Open file .htaccess (located in the root of your web) and find:',
	'ACP_SN_ACTIVITYPAGE_NOT_MAIN_DELETE'			 => 'Delete it, save the file and upload to your web.',
	'ACP_SN_ACTIVITYPAGE_WELCOME'					 => 'Edit Welcome text on Activity page',
	'ACP_SN_ACTIVITYPAGE_WELCOME_EXPLAIN'			 => 'If you want to display the Welcome text for guests on Activity page, you can follow these instructions.',
	'ACP_SN_ACTIVITYPAGE_WELCOME_INSTRUCTIONS'		 => 'Open file language/en/mods/socialnet.php using a <a href="http://www.pspad.com/">text editor</a> and find:',
	'ACP_SN_ACTIVITYPAGE_WELCOME_EDIT'				 => 'There you can see two lines containing the title and the text of Welcome block. Feel free to edit them and also style them using <a href="http://www.w3.org/wiki/HTML">HTML</a> and CSS.',
));

// USER PROFILE ACP
$lang = array_merge($lang, array(
	'SN_ENABLE_REPORT'						 => 'Enable reporting users',
	'SN_ENABLE_REPORT_EXPLAIN'				 => 'Users will be able to report other users',
	'SN_MAX_PROFILE_VALUE'					 => 'Max length of value displayed on Activity page',
	'SN_MAX_PROFILE_VALUE_EXPLAIN'			 => 'Set the maximum number of characters of the values displayed on Activity page after the profile update.',
	'SN_PROFILE_NO_REASON'					 => 'You need to create at least one report reason',
	'SN_PROFILE_REPORT_REASONS'				 => 'Report user reasons',
	'SN_PROFILE_REPORT_REASONS_EXPLAIN'		 => 'Here you can manage reasons for reporting users',
	'SN_PROFILE_ADD_REASON'					 => 'Add reason',
	'SN_PROFILE_DELETE_REASON_CONFIRM'		 => 'Are you sure you want to delete this reason?',
	'SN_PROFILE_REASON_ADDED'				 => 'Report reason has been added successfully',
	'SN_PROFILE_REASON_DELETED'				 => 'Report reason has been deleted successfully',
	'SN_PROFILE_MANAGE_EMOTES'				 => 'Emotes Management',
	'SN_PROFILE_MANAGE_EMOTES_EXPLAIN'		 => 'You can manage Emotes via this panel',
	'SN_PROFILE_EMOTE_IMAGE'				 => 'Emoticon',
	'SN_PROFILE_EMOTE_NAME'					 => 'Emote',
	'SN_PROFILE_ADD_EMOTE'					 => 'Add new Emote',
	'SN_PROFILE_EDIT_EMOTE'					 => 'Edit Emote',
	'SN_PROFILE_MANAGE_EMOTES_EMPTY_NAME'	 => 'You have to give the emote a name',
	'SN_PROFILE_EMOTE_EDITED'				 => 'Emote has been edited successfully',
	'SN_PROFILE_EMOTE_ADDED'				 => 'Emote has been added successfully',
	'SN_PROFILE_EMOTE_DELETED'				 => 'Emote has been deleted successfully',
));

// PHPBB LOG CONFIG
$lang_log_main = '<strong>Social Network &raquo</strong> ';

$lang = array_merge($lang, array(
	'LOG_CONFIG_SN_MAIN'									 => $lang_log_main . 'Global settings changed',
	'LOG_CONFIG_SN_MODULES'									 => $lang_log_main . 'Available modules changed',
	'LOG_CONFIG_SN_CB'										 => $lang_log_main . 'Confirmation box settings changed',
	'LOG_CONFIG_SN_BLOCKS'									 => $lang_log_main . 'Available blocks changed',
	'LOG_CONFIG_SN_BLOCK_USERONLINE'						 => $lang_log_main . 'Settings User OnlineBlock changed',
	'LOG_CONFIG_SN_BLOCK_MENU_ADD_BUTTON'					 => $lang_log_main . 'Menu block item <strong>%1$s</strong> added',
	'LOG_CONFIG_SN_BLOCK_MENU_EDIT_BUTTON'					 => $lang_log_main . 'Menu block item <strong>%1$s</strong> changed',
	'LOG_CONFIG_SN_BLOCK_MENU_DELETE'						 => $lang_log_main . 'Menu block item <strong>%1$s</strong> deleted',
	'LOG_CONFIG_SN_BLOCK_MENU_MOVE_UP'						 => $lang_log_main . 'Menu block moved item <strong>%1$s</strong> above <strong>%2$s</strong>',
	'LOG_CONFIG_SN_BLOCK_MENU_MOVE_DOWN'					 => $lang_log_main . 'Menu block moved item <strong>%1$s</strong> below <strong>%2$s</strong>',
	'LOG_CONFIG_SN_ADDONS_ADD_ADDON'						 => $lang_log_main . 'Add-on <strong>%1$s</strong> added',
	'LOG_CONFIG_SN_ADDONS_EDIT_ADDON'						 => $lang_log_main . 'Add-on <strong>%1$s</strong> changed',
	'LOG_CONFIG_SN_ADDONS_DELETE'							 => $lang_log_main . 'Add-on <strong>%1$s</strong> deleted',
	'LOG_CONFIG_SN_ADDONS_MOVE_UP'							 => $lang_log_main . 'Add-on moved item <strong>%1$s</strong> above <strong>%2$s</strong>',
	'LOG_CONFIG_SN_ADDONS_MOVE_DOWN'						 => $lang_log_main . 'Add-on moved item <strong>%1$s</strong> below <strong>%2$s</strong>',
	'LOG_CONFIG_SN_ADDONS_ENABLE_ADDON'						 => $lang_log_main . 'Add-on <strong>%1$s</strong> enabled',
	'LOG_CONFIG_SN_ADDONS_DISABLE_ADDON'					 => $lang_log_main . 'Add-on <strong>%1$s</strong> disabled',
	'LOG_CONFIG_SN_IM'										 => $lang_log_main . 'Instant Messenger module settings changed',
	'LOG_CONFIG_SN_IM_MSG_PURGED'							 => $lang_log_main . 'Instant Messenger messages purged',
	'LOG_CONFIG_SN_IM_CHATBOXES_CLOSED'						 => $lang_log_main . 'Instant Messenger chatboxes closed',
	'LOG_CONFIG_SN_USERSTATUS'								 => $lang_log_main . 'UserStatus module settings changed',
	'LOG_CONFIG_SN_USERSTATUS_BASICTOOLS_DELETE_STATUSES'	 => $lang_log_main . 'Userstatuses deleted for user %1$s',
	'LOG_CONFIG_SN_USERSTATUS_BASICTOOLS_DELETE_COMMENTS'	 => $lang_log_main . 'Userstatus comments deleted for user %1$s',
	'LOG_CONFIG_SN_USERSTATUS_BASICTOOLS_USER_DELETED'		 => $lang_log_main . 'Userstatuses and userstatus comments deleted for user deleted users',
	'LOG_CONFIG_SN_FMS'										 => $lang_log_main . 'Friend Management module settings changed',
	'LOG_CONFIG_SN_FMS_BASICTOOLS_DELETED_USER'				 => $lang_log_main . 'Purged all friends and friend groups of deleted users',
	'LOG_CONFIG_SN_AP'										 => $lang_log_main . 'Mainpage module settings changed',
	'LOG_CONFIG_SN_NTF'										 => $lang_log_main . 'Notifications module settings changed',
	'LOG_CONFIG_SN_UP'										 => $lang_log_main . 'Profile module settings changed',
));

?>