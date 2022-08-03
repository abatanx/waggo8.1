<?php

class ExampleParameter
{
	public WGMModel $model;
	public int $id;

	public function __construct()
	{
		$this->model = new WGMModel('example');
		$this->id = 1000;
	}
}


