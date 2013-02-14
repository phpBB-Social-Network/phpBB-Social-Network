<?php
/**
 *
 * @package phpBB Social Network
 * @version 0.7.0
 * @copyright (c) phpBB Social Network Team 2010-2012 http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

if (isset($config['version_socialNet']))
{

	class mcp_socialnet_info
	{
		function module()
		{
			global $config;

			return array(
				'filename'	 => 'mcp_socialnet',
				'title'		 => 'MCP_SOCIALNET',
				'version'	 => $config['version_socialNet'],
				'modes'		 => array(
					'module_reportuser'	 => array('title' => 'MCP_SN_REPORTUSER', 'auth' => 'acl_m_sn_close_reports', 'cat' => array('MCP_SOCIALNET')),
				),
			);
		}

		function install()
		{}

		function uninstall()
		{}
	}

}
?>