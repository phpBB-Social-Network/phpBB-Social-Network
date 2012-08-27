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


class ucp_im
{
	var $p_master = null;

	function ucp_im(&$p_master)
	{
		$this->p_master =& $p_master;
	}

	function main($id, $module)
	{
		global $template, $config, $user, $db, $socialnet;
		$display_vars = array();

		switch ($module)
		{
			case 'default':

				$display_vars = array(
					'title'	 => 'ACP_IM_SETTINGS',
					'vars'	 => array(
						'legend1'			 => 'UCP_SN_IM_SETTINGS',
						'user_im_online'	 => array('lang' => 'IM_ONLINE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'user_im_sound'		 => array('lang' => 'IM_ALLOW_SOUND', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'user_im_soundname'	 => array('lang' => 'IM_SOUND_SELECT_NAME', 'validate' => 'string', 'type' => 'custom', 'function' => array($this, '_soundSelect'), 'explain' => true),
					)
				);

				$this->p_master->_settings($id, 'sn_im', $display_vars);

				$template->assign_vars(array(
					'S_SN_IM_USER_SOUNDNAME' => $this->p_master->new_config['user_im_soundname'],
					'SN_IM_ONLINE'			 => $this->p_master->new_config['user_im_online'],
				));

				break;

			case 'history':

				$this->p_master->tpl_name = 'socialnet/ucp_im_history';

				$u_id = request_var('u', 0);

				if (!$u_id)
				{
					$start = request_var('start', 0);
					$limit = request_var('limit', 15);

					// select conversations
					$sql = "SELECT DISTINCT u.username, u.user_id, u.user_colour, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height, MAX(im.sent) AS sent
										FROM " . SN_IM_TABLE . " AS im, " . USERS_TABLE . " AS u
											WHERE (im.uid_from = u.user_id OR im.uid_to = u.user_id )
												AND (im.uid_from = {$user->data['user_id']} OR im.uid_to = {$user->data['user_id']} )
												AND u.user_id <> {$user->data['user_id']}
										GROUP BY u.username, u.user_id, u.user_colour, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height
										ORDER BY MAX(im.sent) DESC, u.username";
					$rs = $db->sql_query($sql);
					$rows = $db->sql_fetchrowset($rs);

					$users_total = count($rows);

					$messages = array();
					$conversations = array();

					for ($i = 0; $i < $users_total && isset($rows[$i]); $i++)
					{
						$messages[] = "( (im.uid_from = {$user->data['user_id']} OR im.uid_from = {$rows[$i]['user_id']})
										AND (im.uid_to = {$user->data['user_id']} OR im.uid_to = {$rows[$i]['user_id']})
										AND im.sent = {$rows[$i]['sent']} )";
						$conversations[$rows[$i]['user_id']] = $rows[$i];
					}

					if ($users_total == 0)
					{
						$messages[] = '1 = 0';
					}

					$sql = "SELECT im.uid_from, im.uid_to, im.message, im.bbcode_uid, im.bbcode_bitfield
										FROM " . SN_IM_TABLE . " AS im
											WHERE " . implode(' OR ', $messages) . "
										ORDER BY im.sent DESC";
					$rs = $db->sql_query_limit($sql, $limit, $start);

					while ($row = $db->sql_fetchrow($rs))
					{
						$direction = ($row['uid_from'] == $user->data['user_id']) ? 'sn-im-from' : 'sn-im-to';
						$usr = ($row['uid_from'] == $user->data['user_id']) ? $row['uid_to'] : $row['uid_from'];

						$row = array_merge($row, $conversations[$usr]);

						$trim_message = $socialnet->trim_text_withsmilies($row['message'], $row['bbcode_uid'], 300, 0, array(' ', "\n"), '...', $row['bbcode_bitfield']);

						$template->assign_block_vars('users', array(
							'U_USERNAME' => $socialnet->get_username_string($config['im_colour_username'], 'no_profile', $row['user_id'], $row['username'], $row['user_colour']),
							'AVATAR'	 => $socialnet->get_user_avatar_resized($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height'], 50),
							'TIME'		 => $socialnet->time_ago($row['sent']),
							'MSSG'		 => generate_text_for_display($trim_message, $row['bbcode_uid'], $row['bbcode_bitfield'], $socialnet->bbCodeFlags),
							'U_HISTORY'	 => append_sid($this->p_master->u_action, 'u=' . $row['user_id']),
							'S_FROM_ME'	 => ($direction == 'sn-im-from') ? true : false,
						));
					}
					$db->sql_freeresult($rs);

					$pagination_url = append_sid($this->p_master->u_action);

					$template->assign_vars(array(
						'PAGINATION'	 => generate_pagination($pagination_url, $users_total, $limit, $start),
						'PAGE_NUMBER'	 => on_page($users_total, $limit, $start),
						'USERS_TOTAL'	 => ($users_total == 1) ? $user->lang['IM_CONVERSATION_TOTAL'] : sprintf($user->lang['IM_CONVERSATIONS_TOTAL'], $users_total),
					));
				}
				else
				{
					// Export history to .txt file
					$export = (isset($_POST['export'])) ? true : false;

					if ($export)
					{
						$sql = "SELECT uid_from, message, bbcode_uid, sent
  										FROM " . SN_IM_TABLE . "
    						        WHERE uid_from IN ({$user->data['user_id']}, {$u_id})
                          AND uid_to IN ({$user->data['user_id']}, {$u_id})
    						        ORDER BY sent ASC";
						$rs = $db->sql_query($sql);
						$output = '';

						$previous_sender = 0;
						while ($row = $db->sql_fetchrow($rs))
						{
							if ($previous_sender != $row['uid_from'])
							{
								$username = ($row['uid_from'] == $user->data['user_id']) ? $user->data['username'] : $history_username;
								$time = $user->format_date($row['sent']);
								strip_bbcode($row['message'], $row['bbcode_uid']);
								$message = str_replace("<br />", "\n", $row['message']);

								$line = $username . " » " . $time . "\n" . $message . "\n";
							}
							else
							{
								strip_bbcode($row['message'], $row['bbcode_uid']);
								$message = str_replace("<br />", "\n", $row['message']);

								$line = $message . "\n";
							}

							$output .= $line;

							$previous_sender = $row['uid_from'];
						}
						$db->sql_freeresult($rs);

						$output = pack('CCC', 239, 187, 191) . $output;
						Header('Pragma: no-cache');
						Header('Cache-control: no-cache');
						Header('Expires: ' . gmdate("D, d m Y H:i:s") . ' GMT');
						header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

						header('Content-Description: File Transfer');
						header('Content-Type: text/txt; charset="UTF-8"');
						header("Content-length: " . strlen($output));
						header('Content-Disposition: attachment; filename="' . $history_username . '_im_history.txt"');

						exit($output);
					}

					$error = array();

					$pagination_url = append_sid($this->p_master->u_action, 'u=' . $u_id . '&amp;pg=yes');

					$sql = "SELECT COUNT(*) AS count
                    FROM " . SN_IM_TABLE . "
  						        WHERE uid_from IN ({$user->data['user_id']}, {$u_id})
                        AND uid_to IN ({$user->data['user_id']}, {$u_id})";
					$rs = $db->sql_query($sql);
					$history_total = $db->sql_fetchfield('count');

					$pg = request_var('pg', '');
					$limit = request_var('limit', 30);
					$start = request_var('start', empty($pg)?(int) floor($history_total/ $limit)*$limit:0);
					
					$sql = "SELECT u.username, u.user_colour, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height,
												 im.uid_from, im.message, im.bbcode_uid, im.bbcode_bitfield, im.sent
										FROM " . SN_IM_TABLE . " AS im,
								         " . USERS_TABLE . " AS u
  						        WHERE im.uid_from = u.user_id
												AND uid_from IN ({$user->data['user_id']}, {$u_id})
                        AND uid_to IN ({$user->data['user_id']}, {$u_id})
  						        ORDER BY sent ASC";
					$rs = $db->sql_query_limit($sql, $limit, $start);

					$previous_sender = 0;
					while ($row = $db->sql_fetchrow($rs))
					{
						if (!isset($history_username) && $row['uid_from'] != $user->data['user_id'])
						{
							$history_username = $row['username'];
						}
						$avatar = $socialnet->get_user_avatar_resized($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height'], 50);
						$b_no_avatar = stripos($avatar, 'socialnet/no_avatar') !== false ? true : false;

						$template->assign_block_vars('history', array(
							'U_PROFILE'		 => $socialnet->get_username_string($config['im_colour_username'], 'profile', $row['uid_from'], $row['username'], $row['user_colour']),
							'U_USERNAME'	 => $socialnet->get_username_string($config['im_colour_username'], 'full', $row['uid_from'], $row['username'], $row['user_colour']),
							'AVATAR'		 => $avatar,
							'B_NO_AVATAR'	 => $b_no_avatar,
							'TIME'			 => $socialnet->time_ago($row['sent']),
							'MSSG'			 => generate_text_for_display($row['message'], $row['bbcode_uid'], $row['bbcode_bitfield'], $socialnet->bbCodeFlags),
							'S_UID_SAME'	 => ($previous_sender == $row['uid_from']) ? true : false,
							'S_ME'			 => ($row['uid_from'] == $user->data['user_id']) ? true : false,
						));

						$previous_sender = $row['uid_from'];
					}
					$db->sql_freeresult($rs);

					if ($config['im_msg_purged_time'] != 0)
					{
						$error[] = sprintf($user->lang['IM_HISTORY_PURGED_AT'], $user->format_date($config['im_msg_purged_time']));
					}

					if (!isset($history_username))
					{
						$sql = "SELECT username
  										FROM " . USERS_TABLE . "
    						        WHERE user_id = " . $u_id;
						$rs = $db->sql_query($sql);
						$history_username = $db->sql_fetchfield('username');
					}

					$template->assign_vars(array(
						'ERROR'					 => implode('<br />', $error),
						'PAGINATION'			 => generate_pagination($pagination_url, $history_total, $limit, $start),
						'PAGE_NUMBER'			 => on_page($history_total, $limit, $start),
						'MSG_TOTAL'				 => ($history_total == 1) ? $user->lang['IM_MSG_TOTAL'] : sprintf($user->lang['IM_MSGS_TOTAL'], $history_total),
						'U_EXPORT_IM'			 => append_sid($this->p_master->u_action, 'u=' . $u_id),
						'HISTORY_USERNAME'		 => $history_username,
						'L_EXPORT_IM_HISTORY'	 => sprintf($user->lang['EXPORT_IM_HISTORY'], $history_username),
						'U_SN_IM_HISTORY'		 => append_sid($this->p_master->u_action),
					));

				}

				break;
		}

	}

	function _soundSelect($value, $key)
	{
		global $phpbb_root_path, $cache;

		$soundSelect_ary = $cache->get('_snImSoundSelect');

		if (empty($soundSelect_ary))
		{
			foreach (glob("{$phpbb_root_path}socialnet/styles/sound/*.mp3") as $filename)
			{
				$sound = basename($filename);
				$soundSelect_ary[$sound] = str_replace('-', ' ', str_replace('_', '::', substr($sound, 0, -4)));
			}

			ksort($soundSelect_ary);
			$cache->put('_snImSoundSelect', $soundSelect_ary);
		}

		$soundSelect = '<select name="config[' . $key . ']" id="' . $key . '">';
		if (!empty($soundSelect_ary))
		{
			foreach ($soundSelect_ary as $idx => $soundName)
			{
				$soundSelect .= '<option value="' . $idx . '"' . ($value == $idx ? ' selected="selected"' : '') . '>' . $soundName . '</option>';
			}
		}
		$soundSelect .= '</select>';

		$soundSelect .= '<br /><br />';

		$soundSelect .= '<div id="snImSoundTest"><object id="snTest" type="application/x-shockwave-flash" data="' . $phpbb_root_path . 'socialnet/styles/sound/player_mp3_maxi.swf" width="200" height="20">
  <param name="movie" value="' . $phpbb_root_path . 'socialnet/styles/sound/player_mp3_maxi.swf" />
  <param name="autoload" value="1" />
  <param name="FlashVars" value="mp3=' . $phpbb_root_path . 'socialnet/styles/sound/' . $value . '" />
</object></div>
<script type="text/javascript">
  jQuery(document).ready(function($){
    $("#' . $key . '").change(function(){
		  var mp3 = "mp3=' . $phpbb_root_path . 'socialnet/styles/sound/"+$(this).children("option:selected").val();
		  if ($.browser.msie) {
			 $(\'#snImSoundTest\').html(\'<object height="20" width="200" type="application/x-shockwave-flash" data="' . $phpbb_root_path . 'socialnet/styles/sound/player_mp3_maxi.swf"><param name="movie" value="' . $phpbb_root_path . 'socialnet/styles/sound/player_mp3_maxi.swf"><param name="FlashVars" value="\'+mp3+\'"></object>\');
		  } else {
			 $(\'#snImSoundTest\').html(\'<embed src="' . $phpbb_root_path . 'socialnet/styles/sound/player_mp3_maxi.swf" width="200" height="20" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" FlashVars="\'+mp3+\'"></embed>\');
		  }
    });
  });
</script>';

		return $soundSelect;
	}
}

?>