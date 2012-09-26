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
	/**
	 * @ignore
	 */
	define('IN_PHPBB', true);
	/**
	 * @ignore
	 */
	define('SN_LOADER', 'profile');
	define('SN_PROFILE', true);
	$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
	$phpEx = substr(strrchr(__FILE__, '.'), 1);
	include_once($phpbb_root_path . 'common.' . $phpEx);
	include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);

	// Start session management
	$user->session_begin(false);
	$auth->acl($user->data);
	$user->setup('viewforum');
}

if (!class_exists('socialnet_profile'))
{
	class socialnet_profile
	{
		var $p_master = null;
		var $script_name = '';
		var $friends_entry = array();

		function socialnet_profile(&$p_master = null)
		{
			$this->p_master =& $p_master;
		}

		function init()
		{
			global $db, $template, $user, $config, $auth, $phpEx, $phpbb_root_path, $phpEx;

			$mode = request_var('mode', '', true);

			$user_id = request_var('u', '');
			$user_id = $user_id == '' ? $user->data['user_id'] : $user_id;

			if ($mode == 'viewprofile' && $this->p_master->script_name == 'memberlist')
			{
				unset($_GET['mode']);
				redirect(append_sid("{$phpbb_root_path}profile.$phpEx", http_build_query($_GET, '', '&amp;')));
			}

			$monthNamesShort = $monthNames = '';

			for ($i = 1; $i <= 12; $i++)
			{
				$nM = explode('|', date('F|M', mktime(1, 1, 1, $i, 1, 2011)));
				if ($monthNamesShort != '')
				{
					$monthNamesShort .= ",";
					$monthNames .= ',';
				}

				$monthNamesShort .= "'" . addslashes($user->lang['datetime'][$nM[1]]) . "'";
				$monthNames .= "'" . addslashes($user->lang['datetime'][$nM[0]]) . "'";
			}

			$sql = "SELECT emote_id, emote_name, emote_image
							FROM " . SN_EMOTES_TABLE . "
								ORDER BY emote_order";
			$rs = $db->sql_query($sql);
			$row_emotes = $db->sql_fetchrowset($rs);
			$db->sql_freeresult($rs);

			for ($i = 0; isset($row_emotes[$i]); $i++)
			{
				$template->assign_block_vars('sn_up_emote', array(
					'EMOTE_ID'		 => $row_emotes[$i]['emote_id'],
					'EMOTE_NAME'	 => $row_emotes[$i]['emote_name'],
					'EMOTE_IMAGE'	 => $row_emotes[$i]['emote_image'],
				));
			}

			$template_assign_vars = array(
				'S_OWN_PROFILE'				 => $user->data['user_id'] == $user_id,
				'SN_UP_MONTH_NAMES'			 => $monthNames,
				'SN_UP_MONTH_NAMES_SHORT'	 => $monthNamesShort,
				'S_SN_UP_EMOTES_ENABLED'	 => isset($this->p_master->config['up_emotes']) ? $this->p_master->config['up_emotes'] : 0,
				'SN_UP_EMOTE_FOLDER'		 => $phpbb_root_path . SN_UP_EMOTE_FOLDER,
			);

			$template->assign_vars($template_assign_vars);
		}

		function load($mode, $user_id)
		{
			global $socialnet_root_path, $phpbb_root_path, $phpEx, $socialnet, $template, $user, $auth;

			if ($mode != 'upEdit' && $mode != 'emote')
			{
				$call_mode = 'tab_' . $mode;
			}
			else
			{
				$call_mode = $mode;
			}

			$fullpage = request_var('fullPage', '');

			if ( method_exists($this, $call_mode) )
			{
				$user->add_lang('memberlist');
				$this->$call_mode($user_id);

				$template->assign_vars(array(
					'USER_ID'			 => $user_id,
					'FMS_LIMIT'			 => $this->p_master->config['fas_friendlist_limit'],
					'S_DISPLAY_SEARCH'	 => (!$this->p_master->config['load_search']) ? 0 : (isset($auth) ? ($auth->acl_get('u_search') && $auth->acl_getf_global('f_search')) : 1),
				));

				$template->set_filenames(array(
					"sn_{$call_mode}" 	 => "socialnet/user_profile_{$call_mode}.html",
				));

				if ( $fullpage == 'false')
				{
					$content = $this->p_master->get_page("sn_{$call_mode}");
					header('Content-type: text/html; charset=UTF-8');
					die($content);
				}
				else
				{
					page_header();

					meta_refresh(3,append_sid($phpbb_root_path . 'profile.' . $phpEx . '?u=' . $user_id));

					// $p_url = parse_url($_SERVER['PHP_SELF']); - I do not know why you added this

					$template->set_filenames(array(
						'body'=>'message_body.html'
					));

					$template->assign_vars(array(
						'MESSAGE_TITLE' => 'Oops',
						'MESSAGE_TEXT' => 'Do not open tabs using right click'
					));

					page_footer();
				}
			}

			header('Content-type: text/html; charset=UTF-8');
			print '<h3>Profile</h3>' . $mode . '<br />'; // btw - hardcoded language
			die(__FILE__ . ' ' . __LINE__);
		}

		function tab_info($user_id)
		{
			global $phpbb_root_path, $phpEx;
			global $template, $db, $config, $user, $auth;

			$sql = 'SELECT s.about_me, s.sex, s.interested_in, u.user_birthday, u.user_from, s.hometown, s.languages, u.user_sig, u.user_sig_bbcode_bitfield, u.user_sig_bbcode_uid,
                   s.employer, s.university, s.high_school, u.user_occ, s.religion, s.political_views, s.quotations, u.user_interests, s.music, s.books, s.movies, s.games, s.foods,
                   s.sports, s.sport_teams, s.activities, u.user_website, u.user_icq, u.user_aim, u.user_yim, u.user_msnm, u.user_jabber, u.user_email, u.user_allow_viewemail,
                   s.skype, s.facebook, s.twitter, s.youtube,
                   u.user_id, u.username, u.user_type, u.user_colour, u.user_inactive_reason, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height, u.user_allow_pm
              FROM ' . USERS_TABLE . ' u
                LEFT JOIN ' . SN_USERS_TABLE . ' s
                  ON u.user_id = s.user_id
                WHERE u.user_id = ' . $user_id;
			$result = $db->sql_query($sql);
			$member = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			$age = '';
			if ($config['allow_birthdays'] && $member['user_birthday'])
			{
				list($bday_day, $bday_month, $bday_year) = array_map('intval', explode('-', $member['user_birthday']));

				if ($bday_year)
				{
					$now = getdate(time() + $user->timezone + $user->dst - date('Z'));

					$diff = $now['mon'] - $bday_month;
					if ($diff == 0)
					{
						$diff = ($now['mday'] - $bday_day < 0) ? 1 : 0;
					}
					else
					{
						$diff = ($diff < 0) ? 1 : 0;
					}

					$age = (int) ($now['year'] - $bday_year - $diff);
				}
			}

			if ($member['interested_in'] == '1')
			{
				$interested_in = $user->lang['SN_UP_MALES'];
			}
			elseif ($member['interested_in'] == '2')
			{
				$interested_in = $user->lang['SN_UP_FEMALES'];
			}
			elseif ($member['interested_in'] == '3')
			{
				$interested_in = $user->lang['SN_UP_BOTH'];
			}
			else
			{
				$interested_in = '';
			}

			if ($member['user_birthday'])
			{
				$birthday_arr = array_map('intval', explode('-', $member['user_birthday']));
				if ($birthday_arr[0] && $birthday_arr[1] && $birthday_arr[2])
				{
					$birthday = $user->format_date(gmmktime(0, 0, -$user->timezone, (int) $birthday_arr[1], (int) $birthday_arr[0], (int) $birthday_arr[2]), '|j. F Y|');
				}
				elseif ($birthday_arr[0] && $birthday_arr[1])
				{
					$birthday = $user->format_date(gmmktime(0, 0, -$user->timezone, (int) $birthday_arr[1], (int) $birthday_arr[0], 2000), '|j. F|');
				}
				elseif ($birthday_arr[1] && $birthday_arr[2])
				{
					$birthday = $user->format_date(gmmktime(0, 0, -$user->timezone, (int) $birthday_arr[1], (int) $birthday_arr[0], (int) $birthday_arr[2]), '|F Y|');
				}
				elseif ($birthday_arr[2])
				{
					$birthday = $user->format_date(gmmktime(0, 0, -$user->timezone, 1, 1, (int) $birthday_arr[2]), '|Y|');
				}
				else
				{
					$birthday = '';
				}
			}

			$member['user_sig_bbcode'] = $member['user_sig'];
			decode_message($member['user_sig_bbcode'], $member['user_sig_bbcode_uid']);
			if ($member['user_sig'])
			{
				$member['user_sig'] = censor_text($member['user_sig']);

				if ($member['user_sig_bbcode_bitfield'])
				{
					include_once($phpbb_root_path . 'includes/bbcode.' . $phpEx);
					$bbcode = new bbcode();
					$bbcode->bbcode_second_pass($member['user_sig'], $member['user_sig_bbcode_uid'], $member['user_sig_bbcode_bitfield']);
				}

				$member['user_sig'] = bbcode_nl2br($member['user_sig']);
				$member['user_sig'] = smiley_text($member['user_sig']);
			}

			if ((!empty($member['user_allow_viewemail']) && $auth->acl_get('u_sendemail')) || $auth->acl_get('a_user'))
			{
				$email = ($config['board_email_form'] && $config['email_enable']) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=email&amp;u=' . $user_id) : (($config['board_hide_emails'] && !$auth->acl_get('a_user')) ? '' : 'mailto:' . $member['user_email']);
			}
			else
			{
				$email = '';
			}

			// Custom Profile Fields
			$profile_fields = array();
			if ($config['load_cpf_viewprofile'])
			{
				include_once($phpbb_root_path . 'includes/functions_profile_fields.' . $phpEx);
				$cp = new custom_profile();
				$profile_fields = $cp->generate_profile_fields_template('grab', $user_id);
				$profile_fields = (isset($profile_fields[$user_id])) ? $cp->generate_profile_fields_template('show', false, $profile_fields[$user_id]) : array();
			}

			$template->assign_vars(array(
				'ABOUT_ME'				 => ($member['about_me']) ? preg_replace("/((\r)?\n)/si", '<br />\2', $member['about_me']) : '',
				'SEX'					 => ((int) $member['sex'] != '0') ? (($member['sex'] == '1') ? $user->lang['SN_UP_MALE'] : $user->lang['SN_UP_FEMALE']) : '',
				'INTERESTED_IN'			 => $interested_in,
				'BIRTHDAY'				 => ($member['user_birthday']) ? $birthday : '',
				'AGE'					 => $age,
				'LOCATION'				 => ($member['user_from']) ? $member['user_from'] : '',
				'HOMETOWN'				 => ($member['hometown']) ? $member['hometown'] : '',
				'LANGUAGES'				 => ($member['languages']) ? $member['languages'] : '',
				'SIGNATURE'				 => ($member['user_sig']) ? $member['user_sig'] : '',
				'EMPLOYER'				 => ($member['employer']) ? $member['employer'] : '',
				'UNIVERSITY'			 => ($member['university']) ? $member['university'] : '',
				'HIGH_SCHOOL'			 => ($member['high_school']) ? $member['high_school'] : '',
				'OCCUPATION'			 => ($member['user_occ']) ? preg_replace("/((\r)?\n)/si", '<br />\2', $member['user_occ']) : '',
				'RELIGION'				 => ($member['religion']) ? $member['religion'] : '',
				'POLITICAL_VIEWS'		 => ($member['political_views']) ? $member['political_views'] : '',
				'QUOTATIONS'			 => ($member['quotations']) ? preg_replace("/((\r)?\n)/si", '<br />\2', $member['quotations']) : '',
				'INTERESTS'				 => ($member['user_interests']) ? preg_replace("/((\r)?\n)/si", '<br />\2', $member['user_interests']) : '',
				'MUSIC'					 => ($member['music']) ? preg_replace("/((\r)?\n)/si", '<br />\2', $member['music']) : '',
				'BOOKS'					 => ($member['books']) ? preg_replace("/((\r)?\n)/si", '<br />\2', $member['books']) : '',
				'MOVIES'				 => ($member['movies']) ? preg_replace("/((\r)?\n)/si", '<br />\2', $member['movies']) : '',
				'GAMES'					 => ($member['games']) ? preg_replace("/((\r)?\n)/si", '<br />\2', $member['games']) : '',
				'FOODS'					 => ($member['foods']) ? preg_replace("/((\r)?\n)/si", '<br />\2', $member['foods']) : '',
				'SPORTS'				 => ($member['sports']) ? preg_replace("/((\r)?\n)/si", '<br />\2', $member['sports']) : '',
				'SPORT_TEAMS'			 => ($member['sport_teams']) ? preg_replace("/((\r)?\n)/si", '<br />\2', $member['sport_teams']) : '',
				'ACTIVITIES'			 => ($member['activities']) ? preg_replace("/((\r)?\n)/si", '<br />\2', $member['activities']) : '',
				'S_JABBER_ENABLED'		 => ($config['jab_enable']) ? true : false,
				'U_EMAIL'				 => $email,
				'U_WWW'					 => ($member['user_website']) ? $member['user_website'] : '',
				'U_ICQ'					 => ($member['user_icq']) ? 'http://www.icq.com/people/webmsg.php?to=' . urlencode($member['user_icq']) : '',
				'U_AIM'					 => ($member['user_aim'] && $auth->acl_get('u_sendim')) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=contact&amp;action=aim&amp;u=' . $user_id) : '',
				'U_YIM'					 => ($member['user_yim']) ? 'http://edit.yahoo.com/config/send_webmesg?.target=' . urlencode($member['user_yim']) . '&amp;.src=pg' : '',
				'U_MSN'					 => ($member['user_msnm'] && $auth->acl_get('u_sendim')) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=contact&amp;action=msnm&amp;u=' . $user_id) : '',
				'U_JABBER'				 => ($member['user_jabber'] && $auth->acl_get('u_sendim')) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=contact&amp;action=jabber&amp;u=' . $user_id) : '',
				'U_FACEBOOK'			 => ($member['facebook']) ? $member['facebook'] : '',
				'U_TWITTER'				 => ($member['twitter']) ? $member['twitter'] : '',
				'U_YOUTUBE'				 => ($member['youtube']) ? $member['youtube'] : '',
				'S_CUSTOM_FIELDS'		 => (isset($profile_fields['row']) && sizeof($profile_fields['row'])) ? true : false,
				'UP_EDIT_SEX'			 => "1:'{$user->lang['SN_UP_MALE']}',2:'{$user->lang['SN_UP_FEMALE']}'",
				'UP_EDIT_INTERESTED_IN'	 => "1:'{$user->lang['SN_UP_MALES']}',2:'{$user->lang['SN_UP_FEMALES']}',3:'{$user->lang['SN_UP_BOTH']}'",
				'UP_EDIT_SIGNATURE'		 => preg_replace("/\r?\n/s", '\\n', $member['user_sig_bbcode']),
				'BB_UID'				 => $member['user_sig_bbcode_uid'],
				'BB_BITFIELD'			 => $member['user_sig_bbcode_bitfield'],
				'USER_WWW'				 => $member['user_website'],
				'USER_ICQ'				 => $member['user_icq'],
				'USER_AIM'				 => $member['user_aim'],
				'USER_YIM'				 => $member['user_yim'],
				'USER_MSN'				 => $member['user_msnm'],
				'USER_JABBER'			 => $member['user_jabber'],
				'USER_SKYPE'			 => $member['skype'],
				'USER_MSNM'				 => $member['user_msnm'],
			));

			if (!empty($profile_fields['row']))
			{
				$template->assign_vars($profile_fields['row']);
			}

			if (!empty($profile_fields['blockrow']))
			{
				foreach ($profile_fields['blockrow'] as $field_data)
				{
					$template->assign_block_vars('custom_fields', $field_data);
				}
			}
		}

		function tab_friends($user_id)
		{
			$this->p_master->fms_users(array_merge(array(
				'mode'				 => 'friendProfile',
				'slider'			 => false,
				'user_id'			 => $user_id,
				'limit'				 => 30,
				'add_friend_link'	 => true,
			), $this->p_master->fms_users_sqls('friendProfile', $user_id)));
		}

		function tab_stats($user_id)
		{
			global $phpbb_root_path, $phpEx;
			global $db, $template, $user, $auth, $config, $cache;

			$sql = 'SELECT u.user_posts, u.user_regdate, u.user_allow_viewonline, u.user_lastvisit, u.user_warnings, u.user_rank, s.profile_views, s.profile_last_change, u.group_id,
        					u.user_id, u.username, u.user_type, u.user_colour, u.user_inactive_reason, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height, u.user_allow_pm
								FROM ' . USERS_TABLE . ' u
									LEFT JOIN ' . SN_USERS_TABLE . ' s
										ON s.user_id = u.user_id
									WHERE u.user_id = ' . $user_id;
			$result = $db->sql_query($sql);
			$member = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			// If the user has m_approve permission or a_user permission, then list then display unapproved posts
			if ($auth->acl_getf_global('m_approve') || $auth->acl_get('a_user'))
			{
				$sql = 'SELECT COUNT(post_id) as posts_in_queue
			    				FROM ' . POSTS_TABLE . '
			    					WHERE poster_id = ' . $user_id . '
			    						AND post_approved = 0';
				$result = $db->sql_query($sql);
				$member['posts_in_queue'] = (int) $db->sql_fetchfield('posts_in_queue');
				$db->sql_freeresult($result);
			}
			else
			{
				$member['posts_in_queue'] = 0;
			}

			// Do the relevant calculations
			$memberdays = max(1, round((time() - $member['user_regdate']) / 86400));
			$posts_per_day = $member['user_posts'] / $memberdays;
			$percentage = ($config['num_posts']) ? min(100, ($member['user_posts'] / $config['num_posts']) * 100) : 0;

			if ($member['user_allow_viewonline'] || $auth->acl_get('u_viewonline'))
			{
				$last_visit = (!empty($member['session_time'])) ? $member['session_time'] : $member['user_lastvisit'];
			}
			else
			{
				$last_visit = '';
			}

			$user_notes_enabled = $warn_user_enabled = false;

			// Only check if the user is logged in
			if ($user->data['is_registered'])
			{
				if (!class_exists('p_master'))
				{
					include_once($phpbb_root_path . 'includes/functions_module.' . $phpEx);
				}
				$module = new p_master();

				$module->list_modules('ucp');
				$module->list_modules('mcp');

				$user_notes_enabled = ($module->loaded('notes', 'user_notes')) ? true : false;
				$warn_user_enabled = ($module->loaded('warn', 'warn_user')) ? true : false;

				unset($module);
			}

			if ($config['load_user_activity'])
			{
				display_user_activity($member);
			}

			// Load profile visitors
			$sql = 'SELECT v.visitor_uid, u.username, u.user_colour
	              FROM ' . SN_PROFILE_VISITORS_TABLE . ' v
	                LEFT JOIN ' . USERS_TABLE . ' u
	                  ON u.user_id = v.visitor_uid
	                WHERE v.profile_uid = ' . $user_id . '
	                  ORDER BY v.visit_time DESC';
			$result = $db->sql_query_limit($sql, 10);

			while ($visitors = $db->sql_fetchrow($result))
			{
				$template->assign_block_vars('visitors', array(
					'USERNAME'	 => get_username_string('full', $visitors['visitor_uid'], $visitors['username'], $visitors['user_colour']),
				));
			}
			$db->sql_freeresult($result);

			// Grab rank information for later
			$ranks = $cache->obtain_ranks();
			$rank_title = $rank_img = $rank_img_src = '';
			get_user_rank($member['user_rank'], (($user_id == ANONYMOUS) ? false : $member['user_posts']), $rank_title, $rank_img, $rank_img_src);

			// Get group memberships
			// Also get visiting user's groups to determine hidden group memberships if necessary.
			$auth_hidden_groups = ($user_id === (int) $user->data['user_id'] || $auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel')) ? true : false;
			$sql_uid_ary = ($auth_hidden_groups) ? array(
				$user_id,
			) : array(
				$user_id,
				(int) $user->data['user_id'],
			);

			// Do the SQL thang
			$sql = 'SELECT g.group_id, g.group_name, g.group_type, g.group_colour, ug.user_id
	              FROM ' . GROUPS_TABLE . ' g,
	                   ' . USER_GROUP_TABLE . ' ug
	              WHERE ' . $db->sql_in_set('ug.user_id', $sql_uid_ary) . '
	                AND g.group_id = ug.group_id
	                AND ug.user_pending = 0';
			$result = $db->sql_query($sql);

			// Divide data into profile data and current user data
			$profile_groups = $user_groups = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$row['user_id'] = (int) $row['user_id'];
				$row['group_id'] = (int) $row['group_id'];

				if ($row['user_id'] == $user_id)
				{
					$profile_groups[] = $row;
				}
				else
				{
					$user_groups[$row['group_id']] = $row['group_id'];
				}
			}
			$db->sql_freeresult($result);

			// Filter out hidden groups and sort groups by name
			$group_data = $group_sort = array();
			foreach ($profile_groups as $row)
			{
				if ($row['group_type'] == GROUP_SPECIAL)
				{
					// Lookup group name in language dictionary
					if (isset($user->lang['G_' . $row['group_name']]))
					{
						$row['group_name'] = $user->lang['G_' . $row['group_name']];
					}
				}
				else if (!$auth_hidden_groups && $row['group_type'] == GROUP_HIDDEN && !isset($user_groups[$row['group_id']]))
				{
					// Skip over hidden groups the user cannot see
					continue;
				}

				$group_sort[$row['group_id']] = utf8_clean_string($row['group_name']);
				$group_data[$row['group_id']] = $row;
			}
			unset($profile_groups);
			unset($user_groups);
			asort($group_sort);

			$groups = '';
			foreach ($group_sort as $group_id => $null)
			{
				$row = $group_data[$group_id];

				$row['group_colour'] = ($row['group_colour']) ? ' style="color:#' . $row['group_colour'] . '"' : '';

				if ($row['group_name'] == 'BOTS' || ($user->data['user_id'] != ANONYMOUS && !$auth->acl_get('u_viewprofile')))
				{
					$groups .= '<span' . $row['group_colour'] . '>' . $row['group_name'] . '</span>, ';
				}
				else
				{
					$groups .= '<a' . $row['group_colour'] . ' href="' . append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=group&amp;g=' . $row['group_id']) . '">' . $row['group_name'] . '</a>, ';
				}
			}
			$groups = substr_replace($groups, '', -2, strlen($groups));
			unset($group_data);
			unset($group_sort);

			$template->assign_vars(array(
				'L_POSTS_IN_QUEUE'	 => $user->lang('NUM_POSTS_IN_QUEUE', $member['posts_in_queue']),
				'POSTS_IN_QUEUE'	 => $member['posts_in_queue'],
				'PROFILE_VIEWS'		 => ($member['profile_views']) ? $member['profile_views'] : 0,
				'POSTS_DAY'			 => sprintf($user->lang['POST_DAY'], $posts_per_day),
				'POSTS_PCT'			 => sprintf($user->lang['POST_PCT'], $percentage),
				'JOINED'			 => $user->format_date($member['user_regdate']),
				'VISITED'			 => (empty($last_visit)) ? ' - ' : $user->format_date($last_visit),
				'LAST_CHANGE'		 => ((int) $member['profile_last_change'] == '0') ? '' : $user->format_date($member['profile_last_change']),
				'POSTS'				 => ($member['user_posts']) ? $member['user_posts'] : 0,
				'WARNINGS'			 => isset($member['user_warnings']) ? $member['user_warnings'] : 0,
				'S_WARNINGS'		 => ($auth->acl_getf_global('m_') || $auth->acl_get('m_warn')) ? true : false,
				'U_NOTES'			 => ($user_notes_enabled && $auth->acl_getf_global('m_')) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=notes&amp;mode=user_notes&amp;u=' . $user_id, true, $user->session_id) : '',
				'U_WARN'			 => ($warn_user_enabled && $auth->acl_get('m_warn')) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=warn&amp;mode=warn_user&amp;u=' . $user_id, true, $user->session_id) : '',
				'S_USER_NOTES'		 => ($user_notes_enabled) ? true : false,
				'S_WARN_USER'		 => ($warn_user_enabled) ? true : false,
				'RANK_IMG'			 => $rank_img,
				'RANK_IMG_SRC'		 => $rank_img_src,
				'RANK_TITLE'		 => $rank_title,
				'S_GROUPS'			 => $groups,
			));
		}

		function tab_wall($user_id)
		{
			return '';
		}

		function tab_report_user($user_id)
		{
			global $phpbb_root_path, $phpEx, $socialnet_root_path;
			global $template, $db, $user;

			$redirect_url = append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $user_id);

			$reason_id = request_var('reason_id', 0);
			$report_text = utf8_normalize_nfc(request_var('report_text', '', true));

			$sql = 'SELECT *
	              FROM ' . SN_REPORTS_REASONS_TABLE . '
	                ORDER BY reason_id ASC';
			$result = $db->sql_query($sql);

			while ($row = $db->sql_fetchrow($result))
			{
				$template->assign_block_vars('reason', array(
					'ID'		 => $row['reason_id'],
					'TEXT'		 => $row['reason_text'],
					'S_SELECTED' => ($row['reason_id'] == $reason_id) ? true : false,
				));
			}
			$db->sql_freeresult($result);

			// Has the report been cancelled?
			if (isset($_POST['cancel']))
			{
				redirect($redirect_url);
			}

			$submit = (isset($_POST['submit'])) ? true : false;

			if ($submit && $reason_id)
			{
				$sql = 'SELECT *
	                FROM ' . SN_REPORTS_REASONS_TABLE . '
	                  WHERE reason_id = ' . $reason_id;
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if (!$row || (!$report_text && strtolower($row['reason_text']) == 'other'))
				{
					trigger_error('SN_UP_EMPTY_REPORT');
				}

				$sql_ary = array(
					'reason_id'		 => (int) $reason_id,
					'report_text'	 => (string) $report_text,
					'user_id'		 => (int) $user_id,
					'reporter'		 => (int) $user->data['user_id'],
					'report_closed'	 => 0,
				);

				$sql = 'INSERT INTO ' . SN_REPORTS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
				$db->sql_query($sql);
				$report_id = $db->sql_nextid();

				meta_refresh(3, $redirect_url);

				$message = $user->lang['SN_UP_REPORT_SUCCESS'] . '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . $redirect_url . '">', '</a>');
				trigger_error($message);
			}

			$template->assign_vars(array(
				'REPORT_TEXT'		 => $report_text,
				'S_REPORT_ACTION'	 => append_sid("{$socialnet_root_path}profile.$phpEx", 'mode=report_user&amp;u=' . $user_id),
			));

			$page_title = $user->lang['SN_UP_REPORT_PROFILE'];
			$page_template = 'socialnet/user_profile_report_user.html';
		}

		function last_profile_change()
		{
			global $db, $user, $phpbb_root_path, $phpEx, $auth;

			if ($this->p_master->script_name != 'ucp')
			{
				return;
			}

			$mode = request_var('mode', '');
			$module = request_var('i', '');
			$submit = (isset($_POST['submit'])) ? true : false;

			$modules_ = array(
				'socialnet'	 => array(
					'module_profile',
				),
				'profile'	 => array(
					'profile_info',
					'signature',
					'avatar',
					'reg_details',
					'module_profile',
				)
			);

			if ($submit && isset($modules_[$module]) && in_array($mode, $modules_[$module]))
			{
				switch ($mode)
				{
				case 'profile_info':

					if (!check_form_key('ucp_profile_info'))
					{
						return;
					}

					/**
					 * /includes/ucp/ucp_profile.php LINE 271
					 */
					$data = array(
						'user_icq'		 => request_var('icq', $user->data['user_icq']),
						'user_aim'		 => request_var('aim', $user->data['user_aim']),
						'user_msnm'		 => request_var('msn', $user->data['user_msnm']),
						'user_yim'		 => request_var('yim', $user->data['user_yim']),
						'user_jabber'	 => utf8_normalize_nfc(request_var('jabber', $user->data['user_jabber'], true)),
						'user_website'	 => request_var('website', $user->data['user_website']),
						'user_from'		 => utf8_normalize_nfc(request_var('location', $user->data['user_from'], true)),
						'user_occ'		 => utf8_normalize_nfc(request_var('occupation', $user->data['user_occ'], true)),
						'user_interests' => utf8_normalize_nfc(request_var('interests', $user->data['user_interests'], true)),
					);

					if ($this->p_master->config['allow_birthdays'])
					{
						$data['bday_day'] = $data['bday_month'] = $data['bday_year'] = 0;

						if ($user->data['user_birthday'])
						{
							list($data['bday_day'], $data['bday_month'], $data['bday_year']) = explode('-', $user->data['user_birthday']);
							$user->data['bday_day'] = (int) $data['bday_day'];
							$user->data['bday_month'] = (int) $data['bday_month'];
							$user->data['bday_year'] = (int) $data['bday_year'];
						}

						$data['bday_day'] = request_var('bday_day', $data['bday_day']);
						$data['bday_month'] = request_var('bday_month', $data['bday_month']);
						$data['bday_year'] = request_var('bday_year', $data['bday_year']);

						$data['user_birthday'] = sprintf('%2d-%2d-%4d', $data['bday_day'], $data['bday_month'], $data['bday_year']);
					}

					$validate_array = array(
						'user_icq'		 => array(
							array('string', true, 3, 15),
							array('match', true, '#^[0-9]+$#i')
						),
						'user_aim'		 => array('string', true, 3, 255),
						'user_msnm'		 => array('string', true, 5, 255),
						'user_jabber'	 => array(
							array('string', true, 5, 255),
							array('jabber')
						),
						'user_yim'		 => array('string', true, 5, 255),
						'user_website'	 => array(
							array('string', true, 12, 255),
							array('match', true, '#^http[s]?://(.*?\.)*?[a-z0-9\-]+\.[a-z]{2,4}#i')),
						'user_from'		 => array('string', true, 2, 100),
						'user_occ'		 => array('string', true, 2, 500),
						'user_interests' => array('string', true, 2, 500),
					);

					if ($this->p_master->config['allow_birthdays'])
					{
						$validate_array = array_merge($validate_array, array(
							'bday_day'	 => array('num', true, 1, 31),
							'bday_month' => array('num', true, 1, 12),
							'bday_year'	 => array('num', true, 1901, gmdate('Y', time()) + 50),
						));
					}

					$error = validate_data($data, $validate_array);

					if (sizeof($error))
					{
						return;
					}

					$changed = array_diff_assoc($data, $user->data);
					if (!empty($changed['user_birthday']) && $data['bday_year'] != 0 && $data['bday_month'] != 0 && $data['bday_day'] != 0)
					{
						$changed['user_birthday'] = date('j. F Y', mktime(0, 0, 1, $data['bday_month'], $data['bday_day'], $data['bday_year']));
						unset($changed['bday_year']);
						unset($changed['bday_month']);
						unset($changed['bday_day']);
					}
					else
					{
						unset($changed['user_birthday']);
					}

					if ($this->p_master->config['allow_birthdays'])
					{
						unset($user->data['bday_day']);
						unset($user->data['bday_month']);
						unset($user->data['bday_year']);
					}

					break;

				case 'signature':

					if (!check_form_key('ucp_sig'))
					{
						return;
					}

					$original['user_sig'] = preg_replace('/\[[^]]*\]/si', '', $user->data['user_sig']);
					$data['user_sig'] = preg_replace('/\[[^]]*\]/si', '', utf8_normalize_nfc(request_var('signature', (string) $original['user_sig'], true)));

					$changed = array_diff_assoc($data, $original);

					break;

				case 'avatar':

					if (!check_form_key('ucp_avatar'))
					{
						return;
					}

					$data = array(
						'uploadurl'	 => request_var('uploadurl', ''),
						'remotelink' => request_var('remotelink', ''),
						'width'		 => request_var('width', 0),
						'height'	 => request_var('height', 0),
						'delete'	 => request_var('delete', 0),
					);

					$error = validate_data($data, array(
						'uploadurl'	 => array('string', true, 5, 255),
						'remotelink' => array('string', true, 5, 255),
						'width'		 => array('string', true, 1, 3),
						'height'	 => array('string', true, 1, 3),
					));

					if (isset($_FILES['uploadfile']) && $_FILES['uploadfile']['error'] == 0)
					{
						$image = getimagesize($_FILES['uploadfile']['tmp_name']);
						$aw = $image[0];
						$ah = $image[1];
					}
					else if (isset($_FILES['uploadfile']) && $_FILES['uploadfile']['error'] != 0 && $_FILES['uploadfile']['name'] != '')
					{
						return;
					}
					else if (isset($data['uploadurl']) && !empty($data['uploadurl']))
					{
						$data['user_id'] = $user->data['user_id'];
						avatar_upload($data, $error);
						if (!empty($error))
						{
							return;
						}
						$aw =& $data['width'];
						$ah =& $data['height'];
					}
					else
					{
						$aw =& $data['width'];
						$ah =& $data['height'];
					}

					$cfg =& $this->p_master->config;
					$correct_size = ($cfg['avatar_min_width'] <= $aw && $aw <= $cfg['avatar_max_width']) && ($cfg['avatar_min_height'] <= $ah && $ah <= $cfg['avatar_max_height']);
					// Just deleting or error or nothing submitted
					if (sizeof($error) || !$correct_size || isset($_POST['delete']) || ($data['uploadurl'] == '' && $data['remotelink'] == ''))
					{
						return;
					}
					// Can we upload?
					$change_avatar = $auth->acl_get('u_chgavatar');
					$can_upload = ($this->p_master->config['allow_avatar_upload'] && file_exists($phpbb_root_path . $this->p_master->config['avatar_path']) && phpbb_is_writable($phpbb_root_path . $this->p_master->config['avatar_path']) && $change_avatar && (@ini_get('file_uploads') || strtolower(@ini_get('file_uploads')) == 'on')) ? true : false;
					if (!$can_upload)
					{
						return;
					}

					$changed['user_avatar'] = 'user_avatar';

					break;

				case 'reg_details':

					// Try to manually determine the timezone and adjust the dst if the server date/time complies with the default setting +/- 1

					$data = array(
						'username'	 => utf8_normalize_nfc(request_var('username', $user->data['username'], true)),
						'user_email' => strtolower(request_var('email', $user->data['user_email'])),
					);

					$changed = array_diff_assoc($data, $user->data);

					break;

				case 'module_profile':

					$data = array(
						'hometown'			 => utf8_normalize_nfc(request_var('hometown', $user->data['hometown'], true)),
						'sex'				 => request_var('sex', $user->data['sex']),
						'interested_in'		 => request_var('interested_in', $user->data['interested_in']),
						'languages'			 => utf8_normalize_nfc(request_var('languages', $user->data['languages'], true)),
						'about_me'			 => utf8_normalize_nfc(request_var('about_me', $user->data['about_me'], true)),
						'employer'			 => utf8_normalize_nfc(request_var('employer', $user->data['employer'], true)),
						'university'		 => utf8_normalize_nfc(request_var('university', $user->data['university'], true)),
						'high_school'		 => utf8_normalize_nfc(request_var('high_school', $user->data['high_school'], true)),
						'religion'			 => utf8_normalize_nfc(request_var('religion', $user->data['religion'], true)),
						'political_views'	 => utf8_normalize_nfc(request_var('political_views', $user->data['political_views'], true)),
						'quotations'		 => utf8_normalize_nfc(request_var('quotations', $user->data['quotations'], true)),
						'music'				 => utf8_normalize_nfc(request_var('music', $user->data['music'], true)),
						'books'				 => utf8_normalize_nfc(request_var('books', $user->data['books'], true)),
						'movies'			 => utf8_normalize_nfc(request_var('movies', $user->data['movies'], true)),
						'games'				 => utf8_normalize_nfc(request_var('games', $user->data['games'], true)),
						'foods'				 => utf8_normalize_nfc(request_var('foods', $user->data['foods'], true)),
						'sports'			 => utf8_normalize_nfc(request_var('sports', $user->data['sports'], true)),
						'sport_teams'		 => utf8_normalize_nfc(request_var('sport_teams', $user->data['sport_teams'], true)),
						'activities'		 => utf8_normalize_nfc(request_var('activities', $user->data['activities'], true)),
						'skype'				 => utf8_normalize_nfc(request_var('skype', $user->data['skype'], true)),
						'facebook'			 => request_var('facebook', $user->data['facebook']),
						'twitter'			 => request_var('twitter', $user->data['twitter']),
						'youtube'			 => request_var('youtube', $user->data['youtube']),
					);

					$changed = array_diff_assoc($data, $user->data);

					$this->_prepare_for_entry($changed);

					break;
				}

				$changed = array_filter($changed);

				array_walk($changed, 'profile_change_cut_string');

				if (!empty($changed))
				{
					$sql = 'UPDATE ' . SN_USERS_TABLE . '
                    SET profile_last_change = ' . time() . '
                      WHERE user_id = ' . $user->data['user_id'];
					$db->sql_query($sql);

					$this->p_master->record_entry($user->data['user_id'], 0, SN_TYPE_PROFILE_UPDATED, $changed);
				}
			}

		}

		function emote($user_id)
		{
			global $user, $db, $phpbb_root_path;

			$user_id = request_var('u', 0);
			$emote_id = request_var('emote', 0);

			if (!$user_id || !$emote_id)
			{
				return;
			}

			$emote_data = array();
			$emote_data['emote_id'] = $emote_id;

			$sql = "SELECT emote_name, emote_image
								FROM " . SN_EMOTES_TABLE . "
									WHERE emote_id = {$emote_id}";
			$rs = $db->sql_query($sql);
			$row = $db->sql_fetchrow($rs);
			$db->sql_freeresult($rs);

			$emote_image = '';
			if (!empty($row['emote_image']))
			{
				$image = snFunctions_absolutePathString($phpbb_root_path . SN_UP_EMOTE_FOLDER . $row['emote_image']);
				$emote_image = '<img src="' . $image . '" alt="' . $row['emote_name'] . '" />';
			}

			$link = '';

			$emote_notify = array(
				'text'		 => 'SN_NTF_EMOTE',
				'user'		 => $user->data['username'],
				'emote'		 => $row['emote_name'],
				'image'		 => $emote_image,
				'link'		 => $link,
				'emote_id'	 => $emote_id,
			);
			$this->p_master->notify->add(SN_NTF_EMOTE, $user_id, $emote_notify);
			$this->p_master->record_entry($user->data['user_id'], $user_id, SN_TYPE_EMOTE, $emote_data);

			unset($emote_notify['text']);

			$emote_notify['user'] = $this->p_master->friends['friends'][$user_id];

			$send_emote = array(
				'cbTitle'	 => $user->lang['SN_NTF_EMOTE_CB_TITLE'],
				'cbText'	 => vsprintf($user->lang['SN_NTF_EMOTE_CB_TEXT'], $emote_notify),
			);

			header('Content-type: application/json');
			header("Cache-Control: no-cache, must-revalidate");
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
			die(json_encode($send_emote));
		}

		function hook_template()
		{
			global $template, $config;

			if ($config['module_profile'])
			{
				array_walk_recursive($template->_tpldata, 'hook_template_userstatus_profile_array_callback');
			}
		}

		function upEdit($user_id)
		{
			global $db, $user, $config, $phpbb_root_path, $phpEx;

			$fieldName = request_var('field', '');
			$value = request_var('value', '', true);
			$b_bbcode = request_var('bbcode', false);
			$b_date = request_var('date', false);

			$fieldSet = explode('-', $fieldName);
			$tableSH = $fieldSet[0];
			$field = $fieldSet[1];
			$numFields = isset($fieldSet[2]) ? true : false;
			$edit = $value;

			$table = $tableSH == 'up' || $tableSH == 2 ? SN_USERS_TABLE : USERS_TABLE;

			$fieldset = array(
				$field => $value
			);

			if ($b_bbcode)
			{
				if ($field == 'user_sig')
				{
					include($phpbb_root_path . 'includes/message_parser.' . $phpEx);

					$enable_bbcode = ($config['allow_sig_bbcode']) ? ((request_var('disable_bbcode', false)) ? false : true) : false;
					$enable_smilies = ($config['allow_sig_smilies']) ? ((request_var('disable_smilies', false)) ? false : true) : false;
					$enable_urls = ($config['allow_sig_links']) ? ((request_var('disable_magic_url', false)) ? false : true) : false;

					$value = utf8_normalize_nfc($value);

					$message_parser = new parse_message($value);

					$message_parser->parse($enable_bbcode, $enable_urls, $enable_smilies, $config['allow_sig_img'], $config['allow_sig_flash'], true, $config['allow_sig_links'], true, 'sig');

					$user->optionset('sig_bbcode', $enable_bbcode);
					$user->optionset('sig_smilies', $enable_smilies);
					$user->optionset('sig_links', $enable_urls);

					$fieldset = array(
						'user_sig'					 => (string) $message_parser->message,
						'user_options'				 => $user->data['user_options'],
						'user_sig_bbcode_uid'		 => (string) $message_parser->bbcode_uid,
						'user_sig_bbcode_bitfield'	 => $message_parser->bbcode_bitfield,
					);

					$edit = $value;

					include_once($phpbb_root_path . 'includes/bbcode.' . $phpEx);
					$bbcode = new bbcode();
					$bbcode->bbcode_second_pass($message_parser->message, $message_parser->bbcode_uid, $message_parser->bbcode_bitfield);
					$value = bbcode_nl2br($message_parser->message);
					$value = smiley_text($value);

				}
			}
			else if ($b_date)
			{
				/* Convert date */
				$month = '';
				for ($i = 1; $i <= 12; $i++)
				{
					$month = date('F', mktime(1, 1, 1, $i, 1, 1));
					if (strpos($value, $user->lang['datetime'][$month]) !== false)
					{
						break;
					}
				}

				$gb_value = str_replace($user->lang['datetime'][$month], $month, $value);
				$time = strtotime($gb_value);
				$date = explode('-', date('d-m-Y', $time));

				if ($numFields)
				{
					$fieldset = array(
						$field . '_day'	 => $date[0],
						$field . '_month' => $date[1],
						$field . '_year'	 => $date[2],
					);
				}
				else
				{
					$fieldset = array(
						$field => date('d-m-Y', $time)
					);
				}

			}

			$sql = "UPDATE {$table}
					SET " . $db->sql_build_array('UPDATE', $fieldset) . "
					WHERE user_id = '{$user->data['user_id']}'";
			$db->sql_query($sql);

			$sql = "UPDATE " . SN_USERS_TABLE . " SET profile_last_change = " . time() . " WHERE user_id = '{$user->data['user_id']}'";
			$db->sql_query($sql);

			if (isset($fieldset['user_sig']))
			{
				strip_bbcode($fieldset['user_sig'], $fieldset['user_sig_bbcode_uid']);
				unset($fieldset['user_options']);
				unset($fieldset['user_sig_bbcode_uid']);
				unset($fieldset['user_sig_bbcode_bitfield']);
			}

			array_walk($fieldset, 'profile_change_cut_string');
			$this->_prepare_for_entry($fieldset);
			$this->p_master->record_entry($user->data['user_id'], 0, SN_TYPE_PROFILE_UPDATED, $fieldset);

			header('Content-type: application/json');
			header("Cache-Control: no-cache, must-revalidate");
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
			die(json_encode(array(
				'origin' => $value,
				'edit'	 => $edit,
				//'sql'    => $sql,
				)));
		}

		function _prepare_for_entry(&$changed)
		{
			global $user;

			if (!is_array($changed) || sizeOf($changed) == 0)
			{
				return;
			}

			if (isset($changed['sex']) && !empty($changed['sex']))
			{
				$changed['sex'] = ($changed['sex'] == '1') ? $user->lang['SN_UP_MALE'] : $user->lang['SN_UP_FEMALE'];

				$my_friends = $this->p_master->friends['user_id'];

				for ($i = 0; isset($my_friends[$i]); $i++)
				{
					$this->p_master->purge_friends($my_friends[$i]);
				}
			}

			if (isset($changed['interested_in']) && !empty($changed['interested_in']))
			{
				if ($changed['interested_in'] == '1')
				{
					$changed['interested_in'] = $user->lang['SN_UP_MALES'];
				}
				elseif ($changed['interested_in'] == '2')
				{
					$changed['interested_in'] = $user->lang['SN_UP_FEMALES'];
				}
				elseif ($changed['interested_in'] == '3')
				{
					$changed['interested_in'] = $user->lang['SN_UP_BOTH'];
				}
			}

			foreach ($changed as $idx => $value)
			{
				if (trim($value) == '')
				{
					$changed[$idx] = $user->lang['SN_UP_PROFILE_VALUE_DELETED'];
				}
			}

		}
	}
}

if (!function_exists('hook_template_userstatus_profile_array_callback'))
{
	function hook_template_userstatus_profile_array_callback(&$item, $key)
	{
		global $phpEx;

		$preg_match_profile = '/memberlist\.' . $phpEx . '\?(mode=viewprofile)?(&amp;|&)?(un?=[^&"]{1,})([^"\']*?)/si';

		if (preg_match($preg_match_profile, $item))
		{
			$item = preg_replace($preg_match_profile, 'profile.' . $phpEx . '?\3\4', $item);
		}
	}
}

if (!function_exists('profile_change_cut_string'))
{
	function profile_change_cut_string(&$value)
	{
		global $config;

		if (strlen($value) > $config['ap_max_profile_value'])
		{
			$value = truncate_string($value, $config['ap_max_profile_value'] + 1);
			if (strrpos($value, ' ') != 0)
			{
				$value = substr($value, 0, strrpos($value, ' ')) . '&nbsp;';
			}
			$value .= '...';
		}
	}
}

if (isset($socialnet) && defined('SN_PROFILE'))
{
	if ($user->data['user_type'] == USER_IGNORE || $config['board_disable'] == 1)
	{
		$ann_data = array(
			'user_id'		 => 'ANONYMOUS',
			'more'			 => false,
			'onlineCount'	 => 0,
		);

		header('Content-type: application/json');
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		die(json_encode($ann_data));
	}

	$s_mode = request_var('mode', '');
	$i_user = request_var('u', ANONYMOUS);
	$socialnet->modules_obj['profile']->load($s_mode, $i_user);
}

?>