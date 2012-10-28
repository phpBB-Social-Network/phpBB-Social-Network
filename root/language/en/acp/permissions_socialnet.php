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
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// Social Network Permissions
$lang['permission_cat']['socialnet'] = 'Social Network';

// Adding the permissions
$lang = array_merge($lang, array(
	'acl_a_sn_settings'			 => array('lang' => 'Can edit Social Network settings', 'cat' => 'settings'),
	'acl_u_sn_im'				 => array('lang' => 'Can use Instant Messenger', 'cat' => 'socialnet'),
	'acl_u_sn_notify'			 => array('lang' => 'Can use notifications', 'cat' => 'socialnet'),
	'acl_u_sn_userstatus'		 => array('lang' => 'Can use User status', 'cat' => 'socialnet'),
	'acl_m_sn_close_reports'	 => array('lang' => 'Can close user reports', 'cat' => 'misc'),
));

?>