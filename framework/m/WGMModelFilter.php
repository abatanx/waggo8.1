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

	/**
	 * var to model (input to model)
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function input( mixed $value ): mixed
	{
		return $value;
	}

	/**
	 * model to var (output from model)
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function output( mixed $value ): mixed
	{
		return $value;
	}

	/**
	 * view to model (input to model)
	 * @param WGV8Object $view
	 *
	 * @return mixed
	 */
	public function viewToModel( WGV8Object $view ): mixed
	{
		return $view->getValue();
	}

	/**
	 * model to view (output from model)
	 * @param WGV8Object $view
	 * @param mixed $v
	 *
	 * @return $this
	 */
	public function modelToView( WGV8Object $view, mixed $v ): self
	{
		$view->setValue( $v );

		return $this;
	}
}
