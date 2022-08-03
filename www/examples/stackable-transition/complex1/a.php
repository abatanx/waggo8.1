<?php

require_once __DIR__ . '/../waggo_example.php';
require_once __DIR__ . '/common.php';

class EXComplex1A extends EXPCController
{
	protected ?ExampleParameter $param;

	protected function create()
	{
		$this->param = new ExampleParameter();
	}

	protected function views()
	{
		return [
			'call' => new WGV8BasicSubmit(),
			'val'  => new WGV8BasicElement()
		];
	}

	protected function _call()
	{
		$this->param->id = (int) $this->view('val')->getValue();
		$this->call( 'ret_by_b', 'b.php', $this->param );
	}

	protected function ret_by_b( $param )
	{
		if ( $param instanceof ExampleParameter )
		{
			$this->param = $param;
			$this->view( 'val' )->setValue( $this->param->id );
		}
	}
}

EXComplex1A::START();
