<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
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
		return sprintf( "%d〜%d文字の長さの範囲で入力してください。", $this->min, $this->max );
	}

	public function validate( mixed &$data ): bool
	{
		$l = mb_strlen( $data );

		if ( $l >= $this->min && $l <= $this->max )
		{
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

