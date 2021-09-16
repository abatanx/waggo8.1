<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGG.php';

class WGGMailString extends WGG
{
	public static function _(): self
	{
		return new static();
	}

	public function makeErrorMessage(): string
	{
		return 'メールアドレスの書式に誤りがあります。';
	}

	public function validate( &$data ): bool
	{
		if ( wg_check_input_email( $data ) )
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

