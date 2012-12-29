<?php

class myeee
{
	var $socialnet = null;

	function myeee($socialnet)
	{
		$this->socialnet = $socialnet;

		$socialnet->hook->add_action('sn.copyright_append', array($this, '_myeeee'));
	}

	function direct_access()
	{
		die('heloo');
	}

	function install()
	{

	}

	function uninstall()
	{

	}

	function _myeeee($string)
	{
		return array(
	 		'additional_copy'	=> $string . '<br /> MY COPYYYY!',
		);
	}
}