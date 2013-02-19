<?php

/**
 *
 * @package phpBB Social Network
 * @version 1.0.0
 * @since 1.0.0
 * @copyright (c) phpBB Social Network Team 2010-2012 http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

// get addon name
$addon = request_var('addon', '');

if ( isset($socialnet->addon->$addon) && method_exists($socialnet->addon->$addon, 'direct_access') )
{
	$socialnet->addon->$addon->direct_access();
}
