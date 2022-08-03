<?php
/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/lib.php';

function wg_safe_int( int|string $value ): int
{
	if ( is_string( $value ) )
	{
		if ( ! preg_match( '/^[+\-]?[0-9]{1,100}$/', $value ) )
		{
			wg_log_write( WGLOG_FATAL, "Invalid integer literal, %s.", $value );
		}

		$value = (int) $value;
	}

	return $value;
}

function wg_safe_float( int|float|string $value ): float
{
	if ( is_string( $value ) )
	{
		if ( ! preg_match( '/^[+\-]?[0-9]+$/', $value ) &&
			 ! preg_match( '/^[+\-]?(([0-9]*)[\.][0-9]+|([0-9]+[\.][0-9]*))$/', $value ) )
		{
			wg_log_write( WGLOG_FATAL, "Invalid float literal, %s.", $value );
		}
		$value = (float) $value;
	}
	else if ( is_int( $value ) )
	{
		$value = (float) $value;
	}

	if ( $value === INF )
	{
		$value = PHP_FLOAT_MAX;
	}
	else if ( $value === - INF )
	{
		$value = PHP_FLOAT_MIN;
	}
	else if ( is_nan( $value ) )
	{
		wg_log_write( WGLOG_FATAL, "NaN error." );
	}

	return $value;
}
