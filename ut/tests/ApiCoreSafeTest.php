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

require_once __DIR__ . '/../unittest-config.php';
require_once __DIR__ . '/../../api/core/safe.php';

class ApiCoreSafeTest extends TestCase
{
	public function test_wg_safe_int()
	{
		$this->assertSame( 10, wg_safe_int( 10 ) );
		$this->assertSame( - 10, wg_safe_int( - 10 ) );
		$this->assertSame( 10, wg_safe_int( '10' ) );
		$this->assertSame( 10, wg_safe_int( '+10' ) );
		$this->assertSame( - 10, wg_safe_int( '-10' ) );
		$this->assertSame( 0, wg_safe_int( '-0' ) );
		$this->assertSame( 0, wg_safe_int( '+0' ) );

		$nine1000 = str_repeat( '9', 1000 );

		foreach ( [ '', '-', '+', '--', '++', ' ', '1-1', '1+1', '1-', '1+', '0xff', $nine1000, '-' . $nine1000 ] as $v )
		{
			try
			{
				wg_safe_int( $v );
				$this->fail();
			}
			catch ( WGRuntimeException )
			{
				$this->assertTrue( true );
			}
		}
	}

	public function test_wg_safe_float()
	{
		$this->assertSame( 10.0, wg_safe_float( 10 ) );
		$this->assertSame( - 10.0, wg_safe_float( - 10 ) );
		$this->assertSame( 10.0, wg_safe_float( '10' ) );
		$this->assertSame( 10.0, wg_safe_float( '+10' ) );
		$this->assertSame( - 10.0, wg_safe_float( '-10' ) );
		$this->assertSame( 0.0, wg_safe_float( '-0' ) );
		$this->assertSame( 0.0, wg_safe_float( '+0' ) );

		$zero309    = str_repeat( '0', 309 );
		$nine1000   = str_repeat( '9', 1000 );
		$nine100000 = str_repeat( '9', 100000 );

		$this->assertSame( PHP_FLOAT_MAX, wg_safe_float( '1' . $zero309 ) );
		$this->assertSame( PHP_FLOAT_MIN, wg_safe_float( '-1' . $zero309 ) );

		$this->assertSame( PHP_FLOAT_MAX, wg_safe_float( '1' . $nine1000 ) );
		$this->assertSame( PHP_FLOAT_MIN, wg_safe_float( '-1' . $nine1000 ) );

		$this->assertSame( PHP_FLOAT_MAX, wg_safe_float( '1' . $nine100000 ) );
		$this->assertSame( PHP_FLOAT_MIN, wg_safe_float( '-1' . $nine100000 ) );

		foreach ( [ '', '-', '+', '--', '++', ' ', '1-1', '1+1', '1-', '1+', '0xff' ] as $v )
		{
			try
			{
				wg_safe_float( $v );
				$this->fail();
			}
			catch ( WGRuntimeException )
			{
				$this->assertTrue( true );
			}
		}
	}

}


