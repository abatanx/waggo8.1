<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGV8Object.php';

class WGV8BasicRadioElement extends WGV8BasicElement
{
	public function controller( $c )
	{
		/**
		 * @var WGFController $c
		 */
		parent::controller( $c );

		$nm = htmlspecialchars( $this->getName() );
		$x  = ! $c->isScriptBasedController() ? WGFPCController::RUNJS_ONLOAD : WGFXMLController::RUNJS_ONLOADED;

		if ( $this->isLock() || $c->getInputType() == $c::SHOWHTML )
		{
			$c->runJS( "\$('input[name=\"{$nm}\"').attr({disabled:'disabled'});", $x );
		}
		$c->runJS( "\$('input[name=\"{$nm}\"]').val([" . json_encode( $this->getValue() ) . "]);", $x );
	}

	public function publish()
	{
		$a = $this->getValue() != false ? [ 'checked#' . htmlspecialchars( $this->getValue() ) => 'checked' ] : [];

		return parent::publish() + $a;
	}
}
