<?php
/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/local-common.php';

require_once __DIR__ . '/../../../framework/c/WGFPCController.php';
require_once __DIR__ . '/../../../framework/gauntlet/WGGInt.php';
require_once __DIR__ . '/../../../framework/parameters/WGParameters.php';

class ParametersStoreRestoreParam extends WGParameters
{
	#[WGPara( tags: ['user'], name: 'u1', gauntlet: new WGGInt( - 10, 10 ), default: 0 )]
	public int $user_var1;

	#[WGPara( tags: ['user'], name: 'u2', gauntlet: new WGGInt( - 10, 10 ), default: 0 )]
	public int $user_var2;

	#[WGPara( tags: ['group'], name: 'g1', gauntlet: new WGGInt( - 10, 10 ), default: 0 )]
	public int $group_vars1;

	#[WGPara( tags: ['group'], name: 'g2', gauntlet: new WGGInt( - 10, 10 ), default: 0 )]
	public int $group_vars2;

	#[WGPara( tags: ['company'], name: 'c1', gauntlet: new WGGInt( - 10, 10 ), default: 0 )]
	public int $company_vars1;

	#[WGPara( tags: ['company'], name: 'c2', gauntlet: new WGGInt( - 10, 10 ), default: 0 )]
	public int $company_vars2;
}

class ParametersStoreRestoreTest extends TestCase
{
	public function test_int_v1()
	{
		$_GET = [
			'u1' => 1,
			'u2' => 2,
			'g1' => 3,
			'g2' => 4,
			'c1' => 5,
			'c2' => 6
		];

		// Host controller
		$controller = @new WGFPCController();

		// Create parameter
		$g1 = new ParametersStoreRestoreParam();
		$g1->byGET();
		$this->assertEquals( 'u1=1&u2=2&g1=3&g2=4&c1=5&c2=6', $g1->getParamString() );

		// Store into session of controller
		$g1->store($controller);

		// Restore from session of controller (Same parameter)
		$g2 = ParametersStoreRestoreParam::restore($controller, WGParameters::METHOD_GET);
		$this->assertNotNull($g2);

		// Restore from session of controller (Different parameter)
		$_GET = [
			'u1' => 1,
			'u2' => 2,
			'g1' => 3,
			'g2' => 4,
			'c1' => 5,
			'c2' => 7
		];
		$g3 = ParametersStoreRestoreParam::restore($controller, WGParameters::METHOD_GET);
		$this->assertNull($g3);
	}
}
