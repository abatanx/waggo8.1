<?php
/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 * @noinspection PhpUnused
 */
declare( strict_types=1 );

class WGMModelField
{
	protected int $type;
	protected string $formatType;
	protected bool $isNotNull;
	protected string $name;

	public function __construct( int $type, string $formatType, bool $isNotNull, string $name )
	{
		$this->type       = $type;
		$this->formatType = $formatType;
		$this->isNotNull  = $isNotNull;
		$this->name       = $name;
	}

	public function getType(): int
	{
		return $this->type;
	}

	public function getFormatType(): string
	{
		return $this->formatType;
	}

	public function isNotNull(): bool
	{
		return $this->isNotNull;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getNameAppendingPrefix( string $alias ): string
	{
		return $alias . '.' . $this->name;
	}
}

