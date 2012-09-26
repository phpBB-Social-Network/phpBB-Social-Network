<?php

$phpbb_hook->register(array('template', 'display'), 'compile_SN_style');

function compile_SN_style()
{
	global $user, $template;

	if ( defined('SN_LOADER'))
	{
		return;
	}
	
	$theme_path = str_replace('/template', '/theme', $template->root);
	$ff = pathinfo(__FILE__);
	$file_path = $ff['dirname'];

	$file_check = $theme_path.'/socialnet_css/.'.@$user->theme['theme_path'].'.last.check';
	
	$mfiles = unserialize(@file_get_contents( $file_check));
	
	$mTime = max($mfiles,0);
	
	$dir = @opendir($theme_path . '/socialnet_css/');
	
	if ( !$dir){
		return;
	}
	$check = '';
	
	$compile = false;
	$nfiles = array();
	while( $file = readdir($dir))
	{
		if ( $file == '.' || $file == '..')
		{
			continue;
		}
		
		$cFile = $theme_path . '/socialnet_css/'.$file;
		if( is_dir($cFile))
		{
			continue;
		}
		
		$pInfo = pathinfo($cFile);
		if ($pInfo['extension'] != 'less')
		{
			continue;
		}
		
		$lTime = filemtime($cFile);
		$nfiles[$file] = $lTime;
		if ( $lTime > @$mfiles[$file])
		{
			$compile = true;
		}
		
	}
	
	
	if ( !$compile)
	{
		//return;
	}
	file_put_contents($file_check, serialize($nfiles));
	
	$input_file = $theme_path . '/socialnet_css/socialnet.less';
	$output_file = $theme_path . '/socialnet.css';
	
	
	
	if (file_exists($file_path . '/lessc.inc.php'))
	{
		require_once($file_path . '/lessc.inc.php');
		
		if (file_exists($output_file))
		{
			unlink($output_file);
		}
		
		try
		{
			$lessc = new lessc($input_file);
			$lessc->setFormatter('indent');
			$compile = $lessc->parse();

			unset($lessc);
			
			include_once($file_path . '/class.csstidy.php');
			
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
			$_out = preg_replace("/:([^;\n]+);/si",': \1;', $_out);
			$_out = preg_replace("/\n{\n/si"," {\n", $_out);
			file_put_contents($output_file, $_out);
		}
		catch (exception $ex)
		{
			$template->_tpldata['.'][0]['TRANSLATION_INFO'] .= "
<div id=\"lesscFatal\" class=\"ui-state-error\" style=\"display:none;font-size: 1.2em;\" title=\"Chyba kompilace CSS :: LESSC\">{$ex->getMessage()}</div>
<script type=\"text/javascript\">
		jQuery(document).ready(function($){
			$('#lesscFatal').dialog({
				modal: true,
				width: '60%',
				buttons: {
					Close: function() {
						$( this ).dialog( 'close' );
					}
				}
			});
			
			$('#ui-dialog-title-lesscFatal').css({fontSize:'1.4em'});
			$('[aria-labelledby=ui-dialog-title-lesscFatal]').addClass('ui-state-error');
			$('.ui-widget-overlay').css({background:'#660000 none no-repeat 0 0'});
			
		});
</script>
			";

			//	exit('lessc fatal error:<br />' . $ex->getMessage());

		}
			
	}
}

?>