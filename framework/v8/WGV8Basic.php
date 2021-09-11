<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGV8Object.php';

class WGV8Basic extends WGV8Object
{
	public function postCopy(): self
	{
		parent::postCopy();

		if ( isset( $_POST[ $this->getKey() ] ) )
		{
			$this->setValue( $_POST[ $this->getKey() ] );
		}
		$this->filterGauntlet();

		return $this;
	}

	public function controller( WGFController $c ): self
	{
		parent::controller( $c );
		return $this;
	}
}
