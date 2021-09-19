<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 * @noinspection PhpUnused
 */
declare( strict_types=1 );

class WGMModelJoin
{
	const INNER = 0, LEFT = 1, RIGHT = 2;

	static int $currentPriority = 0x7fff;

	protected int $priority;

	protected WGMModel $joinModel;
	protected int $joinType = self::INNER;
	protected array $joinOn;

	public function __construct( int $joinType, WGMModel $joinModel, array $on )
	{
		$this->priority  = self::$currentPriority ++;
		$this->joinType  = $joinType;
		$this->joinModel = $joinModel;
		$this->joinOn    = $on;
	}

	static public function _( int $joinType, WGMModel $joinModel, array $on ): self
	{
		return new static( $joinType, $joinModel, $on );
	}

	public function getJoinType(): int
	{
		return $this->joinType;
	}

	public function getJoinModel(): WGMModel
	{
		return $this->joinModel;
	}

	public function getOn(): array
	{
		return $this->joinOn;
	}
}
