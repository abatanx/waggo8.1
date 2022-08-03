<?php
/**
 * waggo8
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGG.php';

class WGGInArray extends WGG
{
	protected array $validArray;

	public static function _( array $valid_array ): self
	{
		return new static( $valid_array );
	}

	public function __construct( array $valid_array )
	{
		parent::__construct();
		$this->validArray = $valid_array;
	}

	public function makeErrorMessage(): string
	{
		return '入力を確認してください。';
	}

	public function validate( mixed &$data ): bool
	{
		$v = $this->toValidationString( $data );

		if ( in_array( $v, $this->validArray ) )
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

