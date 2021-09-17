<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGG.php';

class WGGNotFalse extends WGG
{
	public static function _(): self
	{
		return new static();
	}

	public function makeErrorMessage(): string
	{
		return '内容がありません。';
	}

	public function validate( mixed &$data ): bool
	{
		if ( $data !== false )
		{
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

