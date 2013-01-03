<?php
/**
 *
 * @package phpBB Social Network
 * @version 1.0.0
 * @copyright (c) phpBB Social Network Team 2010-2012 http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (!isset($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_SN_COLOUR_USERNAMES_SETTINGS'	=> 'Colour Usernames addon settings',
	'ACP_SN_COLOUR_USERNAMES_SETTINGS_EXPLAIN'	=> 'Here is list of available modules that can have usernames coloured. You can specify colouring for each of them individually.',
	'SN_IM_COLOUR_NAME'	=> 'Colour usernames in IM module',
	'SN_IM_COLOUR_NAME_EXPLAIN'	=> 'If set to yes, all usernames used in IM module will be coloured.',
	'SN_USERSTATUS_COLOUR_NAME'	=> 'Colour usernames in User Status module',
	'SN_USERSTATUS_COLOUR_NAME_EXPLAIN'	=> 'If set to yes, all usernames used in User Status module will be coloured.',
	'SN_APPROVAL_COLOUR_NAME'	=> 'Colour usernames in Friends Management System module',
	'SN_APPROVAL_COLOUR_NAME_EXPLAIN'	=> 'If set to yes, all usernames used in Friends Management System module will be coloured.',
	'SN_ACTIVITYPAGE_COLOUR_NAME'	=> 'Colour usernames in Activity Page module',
	'SN_ACTIVITYPAGE_COLOUR_NAME_EXPLAIN'	=> 'If set to yes, all usernames used in Activity Page module will be coloured.',
	'SN_NOTIFY_COLOUR_NAME'	=> 'Colour usernames in Notification module',
	'SN_NOTIFY_COLOUR_NAME_EXPLAIN'	=> 'If set to yes, all usernames used in Notification module will be coloured.',
	'SN_PROFILE_COLOUR_NAME'	=> 'Colour usernames in Profile module',
	'SN_PROFILE_COLOUR_NAME_EXPLAIN'	=> 'If set to yes, all usernames used in Profile module will be coloured.',
));

?>