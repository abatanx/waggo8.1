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
	const ORDER_NONE = 0, ORDER_ASC = 1, ORDER_DESC = 2;

	static int $currentPriority = 0x7fff;

	protected int $priority;

	protected string $formula;
	protected int $order = self::ORDER_NONE;

	public function __construct()
	{
		$this->priority = self::$currentPriority ++;
		$this->formula  = '';
		$this->order    = self::ORDER_NONE;
	}

	public function getFormula(): string
	{
		return $this->formula;
	}

	public function setFormula( $name ): self
	{
		$this->formula = $name;

		return $this;
	}

	public function asc(): self
	{
		$this->order = self::ORDER_ASC;

		return $this;
	}

	public function desc(): self
	{
		$this->order = self::ORDER_DESC;

		return $this;
	}

	public function setOrder( int $order ): self
	{
		$this->order = $order;

		return $this;
	}

	public function setOrderByString( string $order ): self
	{
		switch ( strtoupper( $order ) )
		{
			case 'ASC':
				$this->order = self::ORDER_ASC;
				break;
			case 'DESC':
				$this->order = self::ORDER_DESC;
				break;
			default:
				$this->order = self::ORDER_NONE;
		}

		return $this;
	}

	public function getOrder(): int
	{
		return $this->order;
	}

	public function getNameAppendingPrefix( string $alias ): string
	{
		return $alias . '.' . $this->formula;
	}

	public function setOrderSyntax( string $order ): self
	{
		$syntax = trim( $order );
		if ( preg_match( '/^(\w+)\s+(asc|desc)$/i', $syntax, $m ) )
		{
			$this->formula = '{' . $m[1] . '}';
			$this->setOrderByString( $m[2] );
		}
		else if ( preg_match( '/^({\w+})\s+(asc|desc)$/i', $syntax, $m ) )
		{
			$this->formula = $m[1];
			$this->setOrderByString( $m[2] );
		}
		else if ( preg_match( '/^(\w+)$/', $syntax, $m ) )
		{
			$this->formula = '{' . $m[1] . '}';
			$this->setOrder( self::ORDER_ASC );
		}
		else if ( preg_match( '/^({\w+})$/', $syntax, $m ) )
		{
			$this->formula = $m[1];
			$this->setOrder( self::ORDER_ASC );
		}
		else
		{
			$this->formula = $syntax;
			$this->setOrder( self::ORDER_NONE );
		}

		return $this;
	}
}
