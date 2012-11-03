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

if (defined('ADMIN_START'))
{
	$user->add_lang('mods/socialnet_acp');
}

if (!isset($config['version_socialNet']) || defined('UMIL_AUTO') || defined('IN_INSTALL'))
{
	return;
}

/**
 * @ignore
 */
define('SOCIALNET_INSTALLED', true);

/**
 * @ignore
 */
include_once($phpbb_root_path . 'socialnet/includes/socialnet.' . $phpEx);

class hookSocialNet
{

	static function start_socialNet()
	{
		global $db, $user, $socialnet, $config, $template, $phpbb_hook;

		if (defined('SOCIALNET_STARTED'))
		{
			return;
		}
		define('SOCIALNET_STARTED', true);

		$sql = "SELECT config_value FROM " . SN_CONFIG_TABLE . " WHERE config_name = 'sn_global_enable'";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$config['sn_global_enable'] = $row['config_value'];

		if ($config['sn_global_enable'] == 1)
		{
			$user->add_lang(array('posting', 'mods/socialnet'));

			$sql_user_extend = "SELECT *
								FROM " . SN_USERS_TABLE . "
								WHERE user_id = '{$user->data['user_id']}'";
			$result = $db->sql_query($sql_user_extend);

			if (!$db->sql_affectedrows($result))
			{
				$sql_arr = array(
					'user_id'					 => $user->data['user_id'],
					'user_status'				 => '',
					'user_im_sound'				 => 1,
					'user_im_soundname'			 => 'IM_New-message-1.mp3',
					'user_im_online'			 => 1,
					'user_zebra_alert_friend'	 => 1,
					'user_note'					 => '',
					'languages'					 => '',
					'about_me'					 => '',
					'employer'					 => '',
					'university'				 => '',
					'high_school'				 => '',
					'religion'					 => '',
					'political_views'			 => '',
					'quotations'				 => '',
					'music'						 => '',
					'books'						 => '',
					'movies'					 => '',
					'games'						 => '',
					'foods'						 => '',
					'sports'					 => '',
					'sport_teams'				 => '',
					'activities'				 => '',
					'profile_last_change'		 => 0,
				);

				$sql_insert = "INSERT INTO " . SN_USERS_TABLE . $db->sql_build_array('INSERT', $sql_arr);

				$db->sql_query($sql_insert);
				$result = $db->sql_query($sql_user_extend);

				$sql = "INSERT INTO " . SN_FMS_GROUPS_TABLE . " (fms_gid,user_id,fms_name,fms_clean,fms_collapse) VALUES (0, {$user->data['user_id']}, '---', '---',0)";
				$db->sql_return_on_error(true);
				$db->sql_query($sql);
				$db->sql_return_on_error(false);
			}

			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			$user->data = array_merge($user->data, $row);

			$socialnet = new socialnet();

			if (!defined('ADMIN_START'))
			{
				$socialnet->start_modules();
			}
		}
	}

	static function template_display($phpbb_hook, $handle, $include_once = true)
	{
		global $socialnet;

		$return = $phpbb_hook->previous_hook_result(array('template', 'display'));
		if (method_exists($socialnet, 'hook_template'))
		{
			$socialnet->hook_template();
		}
	}
}
/**
 * Register all necessary hooks
 */
$phpbb_hook->register('phpbb_user_session_handler', array('hookSocialNet', 'start_socialNet'));
$phpbb_hook->register(array('template', 'display'), array('hookSocialNet', 'template_display'));

