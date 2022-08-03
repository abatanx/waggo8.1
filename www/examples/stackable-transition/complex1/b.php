<?php

require_once __DIR__ . '/../waggo_example.php';
require_once __DIR__ . '/common.php';

class EXComplex1B extends EXPCController
{
	protected function views()
	{
		return [
			'commit' => new WGV8BasicSubmit(),
			'cancel' => new WGV8BasicSubmit(),
			'val'    => new WGV8BasicElement()
		];
	}

	protected function initFirstCall( $param )
	{
		if( $param instanceof ExampleParameter )
		{
			$this->session->set('param', $param);
			$this->view( 'val' )->setValue( $param->id );
		}
	}

	protected function _commit()
	{
		if( $this->session->get('param') )
		{
			$param = $this->session->get('param');
			$param->id = (int) $this->view('val')->getValue();
			$this->ret( $param );
		}

		$this->ret( null );
	}

	protected function _cancel()
	{
		$this->ret( false );
	}
}

EXComplex1B::START();
