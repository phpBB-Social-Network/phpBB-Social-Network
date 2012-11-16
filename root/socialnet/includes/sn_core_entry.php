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

class sn_core_entry extends sn_core_entry_gets
{
	var $p_master = null;

	var $types = array();
	var $friend = array();
	var $friends_entry = array();

	function sn_core_entry(&$p_master)
	{
		global $cache, $user;
		$this->p_master = &$p_master;

		$this->types = $cache->get(__CLASS__);

		//if (empty($this->types))
		{
			$this->types = array();
			if (in_array('userstatus', $this->p_master->modules))
			{
				$this->types[] = SN_TYPE_NEW_STATUS;
			}
			if ($this->p_master->config['ap_show_new_friendships'])
			{
				$this->types[] = SN_TYPE_NEW_FRIENDSHIP;
			}
			if ($this->p_master->config['ap_show_profile_updated'])
			{
				$this->types[] = SN_TYPE_PROFILE_UPDATED;
			}
			if ($this->p_master->config['ap_show_new_family'])
			{
				$this->types[] = SN_TYPE_NEW_FAMILY;
			}
			if ($this->p_master->config['ap_show_new_relationship'])
			{
				$this->types[] = SN_TYPE_NEW_RELATIONSHIP;
			}
			$this->types[] = SN_TYPE_EMOTE;
			$cache->put(__CLASS__, $this->types);
		}

		$this->friends = $this->p_master->friends['user_id'];
		$this->friends[] = $user->data['user_id'];
	}

	/**
	 * Load entries for Activity
	 *
	 * @param integer $last_time The last loaded entry
	 * @param integet $limit How many entries will be loaded
	 * @param boolean $older Load older entries (true), load newer entries (false)
	 * @param integer $user_id Load entries for specifics user
	 * @return array pole plne dat se vstupy na AP
	 */
	function get_entry_array($entry_id)
	{
		global $db, $user;

		$sql = 'SELECT *
				FROM ' . SN_ENTRIES_TABLE . '
				WHERE entry_id = ' . $entry_id;
		$res = $db->sql_query_limit($sql, 1);
		$entry_row = $db->sql_fetchrow($res);
		$db->sql_freeresult($res);

		$entry_arr = array();

		switch ($entry_row['entry_type'])
		{
		case SN_TYPE_NEW_STATUS:
			$entry_arr = $this->entry_status($entry_row['user_id'], $entry_row['entry_target'], $entry_row['entry_time']);
			break;

		case SN_TYPE_NEW_FRIENDSHIP:
			$entry_arr = $this->entry_friends($entry_row['user_id'], $entry_row['entry_target']);
			break;

		case SN_TYPE_PROFILE_UPDATED:
			$entry_arr = $this->entry_profile($entry_row['user_id'], $entry_row['entry_target'], $entry_row['entry_additionals']);
			break;

		case SN_TYPE_NEW_FAMILY:
		case SN_TYPE_NEW_RELATIONSHIP:
			$entry_arr = $this->entry_relation($entry_row['entry_type'], $entry_row['user_id'], $entry_row['entry_target'], $entry_row['entry_additionals']);
			break;

		case SN_TYPE_EMOTE:
			$entry_arr = $this->entry_emote($entry_row['user_id'], $entry_row['entry_target'], $entry_row['entry_additionals']);
			break;
		}

		$entry_arr['ID'] = $entry_row['entry_id'];
		$entry_arr['TYPE'] = $entry_row['entry_type'];
		$entry_arr['TIME'] = $entry_row['entry_time'];
		$entry_arr['TIME_AGO'] = $this->p_master->time_ago($entry_row['entry_time']);
		$entry_arr['TARGET'] = $entry_row['entry_target'];
		$entry_arr['DELETE_ENTRY'] = ($this->_can_delete($entry_row['entry_type'], $entry_row['user_id'], $entry_row['entry_target'])) ? true : false;

		return $entry_arr;
	}

	/**
	 * Load entries for Activity
	 *
	 * @param integer $last_time The last loaded entry
	 * @param integet $limit How many entries will be loaded
	 * @param boolean $older Load older entries (true), load newer entries (false)
	 * @param integer $user_id Load entries for specifics user
	 * @return array pole plne dat se vstupy na AP
	 */
	function get($last_time, $limit = 15, $older = true, $user_id = 0)
	{
		global $db, $user;

		$sql_where = array();
		if ($user_id == 0)
		{
			$sql_where[] = $db->sql_in_set('sn_e.user_id', $this->friends, false, true);
		}
		else
		{
			$sql_where[] = "( ( sn_e.user_id = '{$user_id}') OR ( sn_e.entry_type IN (" . SN_TYPE_EMOTE . ", " . SN_TYPE_NEW_FRIENDSHIP . ") AND  sn_e.entry_target = '{$user_id}' ) )";
		}

		if ($last_time != 0)
		{
			$sql_where[] = "sn_e.entry_time " . ($older ? '<' : '>') . " {$last_time}";
		}
		$sql_where[] = $db->sql_in_set('sn_e.entry_type', $this->types);

		$sql_ary = array(
			'SELECT'   => '*',
			'FROM'     => array(
				SN_ENTRIES_TABLE => 'sn_e'
			),
			'WHERE'    => implode(' AND ', $sql_where),
			'ORDER_BY' => 'sn_e.entry_time DESC'
		);
		$sql = $db->sql_build_query('SELECT', $sql_ary);

		if ($older)
		{
			$rs = $db->sql_query($sql, $limit + 3);
		}
		else
		{
			$rs = $db->sql_query($sql);
			$limit = 9999999999;
		}

		$entries_rowset = $db->sql_fetchrowset($rs);
		$db->sql_freeresult($rs);

		$entries = array();
		for ($i = 0; $i < $limit && isset($entries_rowset[$i]); $i++)
		{
			$entries_row = $entries_rowset[$i];

			switch ($entries_row['entry_type'])
			{
			case SN_TYPE_NEW_STATUS:
				$entries[$i] = $this->entry_status($entries_row['user_id'], $entries_row['entry_target'], $entries_row['entry_time']);
				break;

			case SN_TYPE_NEW_FRIENDSHIP:
				$entries[$i] = $this->entry_friends($entries_row['user_id'], $entries_row['entry_target']);
				break;

			case SN_TYPE_PROFILE_UPDATED:
				$entries[$i] = $this->entry_profile($entries_row['user_id'], $entries_row['entry_target'], $entries_row['entry_additionals']);
				break;

			case SN_TYPE_NEW_FAMILY:
			case SN_TYPE_NEW_RELATIONSHIP:
				$entries[$i] = $this->entry_relation($entries_row['entry_type'], $entries_row['user_id'], $entries_row['entry_target'], $entries_row['entry_additionals']);
				break;

			case SN_TYPE_EMOTE:
				$entries[$i] = $this->entry_emote($entries_row['user_id'], $entries_row['entry_target'], $entries_row['entry_additionals']);
				break;
			}

			$entries[$i]['ID'] = $entries_row['entry_id'];
			$entries[$i]['TYPE'] = $entries_row['entry_type'];
			$entries[$i]['TIME'] = $entries_row['entry_time'];
			$entries[$i]['TIME_AGO'] = $this->p_master->time_ago($entries_row['entry_time']);
			$entries[$i]['TARGET'] = $entries_row['entry_target'];
			$entries[$i]['DELETE_ENTRY'] = $this->_can_delete($entries_row['entry_type'], $entries_row['user_id'], $entries_row['entry_target']);
		}

		return array(
			'entries' => $entries,
			'more'    => count($entries_rowset) > $limit
		);
	}

	/**
	 * entries ADD
	 *
	 * @param integer $user_id User ID
	 * @param integer $target Target
	 * @param integer $type Type of entry
	 * @param array $additionals Additionals infromation for entry
	 */
	function add($user_id, $target, $type, $additionals = array())
	{
		global $db;

		$now = time();

		$sql_arr = array(
			'user_id'           => $user_id,
			'entry_target'      => $target,
			'entry_type'        => $type,
			'entry_time'        => $now,
			'entry_additionals' => serialize($additionals),
		);

		$sql = "INSERT INTO " . SN_ENTRIES_TABLE . $db->sql_build_array('INSERT', $sql_arr);
		$db->sql_query($sql);

	}

	/**
	 * entries DELETE
	 *
	 * * 1 param
	 * @param integer $entry Entry ID to be deleted
	 * * 2 params
	 * @param integer $entry Entry target to be deleted
	 * @param integer $entry_type Entry tabe to be deleted
	 *
	 * @return void
	 */
	function del($entry)
	{
		global $db, $user, $auth;
		$num_args = func_num_args();

		$sql_where = '';
		switch ($num_args)
		{
		case 1:
		// DELETE by Entry ID
			$sql_where = "entry_id = {$entry}";
			break;
		case 2:
			$type = func_get_arg(1);
			$sql_where = "entry_target = {$entry} AND entry_type = {$type}";
			break;
		}

		if ($sql_where == '')
		{
			return;
		}

		$sql = "SELECT * FROM " . SN_ENTRIES_TABLE . " WHERE {$sql_where}";
		$rs = $db->sql_query($sql);
		$row = $db->sql_fetchrow($rs);
		$db->sql_freeresult($rs);

		if ($this->_can_delete($row['entry_type'], $row['user_id'], $row['entry_target']))
		{
			$sql = "DELETE FROM " . SN_ENTRIES_TABLE . " WHERE {$sql_where}";
			$db->sql_query($sql);
		}
		return;
	}

	private function _can_delete($entry_type, $user_id, $target)
	{
		global $user, $auth;

		$can_delete = false;
		switch ($entry_type)
		{
		case SN_TYPE_NEW_STATUS:
			$can_delete = true;
			break;
		case SN_TYPE_PROFILE_UPDATED:
			$can_delete = $user->data['user_id'] == $user_id;
			break;

		case SN_TYPE_NEW_FRIENDSHIP:
		case SN_TYPE_NEW_FAMILY:
		case SN_TYPE_NEW_RELATIONSHIP:
		case SN_TYPE_EMOTE:
			$can_delete = $user->data['user_id'] == $user_id || $user->data['user_id'] == $target;
			break;
		}

		return ($can_delete || $auth->acl_get('a_'));
	}
}

class sn_core_entry_gets
{

	/**
	 * Load statuses on Activity page
	 */
	function entry_status($entry_uid, $entry_target, $entry_time)
	{
		global $db, $template;

		if (!in_array('userstatus', $this->p_master->modules))
		{
			return;
		}

		$sn_userstatus = &$this->p_master->modules_obj['userstatus'];

		$data = $sn_userstatus->_get_last_status($entry_uid, $entry_target);

		if (!isset($template->files['body']) || $template->files['body'] != 'socialnet/userstatus_status.html')
		{
			$template->set_filenames(array(
					'body' => 'socialnet/userstatus_status.html'
				));
		}

		$template->destroy_block_vars('us_status');

		$comments = $this->p_master->comments->get($sn_userstatus->commentModule, 'us', $entry_target, 0, 2);
		$data['COMMENTS'] = $comments['comments'];

		$template->assign_block_vars('us_status', $data);

		$template_data = $this->p_master->page_footer();
		return array(
			'DATA' => $template_data,
		);
	}

	/**
	 * Load FMS entries
	 */
	function entry_friends($entry_uid, $entry_target)
	{
		global $db;

		if (!isset($this->friends_entry[$entry_uid]))
		{
			$sql = "SELECT user_id, username, user_colour
					FROM " . USERS_TABLE . "
					WHERE user_id = " . $entry_uid;
			$u1_result = $db->sql_query($sql);
			$this->friends_entry[$entry_uid] = $db->sql_fetchrow($u1_result);
			$db->sql_freeresult($u1_result);
		}

		if (!isset($this->friends_entry[$entry_target]))
		{
			$sql = "SELECT user_id, username, user_colour
					FROM " . USERS_TABLE . "
					WHERE user_id = " . $entry_target;
			$u2_result = $db->sql_query($sql);
			$this->friends_entry[$entry_target] = $db->sql_fetchrow($u2_result);
			$db->sql_freeresult($u2_result);
		}

		return array(
			'USER1_USERNAME'  => $this->friends_entry[$entry_uid]['username'],
			'USER2_USERNAME'  => $this->friends_entry[$entry_target]['username'],
			'U_USER1_PROFILE' => $this->p_master->get_username_string($this->p_master->config['ap_colour_username'], 'full', $this->friends_entry[$entry_uid]['user_id'], $this->friends_entry[$entry_uid]['username'], $this->friends_entry[$entry_uid]['user_colour']),
			'U_USER2_PROFILE' => $this->p_master->get_username_string($this->p_master->config['ap_colour_username'], 'full', $this->friends_entry[$entry_target]['user_id'], $this->friends_entry[$entry_target]['username'], $this->friends_entry[$entry_target]['user_colour']),
		);
	}

	/**
	 * Load Profile entries
	 */
	function entry_profile($entry_uid, $entry_target, $entry_additionals = '')
	{
		global $db, $template, $user;

		$sql = "SELECT user_id, username, user_colour
		        FROM " . USERS_TABLE . "
				WHERE user_id = " . $entry_uid;
		$result = $db->sql_query($sql);
		$entry_user = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$entry_add = '';

		if (!empty($entry_additionals))
		{
			$entry_addArray = unserialize($entry_additionals);

			// Specific fields
			if (isset($entry_addArray['user_avatar']))
			{
				$entry_add = '';
			}
			else
			{
				// All fields
				$entry_size = sizeof($entry_addArray);
				$entry_size--;
				$idx = 0;

				if (is_array($entry_addArray) && !empty($entry_addArray))
				{
					foreach ($entry_addArray as $field => $value)
					{
						$field = strtoupper($field);
						$field_ = strtoupper(preg_replace('/^user_/s', '', $field));

						if (!empty($entry_add))
						{
							if ($idx < $entry_size)
							{
								$entry_add .= ', ';
							}
							else
							{
								$entry_add .= ' ' . strtolower($user->lang['AND']) . ' ';
							}
						}

						$entry_add .= '<strong>';
						$entry_add .= str_replace(' ', '&nbsp;', isset($user->lang[$field]) ? $user->lang[$field] : (isset($user->lang['SN_UP_' . $field]) ? $user->lang['SN_UP_' . $field] : (isset($user->lang[$field_]) ? $user->lang[$field_] : "{ $field }")));
						$entry_add .= '</strong>: ' . $value;

						$idx++;
					}
				}
			}
		}

		return array(
			'USERNAME'                => $entry_user['username'],
			'U_PROFILE'               => $this->p_master->get_username_string($this->p_master->config['ap_colour_username'], 'full', $entry_user['user_id'], $entry_user['username'], $entry_user['user_colour']),
			'PROFILE_FIELDS'          => $entry_add,
			'L_SN_AP_CHANGED_PROFILE' => $user->lang[$this->p_master->gender_lang('SN_AP_CHANGED_PROFILE', $entry_user['user_id'])],
			'L_SN_UP_CHANGED_AVATAR'  => $user->lang[$this->p_master->gender_lang('SN_UP_CHANGED_AVATAR', $entry_user['user_id'])],
		);
	}

	/**
	 * Load Family and Relation entries
	 */
	function entry_relation($entry_type, $entry_uid, $entry_target, $entry_additionals = '')
	{
		global $db, $template, $phpbb_root_path, $phpEx, $socialnet, $user;

		$sql = "SELECT user_id, username, user_colour
		FROM " . USERS_TABLE . "
		WHERE user_id = " . $entry_uid;
		$result = $db->sql_query($sql);
		$entry_user = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$partner_id = $family_id = 0;
		$partner_name = $family_name = $rel_msg = $family_msg = '';

		if ($entry_type == SN_TYPE_NEW_FAMILY)
		{
			$entry_addArray = unserialize($entry_additionals);

			if (isset($entry_addArray['family']))
			{
				if (is_numeric($entry_addArray['family']))
				{
					$family_id = $entry_addArray['family'];

					$sql = "SELECT username, user_colour
					FROM " . USERS_TABLE . "
					WHERE user_id = " . $family_id;
					$result = $db->sql_query($sql);
					$family = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);
				}
				else
				{
					$family_name = $entry_addArray['family'];
				}
			}

			$entry_status = $this->p_master->family_status($entry_target);
		}
		elseif ($entry_type == SN_TYPE_NEW_RELATIONSHIP)
		{
			if (!empty($entry_additionals))
			{
				$entry_addArray = unserialize($entry_additionals);

				if (isset($entry_addArray['relationship']))
				{
					if (is_numeric($entry_addArray['relationship']))
					{
						$partner_id = $entry_addArray['relationship'];

						$sql = "SELECT username, user_colour
						FROM " . USERS_TABLE . "
						WHERE user_id = " . $partner_id;
						$result = $db->sql_query($sql);
						$partner = $db->sql_fetchrow($result);
						$db->sql_freeresult($result);
					}
					else
					{
						$partner_name = $entry_addArray['relationship'];
					}
				}
			}

			$entry_status = $this->p_master->relationship_status($entry_target, ($partner_id != 0 || $partner_name != '') ? true : false);
		}
		else
		{
			$entry_status = '';
		}

		if (isset($partner))
		{
			$rel_msg = $this->p_master->get_username_string($this->p_master->config['ap_colour_username'], 'full', $partner_id, $partner['username'], $partner['user_colour']);
		}
		elseif ($partner_name != '')
		{
			$rel_msg = '<strong>' . $partner_name . '</strong>';
		}

		if (isset($family))
		{
			$family_msg = sprintf($user->lang[$this->p_master->gender_lang('SN_AP_ADDED_NEW_FAMILY_MEMBER', $entry_user['user_id'])], $this->p_master->get_username_string($this->p_master->config['ap_colour_username'], 'full', $family_id, $family['username'], $family['user_colour']), $entry_status);
		}
		elseif ($family_name != '')
		{
			$family_msg = sprintf($user->lang[$this->p_master->gender_lang('SN_AP_ADDED_NEW_FAMILY_MEMBER', $entry_user['user_id'])], '<strong>' . $family_name . '</strong>', $entry_status);
		}

		return array(
			'STATUS'                          => ($entry_status && $entry_type == SN_TYPE_NEW_RELATIONSHIP) ? $entry_status : '',
			'USERNAME'                        => $entry_user['username'],
			'U_PROFILE'                       => $this->p_master->get_username_string($this->p_master->config['ap_colour_username'], 'full', $entry_user['user_id'], $entry_user['username'], $entry_user['user_colour']),
			'U_PARTNER_PROFILE'               => $rel_msg,
			'L_SN_AP_ADDED_NEW_FAMILY_MEMBER' => $family_msg,
			'L_SN_AP_CHANGED_RELATIONSHIP'    => $user->lang[$this->p_master->gender_lang('SN_AP_CHANGED_RELATIONSHIP', $entry_user['user_id'])],
		);
	}

	/**
	 * Load Emotes entries
	 */
	function entry_emote($entry_uid, $entry_target, $entry_additionals = '')
	{
		global $db, $template, $phpbb_root_path, $phpEx;

		$sql = "SELECT user_id, username, user_colour
				FROM " . USERS_TABLE . "
				WHERE user_id = " . $entry_uid;
		$result = $db->sql_query($sql);
		$entry_user = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$u2_sql = "SELECT user_id, username, user_colour
				FROM " . USERS_TABLE . "
				WHERE user_id = " . $entry_target;
		$u2_result = $db->sql_query($u2_sql);
		$user2 = $db->sql_fetchrow($u2_result);
		$db->sql_freeresult($u2_result);

		$entry_addArray = unserialize($entry_additionals);
		$emote_id = $entry_addArray['emote_id'];

		$sql = "SELECT emote_name, emote_image
				FROM " . SN_EMOTES_TABLE . "
				WHERE emote_id = " . $emote_id;
		$result = $db->sql_query($sql);
		$emote = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$template->assign_vars(array(
				'SN_UP_EMOTE_FOLDER' => $phpbb_root_path . SN_UP_EMOTE_FOLDER,
			));

		return array(
			'U_USER1_PROFILE' => $this->p_master->get_username_string($this->p_master->config['ap_colour_username'], 'full', $entry_user['user_id'], $entry_user['username'], $entry_user['user_colour']),
			'U_USER2_PROFILE' => $this->p_master->get_username_string($this->p_master->config['ap_colour_username'], 'full', $user2['user_id'], $user2['username'], $user2['user_colour']),
			'EMOTE_NAME'      => $emote['emote_name'],
			'EMOTE_IMAGE'     => ($emote['emote_image'] != '') ? $emote['emote_image'] : '',
		);
	}

}

?>