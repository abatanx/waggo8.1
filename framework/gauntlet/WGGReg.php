<?php
/**
 * waggo8
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGG.php';

class WGGReg extends WGG
{
	protected string $regex;

	public static function _( $regex ): self
	{
		return new static( $regex );
	}

	public function __construct( $regex )
	{
		parent::__construct();
		$this->regex = $regex;
	}

	public function makeErrorMessage(): string
	{
		return '入力内容を見直してください。';
	}

	public function validate( &$data ): bool
	{
		$v = $this->toValidationString( $data );

		if ( preg_match( $this->regex, $v ) )
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
