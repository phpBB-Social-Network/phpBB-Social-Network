<?php
/**
 *
 * @package phpBB Social Network
 * @version 0.6.3
 * @copyright (c) phpBB Social Network Team 2010-2012 http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

if (!defined('SOCIALNET_INSTALLED') && !defined('IN_PHPBB'))
{
	return;
}

class acp_activitypage extends socialnet
{
	var $p_master = null;

	function acp_activitypage(&$p_master)
	{
		$this->p_master =& $p_master;
	}

	function main($id)
	{
		global $user, $phpbb_root_path, $template, $phpEx;

		$manage = request_var('manage', '');

		$template->assign_block_vars('sn_tabs', array(
			'HREF'		 => $this->p_master->u_action,
			'SELECTED'	 => empty($manage) ? true : false,
			'NAME'		 => $user->lang['SETTINGS']
		));

		$template->assign_block_vars('sn_tabs', array(
			'HREF'		 => $this->p_master->u_action . '&amp;manage=htaccess',
			'SELECTED'	 => $manage == 'htaccess' ? true : false,
			'NAME'		 => $user->lang['SN_PROFILE_REPORT_REASONS']
		));

		$display_vars = array(
			'title'	 => 'ACP_AP_SETTINGS',
			'vars'	 => array(
				'legend1'					 => 'ACP_SN_ACTIVITYPAGE_SETTINGS',
				'ap_num_last_posts'			 => array('lang' => 'AP_NUM_LAST_POSTS', 'validate' => 'int:5', 'type' => 'text:3:4', 'explain' => true),
				'ap_show_new_friendships'	 => array('lang' => 'AP_SHOW_NEW_FRIENDSHIPS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'ap_show_profile_updated'	 => array('lang' => 'AP_SHOW_PROFILE_UPDATED', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'ap_show_new_family'		 => array('lang' => 'AP_SHOW_NEW_FAMILY', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'ap_show_new_relationship'	 => array('lang' => 'AP_SHOW_NEW_RELATIONSHIP', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'ap_display_welcome'		 => array('lang' => 'AP_DISPLAY_WELCOME', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'ap_hide_for_guest'			 => array('lang' => 'AP_HIDE_FOR_GUEST', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'ap_colour_username'		 => array('lang' => 'SN_COLOUR_NAME', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
			)
		);

		if ($manage == 'htaccess')
		{
			$htaccess = @file_get_contents("{$phpbb_root_path}.htaccess");

			preg_match('/\nDirectoryIndex ([^\n]*)/si', $htaccess, $preg_htaccess);

			$template->assign_var('B_ACP_AP_IS_SET_AS_ACTIVITYPAGE', (isset($preg_htaccess[1]) && preg_match('/^activitypage.' . $phpEx . '/i', $preg_htaccess[1])) ? true : false);
			$template->assign_var('B_ACP_SN_ACTIVITYPAGE_HTACCESS', true);
		}
		else
		{
			$this->p_master->_settings($id, 'sn_ap', $display_vars);
			$template->assign_var('B_ACP_SN_ACTIVITYPAGE_HTACCESS', false);
		}
	}
}

?>