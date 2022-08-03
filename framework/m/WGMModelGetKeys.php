<?php
/**
 * waggo8
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 * @noinspection PhpUnused
 */
declare( strict_types=1 );

require_once __DIR__ . '/WGMModel.php';

/**
 * @noinspection PhpUnused
 */

class WGMModelGetKeys
{
	private array $keys;
	private WGMModel $model;

	public function __construct( WGMModel $model )
	{
		$this->keys  = [];
		$this->model = $model;
	}

	/**
	 * @return WGMModel
	 */
	public function getModel(): WGMModel
	{
		return $this->model;
	}

	public function setKeys( array $keys )
	{
		$this->keys = $keys;
	}

	public function getKeys(): array
	{
		return $this->keys;
	}
}
