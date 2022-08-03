<?php
/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/local-common.php';

require_once __DIR__ . '/../../../framework/gauntlet/WGGReg.php';

class FrameworkGauntletWGGRegTest extends TestCase
{
	public function test_wgg_reg()
	{
		$testClass = WGGReg::class;

        $v = 'value';
        $this->assertTrue( $testClass::_('/^[\w-]+$/')->validate( $v ) );

        $v = 'value1';
        $this->assertTrue( $testClass::_('/^[\w-]+$/')->validate( $v ) );

        $v = 'value ';
        $this->assertFalse( $testClass::_('/^[\w-]+$/')->validate( $v ) );

        $v = 'value ';
        $this->assertTrue( $testClass::_('/^[\w-]+\s$/')->validate( $v ) );

		$v = 'foobarbaz';
		$this->assertTrue( $testClass::_('/(foo)(bar)(baz)/')->validate( $v ) );

        $v = 'abcdef';
        $this->assertFalse( $testClass::_('/^def/')->validate( $v ) );

        $v = 'PHP is the web scripting language of choice.';
        $this->assertTrue( $testClass::_('/php/i')->validate( $v ) );

        $v = 'PHP is the web scripting language of choice.';
        $this->assertFalse( $testClass::_('/php/')->validate( $v ) );

        $v = 'http://www.php.net/index.html.';
        $this->assertTrue( $testClass::_('@^(?:http://)?([^/]+)@i')->validate( $v ) );

        $v = '田中はなコ';
        $this->assertTrue( $testClass::_('/^[ぁ-んァ-ヶ一-龠々]+$/u')->validate( $v ) );

        $v = 11111;
        $this->assertTrue( $testClass::_('/[\d]+/')->validate( $v ) );

        $v = null;
        $this->assertFalse( $testClass::_('/[\d]+/')->validate( $v ) );

        $v = 123;
        $this->assertTrue( $testClass::_('/\d*(?:\.\d+)?/')->validate( $v ) );

        $v = 123.45;
        $this->assertTrue( $testClass::_('/\d*(?:\.\d+)?/')->validate( $v ) );
	}
}
