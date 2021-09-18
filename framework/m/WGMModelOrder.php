<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 * @noinspection PhpUnused
 */
declare( strict_types=1 );

class WGMModelOrder
{
	static int $currentPriority = 0x7fff;

	protected int $priority;

	protected string $name;
	protected string $asc;

	public function __construct()
	{
		$this->priority = self::$currentPriority ++;
		$this->name     = '';
		$this->asc      = 'ASC';
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName( $name ): self
	{
		$this->name = $name;

		return $this;
	}

	public function asc(): self
	{
		$this->asc = 'ASC';

		return $this;
	}

	public function desc(): self
	{
		$this->asc = 'DESC';

		return $this;
	}

	public function getNameAppendingPrefix( string $alias ): string
	{
		return $alias . '.' . $this->name;
	}

	public function setOrderSyntax( string $order ): bool
	{
		$syntax = trim($order);
		$e = preg_split('/\s+/',$syntax);




	}
}
