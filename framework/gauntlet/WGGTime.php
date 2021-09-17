<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGG.php';

class WGGTime extends WGG
{
	public static function _()
	{
		return new static();
	}

	public function makeErrorMessage(): string
	{
		return '有効な時間を入力してください。';
	}

	public function validate( &$data ): bool
	{
		$v = $this->toValidationString();

		if ( wg_datetime_checktime( $v ) )
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
