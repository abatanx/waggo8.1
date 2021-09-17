<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGG.php';

class WGGDate extends WGG
{
	public static function _(): self
	{
		return new static();
	}

	public function makeErrorMessage(): string
	{
		return '有効な日付を入力してください。';
	}

	public function validate( mixed &$data ): bool
	{
		$v = $this->toValidationString($data);
		if ( wg_datetime_checkdate( $v ) )
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

