<?php
/**
*
* @package phpBB Social Network
* @version 0.6.3
* @copyright (c) 2010-2012 Kamahl & Culprit http://phpbbsocialnetwork.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

class acp_socialnet_info
{
	function module()
	{
		return array(
			'filename'	 => 'acp_socialnet',
			'title'		 => 'ACP_CAT_SOCIALNET',
			'version'	 => '0.6.1',
			'modes'		 => array(
				'main'				 => array('title' => 'ACP_SN_MAIN', 'auth' => '', 'cat' => array('ACP_CAT_SOCIALNET')),
				'settings'			 => array('title' => 'ACP_SN_GLOBAL_SETTINGS', 'auth' => '', 'cat' => array('ACP_SN_SETTINGS')),
				'sett_modules'		 => array('title' => 'ACP_SN_AVAILABLE_MODULES', 'auth' => '', 'cat' => array('ACP_SN_SETTINGS')),
				'sett_confirmBox'	 => array('title' => 'ACP_SN_CONFIRMBOX_SETTINGS', 'auth' => '', 'cat' => array('ACP_SN_SETTINGS')),
				'blocks_enable'	 => array('title' => 'ACP_SN_BLOCKS_ENABLE', 'auth' => '', 'cat' => array('ACP_SN_SETTINGS')),
				'blocks_config'	 => array('title' => 'ACP_SN_BLOCKS_CONFIGURATION', 'auth' => '', 'cat' => array('ACP_SN_SETTINGS')),
				'addons_config'	=> array('title' => 'ACP_SN_ADDONS_CONFIGURATION', 'auth' => '', 'cat' => array('ACP_SN_SETTINGS')),

				// CORE MODULES
				'module_im'			 => array('title' => 'ACP_SN_IM_SETTINGS', 'auth' => 'acl_a_sn_settings', 'cat' => array('ACP_CAT_SOCIALNET', 'ACP_SN_MODULES_CONFIGURATION')),
				'module_userstatus'	 => array('title' => 'ACP_SN_USERSTATUS_SETTINGS', 'auth' => 'acl_a_sn_settings', 'cat' => array('ACP_CAT_SOCIALNET', 'ACP_SN_MODULES_CONFIGURATION')),
				'module_approval'	 => array('title' => 'ACP_SN_APPROVAL_SETTINGS', 'auth' => 'acl_a_sn_settings', 'cat' => array('ACP_CAT_SOCIALNET', 'ACP_SN_MODULES_CONFIGURATION')),
				'module_mainpage'	 => array('title' => 'ACP_SN_MAINPAGE_SETTINGS', 'auth' => 'acl_a_sn_settings', 'cat' => array('ACP_CAT_SOCIALNET', 'ACP_SN_MODULES_CONFIGURATION')),
				'module_notify'		 => array('title' => 'ACP_SN_NOTIFY_SETTINGS', 'auth' => 'acl_a_sn_settings', 'cat' => array('ACP_CAT_SOCIALNET', 'ACP_SN_MODULES_CONFIGURATION')),
				'module_profile'		 => array('title' => 'ACP_SN_PROFILE_SETTINGS', 'auth' => 'acl_a_sn_settings', 'cat' => array('ACP_CAT_SOCIALNET', 'ACP_SN_MODULES_CONFIGURATION')),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}

?>