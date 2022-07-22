<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGV8Object.php';

class WGV8BasicSelectElement extends WGV8BasicElement
{
	protected array $options;
	protected bool $isPostCheck;

	public function __construct()
	{
		parent::__construct();
		$this->options     = [];
		$this->isPostCheck = true;
	}

	public function setOptions( array $options ): self
	{
		$this->options = $options;

		return $this;
	}

	public function setPostCheck( bool $flag ): self
	{
		$this->isPostCheck = $flag;

		return $this;
	}

	public function isPostCheck(): bool
	{
		return $this->isPostCheck;
	}

	public function postCopy(): self
	{
		if ( isset( $_POST[ $this->getKey() ] ) )
		{
			if ( $this->isPostCheck )
			{
				$skeys = [];
				foreach ( array_keys( $this->options ) as $k )
				{
					$skeys[] = (string) $k;
				}

				$v = (string) $_POST[ $this->getKey() ];
				if ( in_array( $v, $skeys, true ) )
				{
					$this->setValue( $v );
				}
				else
				{
					$this->setValue( false );
				}
			}
			else
			{
				$v = (string) $_POST[ $this->getKey() ];
				$this->setValue( $v );
			}
		}
		$this->filterGauntlet();

		return $this;
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

	public function publish(): array
	{
		$caption = '';
		$opt     = [];
		foreach ( $this->options as $k => $v )
		{
			$selected = '';
			if ( (string) $this->getValue() === (string) $k )
			{
				$selected = " selected";
				$caption  = $v;
			}
			$opt[] = sprintf( '<option value="%s"%s>%s</option>', htmlspecialchars( $k ), $selected, htmlspecialchars( $v ) );
		}

		return parent::publish() + [
				"options"    => implode( "", $opt ),
				"caption"    => htmlspecialchars( $caption, ENT_QUOTES | ENT_HTML5 ),
				"rawCaption" => $caption,
			];
	}
}
