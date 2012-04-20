<?php
/**
*
* @package phpBB Social Network
* @version 0.6.3
* @copyright (c) 2010-2012 Kamahl & Culprit http://phpbbsocialnetwork.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

class ucp_socialnet_info
{
	function module()
	{
		global $config;

		if (!isset($config['version_socialNet']))
		{
			return array();
		}

		return array(
			'filename'	 => 'ucp_socialnet',
			'title'		 => 'UCP_SOCIALNET',
			'version'	 => $config['version_socialNet'],
			'modes'		 => array(
				// Instant Messenger
				'module_im'					 => array('title' => 'UCP_SN_IM', 'auth' => '', 'cat' => array('UCP_SOCIALNET')),
				'module_im_settings'		 => array('title' => 'UCP_SN_IM_SETTINGS', 'auth' => '', 'cat' => array('UCP_SN_IM')),
				//* SOCIALNET MAIN */
				'settings'					 => array('title' => 'UCP_SOCIALNET_SETTINGS', 'auth' => '', 'cat' => array('UCP_IM_MAIN')),
				//* APPROVAL SYSTEM */
				'module_approval_friends'	 => array('title' => 'UCP_ZEBRA_FRIENDS', 'auth' => '', 'cat' => array('UCP_ZEBRA')),
				'module_approval_foes'		 => array('title' => 'UCP_ZEBRA_FOES', 'auth' => '', 'cat' => array('UCP_ZEBRA')),
				'module_approval_ufg'		 => array('title' => 'UCP_SN_APPROVAL_UFG', 'auth' => '', 'cat' => array('UCP_ZEBRA')),
				//* INSTANT MESSENGER */
				'purgemsgs'					 => array('title' => 'UCP_SOCIALNET_IM_PURGE_MESSAGES', 'auth' => '', 'cat' => array('UCP_IM_MAIN')),
				//* USER STATUS */
				'userstatus'				 => array('title' => 'UCP_SOCIALNET_USERSTATUS', 'auth' => '', 'cat' => array('UCP_IM_MAIN')),
				//* USER PROFILE */
				'module_profile'			 => array('title' => 'UCP_SN_PROFILE', 'auth' => '', 'cat' => array('UCP_SOCIALNET')),
				'module_profile_relations'	 => array('title' => 'UCP_SN_PROFILE_RELATIONS', 'auth' => '', 'cat' => array('UCP_SOCIALNET')),
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