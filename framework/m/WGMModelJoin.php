<?php
/**
 * waggo8
 * @copyright 2013-2022 CIEL, K.K., project waggo.
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

	protected ?string $leftConstraint;
	protected ?string $rightConstraint;

	public function __construct(
		int $joinType, WGMModel $joinModel,
		array $on, ?string $leftConstraint = null, ?string $rightConstraint = null
	) {
		$this->priority        = self::$currentPriority ++;
		$this->joinType        = $joinType;
		$this->joinModel       = $joinModel;
		$this->joinOn          = $on;
		$this->leftConstraint  = $leftConstraint;
		$this->rightConstraint = $rightConstraint;
	}

	static public function _(
		int $joinType, WGMModel $joinModel,
		array $on, ?string $leftConstraint = null, ?string $rightConstraint = null
	): self {
		return new static( $joinType, $joinModel, $on, $leftConstraint, $rightConstraint );
	}

	public function getJoinType(): int
	{
		return $this->joinType;
	}

	public function getJoinTypeOperatorString(): string
	{
		return match ( $this->getJoinType() )
		{
			self::INNER => 'INNER JOIN',
			self::LEFT => 'LEFT JOIN',
			self::RIGHT => 'RIGHT JOIN'
		};
	}

	public function getJoinModel(): WGMModel
	{
		return $this->joinModel;
	}

	public function getOn(): array
	{
		return $this->joinOn;
	}

	public function getLeftConstraint(): ?string
	{
		return $this->leftConstraint;
	}

	public function getRightConstraint(): ?string
	{
		return $this->rightConstraint;
	}
}
