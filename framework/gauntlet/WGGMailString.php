<?php
/**
 * waggo8
 * @copyright 2013-2022 CIEL, K.K., project waggo.
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
		$v = $this->toValidationString( $data );

		if ( wg_check_input_email( $v ) )
		{
			$data = $v;

			$this->addChainState( WGGChainState::_( true ) );

			return true;
		}
		else
		{
			$this->addChainState( WGGChainState::_( false, $this->makeErrorMessage() ) );

			return false;
		}
	}
}

