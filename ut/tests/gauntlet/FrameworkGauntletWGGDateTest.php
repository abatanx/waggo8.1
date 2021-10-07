<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/local-common.php';

require_once __DIR__ . '/../../../framework/gauntlet/WGGDate.php';
require_once __DIR__ . '/../../../api/datetime/datetime.php';
require_once __DIR__ . '/../../../api/datetime/WGDateTime.php';

class FrameworkGauntletWGGDateTest extends TestCase
{
	public function test_wgg_date()
	{
		$testClass = WGGDate::class;

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
        $this->assertTrue( $testClass::_()->validate($v) );

        $v =  '1900/1/01';
        $this->assertTrue( $testClass::_()->validate($v) );

        $v =  '1900/01/1';
        $this->assertTrue( $testClass::_()->validate($v) );

        $v =  '1900/1/1';
        $this->assertTrue( $testClass::_()->validate($v) );

        $v =  '1900-01-01';
        $this->assertTrue( $testClass::_()->validate($v) );

        $v =  ' 1900-01-01 ';
        $this->assertTrue( $testClass::_()->validate($v) );

        $v =  '1900-01-01a';
        $this->assertFalse( $testClass::_()->validate($v) );

        $v =  '19000101';
        $this->assertFalse( $testClass::_()->validate($v) );

        $v =  '1900-01-01 12:00:00';
        $this->assertFalse( $testClass::_()->validate($v) );

        $v =  '1900/01/32';
        $this->assertFalse( $testClass::_()->validate($v) );

        $v =  '2024/02/28';
        $this->assertTrue( $testClass::_()->validate($v) );

        $v =  '2024/02/29';
        $this->assertTrue( $testClass::_()->validate($v) );

        $v =  '2024/02/30';
        $this->assertFalse( $testClass::_()->validate($v) );

        $d = new WGDateTime();
        $d->set( 2001, 2, 3 );
        $v = $d->getYMDString();
        $this->assertTrue( $testClass::_()->validate($v) );

        $d->set( 2001, 2, 3, 4, 56, 60 );
        $v = $d->getYMDHISString();
        $this->assertFalse( $testClass::_()->validate($v) );
	}
}
