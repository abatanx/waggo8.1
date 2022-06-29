<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGG.php';

class WGGNull extends WGG
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
		if ( ! empty( $data ) )
		{
			$this->addChainState( WGGChainState::_( false, $this->makeErrorMessage() ) );

			return false;
		}

		$data = null;

		$this->addChainState( WGGChainState::_( true ) );

		return true;
	}
}

