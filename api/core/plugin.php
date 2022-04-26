<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

$plugin_directories = WGCONF_DIR_SYS . '/plugins';
if ( is_dir( $plugin_directories ) )
{
	global $WGCONF_AUTOLOAD;

	$entries = scandir( $plugin_directories, SCANDIR_SORT_ASCENDING );
	if ( is_array( $entries ) )
	{
		foreach( $entries as $entry )
		{
			if( preg_match('/^[0-9a-zA-Z]/', $entry) )
			{
				$plugin_directory = $plugin_directories . '/' . $entry;
				if( is_dir($plugin_directory) && is_readable($plugin_directory) )
				{
					$WGCONF_AUTOLOAD[] = $plugin_directory;
					wg_log_write( WGLOG_INFO, "[[ Activated plugin : $plugin_directory ]]" );
				}
			}
		}
	}
}
