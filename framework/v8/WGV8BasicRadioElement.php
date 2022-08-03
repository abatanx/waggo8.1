<?php
/**
 * waggo8
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGV8Object.php';

class WGV8BasicRadioElement extends WGV8BasicElement
{
	public function controller( WGFController $c ): self
	{
		parent::controller( $c );

		$nm = htmlspecialchars( $this->getName() );
		$x  = ! $c->isScriptBasedController() ? WGFPCController::RUNJS_ONLOAD : WGFXMLController::RUNJS_ONLOADED;

		if ( $this->isLock() || $c->getInputType() == $c::SHOWHTML )
		{
			$c->runJS( "\$('input[name=\"{$nm}\"').attr({disabled:'disabled'});", $x );
		}
		$c->runJS( "\$('input[name=\"{$nm}\"]').val([" . json_encode( $this->getValue() ) . "]);", $x );

		return $this;
	}

	public function publish(): array
	{
		$a = $this->getValue() != false ? [ 'checked#' . htmlspecialchars( $this->getValue() ) => 'checked' ] : [];

		return parent::publish() + $a;
	}
}
