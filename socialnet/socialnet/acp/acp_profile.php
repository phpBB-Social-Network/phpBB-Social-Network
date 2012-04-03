<?php
/**
*
* @package phpBB Social Network
* @version 0.6.3
* @copyright (c) 2010-2012 Kamahl & Culprit http://phpbbsocialnetwork.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('SOCIALNET_INSTALLED') && !defined('IN_PHPBB'))
{
	return;
}

/**
 * Admin class fro module User Profile for Social Network
 * @package User Profile
 */
class acp_profile extends socialnet
{
	var $p_master = null;

	function acp_profile(&$p_master)
	{
		$this->p_master =& $p_master;
	}

	function main($id)
	{
	 global $db, $template, $user;
	 
		$display_vars = array(
			'title'	 => 'ACP_UP_SETTINGS',
			'vars'	 => array(
				'legend1'	 => 'ACP_SN_PROFILE_SETTINGS',
				'up_enable_report'		 => array('lang' => 'SN_ENABLE_REPORT', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				//'up_enable_subscriptions'		 => array('lang' => 'SN_ENABLE_SUBSCRIPTIONS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'mp_max_profile_value'		 => array('lang' => 'SN_MAX_PROFILE_VALUE', 'validate' => 'int:0:255', 'type' => 'text:3:4', 'explain' => true),
				'up_alert_relation_pm'	 => array('lang' => 'SN_PROFILE_ALERT_RELATION_BY_PM', 'validate' => 'bool', 'type' => 'radio:yes:no', 'explain' => true),
		  )
		);

    $this->p_master->_settings($id, 'sn_up', $display_vars);    
    
    $action	= request_var('action', '');
    $reason_id	= request_var('reason_id', 0);
    
    switch($action)                   
    {
		  case "delete_reason":
			
				if (confirm_box(true))
				{
   				$sql = 'DELETE FROM ' . SN_REPORTS_REASONS_TABLE . '
					 				  WHERE reason_id = ' . $reason_id;
					$db->sql_query($sql);
					
					trigger_error($user->lang['SN_PROFILE_REASON_DELETED'] . adm_back_link($this->p_master->u_action));
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
        
        while ($row = $db->sql_fetchrow($result))
        {
          $template->assign_block_vars('reason', array(
            'TEXT'        => $row['reason_text'],
            'U_DELETE'    => $this->p_master->u_action . '&amp;action=delete_reason&amp;reason_id=' . $row['reason_id'],
          ));
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
					
					trigger_error($user->lang['SN_PROFILE_REASON_ADDED'] . adm_back_link($this->p_master->u_action));
        }    
    }

	}
	
}

?>