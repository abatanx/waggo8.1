<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGG.php';

class WGGNotFalse extends WGG
{
	public static function _()
	{
		return new static();
	}

	public function makeErrorMessage()
	{
		return sprintf("内容がありません。");
	}

	public function validate(&$data)
	{
		if( $data !== false )
		{
			return true;
		}
		else
		{
			if( !$this->isBranch() )
				$this->addError($this->makeErrorMessage());
			return false;
		}
	}
}

