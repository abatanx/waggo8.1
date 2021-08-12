<?php

require_once 'waggo_example.php';

class EXB extends EXPCController
{
	protected function views()
	{
		return [
			'commit'	=>	new WGV8BasicSubmit(),
			'cancel'	=>	new WGV8BasicSubmit(),
			'val'		=>	new WGV8BasicElement()
		];
	}

	protected function initFirstCall($data)
	{
		$this->view('val')->setValue($data);
	}

	protected function _commit()
	{
		$this->ret($this->view('val')->getValue());
	}

	protected function _cancel()
	{
		$this->ret(false);
	}
}

EXB::START();
