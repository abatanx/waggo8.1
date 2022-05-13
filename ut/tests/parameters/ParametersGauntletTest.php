<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/local-common.php';

require_once __DIR__ . '/../../../framework/gauntlet/WGGInt.php';
require_once __DIR__ . '/../../../framework/parameters/WGParameters.php';

class ParametersGauntletParam extends WGParameters
{
	#[WGPara( gauntlet: new WGGInt( - 10, 10 ), default: 0 )]
	public int $a;
}

class ParametersGauntletTest extends TestCase
{
	public function test_int_v1()
	{
		$_GET = [];

		$_GET['a'] = 0;

		$g = new ParametersGauntletParam( null );
		$g->byGET();

		$this->assertEquals( 0, $g->a );
		$this->assertEquals( 'a=0', $g->getParamString() );

		$this->assertEquals( 0, $g->getErrorCount() );

		$_GET['a'] = '0';

		$g = new ParametersGauntletParam( null );
		$g->byGET();

		$this->assertEquals( 0, $g->a );
		$this->assertEquals( 'a=0', $g->getParamString() );

		$this->assertEquals( 0, $g->getErrorCount() );

		$_GET['a'] = '000000';

		$g = new ParametersGauntletParam( null );
		$g->byGET();

		$this->assertEquals( 0, $g->a );
		$this->assertEquals( 'a=0', $g->getParamString() );

		$this->assertEquals( 0, $g->getErrorCount() );
	}

	public function test_int_v2()
	{
		$_GET = [];

		$_GET['a'] = - 100;

		$g = new ParametersGauntletParam( null );
		$g->byGET();

		$this->assertEquals( 0, $g->a );
		$this->assertEquals( 'a=0', $g->getParamString() );

		$this->assertEquals( 1, $g->getErrorCount() );
		echo $g->getError('a');
	}

	public function test_int_v3()
	{
		$_GET = [];

		$_GET['a'] = 100;

		$g = new ParametersGauntletParam( null );
		$g->byGET();

		$this->assertEquals( 0, $g->a );
		$this->assertEquals( 'a=0', $g->getParamString() );

		$this->assertEquals( 1, $g->getErrorCount() );
		echo $g->getError('a');
	}
}
