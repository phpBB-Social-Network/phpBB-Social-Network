<?php
/**
 * Hooks class
 *
 * Hooks in phpBB Social Network are small pieces of code that allow addons
 * to modify some variables on certain places (usually functions or methods
 * of classes). Addon is able to register it's function(s) to specified
 * place which is identified by "tag". As script runs and arrives to that
 * place, it triggers all custom functions which are registered to that
 * place. Each custom function recieves array of variables, which are
 * available to be modified, and hook system requires function to return
 * exactly the same array, as it delivered to custom function.
 * Addon can register to specified hook using this simple line of code:
 *
 * $sn_hook->add_action('sn.<tag>', 'custom_function_name');
 *
 * From now, as script reaches specified tag, it will trigger your
 * "custom_function_name()" function with first parameter full of data you
 * expect (what data are sent by each hook, you can find in hooks
 * documentation)
 *
 *
 * ------------------------
 * Add hooks for your addon
 * ------------------------
 *
 * It is possible to specify new hooks by inserting small piece of code
 * to your addon code:
 *
 * $vars = array('name', 'email');
 * extract($sn_hook->do_action('<addon-identifier>.<tag>', compact($vars)));
 *
 * We urge you to use standardisied documentation for all your hooks:
 *
 * /**
 *	 * This line is for description of the hook
 *	 *
 *	 * @hook <addon-identifier>.<tag>
 *	 * @var <variable1 type> <variable1 name> <variable1 description>
 *	 * @var <variable2 type> <variable2 name> <variable2 description>
 *	 * @since <version>
 *	 * /
 *
 * From now, anyone can access and register to your hook using classic method:
 *
 * $sn_hook->add_action('<addon-identifier>.<tag>', 'custom_function_name');
 *
 *
 * @package	phpBB-Social-Network
 * @author 	Senky
 * @version	0.7.2
 * @access 	public
 */
class sn_hooks {

	/**
	 * Holds names of all custom functions sorted according to tag
	 *
	 * @var $actions
	 */
	private $actions = array();

	/**
	 * Registers custom function for hook at specified tag
	 *
	 * @param	string	$taghook 	tag
	 * @param	string	$function	name of custom function to be triggered
	 */
	function add_action($tag, $function)
	{
		$this->actions[$tag][] = $function;
	}

	/**
	 * Triggers registered functions for a specified tag
	 *
	 * @param 	string	$tag		hook tag
	 * @param 	array 	$variables	compacted variables to be modified by custom function(s)
	 * @return	array 	modified variables compacted to array
	 */
	function do_action($tag, $variables)
	{
		foreach ( $this->actions[$tag] as $function )
		{
			$return = (array) call_user_func_array($function, $variables);

			// return modified variables only if they fully correspond to sent ones
			if ( !array_diff_key($variables, $return) && !array_diff_key($return, $variables) )
			{
				return $return;
			}
		}

		// extract() does not accept null, so we need to make sure this function always returns array
		return array();
	}
}