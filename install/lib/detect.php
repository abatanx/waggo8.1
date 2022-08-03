<?php
/**
 * waggo8
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

function wi_search_pear(): string|false
{
	$pear_dir  = false;
	$pear_exec = dirname( $_SERVER['_'] ) . '/pear';
	if ( file_exists( $pear_exec ) && is_executable( $pear_exec ) )
	{
		$p = trim( exec( "{$pear_exec} config-get php_dir" ) );
		if ( ! empty( $p ) )
		{
			$t_pear_dir = rtrim( $p, '/\\' );
			$t_pear_php = $t_pear_dir . '/PEAR.php';
			if ( file_exists( $t_pear_php ) )
			{
				$pear_dir = $t_pear_dir;
			}
		}
	}

	return $pear_dir;
}

function wi_search_command( $file ): string|false
{
	$paths = preg_split( '/[:;]/', getenv( "PATH" ) );
	foreach ( $paths as $path )
	{
		$t_exec_dir  = rtrim( $path, '/\\' );
		$t_exec_file = $t_exec_dir . '/' . $file;
		if ( file_exists( $t_exec_file ) && is_executable( $t_exec_file ) )
		{
			return $t_exec_file;
		}
	}

	return false;
}

function wi_search_phpcli(): string|false
{
	$t_exec_file = $_SERVER['_'];

	return file_exists( $t_exec_file ) && is_executable( $t_exec_file ) ? $t_exec_file : false;
}

function wi_search_convert(): string|false
{
	return wi_search_command( 'convert' );
}

function wi_search_ffmpeg(): string|false
{
	return wi_search_command( 'ffmpeg' );
}
