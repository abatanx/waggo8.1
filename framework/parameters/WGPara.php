<?php
/**
 * waggo8
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGParameters.php';

#[Attribute]
class WGPara
{
	public ?string $name;
	public array $tags;
	public mixed $default;

	public array $assign;
	public ?WGG $gauntlet = null;

	public function __construct( $tags = [], $name = null, $gauntlet = null, $default = null )
	{
		$this->tags     = $tags;
		$this->name     = $name;
		$this->gauntlet = $gauntlet;
		$this->default  = $default;
	}

	public function getGauntlet()
	{
		return $this->gauntlet;
	}

	public function getName( ReflectionProperty $refProp )
	{
		return $this->name ?? $refProp->getName();
	}

	public function input( string $method, ReflectionProperty $refProp ): mixed
	{
		return match ( $method )
		{
			WGParameters::METHOD_GET => $_GET[ $this->getName( $refProp ) ] ?? null,
			WGParameters::METHOD_POST => $_POST[ $this->getName( $refProp ) ] ?? null,
			default => null,
		};
	}
}
