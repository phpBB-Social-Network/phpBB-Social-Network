<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require( 'closure_interface.php');

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
