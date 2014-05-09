<?php
/**
 * closure Compiler
 * Minify JS files trouth Google Closure API
 * @see https://developers.google.com/closure/compiler/
 * @author Culprit <jankalach@gmail.com>
 * @version 1.0.0
 * @package SN JavaScript minify
 */
/**
 * You can use online Closure compiler
 * @ignore
 * @problem Count compiled files is limited
 */
// require( 'closure/closure_online.php');

/**
 * You can use offline Closure compiler
 * @ignore
 * @require java and PHP exec command
 */
require( 'closure/closure_offline.php');

/**
 * closureCompiler
 * PHP Class for closure compiler
 * @package SN JavaScript minify
 * @author Culprit <jankalach@gmail.com>
 * @version 1.0.0
 */
class closureCompiler extends closureCompile
{
	var $cache = array();
	var $cacheFile = 'cache/cache.php';
	var $closureURL = 'http://closure-compiler.appspot.com/compile';
	var $filename = '';
	var $script = '';
	var $compiled = '';
	var $optimalization = 'SIMPLE_OPTIMIZATIONS';
	var $availableOptimalizations = array(
		'SIMPLE_OPTIMIZATIONS',
		'WHITESPACE_ONLY',
		'ADVANCED_OPTIMIZATIONS'
	);
	var $output = 'text';
	var $availableOutput = array(
		'text',
		'json',
		'xml'
	);
	var $postFields = array(
		'output_info'		 => 'compiled_code',
		'output_format'		 => '',
		'compilation_level'	 => '',
		'js_code'			 => ''
	);

	/**
	 * constructor
	 * @access public
	 */
	public function closureCompiler()
	{
		$this->setCacheRoot();
		$this->loadCache();
	}

	private function setCacheRoot()
	{
		$this->cacheFile = __DIR__ . '/' . $this->cacheFile;
	}

	/**
	 * Load cached files
	 * @access private
	 */
	private function loadCache()
	{
		if (file_exists($this->cacheFile))
		{
			include_once($this->cacheFile);
		}
	}

	/**
	 * Save info about cached files
	 * @access private
	 */
	private function saveCache()
	{
		$cache = '';
		if (!empty($this->cache))
		{
			foreach ($this->cache as $filename => $mtime)
			{
				$cache .= "'{$filename}' => $mtime,\n";
			}
		}

		file_put_contents($this->cacheFile, "<?php\n" . '$this->cache = array(' . "\n" . $cache . ');');
	}

	/**
	 * Save current file to cache
	 * @param string $filename filename to cache
	 * @access private
	 */
	private function putToCache($filename)
	{
		$this->cache[$filename] = filemtime($filename);
		$this->saveCache();
	}

	/**
	 * Control if JS file is modified
	 * @param string $filename filename to check
	 * @return boolean
	 * @access private
	 */
	private function isModified($filename)
	{
		if (array_key_exists($filename, $this->cache))
		{
			return $this->cache[$filename] < filemtime($filename);
		}

		return true;
	}

	/**
	 * Set type of output
	 * @param string $output 
	 * @access public
	 */
	public function setOutput($output)
	{
		if (!in_array($output, $this->availableOutput))
		{
			exit('Unsuported output: ' . $optimalization . '<br />Available output is' . implode(', ', $this->availableOptimalizations));
		}
		$this->output = $output;
	}

	/**
	 * Set type of optimalization & minification
	 * @param string $optimalization
	 * @access public
	 */
	public function setOptimalization($optimalization)
	{
		if (!in_array($optimalization, $this->availableOptimalizations))
		{
			exit('Unsuported optimalization: ' . $optimalization . '<br />Available optimalization is' . implode(', ', $this->availableOptimalizations));
		}
		$this->optimalization = $optimalization;
	}

	/**
	 * Load JS file
	 * @param string $filename filename path
	 * @param boolean $force true for force compilation
	 * @access public
	 */
	public function loadFile($filename, $force = false)
	{
		if ($this->isModified($filename) || $force)
		{
			$this->script = file_get_contents($filename);
			$this->filename = $filename;
		}
		else
		{
			$this->script = '';
		}
	}

	/**
	 * Save compiled code to file
	 * @param string $filename file path to save
	 * @return void
	 */
	public function saveFile($filename)
	{
		if ($this->compiled == '')
		{
			return;
		}
		file_put_contents($filename, $this->compiled);
	}

	/**
	 * Set JS script manualy
	 * @param string $script JS string
	 */
	public function setScript($script)
	{
		$this->script = $script;
	}

	/**
	 * Get compiled code
	 * @return string compiled code
	 */
	public function getScript()
	{
		return $this->compiled;
	}
}
