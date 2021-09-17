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

require_once __DIR__ . '/../../framework/gauntlet/WGGInArray.php';
require_once __DIR__ . '/../../framework/gauntlet/WGGInArrayStrict.php';


class FrameworkGauntletWGGInArrayStrictTest extends TestCase
{
	public function test_wgg_in_array_strict()
	{
        $valid_array = ['', 'ab', 'cd', 'ef', 'gh', 'あいう', 'アイウ', '日本語', 123, 123.001, '0'];
        $valid_array_two = [
            'ID' =>  1,
            'name' =>  'Peter',
        ];

        $testClass = WGGInArrayStrict::class;

        $v = '';
        $this->assertTrue( $testClass::_($valid_array)->validate($v) );

        $v = null;
        $this->assertTrue( $testClass::_($valid_array)->validate($v) );

        $v = 'a';
        $this->assertFalse( $testClass::_($valid_array)->validate($v) );

        $v = 'ab　';
        $this->assertFalse( $testClass::_($valid_array)->validate($v) );

        $v = 'ab';
        $this->assertTrue( $testClass::_($valid_array)->validate($v) );

        $v = 'あいう';
        $this->assertTrue( $testClass::_($valid_array)->validate($v) );

        $v = 'ｱｲｳ';
        $this->assertFalse( $testClass::_($valid_array)->validate($v) );

        $v = '日本';
        $this->assertFalse( $testClass::_($valid_array)->validate($v) );

        $v = '日本語';
        $this->assertTrue( $testClass::_($valid_array)->validate($v) );

        $v = '123';
        $this->assertTrue( $testClass::_($valid_array)->validate($v) );

        $v = 123;
        $this->assertTrue( $testClass::_($valid_array)->validate($v) );

        $v = 123.0;
        $this->assertTrue( $testClass::_($valid_array)->validate($v) );

        $v = 123.002;
        $this->assertFalse( $testClass::_($valid_array)->validate($v) );

        $v = 'Peter';
        $this->assertTrue( $testClass::_($valid_array_two)->validate($v) );

        $v = 'John';
        $this->assertFalse( $testClass::_($valid_array_two)->validate($v) );

        $v = 'ID';
        $this->assertFalse( $testClass::_($valid_array_two)->validate($v) );

        $v = '0';
        $this->assertTrue( $testClass::_($valid_array_two)->validate($v) );
	}
}
