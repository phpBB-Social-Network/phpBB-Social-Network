<?php
/*
 * JS-PACKER
 */

if ( !defined( 'IN_PHPBB'))
{
	exit();
}

function sn_js_minify()
{
	global $phpbb_root_path;

	if ( defined('SN_LOADER'))
	{
		return;
	}
	
	$JSpacker_path = $phpbb_root_path . '../js-packer/';

	require_once $JSpacker_path . 'closureapi.php';

	$closure = new closureCompiler();
	$closure->setOptimalization('WHITESPACE_ONLY');

	$files = glob($phpbb_root_path . 'socialnet/js/m.*.js');

	foreach ($files as $idx => $file)
	{
		$closure->loadfile($file);
		$closure->compile();
		$closure->saveFile(str_replace(array('.js', $phpbb_root_path . 'socialnet/js/'), array('.min.js', $JSpacker_path . 'js/'), $file));
	}
}


$phpbb_hook->register('phpbb_user_session_handler', 'sn_js_minify');
