<?php

if (!defined('IN_PHPBB') || !defined('SOCIALNET_INSTALLED'))
{
	exit();
}

class addon_example
{
	var $master = null;

	function addon_example($socialnet)
	{
		global $user;

		//$user->add_lang('mods/sn_addon_example');

		$this->master = $socialnet;
	}

	function example1($script, $block)
	{
		global $template, $db, $user;

		$user->add_lang('ucp');
		$date = $user->format_date(time());

		$user->lang['DATE'] = 'Date';

		$template->assign_vars(array(

			'EXAMPLE'	 => 'This is example addon',
			'DATE'		 => $date,
		));
	}

	function install()
	{
		global $user;
		return array(
			'name'	 => 'First AddOn Example',
			'addon'	 => array(
				'example1'	 => 'Example AddOn Function 1'
			),
		);
	}
}
?>