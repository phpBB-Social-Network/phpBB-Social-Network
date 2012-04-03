<?php
/**
*
* @package phpBB Social Network
* @version 0.6.3
* @copyright (c) 2010-2012 Kamahl & Culprit http://phpbbsocialnetwork.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('SOCIALNET_INSTALLED') && !defined('IN_PHPBB'))
{
	return;
}

/**
 * Admin class fro module Main PAge for Social Network
 * @package MainPage
 */
class acp_mainpage extends socialnet
{
	var $p_master = null;

	/**
	 * Constructor for this class
	 * @param $p_master object Social Network class
	 * @access public
	 */
	function acp_mainpage(&$p_master)
	{
		$this->p_master =& $p_master;
	}

	/**
	 * Main function for Instant Messenger
	 * Prepare config data for configuring
	 * @param $id int phpBB variable
	 * @access public
	 * @return void
	 */
	function main($id)
	{
		global $user, $phpbb_root_path, $template, $phpEx;

		$display_vars = array(
			'title'	 => 'ACP_MP_SETTINGS',
			'vars'	 => array(
				'legend1'					 => 'ACP_SN_MAINPAGE_SETTINGS',
				'mp_num_last_posts'			 => array('lang' => 'MP_NUM_LAST_POSTS', 'validate' => 'int:5', 'type' => 'text:3:4', 'explain' => true),
				'mp_show_new_friendships'	 => array('lang' => 'MP_SHOW_NEW_FRIENDSHIPS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'mp_show_profile_updated'	 => array('lang' => 'MP_SHOW_PROFILE_UPDATED', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'mp_show_new_family'	 => array('lang' => 'MP_SHOW_NEW_FAMILY', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'mp_show_new_relationship'	 => array('lang' => 'MP_SHOW_NEW_RELATIONSHIP', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'mp_display_welcome'		 => array('lang' => 'MP_DISPLAY_WELCOME', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'mp_hide_for_guest'			 => array('lang' => 'MP_HIDE_FOR_GUEST', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				// 0.6.0.1
				'mp_colour_username'		 => array('lang' => 'SN_COLOUR_NAME', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				//'mp_replace_register'		 => array('lang' => 'MP_REPLACE_REGISTER', 'validate' => 'bool', 'type' => 'radio:no_yes', 'explain' => true),
				)
		);

		$htaccess = @file_get_contents("{$phpbb_root_path}.htaccess");

		preg_match('/\nDirectoryIndex ([^\n]*)/si', $htaccess, $preg_htaccess);

		$template->assign_var('B_ACP_MP_IS_SET_AS_MAINPAGE', (isset($preg_htaccess[1]) && preg_match('/^mainpage.' . $phpEx . '/i', $preg_htaccess[1])) ? true : false);

		$this->p_master->_settings($id, 'sn_mp', $display_vars);
	}
}

?>