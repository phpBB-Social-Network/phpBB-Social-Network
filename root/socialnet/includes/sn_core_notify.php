<?php
/**
 *
 * @package phpBB Social Network
 * @version 0.6.3
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
	var $enabled = null;

	function sn_core_notify( $p_master)
	{
		$this->time = time();
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
		global $db;

		if (!$this->enabled)
		{
			return;
		}

		$sqls = array();
		if (is_array($to_user))
		{
			foreach ($to_user as $idx => $user_id)
			{
				$sqls[] = $this->prepare_add_sql($type, $user_id, $data);
			}
		}
		else
		{
			$sqls[] = $this->prepare_add_sql($type, $to_user, $data);
		}
		$db->sql_multi_insert(SN_NOTIFY_TABLE, $sqls);
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
			'ntf_time'   => $this->time,
			'ntf_type'   => $type,
			'ntf_user'   => $to_user,
			'ntf_poster' => $user->data['user_id'],
			'ntf_read'   => SN_NTF_STATUS_NEW,
			'ntf_change' => $this->time,
			'ntf_data'   => serialize($data),
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
}

?>