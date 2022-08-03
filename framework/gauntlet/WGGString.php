<?php
/**
 * waggo8
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGG.php';

class WGGString extends WGG
{
	protected int $min, $max;

	public static function _( $min = 0, $max = 255 ): self
	{
		return new static( $min, $max );
	}

	public function __construct( $min = 0, $max = 255 )
	{
		parent::__construct();
		$this->min = $min;
		$this->max = $max;
	}

	public function makeErrorMessage(): string
	{
		return $this->min != $this->max ?
			sprintf( "%d〜%d文字の長さの範囲で入力してください。", $this->min, $this->max ) :
			sprintf( "%d文字で入力してください。", $this->min );
	}

	public function validate( mixed &$data ): bool
	{
		$v = $this->toValidationString( $data );

		$l = mb_strlen( $v );

		if ( $l >= $this->min && $l <= $this->max )
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

