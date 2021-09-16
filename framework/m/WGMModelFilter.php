<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

class WGMModelFilter
{
	public function __construct()
	{
	}

	public function input( mixed $value ): mixed
	{
		return $value;
	}

	public function output( mixed $value ): mixed
	{
		return $value;
	}

	public function modelToView( WGV8Object $view, mixed $v ): self
	{
		$view->setValue( $v );

		return $this;
	}

	public function viewToModel( WGV8Object $view ): mixed
	{
		return $view->getValue();
	}
}
