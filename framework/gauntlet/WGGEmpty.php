<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGG.php';

class WGGEmpty extends WGG
{
	public static function _(): self
	{
		return new static();
	}

	public function makeErrorMessage(): string
	{
		return '内容が空ではありません。';
	}

	public function validate( mixed &$data ): bool
	{
		if( $data === true )
		{
			if ( ! $this->isBranch() )
			{
				$this->addError( $this->makeErrorMessage() );
			}

			return false;
		}
		else if( $data === false )
		{
			$data = '';
		}

		$v = $this->toValidationString( (string) $data );
		if ( strlen( $v ) === 0 )
		{
			$data = $v;

			return true;
		}
		else
		{
			if ( ! $this->isBranch() )
			{
				$this->addError( $this->makeErrorMessage() );
			}

			return false;
		}
	}
}

