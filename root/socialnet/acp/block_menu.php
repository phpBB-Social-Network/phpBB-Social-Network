<?php
/**
 *
 * @package phpBB Social Network
 * @version 0.7.0
 * @copyright (c) phpBB Social Network Team 2010-2012 http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

class acp_socialnet_block_menu
{
	var $tpl_name = '';

	function acp_socialnet_block_menu(&$p_master)
	{
		$this->p_master =& $p_master;
		$this->tpl_name = 'acp_socialnet_block_menu';
	}

	function main($id)
	{
		global $db, $template, $phpbb_root_path, $phpEx, $user;

		$action = request_var('action', '');
		$parent_id = request_var('parent_id', 0);
		$button_id = request_var('button_id', 0);

		switch ($action)
		{
			case "delete":

				if (confirm_box(true))
				{
					$sql = 'SELECT button_id
                    FROM ' . SN_MENU_TABLE . '
                      WHERE parent_id = ' . $button_id;
					$result = $db->sql_query($sql);

					while ($row = $db->sql_fetchrow($result))
					{
						$sql = 'DELETE FROM ' . SN_MENU_TABLE . '
						 				  WHERE button_id = ' . $row['button_id'];
						$db->sql_query($sql);
					}
					$db->sql_freeresult($result);

					$sql = "SELECT button_name
										FROM " . SN_MENU_TABLE . "
											WHERE button_id = {$button_id}";
					$rs = $db->sql_query($sql);
					$button_name = $db->sql_fetchfield($rs);
					$db->sql_freeresult($rs);

					$sql = 'DELETE FROM ' . SN_MENU_TABLE . '
					 				  WHERE button_id = ' . $button_id;
					$db->sql_query($sql);

					add_log('admin', 'LOG_CONFIG_SN_BLOCK_MENU_' . strtoupper($action), 'aaa' . $button_name);
					redirect($this->p_master->u_action . '&amp;block=block_menu&amp;parent_id=' . $parent_id);
				}
				else
				{
					$sql = 'SELECT button_id
                    FROM ' . SN_MENU_TABLE . '
                      WHERE parent_id = ' . $button_id;
					$result = $db->sql_query($sql);

					($db->sql_affectedrows()) ? confirm_box(false, $user->lang['BLOCK_MENU_DELETE_SUBBUTTONS_CONFIRM']) : confirm_box(false, $user->lang['BLOCK_MENU_DELETE_BUTTON_CONFIRM']);

					redirect($this->p_master->u_action . '&amp;block=block_menu&amp;parent_id=' . $parent_id);
				}

			break;

			case "add_button":

				$button_name = request_var('button_name', '', true);

				$template->assign_vars(array(
					'S_NAME'				 => $button_name,
					'S_MENU_CREATE_BUTTON'	 => true,
					'S_PARENT_ID'			 => $parent_id,
				));

				// Load buttons for select
				$sql = 'SELECT button_name, button_id
                  FROM ' . SN_MENU_TABLE . '
                    WHERE parent_id = 0
                      ORDER BY left_id';
				$result = $db->sql_query($sql);

				while ($row = $db->sql_fetchrow($result))
				{
					$template->assign_block_vars('parents', array(
						'ID'	 => $row['button_id'],
						'NAME'	 => $row['button_name'],
					));
				}
				$db->sql_freeresult($result);

				$submit = (isset($_POST['submit'])) ? true : false;

				if ($submit)
				{
					$button_url = request_var('button_url', '', true);
					$button_name = request_var('button_name', '', true);
					$button_parent = request_var('button_parent', 0);
					$button_external = request_var('button_external', 0);
					$button_display = request_var('button_display', 1);
					$button_only_registered = request_var('button_only_registered', 0);
					$button_only_guest = request_var('button_only_guest', 0);

					$sql = 'SELECT MAX(right_id) AS right_id
										FROM ' . SN_MENU_TABLE;
					$result = $db->sql_query($sql);
					$row = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);

					$left_id = $row['right_id'] + 1;
					$right_id = $row['right_id'] + 2;

					$sql = 'INSERT INTO ' . SN_MENU_TABLE . ' (button_url, button_name, button_external, button_display, button_only_registered, button_only_guest, left_id, right_id, parent_id)
                    VALUES ("' . $button_url . '", "' . $button_name . '", ' . $button_external . ', ' . $button_display . ',
                            ' . $button_only_registered . ', ' . $button_only_guest . ', ' . $left_id . ', ' . $right_id . ', ' . $button_parent . ')';
					$db->sql_query($sql);
					add_log('admin', 'LOG_CONFIG_SN_BLOCK_MENU_' . strtoupper($action), $button_name);
					trigger_error($user->lang['BLOCK_MENU_BUTTON_ADDED'] . adm_back_link($this->p_master->u_action . '&amp;block=block_menu&amp;parent_id=' . $button_parent));
				}

			break;

			case "edit_button":

				// Load buttons for select
				$sql = 'SELECT button_name, button_id
                  FROM ' . SN_MENU_TABLE . '
                    WHERE parent_id = 0
                      AND button_id <> ' . $button_id . '
                      ORDER BY left_id';
				$result = $db->sql_query($sql);

				while ($row = $db->sql_fetchrow($result))
				{
					$template->assign_block_vars('parents', array(
						'ID'	 => $row['button_id'],
						'NAME'	 => $row['button_name'],
					));
				}
				$db->sql_freeresult($result);

				$sql = 'SELECT *
                  FROM ' . SN_MENU_TABLE . '
                    WHERE button_id = ' . $button_id;
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);

				$template->assign_vars(array(
					'S_URL'				 => $row['button_url'],
					'S_EXTERNAL'		 => $row['button_external'],
					'S_NAME'			 => $row['button_name'],
					'S_PARENT'			 => $row['parent_id'],
					'S_DISPLAY'			 => $row['button_display'],
					'S_ONLY_REGISTERED'	 => $row['button_only_registered'],
					'S_ONLY_GUEST'		 => $row['button_only_guest'],
					'S_MENU_EDIT_BUTTON' => true,
					'S_PARENT_ID'		 => $parent_id,
				));
				$db->sql_freeresult($result);

				$submit = (isset($_POST['submit'])) ? true : false;

				if ($submit)
				{
					$button_url = request_var('button_url', '', true);
					$button_name = request_var('button_name', '', true);
					$button_parent = request_var('button_parent', 0);
					$button_external = request_var('button_external', 0);
					$button_display = request_var('button_display', 1);
					$button_only_registered = request_var('button_only_registered', 0);
					$button_only_guest = request_var('button_only_guest', 0);

					if ($button_parent && !$row['parent_id'])
					{
						$sql = 'SELECT button_id
                    FROM ' . SN_MENU_TABLE . '
                      WHERE parent_id = ' . $button_id;
						$result = $db->sql_query($sql);

						if ($db->sql_affectedrows())
						{
							trigger_error($user->lang['BLOCK_MENU_MOVE_BUTTON_WITH_SUBS'] . adm_back_link($this->p_master->u_action . '&amp;block=block_menu&amp;parent_id=' . $parent_id), E_USER_WARNING);
						}
					}

					$sql = 'UPDATE ' . SN_MENU_TABLE . '
                    SET button_url = "' . $button_url . '", button_name = "' . $button_name . '", button_external = ' . $button_external . ',
                        button_display = ' . $button_display . ', button_only_registered = ' . $button_only_registered . ',
                        button_only_guest = ' . $button_only_guest . ', parent_id = ' . $button_parent . '
                      WHERE button_id = ' . $button_id;
					$db->sql_query($sql);
					add_log('admin', 'LOG_CONFIG_SN_BLOCK_MENU_' . strtoupper($action), $button_name);
					trigger_error($user->lang['BLOCK_MENU_BUTTON_EDITED'] . adm_back_link($this->p_master->u_action . '&amp;block=block_menu&amp;parent_id=' . $button_parent));
				}

			break;

			case 'move_up':
			case 'move_down':

				$sql = 'SELECT left_id, right_id, button_name
                  FROM ' . SN_MENU_TABLE . '
                    WHERE button_id = ' . $button_id;
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);

				$button_moved_by = acp_move_button($row, $action);

				add_log('admin', 'LOG_CONFIG_SN_BLOCK_MENU_' . strtoupper($action), $row['button_name'], $button_moved_by);
				redirect($this->p_master->u_action . '&amp;block=block_menu&amp;parent_id=' . $parent_id);

			break;

			default:

				$sql = 'SELECT *
                  FROM ' . SN_MENU_TABLE . '
                    WHERE parent_id = ' . $parent_id . '
                      ORDER BY left_id';
				$result = $db->sql_query($sql);

				while ($row = $db->sql_fetchrow($result))
				{
					$template->assign_block_vars('buttons', array(
						'ID'			 => $row['button_id'],
						'NAME'			 => $row['button_name'],
						'URL'			 => $row['button_url'],
						'U_OPEN'		 => ($row['parent_id'] == 0) ? $this->p_master->u_action . '&amp;block=block_menu&amp;action=&amp;parent_id=' . $row['button_id'] : $this->p_master->u_action . '&amp;block=block_menu&amp;action=&amp;parent_id=' . $row['parent_id'] . '&amp;button_id=' . $row['button_id'],
						'U_DELETE'		 => ($row['parent_id'] == 0) ? $this->p_master->u_action . '&amp;block=block_menu&amp;action=delete&amp;parent_id=0&amp;button_id=' . $row['button_id'] : $this->p_master->u_action . '&amp;block=block_menu&amp;action=delete&amp;parent_id=' . $row['parent_id'] . '&amp;button_id=' . $row['button_id'],
						'U_EDIT'		 => ($row['parent_id'] == 0) ? $this->p_master->u_action . '&amp;block=block_menu&amp;action=edit_button&amp;parent_id=0&amp;button_id=' . $row['button_id'] : $this->p_master->u_action . '&amp;block=block_menu&amp;action=edit_button&amp;parent_id=' . $row['parent_id'] . '&amp;button_id=' . $row['button_id'],
						'U_MOVE_UP'		 => ($row['parent_id'] == 0) ? $this->p_master->u_action . '&amp;block=block_menu&amp;action=move_up&amp;parent_id=0&amp;button_id=' . $row['button_id'] : $this->p_master->u_action . '&amp;block=block_menu&amp;action=move_up&amp;parent_id=' . $row['parent_id'] . '&amp;button_id=' . $row['button_id'],
						'U_MOVE_DOWN'	 => ($row['parent_id'] == 0) ? $this->p_master->u_action . '&amp;block=block_menu&amp;action=move_down&amp;parent_id=0&amp;button_id=' . $row['button_id'] : $this->p_master->u_action . '&amp;block=block_menu&amp;action=move_down&amp;parent_id=' . $row['parent_id'] . '&amp;button_id=' . $row['button_id'],
					));
				}
				$db->sql_freeresult($result);

				$submit = (isset($_POST['submit'])) ? true : false;

				if ($submit)
				{
					$button_name = request_var('button_name', '', true);
					redirect($this->p_master->u_action . '&amp;block=block_menu&amp;action=add_button&amp;parent_id=' . $parent_id . '&amp;button_name=' . urlencode($button_name));
				}

				$button_nav = $user->lang['BLOCK_MENU_NAV'];

				if ($parent_id)
				{

					$sql = 'SELECT button_name
                  FROM ' . SN_MENU_TABLE . '
                    WHERE button_id = ' . $parent_id;
					$result = $db->sql_query($sql);

					$button_nav = '<a href="'.$this->p_master->u_action . '&amp;block=block_menu">' . $button_nav . '</a> &raquo; ' . $db->sql_fetchfield('button_name');
				}

				$template->assign_vars(array(
					'S_MENU_BUTTONS_LIST'	 => true,
					'S_BUTTONS_NAV'			 => $button_nav,
					'S_PARENT_ID'			 => $parent_id,
				));
		}
	}
}

function acp_move_button($button_row, $action = 'move_up')
{
	global $db;

	$sql_extend = ($action == 'move_up') ? "right_id < {$button_row['right_id']} ORDER BY right_id DESC" : "left_id > {$button_row['left_id']} ORDER BY left_id ASC";

	$sql = 'SELECT *
						FROM ' . SN_MENU_TABLE . '
						  WHERE ' . $sql_extend;
	$result = $db->sql_query_limit($sql, 1);

	$target = array();
	while ($row = $db->sql_fetchrow($result))
	{
		$target = $row;
	}
	$db->sql_freeresult($result);

	if (!sizeof($target))
	{
		// The button is already on top or bottom
		return false;
	}

	/**
	 * $left_id and $right_id define the scope of the nodes that are affected by the move.
	 * $diff_up and $diff_down are the values to substract or add to each node's left_id
	 * and right_id in order to move them up or down.
	 * $move_up_left and $move_up_right define the scope of the nodes that are moving
	 * up. Other nodes in the scope of ($left_id, $right_id) are considered to move down.
	 */
	if ($action == 'move_up')
	{
		$left_id = $target['left_id'];
		$right_id = $button_row['right_id'];

		$diff_up = $button_row['left_id'] - $target['left_id'];
		$diff_down = $button_row['right_id'] + 1 - $button_row['left_id'];

		$move_up_left = $button_row['left_id'];
		$move_up_right = $button_row['right_id'];

	}
	else
	{
		$left_id = $button_row['left_id'];
		$right_id = $target['right_id'];

		$diff_up = $button_row['right_id'] + 1 - $button_row['left_id'];
		$diff_down = $target['right_id'] - $button_row['right_id'];

		$move_up_left = $button_row['right_id'] + 1;
		$move_up_right = $target['right_id'];
	}

	$sql = 'UPDATE ' . SN_MENU_TABLE . "
						SET left_id = left_id + CASE
							WHEN left_id BETWEEN {$move_up_left} AND {$move_up_right} THEN -{$diff_up}
							ELSE {$diff_down}
						END,
						right_id = right_id + CASE
							WHEN right_id BETWEEN {$move_up_left} AND {$move_up_right} THEN -{$diff_up}
							ELSE {$diff_down}
						END
						WHERE
							left_id BETWEEN {$left_id} AND {$right_id}
							AND right_id BETWEEN {$left_id} AND {$right_id}";
	$db->sql_query($sql);

	return $target['button_name'];
}

?>