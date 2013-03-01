<?php
/*
 * JS-PACKER
 */

if (!defined('IN_PHPBB') || defined('IN_ADMIN'))
{
	exit;
}



function sn_js_minify()
{
	global $phpbb_root_path;

	if ( defined('SN_LOADER'))
	{
		return;
	}
	
	$JSpacker_path = $phpbb_root_path . '../dev-js/';

	require_once $JSpacker_path . 'closureapi.php';

	$closure = new closureCompiler();
	$closure->setOptimalization('SIMPLE_OPTIMIZATIONS');

	$files = glob($JSpacker_path . 'js/*.js');
	foreach ($files as $file)
	{
		$new_file = str_replace(array('.js', $JSpacker_path . 'js/'), array('.min.js', $phpbb_root_path . 'socialnet/js/'), $file);
		$closure->loadFile($file);
		$closure->compile();
		$closure->saveFile($new_file);
	}
}


$phpbb_hook->register('phpbb_user_session_handler', 'sn_js_minify');