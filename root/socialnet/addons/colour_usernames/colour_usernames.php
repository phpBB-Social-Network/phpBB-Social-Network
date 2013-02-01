<?php

/**
 * Colour usernames addon
 *
 * @package	phpBB-Social-Network
 * @author	Senky
 * @version 1.0.0
 * @access public
 */
class colour_usernames
{
	/**
	 * Socialnet instance
	 *
	 * @var	object	$socialnet
	 */
	var $socialnet = null;

	/**
	 * Addon directory
	 *
	 * @var	string	$addon_directory
	 */
	var $addon_directory = '';

	/**
	 * Constructor
	 *
	 * Registers hooks
	 *
	 * @access public
	 *
	 * @param	object	$socialnet	socialnet instance
	 */
	function colour_usernames($socialnet)
	{
		$this->socialnet =& $socialnet;
		$this->addon_directory = $this->socialnet->get_addon_directory('colour_usernames');

		$this->socialnet->hook->add_action('sn.get_username_string_before', array($this, 'make_usernames_colourful'));
	}

	/**
	 * Direct access
	 *
	 * @access public
	 *
	 */
	function direct_access()
	{
	}

	/**
	 * ACP options
	 *
	 * @access public
	 *
	 */
	function acp_options($acp_socialnet, $id, $mode)
	{
		global $user, $template, $phpbb_admin_path, $phpEx;

		$addon_id = request_var('addon_id', 0);

		// small hack - in this case, directory is set to language/<lang_id>, so we need to go one level up
		$user->add_lang('../' . $this->addon_directory . 'language/' . $user->data['user_lang'] . '/username_colours');

		// small hack - in this case, directory is set to adm/style, so we need to go one level up
		$acp_socialnet->tpl_name = '../' . $this->addon_directory . 'style/template/acp_options';

		$acp_socialnet->u_action = append_sid($phpbb_admin_path . 'index.' . $phpEx, "i={$id}&amp;mode={$mode}&amp;action=addon_options&amp;addon_id={$addon_id}");

		$display_vars = array(
			'title'	 => 'ACP_SN_COLOUR_USERNAMES_SETTINGS',
			'vars'	 => array(
				'legend1'						 => 'ACP_SN_COLOUR_USERNAMES_SETTINGS',
				'im_colour_username'			 => array('lang' => 'SN_IM_COLOUR_NAME', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'userstatus_colour_username'	 => array('lang' => 'SN_USERSTATUS_COLOUR_NAME', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'approval_colour_username'		 => array('lang' => 'SN_APPROVAL_COLOUR_NAME', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'activitypage_colour_username'	 => array('lang' => 'SN_ACTIVITYPAGE_COLOUR_NAME', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'notify_colour_username'		 => array('lang' => 'SN_NOTIFY_COLOUR_NAME', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'profile_colour_username'		 => array('lang' => 'SN_PROFILE_COLOUR_NAME', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
			));

		$acp_socialnet->_settings($id, 'sn_addon_colour_usernames', $display_vars);

		$acp_socialnet->page_title = $display_vars['title'];

		$template->assign_vars(array(
			'L_TITLE'			=> $user->lang[$display_vars['title']],
			'L_TITLE_EXPLAIN'	=> $user->lang[$display_vars['title'] . '_EXPLAIN'],
			'U_ADDONS_BACKLINK'	=> append_sid($phpbb_admin_path . 'index.' . $phpEx, "i={$id}&amp;mode={$mode}"),
		));
	}

	/**
	 * Installs addon
	 *
	 * @access public
	 *
	 * @return
	 */
	function install()
	{
		global $db;

		$sqls = array(
			'INSERT INTO ' . SN_CONFING_TABLE . ' VALUES ("im_colour_username", 0, 0)',
			'INSERT INTO ' . SN_CONFING_TABLE . ' VALUES ("userstatus_colour_username", 0, 0)',
			'INSERT INTO ' . SN_CONFING_TABLE . ' VALUES ("approval_colour_username", 0, 0)',
			'INSERT INTO ' . SN_CONFING_TABLE . ' VALUES ("activitypage_colour_username", 0, 0)',
			'INSERT INTO ' . SN_CONFING_TABLE . ' VALUES ("notify_colour_username", 0, 0)',
			'INSERT INTO ' . SN_CONFING_TABLE . ' VALUES ("profile_colour_username", 0, 0)',
		);

		foreach ($sqls as $sql)
		{
			$db->sql_query($sql);
		}

		$cache->destroy('config');
	}

	/**
	 * Uninstalls addon
	 *
	 * @access public
	 *
	 * @return
	 */
	function uninstall()
	{
		global $db;

		$sqls = array(
			'DELETE FROM ' . SN_CONFING_TABLE . ' WHERE config_name = "im_colour_username"',
			'DELETE FROM ' . SN_CONFING_TABLE . ' WHERE config_name = "userstatus_colour_username"',
			'DELETE FROM ' . SN_CONFING_TABLE . ' WHERE config_name = "approval_colour_username"',
			'DELETE FROM ' . SN_CONFING_TABLE . ' WHERE config_name = "activitypage_colour_username"',
			'DELETE FROM ' . SN_CONFING_TABLE . ' WHERE config_name = "notify_colour_username"',
			'DELETE FROM ' . SN_CONFING_TABLE . ' WHERE config_name = "profile_colour_username"',
		);

		foreach ($sqls as $sql)
		{
			$db->sql_query($sql);
		}

		$cache->destroy('config');
	}

	/**
	 * Tells name of the addon
	 *
	 * @access static
	 *
	 * @return	string	name of the addon
	 */
	function addon_name()
	{
		return 'Colour Usernames';
	}

	/**
	 * Makes usernames colorful or not
	 *
	 * @access	public
	 *
	 * @param 	string	$module_name	name of module that requests socialnet::get_username_string()
	 *
	 * @return	array 	witch changed username colour if needed
	 */
	function make_usernames_colourful($module_name)
	{
		global $config;

		$modules = array('im', 'userstatus', 'approval', 'activitypage', 'notify', 'profile');

		if ( in_array($module_name, $modules) && $config[$module_name . '_colour_username'] == 0 )
		{
			return array(
				'username_colour' => ''
			);
		}
		else
		{
			return array();
		}
	}
}