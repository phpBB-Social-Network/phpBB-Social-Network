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
 * Offline compilation 
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
		global $phpbb_root_path;

		if ($this->script == '')
		{
			return;
		}

		$exec_cmd = 'java -jar ' . $phpbb_root_path . '../dev-js/closure/compiler.jar ' . $this->filename . ' --compilation_level ' . $this->optimalization;
		$this->compiled = exec($exec_cmd);
		$this->putToCache($this->filename);
		$this->script = '';
	}
}
