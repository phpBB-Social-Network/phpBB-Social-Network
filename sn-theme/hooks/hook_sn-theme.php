<?php
/*
 * @version 1.1.0
 * @author Culprit
 * 
 * SN theme compiler
 * Compile theme for SN from LESS files
 */

$phpbb_hook->register(array('template', 'display'), 'sn_theme_compiler');

/**
 * @version 1.1.0
 * @author Culprit
 * @global type $template
 * @global type $phpbb_root_path
 * @global type $phpEx
 * @param type $theme_path
 */
function is_modified($file_path)
{
	global $template, $phpbb_root_path, $phpEx;

	$check_file = $file_path . '.file_check';

	if (!file_exists($check_file))
	{
		$mfiles = array();
		$mtime = 0;
	}
	else
	{
		$mfiles = unserialize(file_get_contents($check_file));
		$mTime = max($mFiles, 0);
	}
	$files = glob($file_path . '*.less');

	if (empty($files))
	{
		return false;
	}

	$compile = false;

	foreach ($files as $filename)
	{
		$ftime = filemtime($filename);

		if ($ftime > $mTime)
		{
			$compile = true;
		}

		$mfiles[$filename] = $ftime;
	}

	file_put_contents($check_file, serialize($mfiles));

	return $compile;
}

function sn_theme_compiler()
{
	global $user, $template, $phpbb_root_path, $phpEx;

	/**
	 * Exit the compiler when is not whole page loaded
	 */
	if (defined('SN_LOADER'))
	{
		return;
	}

	$compiler_path = $phpbb_root_path . '../sn-theme/';

	$file_path = $compiler_path . $user->theme['theme_path'] . '/';

	if (!is_modified($file_path))
	{
		return;
	}

	$less_path = $compiler_path . 'lessphp/';

	if (!file_exists($less_path . 'lessc.inc.' . $phpEx) || !file_exists($less_path . 'class.csstidy.' . $phpEx))
	{
		return;
	}

	$less_input_file = $file_path . 'socialnet.less';
	$less_output_file = $phpbb_root_path . 'styles/' . $user->theme['theme_path'] . '/theme/socialnet.css';

	if (file_exists($less_output_file))
	{
		unlink($less_output_file);
	}

	include_once($less_path . 'lessc.inc.' . $phpEx);
	include_once($less_path . 'class.csstidy.' . $phpEx);

	try
	{
		$lessc = new lessc($less_input_file);
		$lessc->setFormatter('indent');
		$compile = $lessc->parse();
		unset($lessc);
		
		$css = new csstidy();

		$css->set_cfg('remove_last_;', FALSE);
		$css->set_cfg('allow_html_in_templates', FALSE);
		$css->set_cfg('template', 'low');
		$css->set_cfg('compress_colors', TRUE);
		$css->set_cfg('sort_properties', TRUE);
		$css->set_cfg('sort_selectors', false);
		$css->set_cfg('compress_font-weight', false);

		$css->parse($compile);
		$_out = $css->print->plain();
		$_out = preg_replace('/url\(([^)]+)\)/si', 'url("\1")', $_out);
		$_out = preg_replace("/:([^;\n]+);/si", ': \1;', $_out);
		$_out = preg_replace("/\n{\n/si", " {\n", $_out);
		file_put_contents($less_output_file, $_out);
	}
	catch (exception $ex)
	{
		$message = file_get_contents($compiler_path . 'error_body.html');
		$message = preg_replace('/\{ERROR_MESSAGE\}/s', $ex->getMessage(), $message);

		$template->_tpldata['.'][0]['TRANSLATION_INFO'] .= $message;
	}
}