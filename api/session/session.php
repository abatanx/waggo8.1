<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

function wg_unset_session( $regptn )
{
	foreach ( $_SESSION as $k => $v )
	{
		if ( preg_match( $regptn, $k ) )
		{
			$_SESSION[ $k ] = null;
			unset( $_SESSION[ $k ] );
		}
	}
}
