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

class acp_profile extends socialnet
{
	var $p_master = null;

	function acp_profile(&$p_master)
	{
		$this->p_master = &$p_master;
	}

	function main($id)
	{
		global $db, $template, $user;

		$manage = request_var('manage', '');

		$template->assign_block_vars('sn_tabs', array(
			'HREF'     => $this->p_master->u_action,
			'SELECTED' => empty($manage) ? true : false,
			'NAME'     => $user->lang['SETTINGS']
		));
		
		$template->assign_block_vars('sn_tabs', array(
			'HREF'     => $this->p_master->u_action . '&amp;manage=reason',
			'SELECTED' => $manage == 'reason' ? true : false,
			'NAME'     => $user->lang['SN_PROFILE_REPORT_REASONS']
		));
		
		$template->assign_block_vars('sn_tabs', array(
			'HREF'     => $this->p_master->u_action . '&amp;manage=emotes',
			'SELECTED' => $manage == 'emotes' ? true : false,
			'NAME'     => $user->lang['SN_PROFILE_MANAGE_EMOTES']
		));

		if (empty($manage))
		{
			$display_vars = array(
				'title' => 'ACP_UP_SETTINGS',
				'vars'  => array(
					'legend1'              => 'ACP_SN_PROFILE_SETTINGS',
					'up_enable_report'     => array(
						'lang'     => 'SN_ENABLE_REPORT',
						'validate' => 'bool',
						'type'     => 'radio:yes_no',
						'explain'  => true
					),
					'ap_max_profile_value' => array(
						'lang'     => 'SN_MAX_PROFILE_VALUE',
						'validate' => 'int:0:255',
						'type'     => 'text:3:4',
						'explain'  => true
					),
					'up_emotes'            => array(
						'lang'     => 'SN_UP_EMOTES',
						'validate' => 'bool',
						'type'     => 'radio:yes:no',
						'explain'  => true
					),
				)
			);

			$this->p_master->_settings($id, 'sn_up', $display_vars);
		}
		else
		{
			$template->assign_var('LINK_BACK', true);
		}

		$template->assign_vars(array(
			'B_ACP_SN_MANAGE_REASONS' => ($manage == 'reason' ? true : false),
			'B_ACP_SN_MANAGE_EMOTES'  => ($manage == 'emotes' ? true : false)
		));

		if ($manage == 'reason')
		{
			$this->manage_reasons();
		}

		if ($manage == 'emotes')
		{
			$this->manage_emotes();
		}
	}

	function manage_reasons()
	{
		global $db, $template, $user;

		$action = request_var('action', '');
		$reason_id = request_var('reason_id', 0);

		$this->p_master->acpPanel_title = $user->lang['SN_PROFILE_REPORT_REASONS'];
		$this->p_master->acpPanel_explain = $user->lang['SN_PROFILE_REPORT_REASONS_EXPLAIN'];

		switch ($action)
		{
			case "delete_reason":
			
				if (confirm_box(true))
				{
					$sql = 'DELETE FROM ' . SN_REPORTS_REASONS_TABLE . '
										WHERE reason_id = ' . $reason_id;
					$db->sql_query($sql);

					trigger_error($user->lang['SN_PROFILE_REASON_DELETED'] . adm_back_link($this->p_master->u_action . '&amp;manage=reason'));
				}
				else
				{
					confirm_box(false, $user->lang['SN_PROFILE_DELETE_REASON_CONFIRM']);
				}

				redirect($this->p_master->u_action);

			break;

			default:

				$sql = 'SELECT reason_id, reason_text
									FROM ' . SN_REPORTS_REASONS_TABLE . '
										ORDER BY reason_id';
				$result = $db->sql_query($sql);

				$counter = 0;
				while ($row = $db->sql_fetchrow($result))
				{
					$template->assign_block_vars('reason', array(
							'TEXT'     => $row['reason_text'],
							'U_DELETE' => $this->p_master->u_action . '&amp;action=delete_reason&amp;manage=reason&amp;reason_id=' . $row['reason_id'],
							'LINE'     => (($counter % 2) + 1),
						));
					$counter++;
				}
				$db->sql_freeresult($result);

				$add_reason = (isset($_POST['add_reason'])) ? true : false;

				if ($add_reason)
				{
					$reason_text = request_var('reason_text', '', true);

					if ($reason_text == '')
					{
						redirect($this->p_master->u_action);
					}

					$sql = 'INSERT INTO ' . SN_REPORTS_REASONS_TABLE . ' (reason_text)
										VALUES ("' . $reason_text . '")';
					$db->sql_query($sql);

					trigger_error($user->lang['SN_PROFILE_REASON_ADDED'] . adm_back_link($this->p_master->u_action . '&amp;manage=reason'));
				}
		}

	}

	function manage_emotes()
	{
		global $template, $db, $user, $phpbb_root_path;

		$this->p_master->acpPanel_title = isset($user->lang['SN_PROFILE_MANAGE_EMOTES']) ? $user->lang['SN_PROFILE_MANAGE_EMOTES'] : '{ SN_PROFILE_MANAGE_EMOTES }';
		$this->p_master->acpPanel_explain = isset($user->lang['SN_PROFILE_MANAGE_EMOTES_EXPLAIN']) ? $user->lang['SN_PROFILE_MANAGE_EMOTES_EXPLAIN'] : '{ SN_PROFILE_MANAGE_EMOTES_EXPLAIN }';

		$action = request_var('action', '');

		$error = array();
		$template->assign_vars(array(
			'B_SN_PROFILE_EDIT_EMOTE' => ($action == 'add' || $action == 'edit') ? true : false,
			'SN_UP_EMOTE_FOLDER'      => $phpbb_root_path . SN_UP_EMOTE_FOLDER,
			'SN_PROFILE_EMOTE_ADD'    => ($action == 'add') ? true : false,
			'SN_PROFILE_EMOTE_EDIT'   => ($action == 'edit') ? true : false,
		));

		switch ($action)
		{
			case 'add':
			case 'edit':
				$this->emote_edit($error);
			break;

			case 'mdown':
			case 'mup':
				$this->emote_order($error, $action);
			break;
			
			case 'delete':
				$this->emote_delete($error);
			break;
		}

		$sql = "SELECT *
						FROM " . SN_EMOTES_TABLE . "
							ORDER BY emote_order";
		$rs = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($rs))
		{
			$template->assign_block_vars('emote', array(
				'ID'          => $row['emote_id'],
				'NAME'        => $row['emote_name'],
				'IMAGE'       => $row['emote_image'],
				'ORDER'       => $row['emote_order'],
				'U_MOVE_DOWN' => $this->p_master->u_action . "&amp;manage=emotes&amp;action=mdown&amp;emote_id={$row['emote_id']}&amp;order={$row['emote_order']}",
				'U_MOVE_UP'   => $this->p_master->u_action . "&amp;manage=emotes&amp;action=mup&amp;emote_id={$row['emote_id']}&amp;order={$row['emote_order']}",
				'U_EDIT'      => $this->p_master->u_action . "&amp;manage=emotes&amp;action=edit&amp;emote_id={$row['emote_id']}",
				'U_DELETE'    => $this->p_master->u_action . "&amp;manage=emotes&amp;action=delete&amp;emote_id={$row['emote_id']}",
			));
		}
		$db->sql_freeresult($rs);

		$template->assign_vars(array(
			'S_ERROR'   => (sizeof($error)) ? true : false,
			'ERROR_MSG' => implode('<br />', $error),
		));
	}

	function emote_order(&$error, $action)
	{
		global $template, $user, $db;

		$emote_order = request_var('order', 0);
		$emote_id = request_var('emote_id', 0);

		$emote_neworder = $emote_order + (($action == 'mup') ? -1 : 1);

		$sql = "UPDATE " . SN_EMOTES_TABLE . " SET emote_order = {$emote_order} WHERE emote_order = {$emote_neworder}";
		$db->sql_query($sql);

		$sql = "UPDATE " . SN_EMOTES_TABLE . " SET emote_order = {$emote_neworder} WHERE emote_id = {$emote_id}";
		$db->sql_query($sql);
	}

	function emote_edit(&$error)
	{
		global $template, $user, $db, $phpbb_root_path;

		$emote_id = request_var('emote_id', 0);
		$emote_name = request_var('emote_name', '', true);
		$emote_image = request_Var('emote_image', '');

		$template->alter_block_array('sn_tabs', array(
			'SELECTED' => false
		), true, 'change');
		
		$template->assign_block_vars('sn_tabs', array(
			'HREF'     => $this->p_master->u_action . '&amp;manage=emotes&amp;action=edit&amp;emote_id=' . $emote_id,
			'NAME'     => ($emote_id == 0) ? $user->lang['SN_PROFILE_ADD_EMOTE'] : $user->lang['SN_PROFILE_EDIT_EMOTE'],
			'SELECTED' => true,
		));

		$submit = (request_var('submit', '', true) == '') ? false : true;

		if ($submit)
		{
			if (empty($emote_name))
			{
				$error[] = $user->lang['SN_PROFILE_MANAGE_EMOTES_EMPTY_NAME'];
			}
			else
			{
				$sql_ary = array(
					'emote_name'  => $emote_name,
					'emote_image' => $emote_image,
					'emote_order' => 0,
				);

				if ($emote_id == 0)
				{
					$sql = "SELECT MAX(emote_order) AS max
										FROM " . SN_EMOTES_TABLE;
					$rs = $db->sql_query($sql);
					$emote_order = $db->sql_fetchfield('max');
					$db->sql_freeresult($rs);

					$sql_ary['emote_order'] = $emote_order + 1;

					$sql = "INSERT INTO " . SN_EMOTES_TABLE . " " . $db->sql_build_array('INSERT', $sql_ary);
					$message = $user->lang['SN_PROFILE_EMOTE_ADDED'];
				}
				else
				{
					$sql = "SELECT emote_order
										FROM " . SN_EMOTES_TABLE . "
											WHERE emote_id = {$emote_id}";
					$db->sql_query($sql);
					$emote_order = $db->sql_fetchfield('emote_order');
					
					$sql_ary['emote_order'] = $emote_order;
			
					$sql = "UPDATE " . SN_EMOTES_TABLE . "
										SET " . $db->sql_build_array('UPDATE', $sql_ary) . "
											WHERE emote_id = {$emote_id}";
					$message = $user->lang['SN_PROFILE_EMOTE_EDITED'];
				}

				$db->sql_query($sql);

				trigger_error($message . adm_back_link($this->p_master->u_action . '&amp;manage=emotes'));
			}
		}

		if ($emote_id != 0)
		{
			$sql = "SELECT emote_name, emote_image
								FROM " . SN_EMOTES_TABLE . "
									WHERE emote_id = {$emote_id}";
			$rs = $db->sql_query($sql);
			$row = $db->sql_fetchrow($rs);
			$db->sql_freeresult($rs);

			$emote_name = $row['emote_name'];
			$emote_image = $row['emote_image'];
		}

		$emotes_path = $phpbb_root_path . SN_UP_EMOTE_FOLDER;
		$emotes_dir = opendir($emotes_path);

		while ($emote = readdir($emotes_dir))
		{
			if ($emote == '.' || $emote == '..' || !preg_match('/\.(jpg|png|jpeg|gif)$/si', $emote))
			{
				continue;
			}
			$emote_parts = pathinfo($emote);

			$template->assign_block_vars('emote_image', array(
				'PATH'     => $emote_parts['basename'],
				'NAME'     => $emote_parts['basename'],
				'SELECTED' => $emote_parts['basename'] == $emote_image ? true : false,
			));
		}

		$template->assign_vars(array(
			'EMOTE_ID'    => $emote_id,
			'EMOTE_NAME'  => $emote_name,
			'EMOTE_IMAGE' => $emote_image,
		));
	}

	function emote_delete($error)
	{
		global $db, $user;
		$emote_id = request_var('emote_id', 0);

		if (!confirm_box(true))
		{
			confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
				'mode'     => 'module_profile',
				'manage'   => 'emotes',
				'submit'   => true,
				'action'   => 'delete',
				'emote_id' => $emote_id,
			)));
		}

		$sql = "DELETE FROM " . SN_EMOTES_TABLE . " WHERE emote_id = {$emote_id}";
		$db->sql_query($sql);

		trigger_error($user->lang['SN_PROFILE_EMOTE_DELETED'] . adm_back_link($this->p_master->u_action . '&amp;manage=emotes'));
	}
}

?>