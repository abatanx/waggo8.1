<?php
/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

function wg_unset_session( $regex ): void
{
	foreach ( $_SESSION as $k => $v )
	{
		if ( preg_match( $regex, $k ) )
		{
			$_SESSION[ $k ] = null;
			unset( $_SESSION[ $k ] );
		}
	}
}
