<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

if ( ! defined( 'WG_UNITTEST' ) )
{
	define( 'WG_UNITTEST', true );
}

require_once __DIR__ . '/../../framework/gauntlet/WGGInt.php';

class FrameworkGauntletWGGIntTest extends TestCase
{
	public function test_wgg_int()
	{
		$testClass = WGGInt::class;

		$v =  '';
		$this->assertFalse( $testClass::_()->validate($v) );

		$v =  'value';
		$this->assertFalse( $testClass::_()->validate($v) );

		$v =  true;
		$this->assertFalse( $testClass::_()->validate($v) );

		$v =  false;
		$this->assertFalse( $testClass::_()->validate($v) );

		$v =  999;
		$this->assertTrue( $testClass::_()->validate($v) );

		$v =  999.9876;
		$this->assertFalse( $testClass::_()->validate($v) );

		$v =  null;
		$this->assertFalse( $testClass::_()->validate($v) );
	}
}