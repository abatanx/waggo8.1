<?php
/**
 * waggo8
 * @copyright 2013-2022 CIEL, K.K., project waggo.
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
			$data = $v;
			$data = preg_replace('/^[ \n\r\t\v\x00　]+/u', '', $data);
			$data = preg_replace('/[ \n\r\t\v\x00　]+$/u', '', $data);
		}

		$this->addChainState( WGGChainState::_( true ) );

		return true;
	}
}

