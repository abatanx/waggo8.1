<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

class WGV8Params
{
	public array $params;
	public string $errorStyle;

	public function __construct()
	{
		$this->params     = [];
		$this->errorStyle = '';
	}

	public function add( array $keyValueList ): self
	{
		foreach ( $keyValueList as $k => $v )
		{
			$this->params[ $k ] = $v;
		}

		return $this;
	}

	public function clear(): self
	{
		$this->params = [];

		return $this;
	}

	public function delete( string $key ): self
	{
		unset( $this->params[ $key ] );

		return $this;
	}

	public function get( string $key ): mixed
	{
		return $this->params[ $key ];
	}

	public function setErrorStyle( string $errorStyle ): self
	{
		$this->errorStyle = $errorStyle;

		return $this;
	}

	public function clearErrorStyle(): self
	{
		$this->errorStyle = '';

		return $this;
	}

	public function toString(): string
	{
		$tmp = $this->params;
		if ( isset( $tmp['style'] ) )
		{
			$tmp['style'] .= $this->errorStyle;
		}

		if ( empty( $tmp['style'] ) )
		{
			unset( $tmp['style'] );
		}

		$str = '';
		foreach ( $tmp as $k => $v )
		{
			$str .= empty( $v ) ?
				sprintf( '%s ', htmlspecialchars( $k ) ) :
				sprintf( '%s="%s" ', htmlspecialchars( $k ), addcslashes( $v, '"' ) );
		}

		return ( trim( $str ) == '' ) ? '' : ( ' ' . trim( $str ) );
	}
}
