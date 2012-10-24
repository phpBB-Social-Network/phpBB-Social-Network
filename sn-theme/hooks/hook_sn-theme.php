<?php
/*
 * @version 1.1.0
 * @author Culprit
 * 
 * SN theme compiler
 * Compile theme for SN from LESS files
 */

/**
 * is_modified
 * 
 * Function control is some of less file was modified
 * @version 1.1.0
 * @author Culprit
 * @param string $file_path Path to less files that whould be compiled
 * @return boolean Return true if files modified else false
 */
function is_modified($file_path, $check_file)
{
	print $check_file . ' ' . ( file_exists($check_file) ? 'existuje' : 'neexistuje') . '<hr />';
	if (!file_exists($check_file))
	{
		$mFiles = array();
	}
	else
	{
		$mFiles = unserialize(file_get_contents($check_file));
	}

	print_r($mFiles);

	$files = glob($file_path . '*.less');

	if (empty($files))
	{
		return false;
	}

	foreach ($files as $filename)
	{
		print $filename . ' ==' . (isset($mFiles[$filename]) ? 'EXISTUJE' : 'NEEXISTUJE');
		$ftime = filemtime($filename);
		if (isset($mFiles[$filename]))
		{
			print 'New file: ' . $filename . '<br />';
			$compile = true;
			$mFiles[$filename] = $ftime;
		}
		else if ($ftime > $mFiles[$filename])
		{
			print $filename . ' -> ' . $ftime . '<br />';
			$compile = true;
			$mFiles[$filename] = $ftime;
		}
	}
	print $compile ? 'ano' : 'me';

	file_put_contents($check_file, serialize($mFiles));

	return $compile;
}

/**
 * sn_theme_compiler
 * 
 * Compile LESS files into one CSS file
 * Requires: {@link http://leafo.net/lessphp/ lessPHP}, {@link http://csstidy.sourceforge.net/usage.php CSSTidy}
 * Settings for lessPHP is indent
 * @global object $user phpBB user object
 * @global object $template phpBB template object
 * @global string $phpbb_root_path phpBB root path string
 * @global string $phpEx php extension
 * @return void
 */
function sn_theme_compiler()
{
	global $user, $template, $phpbb_root_path, $phpEx, $socialnet;


	// Exit the compiler when is not whole page loaded
	if (defined('SN_LOADER') || !isset($socialnet) || !$socialnet->useTemplateHook)
	{
		return;
	}

	if (defined('DEBUG') || defined('DEBUG_EXTRA'))
	{
		error_reporting(E_ALL & ~E_STRICT);
	}

	$compiler_path = $phpbb_root_path . '../sn-theme/';

	$file_path = $compiler_path . $user->theme['theme_path'] . '/';

	$check_file = $file_path . '.file_check';
	// Exist when LESS files are not modified
	if (!is_modified($file_path, $check_file))
	{
		return;
	}

	$less_path = $compiler_path . 'lessphp/';

	// Exit when less & csstidy is not present
	if (!file_exists($less_path . 'lessc.inc.' . $phpEx) || !file_exists($less_path . 'class.csstidy.' . $phpEx))
	{
		return;
	}

	$less_input_file = $file_path . 'socialnet.less';
	$less_output_file = $phpbb_root_path . 'styles/' . $user->theme['theme_path'] . '/theme/socialnet.css';

	// Delete output file if exist
	if (file_exists($less_output_file))
	{
		unlink($less_output_file);
	}

	/**
	 * @ignore Include lessPHP file
	 */
	include_once($less_path . 'lessc.inc.' . $phpEx);
	/**
	 * @ignore Include CSSTidy file
	 */
	include_once($less_path . 'class.csstidy.' . $phpEx);

	error_reporting(E_ALL & ~E_STRICT);
	try
	{
		$lessc = new lessc($less_input_file);
		$lessc->setFormatter('classic');
		$compile = $lessc->parse();
		unset($lessc);

		// Set the CSSTidy to be CSS optimized such as phpBB
		$css = new csstidy();

		$css->set_cfg('remove_last_;', FALSE);
		$css->set_cfg('allow_html_in_templates', FALSE);
		$css->set_cfg('template', 'low');
		$css->set_cfg('compress_colors', TRUE);
		$css->set_cfg('sort_properties', TRUE);
		$css->set_cfg('sort_selectors', false);
		$css->set_cfg('compress_font-weight', false);

		// Optimize the Cascade Style Sheet
		$css->parse($compile);
		// Take the plain CSS not the HTML output
		$_out = $css->print->plain();
		// Because in some way is removed " from URLs with preg_replace I get the " back
		$_out = preg_replace('/url\(([^)]+)\)/si', 'url("\1")', $_out);
		$_out = preg_replace("/:([^;\n]+);/si", ': \1;', $_out);
		$_out = preg_replace("/\n{\n/si", " {\n", $_out);
		file_put_contents($less_output_file, $_out);
		$message = 'CSS file compiled succesfully ';
		$class = 'info';
	}
	catch (exception $ex)
	{
		$message = $ex->getMessage();
		$class = 'alert';
		unlink($check_file);
	}

	$s_message = file_get_contents($compiler_path . 'error_body.html');
	$s_message = str_replace(array('{MESSAGE}', '{CLASS}', '{BKG_COLOR}'), array($message, $class, $class == 'info' ? '000' : '600'), $s_message);

	if (!isset($template->_tpldata['.'][0]['TRANSLATION_INFO']))
	{
		$template->assign_var('TRANSLATION_INFO', 'INFO');
	}
	$template->_tpldata['.'][0]['TRANSLATION_INFO'] .= $s_message;
}

// Register hook
$phpbb_hook->register('exit_handler', 'sn_theme_compiler');
