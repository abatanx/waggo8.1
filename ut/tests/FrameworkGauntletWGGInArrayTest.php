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

class FrameworkGauntletWGGInArrayTest extends TestCase
{
	public function test_wgg_in_array()
	{
        $valid_array = ['ab', 'cd', 'ef', 'gh', 'あいう', 'かきく', 123, 'アイウ' ];
		$testClass = WGGInArray::class;

        $v = '';
        $this->assertFalse( $testClass::_($valid_array)->validate($v) );

        $v = 'a';
        $this->assertFalse( $testClass::_($valid_array)->validate($v) );

        $v = 'ab';
        $this->assertTrue( $testClass::_($valid_array)->validate($v) );

        $v = 'ab ';
        $this->assertFalse( $testClass::_($valid_array)->validate($v) );

        $v = 'あいう';
        $this->assertTrue( $testClass::_($valid_array)->validate($v) );

        $v = 123;
        $this->assertFalse( $testClass::_($valid_array)->validate($v) );

        $v = '123';
        $this->assertTrue( $testClass::_($valid_array)->validate($v) );

        $v = 'ｱｲｳ';
        $this->assertFalse( $testClass::_($valid_array)->validate($v) );

        $valid_array_two = [
            'ID' =>  1,
            'name' =>  'Peter',
        ];
        $v = 'Peter';
        $this->assertTrue( $testClass::_($valid_array_two)->validate($v) );

        $valid_array_two = [
            'ID' =>  1,
            'name' =>  'Peter',
        ];
        $v = 'John';
        $this->assertFalse( $testClass::_($valid_array_two)->validate($v) );
	}
}
