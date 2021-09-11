<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGV8Object.php';

class WGV8BasicElement extends WGV8Basic
{
	public function isSubmit(): bool
	{
		return false;
	}

	public function controller( WGFController $c ): self
	{
		parent::controller( $c );

		$id = htmlspecialchars( $this->getId() );
		$x  = ! $c->isScriptBasedController() ? WGFPCController::RUNJS_ONLOAD : WGFXMLController::RUNJS_ONLOADED;

		if ( $this->isLock() || $c->getInputType() == $c::SHOWHTML )
		{
			$c->runJS( "\$('#{$id}').attr({disabled:'disabled'});", $x );
		}

		return $this;
	}
}
