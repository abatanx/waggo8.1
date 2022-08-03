<?php
/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/local-common.php';

require_once __DIR__ . '/../../../framework/gauntlet/WGGEmpty.php';

class FrameworkGauntletWGGEmptyTest extends TestCase
{
	public function test_wgg_empty()
	{
		$testClass = WGGEmpty::class;

		$v =  '';
		$this->assertTrue( $testClass::_()->validate($v) );

        $v =  ' ';
        $this->assertFalse( $testClass::_()->validate($v) );

		$v =  'value';
		$this->assertFalse( $testClass::_()->validate($v) );

        $v =  'あいうえお';
        $this->assertFalse( $testClass::_()->validate($v) );

        $v =  'アイウエオ';
        $this->assertFalse( $testClass::_()->validate($v) );

        $v =  'ｱ';
        $this->assertFalse( $testClass::_()->validate($v) );

        $v =  '日本';
        $this->assertFalse( $testClass::_()->validate($v) );

		$v =  true;
		$this->assertFalse( $testClass::_()->validate($v) );

		$v =  false;
		$this->assertTrue( $testClass::_()->validate($v) );

        $v =  null;
        $this->assertTrue( $testClass::_()->validate($v) );

		$v =  999;
		$this->assertFalse( $testClass::_()->validate($v) );

		$v =  999.9876;
		$this->assertFalse( $testClass::_()->validate($v) );
	}
}
