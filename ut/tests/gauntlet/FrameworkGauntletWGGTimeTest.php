<?php
/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/local-common.php';

require_once __DIR__ . '/../../../framework/gauntlet/WGGTime.php';
require_once __DIR__ . '/../../../api/datetime/datetime.php';
require_once __DIR__ . '/../../../api/datetime/WGDateTime.php';

class FrameworkGauntletWGGTimeTest extends TestCase
{
	public function test_wgg_time()
	{
		$testClass = WGGTime::class;

		$v =  '';
		$this->assertFalse( $testClass::_()->validate($v) );

		$v =  'value';
		$this->assertFalse( $testClass::_()->validate($v) );

		$v =  999;
		$this->assertFalse( $testClass::_()->validate($v) );

		$v =  999.9876;
		$this->assertFalse( $testClass::_()->validate($v) );

		$v =  null;
		$this->assertFalse( $testClass::_()->validate($v) );

        $v =  '1900/01/01';
        $this->assertFalse( $testClass::_()->validate($v) );

        $v =  '1900-01-01 12:00:00';
        $this->assertFalse( $testClass::_()->validate($v) );

        $v =  '18:00:00';
        $this->assertTrue( $testClass::_()->validate($v) );

        $v =  '18:00';
        $this->assertTrue( $testClass::_()->validate($v) );

        $v =  '6:00 p.m.';
        $this->assertTrue( $testClass::_()->validate($v) );

        $v =  '25:00';
        $this->assertFalse( $testClass::_()->validate($v) );

        $d = new WGDateTime();
        $d->set( 2001, 2, 3 );
        $v = $d->getYMDString();
        $this->assertFalse( $testClass::_()->validate($v) );

        $d->set( 2001, 2, 3, 4, 56, 60 );
        $v = $d->getHISString();
        $this->assertTrue( $testClass::_()->validate($v) );
	}
}
