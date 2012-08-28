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

class ucp_profile
{
	var $p_master = null;

	function ucp_profile(&$p_master)
	{
		$this->p_master =& $p_master;
	}

	function main($id, $module)
	{
		global $db, $user, $template, $phpbb_root_path, $phpEx, $socialnet, $socialnet_root_path, $config;

		include_once($socialnet_root_path . 'includes/constants.' . $phpEx);

		switch ($module)
		{
		case 'default':

			$this->p_master->tpl_name = 'socialnet/ucp_profile';

			$error = $data = array();

			$row =& $user->data;

			$data = array(
				'hometown'			 => utf8_normalize_nfc(request_var('hometown', $row['hometown'], true)),
				'sex'				 => request_var('sex', $row['sex']),
				'interested_in'		 => request_var('interested_in', $row['interested_in']),
				'languages'			 => utf8_normalize_nfc(request_var('languages', $row['languages'], true)),
				'about_me'			 => utf8_normalize_nfc(request_var('about_me', $row['about_me'], true)),
				'employer'			 => utf8_normalize_nfc(request_var('employer', $row['employer'], true)),
				'university'		 => utf8_normalize_nfc(request_var('university', $row['university'], true)),
				'high_school'		 => utf8_normalize_nfc(request_var('high_school', $row['high_school'], true)),
				'religion'			 => utf8_normalize_nfc(request_var('religion', $row['religion'], true)),
				'political_views'	 => utf8_normalize_nfc(request_var('political_views', $row['political_views'], true)),
				'quotations'		 => utf8_normalize_nfc(request_var('quotations', $row['quotations'], true)),
				'music'				 => utf8_normalize_nfc(request_var('music', $row['music'], true)),
				'books'				 => utf8_normalize_nfc(request_var('books', $row['books'], true)),
				'movies'			 => utf8_normalize_nfc(request_var('movies', $row['movies'], true)),
				'games'				 => utf8_normalize_nfc(request_var('games', $row['games'], true)),
				'foods'				 => utf8_normalize_nfc(request_var('foods', $row['foods'], true)),
				'sports'			 => utf8_normalize_nfc(request_var('sports', $row['sports'], true)),
				'sport_teams'		 => utf8_normalize_nfc(request_var('sport_teams', $row['sport_teams'], true)),
				'activities'		 => utf8_normalize_nfc(request_var('activities', $row['activities'], true)),
				'skype'				 => utf8_normalize_nfc(request_var('skype', $row['skype'], true)),
				'facebook'			 => request_var('facebook', $row['facebook']),
				'twitter'			 => request_var('twitter', $row['twitter']),
				'youtube'			 => request_var('youtube', $row['youtube']),
			);

			// display settings
			$template->assign_vars(array(
				'HOMETOWN'			 => $data['hometown'],
				'SEX'				 => $data['sex'],
				'INTERESTED_IN'		 => $data['interested_in'],
				'LANGUAGES'			 => $data['languages'],
				'ABOUT_ME'			 => $data['about_me'],
				'EMPLOYER'			 => $data['employer'],
				'UNIVERSITY'		 => $data['university'],
				'HIGH_SCHOOL'		 => $data['high_school'],
				'RELIGION'			 => $data['religion'],
				'POLITICAL_VIEWS'	 => $data['political_views'],
				'QUOTATIONS'		 => $data['quotations'],
				'MUSIC'				 => $data['music'],
				'BOOKS'				 => $data['books'],
				'MOVIES'			 => $data['movies'],
				'GAMES'				 => $data['games'],
				'FOODS'				 => $data['foods'],
				'SPORTS'			 => $data['sports'],
				'SPORT_TEAMS'		 => $data['sport_teams'],
				'ACTIVITIES'		 => $data['activities'],
				'SKYPE'				 => $data['skype'],
				'FACEBOOK'			 => $data['facebook'],
				'TWITTER'			 => $data['twitter'],
				'YOUTUBE'			 => $data['youtube'],
			));

			$submit = (isset($_POST['submit'])) ? true : false;

			if ($submit)
			{
				$validate_array = array(
					'facebook'			 => array(
						array('string', true, 12, 255),
						array('match', true, '#^http[s]?://(.*?\.)*?[a-z0-9\-]+\.[a-z]{2,4}#i')),
					'twitter'			 => array(
						array('string', true, 12, 255),
						array('match', true, '#^http[s]?://(.*?\.)*?[a-z0-9\-]+\.[a-z]{2,4}#i')),
					'youtube'			 => array(
						array('string', true, 12, 255),
						array('match', true, '#^http[s]?://(.*?\.)*?[a-z0-9\-]+\.[a-z]{2,4}#i')),
					'hometown'			 => array('string', true, 2, 255),
					'languages'			 => array('string', true, 2, 500),
					'employer'			 => array('string', true, 2, 1024),
					'university'		 => array('string', true, 2, 500),
					'high_school'		 => array('string', true, 2, 500),
					'religion'			 => array('string', true, 2, 500),
					'political_views'	 => array('string', true, 2, 1024),
					'about_me'			 => array('string', true, 2, 1024),
					'quotations'		 => array('string', true, 2, 1024),
					'music'				 => array('string', true, 2, 1024),
					'books'				 => array('string', true, 2, 1024),
					'movies'			 => array('string', true, 2, 1024),
					'games'				 => array('string', true, 2, 1024),
					'foods'				 => array('string', true, 2, 1024),
					'sports'			 => array('string', true, 2, 1024),
					'sport_teams'		 => array('string', true, 2, 1024),
					'activities'		 => array('string', true, 2, 1024),
					'skype'				 => array('string', true, 6, 32),
				);

				$error = validate_data($data, $validate_array);

				$sql_ary = (array(
					'user_id'			 => $user->data['user_id'],
					'hometown'			 => $data['hometown'],
					'sex'				 => $data['sex'],
					'interested_in'		 => $data['interested_in'],
					'languages'			 => $data['languages'],
					'about_me'			 => $data['about_me'],
					'employer'			 => $data['employer'],
					'university'		 => $data['university'],
					'high_school'		 => $data['high_school'],
					'religion'			 => $data['religion'],
					'political_views'	 => $data['political_views'],
					'quotations'		 => $data['quotations'],
					'music'				 => $data['music'],
					'books'				 => $data['books'],
					'movies'			 => $data['movies'],
					'games'				 => $data['games'],
					'foods'				 => $data['foods'],
					'sports'			 => $data['sports'],
					'sport_teams'		 => $data['sport_teams'],
					'activities'		 => $data['activities'],
					'skype'				 => $data['skype'],
					'facebook'			 => $data['facebook'],
					'twitter'			 => $data['twitter'],
					'youtube'			 => $data['youtube'],
				));

				if (!sizeof($error))
				{
					$socialnet->modules_obj['profile']->last_profile_change();

					$sql = "UPDATE " . SN_USERS_TABLE . "
    								  SET " . $db->sql_build_array('UPDATE', $sql_ary) . "
    								    WHERE user_id = " . $user->data['user_id'];
					$db->sql_query($sql);

					$message = $user->lang['SN_UP_PROFILE_UPDATED'] . '<br /><br />' . sprintf($user->lang['RETURN_UCP'], '<a href="' . $this->p_master->u_action . '">', '</a>');

					meta_refresh(3, $this->p_master->u_action);
					trigger_error($message);
				}
				else
				{
					$error = preg_replace('#^([A-Z_]+)$#e', "(!empty(\$user->lang['\\1'])) ? \$user->lang['\\1'] : '\\1'", $error);
				}
			}

			$template->assign_vars(array(
				'U_ACTION'	 => $this->p_master->u_action,
				'ERROR'		 => (sizeof($error)) ? implode('<br />', $error) : '',
			));

			break;

		case 'relations':

			$this->p_master->tpl_name = 'socialnet/ucp_profile_relations';

			$error = $data = $changed = array();

			$action = request_var('action', '');

			switch ($action)
			{
			case 'select_friends':
				$socialnet->users_autocomplete();
				break;

			case 'delete_relation':
				$this->delete_relation();
				break;

			case 'approve_relation':
				$this->approve_relation(true);
				break;

			case 'refuse_relation':
				$this->approve_relation(false);
				break;

			default:

				$data = array(
					'relationship_status'	 => request_var('relationship_status', 0),
					'relationship_user'		 => utf8_normalize_nfc(request_var('relationship_user', '', true)),
					'anniversary'			 => request_var('anniversary_picker', ''),
					'family_status'			 => request_var('family_status', 0),
					'family_user'			 => utf8_normalize_nfc(request_var('family_user', '', true)),
				);

				$submit = (isset($_POST['submit'])) ? true : false;

				if ($submit)
				{
					// Insert Relationship
					if ($data['relationship_status'])
					{
						$relationships_with_partner = array(2, 3, 4, 5, 6);

						// Relationship with a partner
						if (in_array($data['relationship_status'], $relationships_with_partner))
						{
							if (!empty($data['anniversary']))
							{
								$validate_array = array(
									'anniversary'	 => array(
										array('string', true, 8, 10),
										array('match', true, '^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$^')),
								);
								$error = validate_data($data, $validate_array);
							}

							// No partner chosen
							if (empty($data['relationship_user']))
							{
								if (!sizeof($error))
								{
									$sql = 'INSERT INTO ' . SN_FAMILY_TABLE . ' (user_id, relative_user_id, status_id, approved, anniversary, family)
											VALUES (' . $user->data['user_id'] . ', 0, ' . $data['relationship_status'] . ', ' . SN_RELATIONSHIP_APPROVED . ', "' . $data['anniversary'] . '", 0)';
									$db->sql_query($sql);

									$socialnet->record_entry($user->data['user_id'], $data['relationship_status'], SN_TYPE_NEW_RELATIONSHIP);
								}
							}
							// Partner has been chosen
							else
							{
								$sql = 'SELECT user_id
								      	FROM ' . USERS_TABLE . '
								       	WHERE username = "' . $db->sql_escape($data['relationship_user']) . '"';
								$result = $db->sql_query($sql);
								$new_relationship = $db->sql_fetchrow($result);

								// Partner is registered
								if ($db->sql_affectedrows())
								{
									if ($new_relationship['user_id'] == $user->data['user_id'])
									{
										$error[] = $user->lang['SN_UP_APPROVE_RELATION_ERROR_MYSELF'];
									}

									if (!sizeof($error))
									{
										$sql = 'INSERT INTO ' . SN_FAMILY_TABLE . ' (user_id, relative_user_id, status_id, approved, anniversary, family)
	                        					VALUES (' . $user->data['user_id'] . ', ' . $new_relationship['user_id'] . ', ' . $data['relationship_status'] . ', ' . SN_RELATIONSHIP_UNANSWERED . ', "' . $data['anniversary'] . '", 0)';
										$db->sql_query($sql);

										$socialnet->record_entry($user->data['user_id'], $data['relationship_status'], SN_TYPE_NEW_RELATIONSHIP);

										$sql = 'SELECT id
																	FROM ' . SN_FAMILY_TABLE . '
																		WHERE user_id = ' . $user->data['user_id'] . '
	                                  	AND relative_user_id = ' . $new_relationship['user_id'] . '
	                                  		AND status_id = ' . $data['relationship_status'] . '
	                             		ORDER BY id DESC';
										$db->sql_query($sql);
										$new_relationship_id = $db->sql_fetchfield('id');

										if ($socialnet->is_enabled('notify'))
										{
											$link = "ucp.{$phpEx}?i=socialnet&amp;mode=module_profile_relations&amp;action=approve_relation&amp;id={$new_relationship_id}";

											$socialnet->notify->add(SN_NTF_RELATION, $new_relationship['user_id'], array('text' => 'SN_NTF_APPROVE_RELATIONSHIP', 'user' => $user->data['username'], 'link' => $link));
										}
										else
										{
											include_once($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);

											$status = $socialnet->relationship_status($data['relationship_status'], true);
											$link = generate_board_url() . "/ucp.{$phpEx}?i=socialnet&amp;mode=module_profile_relations&amp;action=approve_relation&amp;id={$new_relationship_id}";

											$subject = sprintf($user->lang('SN_UP_APPROVE_RELATION_SUBJECT'), $user->data['username']);
											$message = sprintf($user->lang['SN_UP_APPROVE_RELATION_TEXT'], '<a href="' . $link . '">', $user->data['username'], $status, '</a>');

											$uid = $bitfield = $options = '';
											generate_text_for_storage($message, $uid, $bitfield, $options, true, true, true);

											$pm_data = array(
												'address_list'		 => array('u' => array($new_relationship['user_id'] => 'to')),
												'from_user_id'		 => $user->data['user_id'],
												'from_username'		 => $user->data['username'],
												'icon_id'			 => 0,
												'from_user_ip'		 => $user->data['user_ip'],
												'enable_bbcode'		 => true,
												'enable_smilies'	 => true,
												'enable_urls'		 => true,
												'enable_sig'		 => false,
												'message'			 => $message,
												'bbcode_bitfield'	 => $bitfield,
												'bbcode_uid'		 => $uid,
											);
											submit_pm('post', $subject, $pm_data, false);
										}
									}
								}
								// Partner is not registered
								else
								{
									if (!sizeof($error))
									{
										$sql = 'INSERT INTO ' . SN_FAMILY_TABLE . ' (user_id, relative_user_id, status_id, approved, anniversary, family, name)
							                    VALUES (' . $user->data['user_id'] . ', 0, ' . $data['relationship_status'] . ', ' . SN_RELATIONSHIP_APPROVED . ', "' . $data['anniversary'] . '", 0, "' . $data['relationship_user'] . '")';
										$db->sql_query($sql);

										$changed['relationship'] = $data['relationship_user'];
										$socialnet->record_entry($user->data['user_id'], $data['relationship_status'], SN_TYPE_NEW_RELATIONSHIP, $changed);
									}
								}
							}
						}
						// Relationship without partner
						else
						{
							$sql = 'INSERT INTO ' . SN_FAMILY_TABLE . ' (user_id, relative_user_id, status_id, approved, anniversary, family)
														VALUES (' . $user->data['user_id'] . ', 0, ' . $data['relationship_status'] . ', ' . SN_RELATIONSHIP_APPROVED . ', "", 0)';
							$db->sql_query($sql);

							$socialnet->record_entry($user->data['user_id'], $data['relationship_status'], SN_TYPE_NEW_RELATIONSHIP);
						}
					}

					// Insert Family Relation
					if ($data['family_status'] && !empty($data['family_user']))
					{
						$sql = 'SELECT user_id, username
													FROM ' . USERS_TABLE . '
														WHERE username = "' . $db->sql_escape($data['family_user']) . '"';
						$result = $db->sql_query($sql);
						$family = $db->sql_fetchrow($result);

						if ($family['user_id'])
						{
							$sql = 'SELECT id
														FROM ' . SN_FAMILY_TABLE . '
															WHERE family = 1
																AND user_id = ' . $user->data['user_id'] . '
																AND relative_user_id = ' . $family['user_id'];
							$result = $db->sql_query($sql);
							$relation_exist = $db->sql_fetchfield('id');

							if ($relation_exist)
							{
								$error[] = sprintf($user->lang['SN_UP_APPROVE_FAMILY_ERROR_EXIST'], $family['username']);
							}

							if ($family['user_id'] == $user->data['user_id'])
							{
								$error[] = $user->lang['SN_UP_APPROVE_FAMILY_ERROR_MYSELF'];
							}
						}

						if (!sizeof($error))
						{
							// Family member is not registered
							if (!$family['user_id'])
							{
								$sql = 'INSERT INTO ' . SN_FAMILY_TABLE . ' (user_id, relative_user_id, status_id, approved, anniversary, family, name)
												    	VALUES (' . $user->data['user_id'] . ', 0, ' . $data['family_status'] . ', ' . SN_RELATIONSHIP_APPROVED . ', "", 1, "' . $data['family_user'] . '")';
								$db->sql_query($sql);

								$changed['family'] = $data['family_user'];
								$socialnet->record_entry($user->data['user_id'], $data['family_status'], SN_TYPE_NEW_FAMILY, $changed);
							}
							else
							{
								$sql = 'INSERT INTO ' . SN_FAMILY_TABLE . ' (user_id, relative_user_id, status_id, approved, anniversary, family)
												    	VALUES (' . $user->data['user_id'] . ', ' . $family['user_id'] . ', ' . $data['family_status'] . ', ' . SN_RELATIONSHIP_UNANSWERED . ', "", 1)';
								$db->sql_query($sql);

								$sql = 'SELECT id
													    FROM ' . SN_FAMILY_TABLE . '
													      WHERE user_id = ' . $user->data['user_id'] . '
														      AND relative_user_id = ' . $family['user_id'] . '
														      AND status_id = ' . $data['family_status'] . '
													    ORDER BY id DESC';
								$db->sql_query($sql);
								$family_id = $db->sql_fetchfield('id');

								if ($socialnet->is_enabled('notify'))
								{
									$link = "ucp.{$phpEx}?i=socialnet&amp;mode=module_profile_relations&amp;action=approve_relation&amp;id={$family_id}";
									$status = $socialnet->family_status($data['family_status']);

									$socialnet->notify->add(SN_NTF_FAMILY, $family['user_id'], array('text' => 'SN_NTF_APPROVE_FAMILY', 'user' => $user->data['username'], 'status' => $status, 'link' => $link));
								}
								else
								{
									include_once($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);

									$status = $socialnet->family_status($data['family_status']);
									$link = generate_board_url() . '/ucp.' . $phpEx . '?i=socialnet&amp;mode=module_profile_relations&amp;action=approve_relation&amp;id=' . $family_id;

									$subject = sprintf($user->lang('SN_UP_APPROVE_FAMILY_SUBJECT'), $user->data['username'], $status);
									$message = sprintf($user->lang('SN_UP_APPROVE_FAMILY_TEXT'), '<a href="' . $link . '">', $user->data['username'], $status, '</a>');

									$uid = $bitfield = $options = '';
									generate_text_for_storage($message, $uid, $bitfield, $options, true, true, true);

									$pm_data = array(
										'address_list'		 => array('u' => array($family['user_id'] => 'to')),
										'from_user_id'		 => $user->data['user_id'],
										'from_username'		 => $user->data['username'],
										'icon_id'			 => 0,
										'from_user_ip'		 => $user->data['user_ip'],
										'enable_bbcode'		 => true,
										'enable_smilies'	 => true,
										'enable_urls'		 => true,
										'enable_sig'		 => false,
										'message'			 => $message,
										'bbcode_bitfield'	 => $bitfield,
										'bbcode_uid'		 => $uid,
									);
									submit_pm('post', $subject, $pm_data, false);
								}
							}
						}
					}
					else if ($data['family_status'])
					{
						$error[] = $user->lang['SN_UP_ADD_FAMILY_ERR_MEMBER_EMPTY'];
					}
				}

				// Load relationships and family
				$sql = 'SELECT f.id, f.status_id, f.anniversary, f.approved, f.relative_user_id, f.family, f.name,
                           u.username, u.user_colour, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height
							        FROM ' . SN_FAMILY_TABLE . ' f
							          LEFT JOIN ' . USERS_TABLE . ' u
							            ON u.user_id = f.relative_user_id
							          WHERE f.user_id = ' . $user->data['user_id'] . '
							        ORDER BY f.approved DESC, f.status_id ASC';
				$result = $db->sql_query($sql);

				while ($relation = $db->sql_fetchrow($result))
				{
					$avatar_img = $socialnet->get_user_avatar_resized($relation['user_avatar'], $relation['user_avatar_type'], $relation['user_avatar_width'], $relation['user_avatar_height'], 50);
					$username = ($relation['relative_user_id']) ? $socialnet->get_username_string($socialnet->config['us_colour_username'], 'full', $relation['relative_user_id'], $relation['username'], $relation['user_colour']) : '';
					$profile_link = ($relation['relative_user_id']) ? $socialnet->get_username_string($socialnet->config['us_colour_username'], 'profile', $relation['relative_user_id'], $relation['username'], $relation['user_colour']) : '';

					if ($relation['family'])
					{
						$template->assign_block_vars('family', array(
							'USER_ID'			 => $relation['relative_user_id'],
							'STATUS'			 => $socialnet->family_status($relation['status_id']),
							'U_RELATIVE'		 => ($relation['name']) ? $relation['name'] : $username,
							'USERNAME_NO_COLOR'	 => ($relation['username']) ? $relation['username'] : '',
							'U_PROFILE_LINK'	 => $profile_link,
							'AVATAR'			 => $avatar_img,
							'APPROVED'			 => ($relation['approved'] == SN_RELATIONSHIP_APPROVED) ? true : false,
							'REFUSED'			 => ($relation['approved'] == SN_RELATIONSHIP_REFUSED) ? true : false,
							'UNANSWERED'		 => ($relation['approved'] == SN_RELATIONSHIP_UNANSWERED) ? true : false,
							'U_DELETE'			 => append_sid($this->p_master->u_action, "action=delete_relation&amp;id=" . $relation['id']),
						));
					}
					else
					{
						if ($relation['anniversary'])
						{
							$relationship_arr = array_map('intval', explode('-', $relation['anniversary']));
							$relationship_anniversary = $user->format_date(gmmktime(0, 0, -$user->timezone, (int) $relationship_arr[1], (int) $relationship_arr[0], (int) $relationship_arr[2]), '|j. F Y|');
						}

						$template->assign_block_vars('relationship', array(
							'USER_ID'			 => $relation['relative_user_id'],
							'STATUS'			 => $socialnet->relationship_status($relation['status_id'], false),
							'U_RELATIVE'		 => ($relation['name']) ? $relation['name'] : $username,
							'USERNAME_NO_COLOR'	 => ($relation['username']) ? $relation['username'] : '',
							'U_PROFILE_LINK'	 => $profile_link,
							'AVATAR'			 => $avatar_img,
							'APPROVED'			 => ($relation['approved'] == SN_RELATIONSHIP_APPROVED) ? true : false,
							'REFUSED'			 => ($relation['approved'] == SN_RELATIONSHIP_REFUSED) ? true : false,
							'UNANSWERED'		 => ($relation['approved'] == SN_RELATIONSHIP_UNANSWERED) ? true : false,
							'U_DELETE'			 => append_sid($this->p_master->u_action, "action=delete_relation&amp;id=" . $relation['id']),
							'ANNIVERSARY'		 => ($relation['anniversary']) ? $relationship_anniversary : '',
						));
					}
				}
				$db->sql_freeresult($result);

				// Load relationships and family requests
				$sql = 'SELECT f.id, f.status_id, f.anniversary, f.approved, f.relative_user_id, f.user_id, f.family, f.name,
                           u.username, u.user_colour, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height
							        FROM ' . SN_FAMILY_TABLE . ' f
							          LEFT JOIN ' . USERS_TABLE . ' u
							            ON u.user_id = f.user_id
							          WHERE f.relative_user_id = ' . $user->data['user_id'] . '
							            AND f.approved = ' . SN_RELATIONSHIP_UNANSWERED . '
							        ORDER BY f.approved DESC, f.status_id ASC';
				$result = $db->sql_query($sql);

				while ($relation = $db->sql_fetchrow($result))
				{
					$avatar_img = $socialnet->get_user_avatar_resized($relation['user_avatar'], $relation['user_avatar_type'], $relation['user_avatar_width'], $relation['user_avatar_height'], 50);
					$username = ($relation['user_id']) ? $socialnet->get_username_string($socialnet->config['us_colour_username'], 'full', $relation['user_id'], $relation['username'], $relation['user_colour']) : '';
					$profile_link = ($relation['user_id']) ? $socialnet->get_username_string($socialnet->config['us_colour_username'], 'profile', $relation['user_id'], $relation['username'], $relation['user_colour']) : '';

					if ($relation['anniversary'])
					{
						$relationship_arr = array_map('intval', explode('-', $relation['anniversary']));
						$relationship_anniversary = $user->format_date(gmmktime(0, 0, -$user->timezone, (int) $relationship_arr[1], (int) $relationship_arr[0], (int) $relationship_arr[2]), '|j. F Y|');
					}

					$template->assign_block_vars('requests', array(
						'USER_ID'			 => $relation['user_id'],
						'STATUS'			 => ($relation['status_id'] < 20) ? $socialnet->relationship_status($relation['status_id']) : $socialnet->family_status($relation['status_id']),
						'U_RELATIVE'		 => ($relation['name']) ? $relation['name'] : $username,
						'USERNAME_NO_COLOR'	 => ($relation['username']) ? $relation['username'] : '',
						'U_PROFILE_LINK'	 => $profile_link,
						'AVATAR'			 => $avatar_img,
						'U_REFUSE'			 => append_sid($this->p_master->u_action, "action=refuse_relation&amp;id=" . $relation['id']),
						'U_APPROVE'			 => append_sid($this->p_master->u_action, "action=approve_relation&amp;id=" . $relation['id']),
						'ANNIVERSARY'		 => ($relation['anniversary']) ? $relationship_anniversary : '',
					));
				}
				$db->sql_freeresult($result);

				if (sizeof($error))
				{
					$error = preg_replace('#^([A-Z_]+)$#e', "(!empty(\$user->lang['\\1'])) ? \$user->lang['\\1'] : '\\1'", $error);
				}

				$template->assign_vars(array(
					'U_ACTION'			 => $this->p_master->u_action,
					'ERROR'				 => (sizeof($error)) ? implode('<br />', $error) : '',
					'U_SELECT_FRIENDS'	 => append_sid($this->p_master->u_action, "action=select_friends"),
				));
			}

			break;
		}

	}

	function approve_relation($approve = true)
	{
		global $db, $user, $phpbb_root_path, $phpEx, $socialnet, $template;

		$relation_id = request_var('id', 0);

		$changed = array();

		$sql = 'SELECT f.family, f.user_id, f.status_id, f.relative_user_id, f.approved, f.anniversary, s.sex, u.username, u.user_colour, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height
      				FROM ' . SN_FAMILY_TABLE . ' f
      					LEFT JOIN ' . USERS_TABLE . ' u
                  ON f.user_id = u.user_id
                LEFT JOIN ' . SN_USERS_TABLE . ' s
                  ON s.user_id = f.user_id
      				WHERE f.id = ' . $relation_id;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);

		// check if this relationship is still active
		if ($row['relative_user_id'] != $user->data['user_id'])
		{
			$message = ($row['family'] == 0) ? $user->lang['SN_UP_APPROVE_RELATION_ERROR_CANCELED'] : $user->lang['SN_UP_APPROVE_FAMILY_ERROR_CANCELED'];
			meta_refresh(3, append_sid("{$phpbb_root_path}index.{$phpEx}"));
			trigger_error($message);
		}

		// Check if is already approved
		if ($row['approved'] == SN_RELATIONSHIP_APPROVED)
		{
			$message = ($row['family'] == 0) ? $user->lang['SN_UP_APPROVE_RELATION_ERROR_APPROVED'] : $user->lang['SN_UP_APPROVE_FAMILY_ERROR_APPROVED'];
			meta_refresh(3, append_sid("{$phpbb_root_path}index.{$phpEx}"));
			trigger_error($message);
		}

		// Check if is already refused
		if ($row['approved'] == SN_RELATIONSHIP_REFUSED)
		{
			$message = ($row['family'] == 0) ? $user->lang['SN_UP_APPROVE_RELATION_ERROR_REFUSED'] : $user->lang['SN_UP_APPROVE_FAMILY_ERROR_REFUSED'];
			meta_refresh(3, append_sid("{$phpbb_root_path}index.{$phpEx}"));
			trigger_error($message);
		}

		$template->assign_vars(array(
			'S_APPROVE'			 => ($approve) ? true : false,
			'S_REL_AVATAR'		 => $socialnet->get_user_avatar_resized($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height'], 50),
			'S_REL_USERNAME'	 => $socialnet->get_username_string($socialnet->config['us_colour_username'], 'full', $row['user_id'], $row['username'], $row['user_colour']),
			'U_REL_PROFILE_LINK' => $socialnet->get_username_string($socialnet->config['us_colour_username'], 'profile', $row['user_id'], $row['username'], $row['user_colour']),
		));

		// Approve / Refuse relation
		if (confirm_box(true))
		{
			if ($approve)
			{
				$vice_versa = request_var('vice_versa', 0);
				$family_status_id = ($row['family']) ? request_var('family_status_id', 0) : 0;

				$sql = 'UPDATE ' . SN_FAMILY_TABLE . '
	                SET approved = ' . SN_RELATIONSHIP_APPROVED . '
	                  WHERE id = ' . $relation_id;
				$db->sql_query($sql);

				// Send notification
				if ($socialnet->is_enabled('notify'))
				{
					$link = "ucp.{$phpEx}?i=socialnet&amp;mode=module_profile_relations";

					if ($row['family'])
					{
						if ($vice_versa && $family_status_id)
						{
							$socialnet->notify->add(SN_NTF_RELATION, $row['user_id'], array('text' => 'SN_NTF_FAMILY_VICEVERSA', 'user' => $user->data['username'], 'link' => $link));
						}
						else
						{
							$socialnet->notify->add(SN_NTF_RELATION, $row['user_id'], array('text' => 'SN_NTF_FAMILY_APPROVED', 'user' => $user->data['username'], 'link' => $link));
						}
					}
					else
					{
						if ($vice_versa)
						{
							$socialnet->notify->add(SN_NTF_RELATION, $row['user_id'], array('text' => 'SN_NTF_RELATIONSHIP_VICEVERSA', 'user' => $user->data['username'], 'link' => $link));
						}
						else
						{
							$socialnet->notify->add(SN_NTF_RELATION, $row['user_id'], array('text' => 'SN_NTF_RELATIONSHIP_APPROVED', 'user' => $user->data['username'], 'link' => $link));
						}
					}
				}

				if ($row['family'])
				{
					$changed['family'] = $row['relative_user_id'];
					$socialnet->record_entry($row['user_id'], $row['status_id'], SN_TYPE_NEW_FAMILY, $changed);

					// Add vice versa family
					if ($vice_versa && $family_status_id)
					{
						$sql = 'INSERT INTO ' . SN_FAMILY_TABLE . ' (user_id, relative_user_id, status_id, approved, anniversary, family)
	                    VALUES (' . $user->data['user_id'] . ', ' . $row['user_id'] . ', ' . $family_status_id . ', ' . SN_RELATIONSHIP_APPROVED . ', "", 1)';
						$db->sql_query($sql);

						$changed['family'] = $row['user_id'];
						$socialnet->record_entry($user->data['user_id'], $family_status_id, SN_TYPE_NEW_FAMILY, $changed);
					}
				}
				else
				{
					$changed['relationship'] = $user->data['user_id'];
					$socialnet->record_entry($row['user_id'], $row['status_id'], SN_TYPE_NEW_RELATIONSHIP, $changed);

					// Add vice versa relation
					if ($vice_versa)
					{
						$sql = 'INSERT INTO ' . SN_FAMILY_TABLE . ' (user_id, relative_user_id, status_id, approved, anniversary, family)
	                    VALUES (' . $user->data['user_id'] . ', ' . $row['user_id'] . ', ' . $row['status_id'] . ', ' . SN_RELATIONSHIP_APPROVED . ', "' . $row['anniversary'] . '", 0)';
						$db->sql_query($sql);

						$changed['relationship'] = $row['user_id'];
						$socialnet->record_entry($user->data['user_id'], $row['status_id'], SN_TYPE_NEW_RELATIONSHIP, $changed);
					}
				}
			}
			else
			{
				$sql = 'UPDATE ' . SN_FAMILY_TABLE . '
	                SET approved = ' . SN_RELATIONSHIP_REFUSED . '
	                  WHERE id = ' . $relation_id;
				$db->sql_query($sql);

				// Send notification
				if ($socialnet->is_enabled('notify'))
				{
					$link = "ucp.{$phpEx}?i=socialnet&amp;mode=module_profile_relations";

					if ($row['family'])
					{
						$socialnet->notify->add(SN_NTF_RELATION, $row['user_id'], array('text' => 'SN_NTF_FAMILY_REFUSED', 'user' => $user->data['username'], 'link' => $link));
					}
					else
					{
						$socialnet->notify->add(SN_NTF_RELATION, $row['user_id'], array('text' => 'SN_NTF_RELATIONSHIP_REFUSED', 'user' => $user->data['username'], 'link' => $link));
					}
				}
			}
		}
		else
		{
			if ($approve)
			{
				if ($row['family'])
				{
					$template->assign_vars(array(
						'S_REL_STATUS'						 => $socialnet->family_status($row['status_id']),
						'L_SN_UP_APPROVE_VICE_VERSA'		 => sprintf($user->lang['SN_UP_APPROVE_FAMILY_VICE_VERSA'], $row['username']),
						'S_FAMILY'							 => true,
						'L_SN_UP_APPROVE_FAMILY_USERNAME'	 => sprintf($user->lang['SN_UP_APPROVE_FAMILY_USERNAME'], $row['username']),
						'S_STATUS_OPTIONS'					 => $socialnet->return_family($row['status_id'], $row['sex']),
					));
				}
				else
				{
					if ($row['anniversary'])
					{
						$relationship_arr = array_map('intval', explode('-', $row['anniversary']));
						$relationship_anniversary = $user->format_date(gmmktime(0, 0, -$user->timezone, (int) $relationship_arr[1], (int) $relationship_arr[0], (int) $relationship_arr[2]), '|j. F Y|');
					}

					$template->assign_vars(array(
						'S_REL_STATUS'				 => $socialnet->relationship_status($row['status_id'], true),
						'S_REL_ANNIVERSARY'			 => ($row['anniversary']) ? $relationship_anniversary : '',
						'L_SN_UP_APPROVE_VICE_VERSA' => $user->lang['SN_UP_APPROVE_RELATION_VICE_VERSA'],
					));
				}

				$s_hidden_fields = '';

				$message = ($row['family']) ? $user->lang['SN_UP_APPROVE_FAMILY_CONFIRM'] : $user->lang['SN_UP_APPROVE_RELATION_CONFIRM'];
				confirm_box(false, $message, $s_hidden_fields, 'socialnet/confirm_relation_body.html');
			}
			else
			{
				$s_hidden_fields = '';

				$message = ($row['family']) ? $user->lang['SN_UP_REFUSE_FAMILY_CONFIRM'] : $user->lang['SN_UP_REFUSE_RELATION_CONFIRM'];
				confirm_box(false, $message, $s_hidden_fields, 'socialnet/confirm_relation_body.html');
			}
		}
		$icat = request_var('icat',0);

		redirect(append_sid("{$phpbb_root_path}ucp.{$phpEx}", "i=socialnet".(($icat==0)?"&amp;icat={$icat}":"")."&amp;mode=module_profile_relations"));
	}

	function delete_relation()
	{
		global $db, $user, $phpbb_root_path, $phpEx;

		$relation_id = request_var('id', 0);
		$icat = request_var('icat',0);

		if (confirm_box(true))
		{
			$sql = 'DELETE FROM ' . SN_FAMILY_TABLE . '
		            WHERE id = ' . $relation_id;
			$db->sql_query($sql);
		}
		else
		{
			// select data for message
			$sql = 'SELECT u.username, f.family
                FROM ' . SN_FAMILY_TABLE . ' f
						      LEFT JOIN ' . USERS_TABLE . ' u
							      ON u.user_id = f.relative_user_id
					     	WHERE f.id = ' . $relation_id;
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);

			$message = ($row['family']) ? sprintf($user->lang['SN_UP_DELETE_FAMILY_CONFIRM'], $row['username']) : $user->lang['SN_UP_DELETE_RELATIONSHIP_CONFIRM'];

			confirm_box(false, $message);
		}

		redirect(append_sid("{$phpbb_root_path}ucp.{$phpEx}", "i=socialnet".(($icat!=0)?"&amp;icat={$icat}":"")."&amp;mode=module_profile_relations"));
	}
}

?>