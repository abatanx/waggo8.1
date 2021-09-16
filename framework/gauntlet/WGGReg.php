<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
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
		return '入力内容を見直してください';
	}

	public function validate( &$data ): bool
	{
		if ( preg_match( $this->regex, $data ) )
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
