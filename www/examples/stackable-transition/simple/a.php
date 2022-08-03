<?php

require_once __DIR__ . '/../waggo_example.php';

class EXA extends EXPCController
{
	protected function views()
	{
		return [
			'call' => new WGV8BasicSubmit(),
			'val'  => new WGV8BasicElement()
		];
	}

	protected function _call()
	{
		$this->call( 'ret_by_b', 'b.php', $this->view( 'val' )->getValue() );
	}

	protected function ret_by_b( $val )
	{
		if ( $val !== false )
		{
			$this->view( 'val' )->setValue( $val );
		}
	}
}

EXSampleA::START();
