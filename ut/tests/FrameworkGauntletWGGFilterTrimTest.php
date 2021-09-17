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

require_once __DIR__ . '/../../framework/gauntlet/WGGFilterTrim.php';

class FrameworkGauntletWGGFilterTrimTest extends TestCase
{
	public function test_wgg_filter_trim()
	{
		$testClass = WGGFilterTrim::class;

		$v =  '';
		$this->assertTrue( $testClass::_()->validate($v) );

        $v =  ' ';
        $this->assertTrue( $testClass::_()->validate($v) );

		$v =  'value';
		$this->assertTrue( $testClass::_()->validate($v) );

        $v =  'あいうえお';
        $this->assertTrue( $testClass::_()->validate($v) );

        $v =  'アイウエオ';
        $this->assertTrue( $testClass::_()->validate($v) );

        $v =  'ｱ';
        $this->assertTrue( $testClass::_()->validate($v) );

        $v =  'ｱ　い';
        $this->assertTrue( $testClass::_()->validate($v) );

        $v =  '日本';
        $this->assertTrue( $testClass::_()->validate($v) );

        $v =  '高';
        $this->assertTrue( $testClass::_()->validate($v) );

        $v =  '髙橋';     //　上の'高'感じとこの'髙'感じ違います。
        $this->assertTrue( $testClass::_()->validate($v) );

        $v =  '「いっぱい」';
        $this->assertTrue( $testClass::_()->validate($v) );

        $v =  '存在する・存在する/存在する。';
        $this->assertTrue( $testClass::_()->validate($v) );

        $v =  '〒120-0000';
        $this->assertTrue( $testClass::_()->validate($v) );

        $v =  '〶120-0000';
        $this->assertTrue( $testClass::_()->validate($v) );

        $v =  '三千円';
        $this->assertTrue( $testClass::_()->validate($v) );

        $v =  'A strange string to pass, maybe with some ø, æ, å characters.';
        $this->assertTrue( $testClass::_()->validate($v) );

		$v =  999;
		$this->assertTrue( $testClass::_()->validate($v) );

		$v =  999.9876;
		$this->assertTrue( $testClass::_()->validate($v) );
	}
}
