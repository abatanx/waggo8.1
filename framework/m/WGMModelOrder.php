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
	const ORDER_ASC = 1, ORDER_DESC = 2;

	static int $currentPriority = 0x7fff;

	protected int $priority;

	protected WGMModel $assignedModel;
	protected string $orderField;
	protected int $order = self::ORDER_ASC;

	public function __construct( WGMModel $ownerModel )
	{
		$this->priority      = self::$currentPriority ++;
		$this->orderField    = '';
		$this->order         = self::ORDER_ASC;
		$this->assignedModel = $ownerModel;
	}

	static function _( WGMModel $ownerModel, string $formulaString ): self
	{
		$order = new static( $ownerModel );

		return $order->setFormula( $formulaString );
	}

	public function getOrderField(): string
	{
		return $this->orderField;
	}

	public function getFormula(): string
	{
		$expanded = $this->assignedModel->expansion( $this->getOrderField() );

		return match ( $this->order )
		{
			self::ORDER_ASC => $expanded . ' ASC',
			self::ORDER_DESC => $expanded . ' DESC',
			default => $expanded,
		};
	}

	public function setOrderField( $name ): self
	{
		$this->orderField = $name;

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
		$this->order = match ( strtoupper( $order ) )
		{
			'DESC' => self::ORDER_DESC,
			default => self::ORDER_ASC,
		};

		return $this;
	}

	public function getOrder(): int
	{
		return $this->order;
	}

	public function getPriority():int
	{
		return $this->priority;
	}

	public function setFormula( string $formulaString ): self
	{
		$syntax = trim( $formulaString );
		$syntax = trim(preg_replace_callback('/\s+(asc|desc)$/i', function($m){
			$this->setOrderByString($m[1]);
			return '';
		},$syntax));

		if( preg_match('/^(\w+)$/', $syntax) )
		{
			$this->orderField = '{' . $syntax . '}';
		}
		else
		{
			$this->orderField = $syntax;
		}

		return $this;
	}
}
