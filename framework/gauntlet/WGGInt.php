<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGG.php';

class WGGInt extends WGG
{
	protected int $min = 0, $max = PHP_INT_MAX;

	public static function _( int $min = 0, int $max = PHP_INT_MAX ): self
	{
		return new static( $min, $max );
	}

	public function __construct( int $min = 0, int $max = PHP_INT_MAX )
	{
		parent::__construct();
		$this->min = $min;
		$this->max = $max;
	}

	public function makeErrorMessage(): string
	{
		return sprintf( "%d〜%d の数値で入力してください。", $this->min, $this->max );
	}

	public function validate( mixed &$data ): bool
	{
		if ( preg_match( '/^\-?[0-9]+$/', $this->toValidationString( $data ), $match ) )
		{
			$n = (int) $match[0];
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
