<?php
/**
 * waggo8
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

class WGParaFilter
{
	public function inputBeforeGauntlet( mixed $v ): mixed
	{
		return $v;
	}

	public function inputAfterGauntlet( mixed $v ): mixed
	{
		return $v;
	}

	public function output( mixed $v ): ?string
	{
		return ! is_null( $v ) ? (string) $v : null;
	}
}
