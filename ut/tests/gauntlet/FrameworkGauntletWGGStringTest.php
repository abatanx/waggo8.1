<?php
/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/local-common.php';

require_once __DIR__ . '/../../../framework/gauntlet/WGGString.php';

class FrameworkGauntletWGGStringTest extends TestCase
{
	public function test_wgg_string()
	{
		$testClass = WGGString::class;

		$v = '';
		$this->assertTrue( $testClass::_()->validate( $v ) );

		$v = 'value';
		$this->assertTrue( $testClass::_()->validate( $v ) );

        $v = 123;
        $this->assertTrue( $testClass::_()->validate( $v ) );

        $v = '123';
        $this->assertTrue( $testClass::_()->validate( $v ) );

        $v = 99.9876;
        $this->assertTrue( $testClass::_()->validate( $v ) );

        $v = null;
        $this->assertTrue( $testClass::_()->validate( $v ) );

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

        $v = '日本語';
        $this->assertTrue( $testClass::_()->validate( $v ) );

        $v = '123456789';
        $this->assertTrue( $testClass::_()->validate( $v ) );

        $v = '12345678901';
        $this->assertFalse( $testClass::_(0,10)->validate( $v ) );

        $v = '一二三四五六七八九十';
        $this->assertTrue( $testClass::_(0,10)->validate( $v ) );

        $v = '一二三四五六七八九十一';
        $this->assertFalse( $testClass::_(0,10)->validate( $v ) );

        $v =  '髙橋';
        $this->assertTrue( $testClass::_(0,10)->validate( $v ) );
    }
}
