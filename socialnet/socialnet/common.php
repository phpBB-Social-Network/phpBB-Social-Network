<?php
/**
*
* @package phpBB Social Network
* @version 0.6.3
* @copyright (c) 2010-2012 Kamahl & Culprit http://phpbbsocialnetwork.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

$socialnet_root_path = $phpbb_root_path . 'socialnet/';

// Load neccessary scripts
/**
 * @ignore
 */
include_once($socialnet_root_path . 'includes/constants.' . $phpEx);
/**
 * @ignore
 */
$dir = opendir( "{$socialnet_root_path}includes/");
while( $file = readdir( $dir))
{
	if ( preg_match("/^sn_core_.*\.{$phpEx}$/i", $file, $match ))
	{
		include( "{$socialnet_root_path}includes/$file");
	}
}
closedir( $dir);

include_once($socialnet_root_path . 'includes/functions.' . $phpEx);
/**
 * @ignore
 */
include_once($socialnet_root_path . 'socialnet.' . $phpEx);

?>