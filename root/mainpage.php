<?php
/**
*
* @package phpBB Social Network
* @version 0.6.3
* @copyright (c) 2010-2012 Kamahl & Culprit http://phpbbsocialnetwork.com
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
$user->setup();

if ($user->data['user_type'] == USER_IGNORE)
{
	include_once($socialnet_root_path . 'mainpage.' . $phpEx);
	include_once($socialnet_root_path . 'userstatus.' . $phpEx);

	$sn_mainpage = new socialnet_mainpage($socialnet);
}
else
{
	$sn_mainpage =& $socialnet->modules_obj['mainpage'];
}
/**
 * Load blocks for mainpage
 */
$socialnet->blocks(array(
	'myprofile',
	'menu',
	'search',
	'recent_discussions',
	'friends_suggestions',
	'birthday',
	'statistics',
	'friend_requests',
));

$socialnet->online_users();

$template->assign_vars(array(
	'U_BOARD'			 => append_sid("{$phpbb_root_path}index.$phpEx"),
	'S_ON_MAINPAGE'		 => true,
	'S_ALLOW_LAST_POSTS' => ($config['mp_num_last_posts'] > 0) ? true : false,
));

// Output page
page_header(@$user->lang['SN_MP_MAINPAGE']);

$template->set_filenames(array(
	'body'	 => 'socialnet/mainpage_body.html'
));

page_footer();

?>