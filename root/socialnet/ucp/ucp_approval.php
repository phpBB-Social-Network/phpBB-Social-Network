<?php
/**
 *
 * @package phpBB Social Network
 * @version 0.7.0
 * @copyright (c) phpBB Social Network Team 2010-2012 http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

include_once("{$phpbb_root_path}/includes/functions_display.{$phpEx}");
include_once("{$phpbb_root_path}/includes/functions_privmsgs.{$phpEx}");

class ucp_approval
{
	var $p_master = null;
	var $p_approval;
	var $p_notify = null;

	function ucp_approval(&$p_master)
	{
		global $socialnet;

		$this->p_master =& $p_master;
		$this->p_approval =& $socialnet->modules_obj['approval'];
		$this->p_notify =& $socialnet->notify;
	}

	function main($id, $module)
	{
		$this->p_master->tpl_name = 'socialnet/ucp_approval_friends';
		if ($module == 'friends' || $module == 'foes')
		{
			$this->friends($id, $module);
		}
		else
		{
			$this->$module($id, $module);
		}
	}

	function friends($id, $mode)
	{
		global $template, $user, $db, $phpEx, $phpbb_root_path, $cache, $socialnet, $config;

		$submit = (isset($_POST['submit']) || isset($_GET['add']) || isset($_GET['remove']) || isset($_POST['approve']) || isset($_POST['no_approve']) || isset($_POST['cancel_request_submit']) || isset($_GET['cancel'])) ? true : false;
		$s_hidden_fields = '';
		$l_mode = strtoupper($mode);

		if ($submit)
		{
			$data = $error = array();

			$var_ary = array(
				'usernames'				 => array(0),
				'approvals'				 => array(0),
				'approvalsName'			 => array(),
				'approve'				 => '',
				'no_approve'			 => '',
				'cancel_request'		 => array(0),
				'cancel_request_submit'	 => '',
				'add'					 => '',
				'redirect'				 => base64_encode(append_sid("{$phpbb_root_path}activitypage.{$phpEx}")),
			);

			foreach ($var_ary as $var => $default)
			{
				$data[$var] = request_var($var, $default, true);
			}
			$data['redirect'] = base64_decode($data['redirect']);

			if (!empty($data['add']) || sizeof($data['usernames']) || sizeof($data['approvals']) || sizeof($data['cancel_request']))
			{
				if (!confirm_box(true) && sizeof($data['usernames']))
				{
					confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
						'mode'		 => 'module_approval_' . $mode,
						'submit'	 => true,
						'usernames'	 => $data['usernames'],
						'redirect'	 => base64_encode($data['redirect']),
					)));
				}
				else
				{
					// Add users to approvals
					$message = array();
					if ($data['add'])
					{
						$this->_friends_foes_add($id, $mode, $data, $error);
						$message = $error;
						if ($data['add'])
						{
							$message[] = $user->lang[$l_mode . '_APPROVALS_ADDED'];
						}

						if (empty($data['add_approval']))
						{
							meta_refresh(3, $data['redirect']);
							trigger_error(implode('<br />', $message) . '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . $data['redirect'] . '">', '</a>'));
						}
					}

					// Add Freinds in approval request
					if ((isset($data['add_approval']) && !empty($data['add_approval'])) || !empty($data['approvals']) || !empty($data['cancel_request']))
					{
						if (isset($data['add_approval']) && !empty($data['add_approval']))
						{
							$where = "username_clean IN ('" . implode("','", $data['add_approval']) . "')";
							$data['approve'] = 'Approve';
						}
						if (isset($data['approvals']) && !empty($data['approvals']))
						{
							$where = "user_id IN ('" . implode("','", $data['approvals']) . "')";
							$data['approvals'] = array();
						}
						if (isset($data['cancel_request']) && !empty($data['cancel_request']))
						{
							$where = "user_id IN ('" . implode("','", $data['cancel_request']) . "')";
						}

						$sql = "SELECT user_id, username
								      FROM " . USERS_TABLE . "
								        WHERE {$where}";
						$rs = $db->sql_query($sql);
						$rowset = $db->sql_fetchrowset($rs);
						$db->sql_freeresult($rs);

						for ($i = 0; $i < count($rowset) && isset($rowset[$i]); $i++)
						{
							$data['approvals'][] = $rowset[$i]['user_id'];
							$data['approvalsName'][] = $rowset[$i]['username'];
							$data['cancelName'][] = $rowset[$i]['username'];
						}

					}
					$link = "ucp.{$phpEx}?i=socialnet&amp;mode=module_approval_friends";

					// Approve users
					if (!empty($data['approvals']))
					{
						$approve = !empty($data['approve']) ? true : false;

						if ($approve)
						{

							foreach ($data['approvals'] as $idx => $user_id)
							{
								$sql = "SELECT * FROM " . ZEBRA_TABLE . " WHERE user_id = {$user->data['user_id']} AND zebra_id = {$user_id}";
								$rs = $db->sql_query($sql);
								if ($db->sql_affectedrows($rs) == 0)
								{
									$sql_update = "UPDATE " . ZEBRA_TABLE . "
                                  SET friend = 1, approval = 0
                                    WHERE user_id = " . $user_id . "
                                      AND zebra_id = " . $user->data['user_id'];
									$db->sql_query($sql_update);

									$sql_insert = "INSERT INTO " . ZEBRA_TABLE . " (user_id, zebra_id, friend, foe, approval)
													         VALUES ( {$user->data['user_id']}, {$user_id}, 1, 0, 0)";
									$db->sql_query($sql_insert);

									$socialnet->purge_friends($user_id);
									$socialnet->record_entry($user->data['user_id'], $user_id, SN_TYPE_NEW_FRIENDSHIP);
									$error[] = $user->lang[$l_mode . '_APPROVALS_SUCCESS'] . " ({$data['approvalsName'][$idx]})";
									$message[] = $user->lang[$l_mode . '_APPROVALS_SUCCESS'] . " ({$data['approvalsName'][$idx]})";
									$ntf_text = 'SN_NTF_FRIENDSHIP_ACCEPT';
									$this->p_notify->add(SN_NTF_FRIENDSHIP, $user_id, array('text' => $ntf_text, 'user' => $user->data['username'], 'link' => $link));

								}
								else
								{
									$error[] = $user->lang[$l_mode . '_APPROVALS_REQUEST_EXIST'] . " \"{$data['add_approval'][$idx]}\"";
									$message[] = $user->lang[$l_mode . '_APPROVALS_REQUEST_EXIST'] . " \"{$data['add_approval'][$idx]}\"";
								}
							}

						}
						else
						{
							$data['approvals'] = array_diff($data['approvals'], $data['cancel_request']);

							foreach ($data['approvals'] as $idx => $user_id)
							{
								$sql_update = "DELETE FROM " . ZEBRA_TABLE . "
																WHERE user_id = " . $user_id . "
                                  AND zebra_id = " . $user->data['user_id'];
								$db->sql_query($sql_update);
								$socialnet->purge_friends($user_id);
								$ntf_text = 'SN_NTF_FRIENDSHIP_DENY';
								$message[] = $user->lang[$l_mode . '_APPROVALS_DENY'] . " \"{$data['approvalsName'][$idx]}\"";
								$this->p_notify->add(SN_NTF_FRIENDSHIP, $user_id, array('text' => $ntf_text, 'user' => $user->data['username'], 'link' => $link));
							}
							$error[] = $user->lang[$l_mode . '_APPROVALS_DENY'];
						}
					}

					// Remove users
					if (!empty($data['usernames']))
					{
						$sql = 'DELETE FROM ' . ZEBRA_TABLE . '
                      WHERE user_id = ' . $user->data['user_id'] . '
								        AND ' . $db->sql_in_set('zebra_id', $data['usernames']);
						$db->sql_query($sql);

						$sql_in_set = $db->sql_in_set('user_id', $data['usernames']);
						$sql = 'DELETE FROM ' . ZEBRA_TABLE . '
                      WHERE zebra_id = ' . $user->data['user_id'] . '
								        AND ' . $sql_in_set;
						$db->sql_query($sql);

						// DELETE from my groups
						$sql = "DELETE
								      FROM " . SN_FMS_USERS_GROUP_TABLE . "
								        WHERE fms_gid IN
                          (SELECT fms_gid FROM " . SN_FMS_GROUPS_TABLE . " WHERE user_id = {$user->data['user_id']}) AND {$sql_in_set}";
						$db->sql_query($sql);

						// DELETE from your groups
						$sql = "DELETE
								      FROM " . SN_FMS_USERS_GROUP_TABLE . "
								        WHERE fms_gid IN
                          (SELECT fms_gid FROM " . SN_FMS_GROUPS_TABLE . " WHERE {$sql_in_set}) AND user_id = {$user->data['user_id']}";
						$db->sql_query($sql);

						foreach ($data['usernames'] as $idx => $user_id)
						{
							$socialnet->purge_friends($user_id);
						}

						$message[] = $user->lang[$l_mode . '_APPROVALS_REMOVE'];
					}

					// Cancel request
					if (!empty($data['cancel_request']))
					{

						foreach ($data['cancel_request'] as $idx => $user_id)
						{
							$sql_cancel_request = "DELETE FROM " . ZEBRA_TABLE . "
                                      WHERE user_id = {$user->data['user_id']}
                                        AND zebra_id = {$user_id}
                                        AND approval = 1";
							$db->sql_query($sql_cancel_request);
							$socialnet->purge_friends($user_id);
							$message[] = $user->lang[$l_mode . '_APPROVALS_DENY'] . " \"{$data['cancelName'][$idx]}\"";
						}
						$ntf_text = 'SN_NTF_FRIENDSHIP_CANCEL';
						$this->p_notify->add(SN_NTF_FRIENDSHIP, $data['cancel_request'], array('text' => $ntf_text, 'user' => $user->data['username'], 'link' => $link));
						;
					}

					$cache->destroy('_snApFriendsToKnow' . $user->data['user_id']);

					$socialnet->purge_friends();
					meta_refresh(3, $data['redirect']);
					trigger_error(implode('<br />', $message) . '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . $data['redirect'] . '">', '</a>'));
				}
			}
		}

		// FRIEND
		$socialnet->fms_users(array_merge(array(
			'mode'			 => 'friend',
			'checkbox'		 => 'usernames',
			'limit'			 => $config['fas_friendlist_limit'],
			'slider'		 => false,
			'profile_link'	 => false,
		), $socialnet->fms_users_sqls('friend', $user->data['user_id'])));

		$s_username_approval_options = '';
		$s_username_cancel_request_options = '';

		if ($mode == 'friends')
		{
			// APROVE
			$socialnet->fms_users(array_merge(array(
				'mode'			 => 'approve',
				'checkbox'		 => 'approvals',
				'slider'		 => false,
				'profile_link'	 => false,
			), $socialnet->fms_users_sqls('approve', $user->data['user_id'])));

			// CANCEL
			$socialnet->fms_users(array_merge(array(
				'mode'			 => 'cancel',
				'checkbox'		 => 'cancel_request',
				'slider'		 => false,
				'profile_link'	 => false,
			), $socialnet->fms_users_sqls('cancel', $user->data['user_id'])));
		}

		$s_hidden_fields = build_hidden_fields(array('redirect' => request_var('redirect', base64_encode($this->p_master->u_action))));

		$template->assign_vars(array(
			'S_MODE'			 => 'approval_' . $mode,
			'L_TITLE'			 => $user->lang['UCP_ZEBRA_' . $l_mode],
			'U_FIND_USERNAME'	 => append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=searchuser&amp;form=ucp&amp;field=add'),
			'S_HIDDEN_FIELDS'	 => $s_hidden_fields,
			'U_ACTION'			 => $this->p_master->u_action,
		));
	}

	/**
	 * UCP Add, remove Friends Requests
	 */
	function _friends_foes_add($id, $mode, &$data, &$error)
	{
		global $db, $user, $template, $auth, $socialnet, $phpEx, $phpbb_root_path;

		$data['add'] = array_map('trim', array_map('utf8_clean_string', explode("\n", $data['add'])));
		$l_mode = strtoupper($mode);

		$sql = 'SELECT z.*, u.username, u.username_clean
				      FROM ' . ZEBRA_TABLE . ' z, ' . USERS_TABLE . ' u
				        WHERE z.user_id = ' . $user->data['user_id'] . '
					        AND u.user_id = z.zebra_id';
		$result = $db->sql_query($sql);

		$approvals = $friends = $foes = array();
		while ($row = $db->sql_fetchrow($result))
		{
			if ($row['approval'])
			{
				$approvals[] = utf8_clean_string($row['username']);
			}
			else if ($row['friend'])
			{
				$friends[] = utf8_clean_string($row['username']);
			}
			else
			{
				$foes[] = utf8_clean_string($row['username']);
			}
		}
		$db->sql_freeresult($result);

		$sql = 'SELECT z.*, u.username, u.username_clean
						  FROM ' . ZEBRA_TABLE . ' z, ' . USERS_TABLE . ' u
						    WHERE z.zebra_id = ' . $user->data['user_id'] . '
							    AND u.user_id = z.user_id';
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			if ($row['approval'])
			{
				$approvals[] = utf8_clean_string($row['username']);
			}
			else if ($row['friend'])
			{
				$friends[] = utf8_clean_string($row['username']);
			}
			else
			{
				$foes[] = utf8_clean_string($row['username']);
			}
		}
		$db->sql_freeresult($result);

		// remove approval friends from the username array
		$data['add_approval'] = array();
		for ($i = 0; $i < count($data['add']) && isset($data['add'][$i]); $i++)
		{
			if (in_array($data['add'][$i], $approvals))
			{
				$data['add_approval'][] = $data['add'][$i];
			}
		}

		$data['add'] = array_diff($data['add'], $data['add_approval']);
		$n = sizeof($data['add']);
		$data['add_appr'] = $data['add'];
		$data['add'] = array_diff($data['add'], $approvals);

		if (sizeof($data['add']) < $n)
		{
			$error[] = isset($user->lang['SN_FAS_NOT_ADDED_FRIENDS_IN_APPROVAL']) ? $user->lang['SN_FAS_NOT_ADDED_FRIENDS_IN_APPROVAL'] : '{ SN_FAS_NOT_ADDED_FRIENDS_IN_APPROVAL }';
		}

		// remove friends from the username array
		$n = sizeof($data['add']);
		$data['add'] = array_diff($data['add'], $friends);

		if (sizeof($data['add']) < $n)
		{
			$error[] = isset($user->lang['SN_FAS_NOT_ADDED_FRIENDS_IN_FRIENDS']) ? $user->lang['SN_FAS_NOT_ADDED_FRIENDS_IN_FRIENDS'] : '{ SN_FAS_NOT_ADDED_FRIENDS_IN_FRIENDS }';
		}

		// remove foes from the username array
		$n = sizeof($data['add']);
		$data['add'] = array_diff($data['add'], $foes);

		if (sizeof($data['add']) < $n)
		{
			$error[] = isset($user->lang['SN_FAS_NOT_ADDED_FRIENDS_IN_FOES']) ? $user->lang['SN_FAS_NOT_ADDED_FRIENDS_IN_FOES'] : '{ SN_FAS_NOT_ADDED_FRIENDS_IN_FOES }';
		}

		// remove the user himself from the username array
		$n = sizeof($data['add']);
		$data['add'] = array_diff($data['add'], array(utf8_clean_string($user->data['username'])));

		if (sizeof($data['add']) < $n)
		{
			$error[] = isset($user->lang['NOT_ADDED_' . $l_mode . '_SELF']) ? $user->lang['NOT_ADDED_' . $l_mode . '_SELF'] : '{ NOT_ADDED_' . $l_mode . '_SELF }';
		}

		unset($approvals, $friends, $foes, $n);

		if (sizeof($data['add']))
		{
			$sql = 'SELECT user_id, user_type
					      FROM ' . USERS_TABLE . '
					        WHERE ' . $db->sql_in_set('username_clean', $data['add']) . '
						        AND user_type <> ' . USER_INACTIVE;
			$result = $db->sql_query($sql);

			$user_id_ary = array();
			while ($row = $db->sql_fetchrow($result))
			{
				if ($row['user_id'] != ANONYMOUS && $row['user_type'] != USER_IGNORE)
				{
					$user_id_ary[] = $row['user_id'];
				}
				else if ($row['user_id'] != ANONYMOUS)
				{
					$error[] = $user->lang['NOT_ADDED_' . $l_mode . '_BOTS'];
				}
				else
				{
					$error[] = $user->lang['NOT_ADDED_' . $l_mode . '_ANONYMOUS'];
				}
			}
			$db->sql_freeresult($result);

			if (sizeof($user_id_ary))
			{
				foreach ($user_id_ary as $idx => $user_id)
				{
					$socialnet->purge_friends($user_id);
				}
				$sql_mode = ($mode == 'friends') ? 'approval' : 'foe';

				$sql_ary = array();
				foreach ($user_id_ary as $zebra_id)
				{
					$sql_ary[] = array(
						'user_id'	 => (int) $user->data['user_id'],
						'zebra_id'	 => (int) $zebra_id,
						$sql_mode	 => 1,
					);
				}

				$db->sql_multi_insert(ZEBRA_TABLE, $sql_ary);

				// purge friend suggestions cache
				foreach ( glob($phpbb_root_path . 'cache/*' . $socialnet->friendsCacheNameMutual . '*') as $file )
				{
					unlink($file);
				}

				if ($sql_mode == 'approval')
				{
					$link = "ucp.{$phpEx}?i=socialnet&amp;mode=module_approval_friends";
					$ntf_text = 'SN_NTF_FRIENDSHIP_REQUEST';
					$this->p_notify->add(SN_NTF_FRIENDSHIP, $user_id_ary, array('text' => $ntf_text, 'user' => $user->data['username'], 'link' => $link));
				}

				/*
				 // Do not send mail by adding foes
				 if ($mode != 'foes' && !empty($user_id_ary))
				 {
				 foreach ($user_id_ary as $idx => $user_id)
				 {
				 $this->send_pm($user_id);
				 }
				 }
				 */

				unset($user_id_ary);
			}
			else if (!sizeof($error))
			{
				$error[] = $user->lang['USER_NOT_FOUND_OR_INACTIVE'];
			}
		}
	}

	function ufg($id, $mode)
	{
		global $user, $template, $db, $socialnet;
		$this->p_master->tpl_name = 'socialnet/ucp_approval_ufg';
		$user->add_lang(array('acp/groups', 'ucp'));

		$submit = (isset($_POST['submit'])) ? true : false;

		if ($submit)
		{
			$data = $error = array();

			$var_ary = array(
				'add'	 => '',
				'group'	 => '',
				'submit' => '',
			);

			foreach ($var_ary as $var => $default)
			{
				$data[$var] = request_var($var, $default, true);
			}

			if (!empty($data['add']))
			{
				// CREATE GROUP
				$sql = "SELECT MAX(fms_gid) AS max_id FROM " . SN_FMS_GROUPS_TABLE . " WHERE user_id = {$user->data['user_id']}";
				$rs = $db->sql_query($sql);
				$max_id = (int) $db->sql_fetchfield('max_id');

				$db->sql_return_on_error(true);

				$sql_ary = array(
					'fms_gid'		 => $max_id + 1,
					'user_id'		 => $user->data['user_id'],
					'fms_name'		 => $data['add'],
					'fms_clean'		 => utf8_clean_string($data['add']),
					'fms_collapse'	 => 0,
				);

				$sql = "INSERT INTO " . SN_FMS_GROUPS_TABLE . "	" . $db->sql_build_array('INSERT', $sql_ary);

				if (!$db->sql_query($sql))
				{
					$error[] = sprintf($user->lang['ERROR_GROUP_ALREADY_EXISTS'], $data['add']);
				}
				$socialnet->reload_groups();
			}
			else
			{
				$error[] = $user->lang['ERROR_GROUP_EMPTY_NAME'];
			}
			$template->assign_var('ERROR', implode('<br />', $error));
		}

		$sql = "SELECT fms_g.*, COUNT(fms_u.user_id) AS count
				      FROM " . SN_FMS_GROUPS_TABLE . " AS fms_g LEFT OUTER JOIN " . SN_FMS_USERS_GROUP_TABLE . " AS fms_u ON fms_g.fms_gid = fms_u.fms_gid AND fms_g.user_id = fms_u.owner_id
				        WHERE fms_g.user_id = {$user->data['user_id']} AND fms_g.fms_gid > 0
				          GROUP BY fms_g.fms_gid, fms_g.fms_name, fms_g.fms_clean, fms_g.fms_collapse
				          ORDER BY fms_g.fms_name ASC";
		$rs = $db->sql_query($sql);
		$rowset = $db->sql_fetchrowset($rs);

		for ($i = 0; isset($rowset[$i]); $i++)
		{
			$row = $rowset[$i];
			$template->assign_block_vars('ufg', array(
				'GID'	 => $row['fms_gid'],
				'NAME'	 => $row['fms_name'],
				'CLEAN'	 => $row['fms_clean'],
				'COUNT'	 => $row['count'],
			));
		}

		$socialnet->fms_users(array_merge(array(
			'mode'	 => 'friend',
			'slider' => false,
			'limit'	 => 20,
		), $socialnet->fms_users_sqls('friend', $user->data['user_id'])));
	}
}
?>