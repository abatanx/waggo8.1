<?php
/**
 * waggo8
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGG.php';

class WGGFilterAnk extends WGG
{
	private string $convertKanaParam;

	public static function _( string $convertKanaParam = "KVas" ): self
	{
		return new static( $convertKanaParam );
	}

	public function __construct( string $convertKanaParam = "KVas" )
	{
		parent::__construct();
		$this->convertKanaParam = $convertKanaParam;
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

		$data = mb_convert_kana( $v, $this->convertKanaParam );

		$this->addChainState( WGGChainState::_( true ) );

		return true;
	}
}

