<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGG.php';

class WGGFloat extends WGG
{
	protected float $min = - 1.0, $max = 1.0;

	public static function _( float $min = - 1.0, float $max = 1.0 ): self
	{
		return new static( $min, $max );
	}

	public function __construct( float $min = - 1.0, float $max = 1.0 )
	{
		parent::__construct();
		$this->min = $min;
		$this->max = $max;
	}

	public function makeErrorMessage(): string
	{
		return sprintf( "%f〜%f の数値で入力してください。", $this->min, $this->max );
	}

	public function validate( mixed &$data ): bool
	{
		$v = $this->toValidationString( $data );

		if ( is_numeric( $v ) )
		{
			$n = (float) $v;
			if ( $n >= $this->min && $n <= $this->max )
			{
				$data = $n;

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
