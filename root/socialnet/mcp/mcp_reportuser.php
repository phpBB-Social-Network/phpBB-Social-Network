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

class mcp_reportuser
{
	var $p_master = null;

	function mcp_reportuser(&$p_master)
	{
		$this->p_master =& $p_master;
	}

	function main($id, $module)
	{
		global $db, $user, $template, $config, $action;
		
		switch ($action)
		{
			case 'close':
			case 'delete':
			
				$report_id_list = request_var('report_id_list', array(0));

				if (!sizeof($report_id_list))
				{
					trigger_error('NO_REPORT_SELECTED');
				}

				$this->close_user_report($report_id_list, $action);

			break;
		}

		$this->p_master->tpl_name = 'socialnet/mcp_reportuser';
		
		$start = request_var('start', 0);   
		
		$sql = 'SELECT r.report_id, rr.reason_text, r.report_text, r.user_id, u.username, u.user_colour, r.reporter, ru.username as reporter_name, ru.user_colour as reporter_colour
              FROM ' . SN_REPORTS_TABLE . ' r,
                   ' . USERS_TABLE . ' u,
                   ' . USERS_TABLE . ' ru,
                   ' . SN_REPORTS_REASONS_TABLE . ' rr
        			WHERE r.report_closed = 0
        				AND u.user_id = r.user_id
        				AND ru.user_id = r.reporter
        				AND rr.reason_id = r.reason_id
        			ORDER BY r.report_id DESC';
		$result = $db->sql_query_limit($sql, $config['topics_per_page'], $start);

		while ($row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('report', array(
				'USER_FULL'         => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'REPORTER_FULL'     => get_username_string('full', $row['reporter'], $row['reporter_name'], $row['reporter_colour']),
				'REPORT_ID'         => $row['report_id'],
				'REASON'            => $row['reason_text'],
				'REPORT_TEXT'       => $row['report_text'],
      ));
		}
		$db->sql_freeresult($result);
		
		$sql = 'SELECT COUNT(report_id) as total_reports
              FROM ' . SN_REPORTS_TABLE . '
                WHERE report_closed = 0';
		$result = $db->sql_query($sql);
  	$total_reports = $db->sql_fetchfield('total_reports');
  	$db->sql_freeresult($result);

		$template->assign_vars(array(
			'S_MCP_ACTION'     => $this->p_master->u_action,
			'PAGINATION'       => generate_pagination($this->p_master->u_action, $total_reports, $config['topics_per_page'], $start),
			'PAGE_NUMBER'      => on_page($total_reports, $config['topics_per_page'], $start),
			'TOTAL_REPORTS'    => ($total_reports == 1) ? $user->lang['LIST_REPORT'] : sprintf($user->lang['LIST_REPORTS'], $total_reports),
		));
	}     
	
	/**
  * Close or delete a report
  */
  function close_user_report($report_id_list, $action)
  {
  	global $db, $user, $phpEx, $phpbb_root_path;
  
    if ( !sizeof($report_id_list) )
    {
      trigger_error('SN_UP_NO_REPORT_SELECTED');  
    }
  
  	$s_hidden_fields = build_hidden_fields(array(
  		'report_id_list'	=> $report_id_list,
  		'action'			=> $action,
    ));
  
  	if (confirm_box(true))
  	{
  		if ($action == 'close')
  		{
  			$sql = 'UPDATE ' . SN_REPORTS_TABLE . '
  				SET report_closed = 1
  				WHERE ' . $db->sql_in_set('report_id', $report_id_list);
  		}
  		else
  		{
  			$sql = 'DELETE FROM ' . SN_REPORTS_TABLE . '
  				WHERE ' . $db->sql_in_set('report_id', $report_id_list);
  		}
  		$db->sql_query($sql);
  	}
  	else
  	{
  		confirm_box(false, $user->lang['SN_UP_'.strtoupper($action) . "_REPORT" . ((sizeof($report_id_list) == 1) ? '' : 'S') . '_CONFIRM'], $s_hidden_fields);
  	}
  	
  	$redirect = append_sid($this->p_master->u_action);
  
  	meta_refresh(3, $redirect);
  
  	$message = $user->lang['SN_UP_'.strtoupper($action).'_REPORT'.((sizeof($report_id_list) == 1) ? '' : 'S').'_SUCCESS'] . '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . $redirect . '">', '</a>');
    trigger_error($message);
  }
}

?>