<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

function wi_license_agreement(): bool
{
	echo file_get_contents( __DIR__ . '/../../LICENSE' );
	echo "\n\n";

	return wi_read( "Do you accept the license agreement ? (Yes/No) -> ", [ 'Yes', 'No' ] ) === 'Yes' ;
}
