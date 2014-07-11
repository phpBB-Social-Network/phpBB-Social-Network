<?php
/**
 *
 * @package phpBB Social Network
 * @version 0.7.0
 * @copyright (c) phpBB Social Network Team 2010-2012 http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

if (!defined('SOCIALNET_INSTALLED') && !defined('IN_PHPBB'))
{
	return;
}

class acp_notify extends socialnet
{
	var $p_master = null;

	function acp_notify(&$p_master)
	{
		$this->p_master =& $p_master;
	}

	function main($id)
	{
		global $user;
		
		$display_vars = array(
			'title'	 => 'ACP_AP_SETTINGS',
			'vars'	 => array(
				'legend1'	 => 'ACP_SN_NOTIFY_SETTINGS',
				'ntf_life'	 => array('lang' => 'SN_NTF_LIFE',  'validate' => 'int:5:20', 'type' => 'text:3:4', 'append' => ' ' . $user->lang['SECONDS'], 'explain' => true),
				'ntf_checktime'	 => array('lang' => 'SN_NTF_CHECKTIME',  'validate' => 'int:10:240', 'type' => 'text:3:4', 'append' => ' ' . $user->lang['SECONDS'], 'explain' => true),
				'ntf_colour_username'		 => array('lang' => 'SN_COLOUR_NAME', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
			)
		);

		$this->p_master->_settings($id, 'sn_ntf', $display_vars);
	}
}

?>