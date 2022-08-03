<?php
/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/local-common.php';

require_once __DIR__ . '/../../../framework/gauntlet/WGGInt.php';
require_once __DIR__ . '/../../../framework/parameters/WGParameters.php';

class ParametersSimpleParam extends WGParameters
{
	#[WGPara]
	public ?int $a;

	#[WGPara]
	public ?int $b;

	#[WGPara]
	public ?string $c;

	public int $extension = 20;
}

class ParametersSimpleTest extends TestCase
{
	public function test_get()
	{
		$_GET = $_POST = [];

		$_GET['b'] = 67890;
		$_GET['c'] = 'ababab';
		$_GET['a'] = 12345;

		$g = new ParametersSimpleParam( null );
		$g->byGET();

		$this->assertEquals( 12345, $g->a );
		$this->assertEquals( 'a=12345&b=67890&c=ababab', $g->getParamString() );

		$this->assertEquals(20, $g->extension);
	}

	public function test_post()
	{
		$_GET = $_POST = [];

		$_POST['b'] = 67890;
		$_POST['c'] = 'ababab';
		$_POST['a'] = 12345;

		$g = new ParametersSimpleParam( null );
		$g->byPOST();

		$this->assertEquals( 12345, $g->a );
		$this->assertEquals( 'a=12345&b=67890&c=ababab', $g->getParamString() );

		$this->assertEquals(20, $g->extension);
	}
}
