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

class acp_activitypage extends socialnet
{
	var $p_master = null;

	function acp_activitypage(&$p_master)
	{
		$this->p_master =& $p_master;
	}

	function main($id)
	{
		global $user, $phpbb_root_path, $template, $phpEx, $db;

		$manage = request_var('manage', '');
		$template->assign_var('ACP_SN_MANAGE', $manage);
		$sn_is_mainpage = false;
		if ($manage == 'htaccess')
		{
			$htaccess = @file_get_contents("{$phpbb_root_path}.htaccess");

			preg_match('/\nDirectoryIndex ([^\n]*)/si', $htaccess, $preg_htaccess);
			$sn_is_mainpage = (isset($preg_htaccess[1]) && preg_match('/^activitypage\.' . $phpEx . '/si', $preg_htaccess[1])) ? false : true;
			$template->assign_var('B_ACP_AP_IS_SET_AS_ACTIVITYPAGE', $sn_is_mainpage);
			$template->assign_var('B_ACP_SN_ACTIVITYPAGE_HTACCESS', true);
		}
		else
		{
			$template->assign_var('B_ACP_SN_ACTIVITYPAGE_HTACCESS', false);
		}

		$template->assign_block_vars('sn_tabs', array(
			'HREF'		 => $this->p_master->u_action,
			'SELECTED'	 => empty($manage) ? true : false,
			'NAME'		 => $user->lang['SETTINGS']
		));
		
		$template->assign_block_vars('sn_tabs', array(
			'HREF'		 => $this->p_master->u_action . '&amp;manage=welcome',
			'SELECTED'	 => $manage == 'welcome' ? true : false,
			'NAME'		 => $user->lang['ACP_SN_ACTIVITYPAGE_WELCOME']
		));

		$template->assign_block_vars('sn_tabs', array(
			'HREF'		 => $this->p_master->u_action . '&amp;manage=htaccess',
			'SELECTED'	 => $manage == 'htaccess' ? true : false,
			'NAME'		 => $sn_is_mainpage ? $user->lang['ACP_SN_ACTIVITYPAGE_IS_MAIN'] : $user->lang['ACP_SN_ACTIVITYPAGE_NOT_MAIN']
		));

		if ($manage == '')
		{
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
			$this->p_master->_settings($id, 'sn_ap', $display_vars);
		}
		else if ($manage == 'welcome')
		{
      $user->add_lang('posting');

			include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
  		include($phpbb_root_path . 'includes/message_parser.' . $phpEx);

			$submit	= (isset($_POST['submit'])) ? true : false;

      if ($submit)
			{
			  $welcome_text_title = request_var('welcome_text_title', '');
        $welcome_text = utf8_normalize_nfc(request_var('welcome_text', '', true));
        $uid = $bitfield = $options = '';

        generate_text_for_storage($welcome_text, $uid, $bitfield, $options, true, true, false);

        $sql_ary = (array(
          'welcome_text_title'		=> $welcome_text_title,
					'welcome_text'					=> $welcome_text,
					'bbcode_uid'      			=> $uid,
	    		'bbcode_bitfield' 			=> $bitfield,
				));

      	$sql = 'UPDATE ' . SN_WELCOME_TEXT_TABLE . '
									SET ' . $db->sql_build_array('UPDATE', $sql_ary);
				$db->sql_query($sql);

      	trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->p_master->u_action . '&amp;manage=welcome'));
			}

			display_custom_bbcodes();

			$sql = 'SELECT welcome_text_title, welcome_text, bbcode_uid, bbcode_bitfield
                FROM ' . SN_WELCOME_TEXT_TABLE;
			$result	 = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);

			$welcome_text_preview = generate_text_for_display($row['welcome_text'], $row['bbcode_uid'], $row['bbcode_bitfield'], OPTION_FLAG_BBCODE + OPTION_FLAG_LINKS);

			decode_message($row['welcome_text'], $row['bbcode_uid']);

  		$template->assign_vars(array(
  		  'WELCOME_TEXT_TITLE'		=> $row['welcome_text_title'],
				'WELCOME_TEXT'					=> $row['welcome_text'],
				'WELCOME_TEXT_PREVIEW'	=> ($welcome_text_preview) ? $welcome_text_preview  : '',
				'S_BBCODE_ALLOWED'			=> true,
				'S_BBCODE_QUOTE'				=> true,
				'S_BBCODE_IMG'					=> true,
				'S_LINKS_ALLOWED'				=> true,
				'S_BBCODE_FLASH'				=> false,
			));
			$db->sql_freeresult($result);
		}
	}
}

?>