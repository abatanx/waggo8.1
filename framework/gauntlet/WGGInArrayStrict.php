<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGG.php';

class WGGInArrayStrict extends WGGInArray
{
	public function validate( mixed &$data ): bool
	{
		$v = $this->toValidationString( $data );

		$a = array_map( function ( $v ) {
			return strval( $v );
		}, $this->validArray );

		if ( in_array( $v, $a, true ) )
		{
			$data = $v;

			return true;
		}
		else
		{
			if ( ! $this->isBranch() )
			{
				$this->setError( $this->makeErrorMessage() );
			}

			return false;
		}
	}
}

