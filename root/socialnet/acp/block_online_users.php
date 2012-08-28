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

class acp_socialnet_block_online_users
{
	function acp_socialnet_block_online_users(&$p_master)
	{
		$this->p_master = &$p_master;
	}
	
	function main($id)
	{
		global $db, $template, $phpbb_root_path, $phpEx, $user;
		
		$display_vars = array(
			'title'	 => 'ACP_SN_BLOCK_ONLINE_USERS_SETTINGS',
			'vars'	 => array(
				'legend1'						 => 'ACP_SN_BLOCK_ONLINE_USERS_SETTINGS',
				'block_uo_all_users' => array('lang' => 'BLOCK_UO_SHOW_ALL', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'block_uo_check_every' => array( 'lang' => 'BLOCK_UO_CHECK_EVERY', 'validate' => 'int', 'type' => 'text:3:4', 'append' => ' ' . $user->lang['SN_TIME_PERIODS']['SECONDS'], 'explain' => true)
			));

		$this->p_master->_settings($id, 'sn_block_userOnline', $display_vars);
	}
}


?>