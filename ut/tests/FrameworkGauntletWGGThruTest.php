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

require_once __DIR__ . '/../../framework/gauntlet/WGGThru.php';

class FrameworkGauntletWGGThruTest extends TestCase
{
	public function test_wgg_thru()
	{
		$testClass = WGGThru::class;

		$v =  '';
		$this->assertTrue( $testClass::_()->validate($v) );

		$v =  'value';
		$this->assertTrue( $testClass::_()->validate($v) );

		$v =  true;
		$this->assertTrue( $testClass::_()->validate($v) );

		$v =  false;
		$this->assertTrue( $testClass::_()->validate($v) );

		$v =  999;
		$this->assertTrue( $testClass::_()->validate($v) );

		$v =  999.9876;
		$this->assertTrue( $testClass::_()->validate($v) );

		$v =  null;
		$this->assertTrue( $testClass::_()->validate($v) );
	}
}
