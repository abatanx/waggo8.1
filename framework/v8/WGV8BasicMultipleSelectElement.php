<?php
/**
 * waggo8
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGV8Object.php';

class WGV8BasicMultipleSelectElement extends WGV8BasicSelectElement
{
	public function setValue( mixed $v ): self
	{
		if ( $v === false || is_null( $v ) )
		{
			parent::setValue( [] );
		}
		else if ( ! is_array( $v ) )
		{
			parent::setValue( [ $v ] );
		}
		else
		{
			parent::setValue( $v );
		}

		return $this;
	}

	public function getValue(): mixed
	{
		$v = parent::getValue();
		if ( $v === false || is_null( $v ) )
		{
			return [];
		}
		else if ( ! is_array( $v ) )
		{
			return [ $v ];
		}
		else
		{
			return $v;
		}
	}

	public function postCopy(): self
	{
		if ( isset( $_POST[ $this->getKey() ] ) )
		{
			$postValue = $_POST[ $this->getKey() ];

			$vs = is_array( $postValue ) ? $postValue : [];

			if ( $this->isPostCheck )
			{
				$rs = [];

				$skeys = [];
				foreach ( array_keys( $this->options ) as $k )
				{
					$skeys[] = (string) $k;
				}

				foreach ( $vs as $vp )
				{
					$v = (string) $vp;
					if ( in_array( $v, $skeys, true ) )
					{
						$rs[] = $v;
					}
				}
				$this->setValue( $rs );
			}
			else
			{
				$this->setValue( $vs );
			}
		}
		$this->filterGauntlet();

		return $this;
	}

	public function publish(): array
	{
		$checkes = array_map( function ( $v ) {
			return (string) $v;
		}, $this->getValue() );

		$i = $this->controller->inputType == WGFController::SHOWHTML ?
			[] : [ 'init' => sprintf( '<input type="hidden" id="%s" name="%s" value="">', $this->getId() . '-init', $this->getName() ) ];

		$opt = [];
		foreach ( $this->options as $k => $v )
		{
			$selected = in_array( (string) $k, $checkes, true ) ? " selected" : "";
			$opt[]    = sprintf( '<option value="%s"%s>%s</option>', htmlspecialchars( $k ), $selected, htmlspecialchars( $v ) );
		}

		return
			[
				'id'       => $this->getId(),
				"name"     => $this->getKey() . '[]',
				'value'    => false,
				'error'    => htmlspecialchars( $this->getError(), ENT_QUOTES | ENT_HTML5 ),
				'rawValue' => $this->getValue(),
				'rawError' => $this->getValue(),
				'params'   => $this->params->toString(),
				'options'  => implode( "", $opt )
			] + $i;
	}
}
