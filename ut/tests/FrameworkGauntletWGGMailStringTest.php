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
require_once __DIR__ . '/../../api/core/check.php';
require_once __DIR__ . '/../../framework/gauntlet/WGGMailString.php';

class FrameworkGauntletWGGMailStringTest extends TestCase
{
	public function test_wgg_mail_string()
	{
		$testClass = WGGMailString::class;

		$v = '';
		$this->assertFalse( $testClass::_()->validate( $v ) );

		$v = 'value';
		$this->assertFalse( $testClass::_()->validate( $v ) );

		$v = 100;
		$this->assertFalse( $testClass::_()->validate( $v ) );

		$v = null;
		$this->assertFalse( $testClass::_()->validate( $v ) );

        $v = 'user@email.com';
        $this->assertTrue( $testClass::_()->validate( $v ) );

        $v = 'user@email.co.jp';
        $this->assertTrue( $testClass::_()->validate( $v ) );

        $v = 'user1@email.co.jp;email2@email.co.jp;';
        $this->assertFalse( $testClass::_()->validate( $v ) );

        $v = '_somename@example.com';
        $this->assertTrue( $testClass::_()->validate( $v ) );

        $v = 'anzai-kt@itec.hankyu-hanshin.co.jp';
        $this->assertTrue( $testClass::_()->validate( $v ) );

        $v = 'very.common@example.com';
        $this->assertTrue( $testClass::_()->validate( $v ) );

        $v = 'Abc@example.com';
        $this->assertTrue( $testClass::_()->validate( $v ) );

        $v = 'Abc.123@example.com';
        $this->assertTrue( $testClass::_()->validate( $v ) );

        $v = 'i_like_underscore@@but_its_not_allowed_in_this_part.example.com';
        $this->assertFalse( $testClass::_()->validate( $v ) );

        $v = 'foo <foo@example.com>';
        $this->assertFalse( $testClass::_()->validate( $v ) );

        $v = 'A@b@c@example.com';
        $this->assertFalse( $testClass::_()->validate( $v ) );

        $v = 'spmodemsgr@wdy.docomo.ne.jp';
        $this->assertTrue( $testClass::_()->validate( $v ) );

        $v = '11111111111@docomo.ne.jp';
        $this->assertTrue( $testClass::_()->validate( $v ) );

        $v = '11111111111@ezweb.ne.jp';
        $this->assertTrue( $testClass::_()->validate( $v ) );
	}
}
