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

require_once __DIR__ . '/../../framework/gauntlet/WGGNotFalse.php';

class FrameworkGauntletWGGNotFalseTest extends TestCase
{
	public function test_wgg_not_false()
	{
		$testClass = WGGNotFalse::class;

        try
        {
            $v =  '';
            $testClass::_()->validate( $v );
            $this->fail();
        }
        catch ( WGRuntimeException $e )
        {
            $this->assertTrue( true );
        }

        try
        {
            $v =  'value';
            $testClass::_()->validate( $v );
            $this->fail();
        }
        catch ( WGRuntimeException $e )
        {
            $this->assertTrue( true );
        }

        try
        {
            $v =  '日本';
            $testClass::_()->validate( $v );
            $this->fail();
        }
        catch ( WGRuntimeException $e )
        {
            $this->assertTrue( true );
        }

		$v =  true;
		$this->assertTrue( $testClass::_()->validate($v) );

		$v =  false;
		$this->assertFalse( $testClass::_()->validate($v) );

        try
        {
            $v =  null;
            $testClass::_()->validate( $v );
            $this->fail();
        }
        catch ( WGRuntimeException $e )
        {
            $this->assertTrue( true );
        }

        try
        {
            $v =  1;
            $testClass::_()->validate( $v );
            $this->fail();
        }
        catch ( WGRuntimeException $e )
        {
            $this->assertTrue( true );
        }

        try
        {
            $v =  0;
            $testClass::_()->validate( $v );
            $this->fail();
        }
        catch ( WGRuntimeException $e )
        {
            $this->assertTrue( true );
        }

        try
        {
            $v =  'true';
            $this->assertTrue( $testClass::_()->validate($v) );
        }
        catch ( WGRuntimeException $e )
        {
            $this->assertTrue( true );
        }

        try
        {
            $v =  'false';
            $this->assertTrue( $testClass::_()->validate($v) );
        }
        catch ( WGRuntimeException $e )
        {
            $this->assertTrue( true );
        }
	}
}