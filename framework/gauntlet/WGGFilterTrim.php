<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGG.php';

class WGGFilterTrim extends WGG
{
	private bool $trimZenkakuSpace;

	public static function _( bool $trimZenkakuSpace = false ): self
	{
		return new static( $trimZenkakuSpace );
	}

	public function __construct( bool $trimZenkakuSpace = false )
	{
		parent::__construct();
		$this->trimZenkakuSpace = $trimZenkakuSpace;
	}

	public function makeErrorMessage(): string
	{
		return '';
	}

	public function isFilter(): bool
	{
		return true;
	}

	public function validate( &$data ): bool
	{
		$v = $this->toValidationString( $data );

		if ( ! $this->trimZenkakuSpace )
		{
			$data = trim( $v );
		}
		else
		{
			$data = trim( $v, " \t\n\r\0\x0B　" );
		}

		return true;
	}
}

