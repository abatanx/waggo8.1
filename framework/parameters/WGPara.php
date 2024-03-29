<?php
/**
 * waggo8
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGParameters.php';

const WP_IGNORE_INIT_BY_DEFAULT     = 1;
const WP_IGNORE_IF_SOURCE_NOT_EXIST = 2;

#[Attribute( Attribute::TARGET_PROPERTY )]
class WGPara
{
	const PRE_FILTER = 0;
	const POST_FILTER = 1;

	public ?string $name;
	public array $tags;
	public mixed $default;

	public bool $isIgnoreInitByDefault;
	public bool $isIgnoreIfSourceNotExist;

	public array $assign;
	public ?WGG $gauntlet = null;

	public ?WGParaFilter $filter = null;

	public function __construct(
		$tags = [], $name = null, $filter = null, $gauntlet = null, $default = null, $flags = 0
	) {
		$this->tags                     = $tags;
		$this->name                     = $name;
		$this->gauntlet                 = $gauntlet;
		$this->filter                   = $filter;
		$this->default                  = $default;
		$this->isIgnoreInitByDefault    = ! ! ( $flags & WP_IGNORE_INIT_BY_DEFAULT );
		$this->isIgnoreIfSourceNotExist = ! ! ( $flags & WP_IGNORE_IF_SOURCE_NOT_EXIST );
	}

	public function setIgnoreInitByDefault( $flag = false ): self
	{
		$this->isIgnoreInitByDefault = $flag;

		return $this;
	}

	public function setIgnoreIfSourceNotExist( $flag = false ): self
	{
		$this->isIgnoreIfSourceNotExist = $flag;

		return $this;
	}

	public function getGauntlet()
	{
		return $this->gauntlet;
	}

	public function getName( ReflectionProperty $refProp )
	{
		return $this->name ?? $refProp->getName();
	}

	public function applyOutputFilter( $value ): mixed
	{
		return $this->filter ? $this->filter->output( $value ) : $value;
	}

	public function applyInputFilterBeforeGauntlet( $value ): mixed
	{
		return $this->filter ? $this->filter->inputBeforeGauntlet( $value ) : $value;
	}

	public function applyInputFilterAfterGauntlet( $value ): mixed
	{
		return $this->filter ? $this->filter->inputAfterGauntlet( $value ) : $value;
	}

	public function isExists( string $method, ReflectionProperty $refProp ): bool
	{
		return match ( $method )
		{
			WGParameters::METHOD_GET => isset( $_GET[ $this->getName( $refProp ) ] ),
			WGParameters::METHOD_POST => isset( $_POST[ $this->getName( $refProp ) ] ),
			default => false
		};
	}

	public function input( string $method, ReflectionProperty $refProp ): mixed
	{
		return match ( $method )
		{
			WGParameters::METHOD_GET => $_GET[ $this->getName( $refProp ) ] ?? null,
			WGParameters::METHOD_POST => $_POST[ $this->getName( $refProp ) ] ?? null,
			default => null
		};
	}
}

#[Attribute( Attribute::TARGET_PROPERTY )]
class WGParaIgnoreIfSourceNotExist
{

}

#[Attribute( Attribute::TARGET_PROPERTY )]
class WGParaIgnoreInitByDefault
{

}
