<?php
/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/local-common.php';

require_once __DIR__ . '/../../../framework/gauntlet/WGGFloat.php';

class FrameworkGauntletWGGFloatTest extends TestCase
{
	public function test_wgg_float()
	{
		$testClass = WGGFloat::class;

		$v = '';
		$this->assertFalse( $testClass::_()->validate( $v ) );

		$v = 'value';
		$this->assertFalse( $testClass::_()->validate( $v ) );

		$v = 0;
		$this->assertTrue( $testClass::_()->validate( $v ) );

		$v = 1;
		$this->assertTrue( $testClass::_()->validate( $v ) );

		$v = 0.2;
		$this->assertTrue( $testClass::_()->validate( $v ) );

		$v = -1.00001;
		$this->assertFalse( $testClass::_()->validate( $v ) );

		$v = 99.9876;
		$this->assertFalse( $testClass::_()->validate( $v ) );

		$v = '1e0';
		$this->assertTrue( $testClass::_()->validate( $v ) );

		$v = null;
		$this->assertFalse( $testClass::_()->validate( $v ) );

		try
		{
			$v = true;
			$testClass::_()->validate( $v );
			$this->fail();
		}
		catch ( WGRuntimeException $e )
		{
			$this->assertTrue( true );
		}

		try
		{
			$v = false;
			$testClass::_()->validate( $v );
			$this->fail();
		}
		catch ( WGRuntimeException $e )
		{
			$this->assertTrue( true );
		}

		try
		{
			$v = [];
			$testClass::_()->validate( $v );
			$this->fail();
		}
		catch ( WGRuntimeException $e )
		{
			$this->assertTrue( true );
		}

		try
		{
			$v = new stdClass();
			$testClass::_()->validate( $v );
			$this->fail();
		}
		catch ( WGRuntimeException $e )
		{
			$this->assertTrue( true );
		}

	}
}
