<?php
/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

function wi_detect_waggo_version(): array
{
	$cs = file( __DIR__ . '/../../waggo.php' );
	foreach ( $cs as $c )
	{
		if ( preg_match( '/^\s*const/', $c ) )
		{
			eval( $c );
		}
	}

	return [
		'version'   => WG_VERSION,
		'name'      => WG_NAME,
		'copyright' => WG_COPYRIGHT
	];
}
