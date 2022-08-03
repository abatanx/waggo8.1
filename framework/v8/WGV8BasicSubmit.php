<?php
/**
 * waggo8
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGV8Object.php';

class WGV8BasicSubmit extends WGV8Basic
{
	public function isSubmit(): bool
	{
		return true;
	}

	public function controller( WGFController $c ): self
	{
		parent::controller( $c );

		$id = htmlspecialchars( $this->getId() );
		$x  = ! $c->isScriptBasedController() ? WGFPCController::RUNJS_ONLOAD : WGFXMLController::RUNJS_ONLOADED;

		if ( $this->isLock() )
		{
			$c->runJS( "\$('#{$id}').attr({disabled:'disabled'});", $x );
		}
		else
		{
			$hiddenId = $id . "_wg_post";
			if ( ! $c->isScriptBasedController() )
			{
				$c->runJS(
					"\$('#{$id}').click(function(){" .
					"\$(this).after(\$('<input>').attr({id:'{$hiddenId}',type:'hidden',value:$(this).val(),name:\$(this).attr('name')}));" .
					"\$(this).closest('form').submit();\$('#{$hiddenId}').remove()});", $x
				);
			}
			else
			{
				$c->runJS(
					"\$('#{$id}').click(function(){" .
					"\$(this).after(\$('<input>').attr({id:'{$hiddenId}',type:'hidden',value:$(this).val(),name:\$(this).attr('name')}));" .
					"WG8.post(WG8.closestForm(\$(this)),'{$c->getNextURL()}');\$('#{$hiddenId}').remove()});", $x
				);
			}
		}

		return $this;
	}
}
