<?php
/**
 * SN Core Comments
 *
 * @author		Culprit <jankalach@gmail.com>
 * @author		Kamahl <kamahl19@gmail.com>
 *
 * @package		phpBB Social Network
 * @version		0.7.1
 * @since		0.6.2
 * @copyright		(c) phpBB Social Network Team 2010-2012 http://phpbbsocialnetwork.com
 * @license		http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

/**
 * @ignore
 */
if (!defined('SOCIALNET_INSTALLED') || !defined('IN_PHPBB'))
{
	return;
}

/**
 * Core Comments class
 *
 * @package phpBB-Social-Network
 */
class sn_core_comments
{
	/**
	 * Master object
	 *
	 * @var object $p_master
	 */
	var $p_master = null;

	/**
	 * Names of loaded modules
	 *
	 * @var array $modulesName
	 */
	var $modulesName = array();

	/**
	 * IDs of loaded modules
	 *
	 * @var array $modulesID
	 */
	var $modulesID = array();

	/**
	 * Current time
	 *
	 * @var int $time
	 */
	var $time = 0;

	/**
	 * Comments template file
	 *
	 * @var string $commentTemplate
	 */
	var $commentTemplate = 'socialnet/block_comments.html';

	/**
	 * Constructor
	 *
	 * Loads existing modules using cache
	 *
	 * @todo remove $cache from globals, it is not used
	 *
	 * @access	public
	 *
	 * @param 	object	$p_master
	 *
	 * @global	object	$template		phpBB template object
	 * @global	string	$phpbb_root_path	phpBB root path string
	 * @global	object	$user			phpBB user object
	 * @global	array	$config			phpBB configuration array
	 */
	public function sn_core_comments(&$p_master)
	{
		global $cache, $template, $phpbb_root_path, $user, $config;

		$this->p_master = $p_master;
		$this->_loadModules();
		$this->time = time();

		if (!isset($template->_tpldata['.'][0]['T_IMAGESET_PATH']))
		{
			$t_imaset_path = "{$phpbb_root_path}styles/" . $user->theme['imageset_path'] . '/imageset';
			$_phpbb_root_path = str_replace('\\', '/', $phpbb_root_path);
			$_script_path = str_replace('//', '/', str_replace('\\', '/', $config['script_path']) . '/');
			$t_imaset_path = preg_replace('#^' . preg_quote($_phpbb_root_path) . '#si', $_script_path, $t_imaset_path);
			$template->assign_var('T_IMAGESET_PATH', $t_imaset_path);
		}
	}

	/**
	 * Create a comment
	 *
	 * @access	public
	 *
	 * @param 	string|array	$module		module name
	 * @param 	int		$module_id	ID of module
	 * @param 	int		$poster		ID of user who posts new comment
	 * @param 	string		$text		Text of new comment
	 *
	 * @global	object		$db		phpBB database object
	 *
	 * @return	int		Comment ID
	 */
	public function add($module, $module_id, $poster, $text)
	{
		global $db;

		if ($text == '' || $module_id == 0)
		{
			return false;
		}

		$uid = $bitfield = $flags = '';

		generate_text_for_storage($text, $uid, $bitfield, $flags, $this->p_master->allow_bbcode, $this->p_master->allow_urls, $this->p_master->allow_smilies);

		$noBBCodeText = $text;
		strip_bbcode($noBBCodeText);
		if ($noBBCodeText == '')
		{
			return false;
		}

		$cmt_module = $this->_moduleID($module);

		$sql = "INSERT INTO " . SN_COMMENTS_TABLE . " (cmt_module, cmt_mid, cmt_time, cmt_poster, cmt_text, bbcode_bitfield, bbcode_uid)
							VALUES ({$cmt_module}, {$module_id}, {$this->time}, {$poster}, '" . $db->sql_escape($text) . "', '{$bitfield}','{$uid}')";
		$db->sql_query($sql);

		$sql = "SELECT cmt_id
							FROM " . SN_COMMENTS_TABLE . "
								WHERE cmt_module = {$cmt_module}
									AND cmt_mid = {$module_id}
									AND cmt_time = {$this->time}
									AND cmt_poster = {$poster}";
		$rs = $db->sql_query($sql);
		$cmt_id = $db->sql_fetchfield('cmt_id');
		$db->sql_freeresult($rs);

		return $cmt_id;
	}

	/**
	 * Delete a comment
	 *
	 * @access	public
	 *
	 * @param 	string|array	$module		module name
	 * @param 	int		$cmt_id		ID of comment or module
	 * @param 	bool		$cmt_one	defines, if comment to be deleted will be identified according to comment_id or comment_module_id
	 *
	 * @global	object		$db		phpBB database object
	 * @global	object		$auth		phpBB auth object
	 */
	public function del($module, $cmt_id, $cmt_one = true)
	{
		global $db, $auth;

		$cmt_module = $this->_moduleID($module);

		if ($cmt_one)
		{
			$sql = "DELETE FROM " . SN_COMMENTS_TABLE . " WHERE cmt_module = {$cmt_module} AND cmt_id = {$cmt_id}";
		}
		else
		{
			$sql = "DELETE FROM " . SN_COMMENTS_TABLE . " WHERE cmt_module = {$cmt_module} AND cmt_mid = {$cmt_id}";
		}
		$db->sql_query($sql);
	}

	/**
	 * Get a comment
	 *
	 * @access	public
	 *
	 * @param 	string|array	$module		module name
	 * @param 	string		$classPrefix	prefix for CSS class
	 * @param 	int		$module_id	ID of module
	 * @param 	int		$cmt_id		ID of comment
	 * @param 	int|bool	$limit		limit of comments to retrieve
	 * @param 	bool		$only_comments	defines, if other stuff around comments will be retrieved as well
	 *
	 * @global	object		$db		phpBB database object
	 * @global	array		$config		phpBB configuration array
	 * @global	object		$user		phpBB user object
	 * @global	object		$template	phpBB template object
	 * @global	object		$auth		phpBB auth object
	 *
	 * @return  	array		comments and information about more comments
	 * @property	int		more information, if more comments exist
	 * @property	string		comments HTML output of comments
	 */
	public function get($module, $classPrefix, $module_id, $cmt_id = 0, $limit = false, $only_comments = false)
	{
		global $db, $config, $user, $template, $auth;

		$cmt_module = $this->_moduleID($module);

		$sql_ary = array(
			'FROM'		 => array(SN_COMMENTS_TABLE => 'cmt', USERS_TABLE => 'u', ),
			'WHERE'		 => 'cmt.cmt_poster = u.user_id',
			'ORDER_BY'	 => 'cmt.cmt_time ' . ($config['userstatus_comments_load_last'] == 0 ? 'ASC' : 'DESC')
		);

		$order = ($config['userstatus_comments_load_last'] == 0);

		$sql_where = '';
		if ($limit == false && $cmt_id != 0)
		{
			$sql_where = "AND cmt_id = '{$cmt_id}'";
			$limit = 1;
		}
		else
		{
			if ($cmt_id != 0)
			{
				$sql = "SELECT cmt_time FROM " . SN_COMMENTS_TABLE . " WHERE cmt_id = {$cmt_id}";
				$rs = $db->sql_query($sql);
				$cmt_time = $db->sql_fetchfield('cmt_time');
				$db->sql_freeresult($rs);

				$sql_where = "AND cmt_time " . ($order ? '>' : '<') . " '{$cmt_time}'";
			}
		}

		$sql = "SELECT cmt.*, u.username, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height, u.user_colour
				FROM " . SN_COMMENTS_TABLE . " AS cmt LEFT OUTER JOIN " . USERS_TABLE . " AS u ON cmt.cmt_poster = u.user_id
				WHERE cmt_module = '{$cmt_module}' AND cmt_mid = '{$module_id}' {$sql_where}
				ORDER BY cmt.cmt_time " . ($order ? 'ASC' : 'DESC');
		$rs = $db->sql_query($sql, $limit + 1);
		$rowset = $db->sql_fetchrowset($rs);
		$db->sql_freeresult($rs);

		$cmt_count = count($rowset);

		$cmt_more = ($cmt_count - $limit);
		if ($cmt_more < 0)
		{
			$cmt_more = 0;
		}

		if ($order)
		{
			$cmt_idx_start = 0;
		}
		else
		{
			$rowset = array_reverse($rowset);
			$cmt_idx_start = $cmt_count - $limit;
			if ($cmt_idx_start < 0)
			{
				$limit = $limit + $cmt_idx_start;
				$cmt_idx_start = 0;
			}
			else
			{
				$limit = $cmt_count;
			}
		}

		for ($i = $cmt_idx_start; $i < $limit && isset($rowset[$i]); $i++)
		{
			$row = $rowset[$i];
			$avatar_img = $this->p_master->get_user_avatar_resized($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height'], 30);

			if ($row['cmt_text'] != '0')
			{
				$comment_text_format = generate_text_for_display($row['cmt_text'], $row['bbcode_uid'], $row['bbcode_bitfield'], $this->p_master->bbCodeFlags);
			}
			else
			{
				$comment_text_format = $row['cmt_text'];
			}

			$template->assign_block_vars('comment', array(
				'COMMENT_ID'				 => $row['cmt_id'],
				'POSTER_USERNAME'			 => $this->p_master->get_username_string($this->p_master->config['us_colour_username'], 'no_profile', $row['cmt_poster'], $row['username'], $row['user_colour']),
				'POSTER_USERNAME_NO_COLOR'	 => $row['username'],
				'U_POSTER_PROFILE'			 => $this->p_master->get_username_string($this->p_master->config['us_colour_username'], 'profile', $row['cmt_poster'], $row['username'], $row['user_colour']),
				'POSTER_AVATAR'				 => $avatar_img,
				'TIME'						 => $this->p_master->time_ago($row['cmt_time']),
				'TEXT'						 => $comment_text_format,
				'DELETE_COMMENT'			 => ($auth->acl_get('a_') || ($row['cmt_poster'] == $user->data['user_id'])) ? true : false,
			));
		}

		$template->assign_vars(array(
			'SN_COMMENTS_MORE'					 => $cmt_more,
			'SN_US_LOAD_MORE_COMMENTS'			 => ($cmt_more == 1) ? $user->lang['SN_US_LOAD_MORE_COMMENT'] : $user->lang['SN_US_LOAD_MORE_COMMENTS'],
			'B_LOAD_FIRST_USERSTATUS_COMMENTS'	 => isset($config['userstatus_comments_load_last']) ? $config['userstatus_comments_load_last'] : 1,
			'SN_COMMENT_MODULE'					 => $module,
			'SN_CLASS_PREFIX'					 => $classPrefix,
			'SN_CLASS_PREFIX_DOT'				 => preg_replace('/[^a-z0-9]/si', '.', $classPrefix),
			'SN_COMMENT_MODULE_ID'				 => $module_id,
			'B_SN_NOT_ONLY_COMMENT'				 => $only_comments,
		));

		$template->set_filenames(array('comments' => $this->commentTemplate));

		$content = $this->p_master->get_page('comments');

		$template->destroy_block_vars('comment');
		return array('more' => $cmt_more, 'comments' => $content);
	}

	/**
	 * Get a field of a comment
	 *
	 * @access	public
	 *
	 * @param 	string|array	$module	module name
	 * @param 	int		$cmt_id	ID of comment
	 * @param 	string		$field	field to be retrieved
	 *
	 * @global	object		$db	phpBB database object
	 *
	 * @return	string		value of field of the comment
	 */
	public function getField($module, $cmt_id, $field)
	{
		global $db;

		$cmt_module = $this->_moduleID($module);

		$sql = "SELECT cmt_{$field} FROM " . SN_COMMENTS_TABLE . " WHERE cmt_module = {$cmt_module} AND cmt_id = {$cmt_id}";
		$rs = $db->sql_query($sql);
		$field = $db->sql_fetchfield("cmt_{$field}");
		$db->sql_freeresult($rs);

		return $field;
	}

	/**
	 * Get posters? See todo!
	 *
	 * @todo add all descriptions
	 *
	 * @access	public
	 *
	 * @param 	string|array	$module		module name
	 * @param 	int		$cmt_mid	ID of module of comment
	 * @param 	boot		$with_me
	 *
	 * @global	object		$db		phpBB database object
	 * @global	object		$user		phpBB user object
	 *
	 * @return	array
	 */
	public function getPosters($module, $cmt_mid, $with_me = false)
	{
		global $db, $user;

		$cmt_module = $this->_moduleID($module);

		$sql = "SELECT DISTINCT cmt_poster FROM " . SN_COMMENTS_TABLE . " WHERE cmt_module = {$cmt_module} AND cmt_mid = {$cmt_mid}";
		$rs = $db->sql_query($sql);
		$rowset = $db->sql_fetchrowset($rs);
		$db->sql_freeresult($rs);

		$return = array();
		for ($i = 0; isset($rowset[$i]); $i++)
		{
			$return[] = $rowset[$i]['cmt_poster'];
		}

		if (!$with_me)
		{
			$return = array_diff($return, array($user->data['user_id']));
		}

		return $return;
	}

	/**
	 * Compiles module name
	 *
	 * @access	private
	 *
	 * @param 	string|array	$module	module name
	 *
	 * @return	string		module name
	 */
	function _moduleName($module)
	{
		if (is_array($module))
		{
			$moduleName = implode('_', $module);
		}
		else
		{
			$moduleName = $module;
		}

		return $moduleName;
	}

	/**
	 * Returns module ID according to module name
	 *
	 * @access	private
	 *
	 * @param 	string|array	$module	module name
	 *
	 * @return	string		module ID
	 */
	function _moduleID($module)
	{
		$moduleName = $this->_moduleName($module);
		if ( !isset($this->modulesName[$moduleName]))
		{
			$this->_addModule($module);
		}
		return $this->modulesName[$moduleName];
	}

	/**
	 * Loads existing modules from DB
	 *
	 * @access	private
	 *
	 * @param 	bool	$force	defines, if load modules by force
	 *
	 * @global	object	$cache	phpBB cache object
	 * @global	object	$db	phpBB database object
	 */
	private function _loadModules($force = false)
	{
		global $cache, $db;

		$cacheName = 'sn_comment_modules';
		$modules = array('ID' => array(), 'NAMES' => array());
		if (!$force)
		{
			$modules = $cache->get($cacheName);
		}
		if (empty($modules['ID']) || $force)
		{
			$modules = array('ID' => array(), 'NAMES' => array());
			$rowset = array();

			//READ MODULES FROM DB
			$sql = "SELECT cmtmd_id, cmtmd_name FROM " . SN_COMMENTS_MODULES_TABLE;
			$rs = $db->sql_query($sql);
			$rowset = $db->sql_fetchrowset($rs);
			$db->sql_freeresult($rs);
			$rsCount = count($rowset);
			for ($i = 0; $i < $rsCount && isset($rowset[$i]); $i++)
			{
				$modules['ID'][$rowset[$i]['cmtmd_id']] = $rowset[$i]['cmtmd_name'];
				$modules['NAMES'][$rowset[$i]['cmtmd_name']] = $rowset[$i]['cmtmd_id'];
			}
			$cache->put($cacheName, $modules);
		}

		$this->modulesID = $modules['ID'];
		$this->modulesName = $modules['NAMES'];
	}

	/**
	 * Creates a new comment module
	 *
	 * @access	private
	 *
	 * @param 	string	$moduleName	module name
	 *
	 * @global	object	$db		phpBB database object
	 */
	private function _addModule($moduleName)
	{
		global $db;

		$sql = "INSERT INTO " . SN_COMMENTS_MODULES_TABLE . " (cmtmd_name) VALUES ('" . $db->sql_escape($moduleName) . "')";
		$db->sql_query($sql);
		$this->_loadModules(true);
	}
}

?>