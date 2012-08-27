<?php
/**
 *
 * @package phpBB Social Network
 * @version 0.7.0
 * @copyright (c) phpBB Social Network Team 2010-2012 http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

if (!defined('SOCIALNET_INSTALLED') || !defined('IN_PHPBB'))
{
	return;
}

class sn_core_notify
{
	var $time = null;
	var $time_read = 86400;
	var $p_master = null;
	var $enabled = null;

	function sn_core_notify($p_master)
	{
		$this->time = time();
		$this->p_master =& $p_master;
		$this->enabled = $p_master->is_enabled('notify');
	}

	/**
	 * Prepare and store into db notification rows
	 * @param integer $type Type of notification
	 * @param mixed $to_user ID user(s), which belongs the notification
	 * @param array $data Notification data to be displayed
	 */
	public function add($type, $to_user, $data)
	{
		global $db, $config;
		if (!is_array($to_user))
		{
			$to_user = array($to_user);
		}

		if (!$this->enabled)
		{
			if ($config['allow_privmsg'])
			{
				// Send PMs
				foreach ($to_user as $idx => $user_id)
				{
					$this->send_pm($type, $user_id, $data);
				}

				$return = 'SN_NTF_PMS_SENDED';
			}
		}
		else
		{
			// Send NTFs
			$sqls = array();

			foreach ($to_user as $idx => $user_id)
			{
				$sqls[] = $this->prepare_add_sql($type, $user_id, $data);
			}

			$db->sql_multi_insert(SN_NOTIFY_TABLE, $sqls);
			$return = 'SN_NTF_NTFS_SENDED';
		}

		return $return;
	}

	/**
	 * Prepare sql array for store into db notification row
	 * @param integer $type Type of notification
	 * @param indeget $to_user ID user, which belongs the notification
	 * @param array $data Notification data to be displayed
	 */
	function prepare_add_sql($type, $to_user, $data)
	{
		global $user;

		return array(
			'ntf_time'	 => $this->time,
			'ntf_type'	 => $type,
			'ntf_user'	 => $to_user,
			'ntf_poster' => $user->data['user_id'],
			'ntf_read'	 => SN_NTF_STATUS_NEW,
			'ntf_change' => $this->time,
			'ntf_data'	 => serialize($data),
		);
	}

	/**
	 * Delete user notification
	 * @param integer $ntf_id ID of notification to be deleted. Delete all read notification which are older then 1 day when parameter $ntf_id = 0.
	 */
	public function del($ntf_id = 0)
	{
		global $db;

		if (!$this->enabled)
		{
			return;
		}

		if ($ntf_id == 0)
		{
			$sql_where = "ntf_read = " . SN_NTF_STATUS_READ . " AND ntf_change < " . ($this->time - $this->time_read);
		}
		else
		{
			$sql_where = "ntf_id = {$ntf_id}";
		}
		$db->sql_query("DELETE FROM " . SN_NOTIFY_TABLE . " WHERE " . $sql_where);
	}

	/**
	 * Send PM
	 */
	function send_pm($type, $send_to, $data)
	{
		global $user, $config, $db, $phpbb_root_path, $phpEx;

		if (!function_exists('display_forums'))
		{
			include_once("{$phpbb_root_path}/includes/functions_display.{$phpEx}");
		}
		if (!function_exists('get_folder'))
		{
			include_once("{$phpbb_root_path}/includes/functions_privmsgs.{$phpEx}");
		}

		$sql = "SELECT user_lang FROM " . USERS_TABLE . " WHERE user_id = $send_to" ;
		$rs = $db->sql_query($sql, 3600);
		$row = $db->sql_fetchrow($rs);
		$user_lang = $row['user_lang'] != '' ? $row['user_lang'] : $config['default_lang'];
		$lang = array();

		include("{$phpbb_root_path}language/{$user_lang}/ucp.{$phpEx}");
		include("{$phpbb_root_path}language/{$user_lang}/mods/socialnet.{$phpEx}");

		$send_from = $user->data['user_id'];

		$lang_text = $data['text'];
		$lang_subject = isset($lang[$lang_text . '_PM_TITLE']) ? $lang[$lang_text . '_PM_TITLE'] : "{ {$lang_text}_PM_TITLE }";
		//$lang_message = isset($lang[$lang_text . '_MESSAGE']) ? $lang[$lang_text . '_MESSAGE'] : "{ {$lang_text}_MESSAGE }";
		//$message = sprintf($lang_message, $user->data['username'], $this->p_master->u_action, $lang['UCP_ZEBRA_FRIENDS']);
		$lang_message = isset($lang[$lang_text]) ? $lang[$lang_text] : "{ {$lang_text} }";
		unset($data['text']);
		$subject = vsprintf($lang_subject, $data);
		$message = vsprintf($lang_message, $data);

		$poll = $uid = $bitfield = $options = '';
		generate_text_for_storage($subject, $uid, $bitfield, $options, false, false, false);
		generate_text_for_storage($message, $uid, $bitfield, $options, true, true, true);

		$data = array(
			'address_list'		 => array('u' => array($send_to => 'to')),
			'from_user_id'		 => $send_from,
			'from_username'		 => $config['sitename'],
			'icon_id'			 => 0,
			'from_user_ip'		 => $user->data['user_ip'],
			'enable_bbcode'		 => true,
			'enable_smilies'	 => true,
			'enable_urls'		 => true,
			'enable_sig'		 => true,
			'message'			 => $message,
			'bbcode_bitfield'	 => $bitfield,
			'bbcode_uid'		 => $uid,
		);
		submit_pm('post', $subject, $data, false);
	}

}
?>