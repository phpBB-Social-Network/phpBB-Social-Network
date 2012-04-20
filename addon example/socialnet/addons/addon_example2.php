<?php

if (!defined('IN_PHPBB') || !defined('SOCIALNET_INSTALLED'))
{
	exit();
}

class addon_example2
{
	var $master = null;

	function addon_example2($socialnet)
	{
		global $user;

		//$user->add_lang('mods/sn_addon_example');

		$this->master = $socialnet;
	}

	function example_1($placeholder = '')
	{
		global $template, $db, $user;

		$template->assign_vars(array(
			'EXAMPLE'	 => 'This is example 2 addon function 1',
		));
	}
	
	function example_2($placeholder = '')
	{
		global $template, $db, $user;

		$template->assign_vars(array(
			'EXAMPLE'	 => 'This is example 2 addon function 2',
		));
	}

	function install()
	{
		global $user;
		return array(
			'name'	 => 'This is an example AddOn Two',
			'addon'	 => array(
				'example_1'	 => 'Example Two AddOn Function 1',
				'example_2'	 => 'Example Second AddOn Function 2',

			),
		);
	}
}
?>