<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGG.php';

class WGGThru extends WGG
{
	public static function _(): self
	{
		return new static();
	}

	public function makeErrorMessage(): string
	{
		return '';
	}

	public function validate( &$data ): bool
	{
		return true;
	}
}
