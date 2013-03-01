<?php
/**
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * @package SN JavaScript minify
 * @author Culprit <jankalach@gmail.com>
 * @version 1.0.0
 */
/**
 * @ignore
 */
require( 'closure_interface.php');

/**
 * Compile 
 * Online compilation 
 * @package SN JavaScript minify
 * @author Culprit <jankalach@gmail.com>
 * @version 1.0.0
 */
class closureCompile implements closure_int
{

	/**
	 * Compile, optimize & minify the source JS code
	 * @return void
	 */
	public function compile()
	{
		if ($this->script == '')
		{
			return;
		}

		$ch = curl_init($this->closureURL);

		$postFields = array_merge($this->postFields, array(
			'output_format'		 => $this->output,
			'compilation_level'	 => $this->optimalization,
			'js_code'			 => ''
		));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields) . urlencode($this->script));
		$this->compiled = curl_exec($ch);
		curl_close($ch);
		$this->putToCache($this->filename);
		$this->script = '';
	}
}