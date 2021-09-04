<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

if( !defined('WG_UNITTEST') ) define( 'WG_UNITTEST', true );

require __DIR__ . '/../../api/http/http.php';

class ApiHttpHttpTest extends TestCase
{
	public function test_wg_myselfurl()
	{
		$this->assertEquals(true, wg_is_myselfurl(''));
		$this->assertEquals(true, wg_is_myselfurl('/'));
		$this->assertEquals(true, wg_is_myselfurl('./'));
		$this->assertEquals(true, wg_is_myselfurl('./index.php'));
		$this->assertEquals(true, wg_is_myselfurl('../'));
		$this->assertEquals(true, wg_is_myselfurl('../index.php'));
		$this->assertEquals(true, wg_is_myselfurl('../'));
		$this->assertEquals(true, wg_is_myselfurl('../index.php'));
		$this->assertEquals(true, wg_is_myselfurl('../../'));
		$this->assertEquals(true, wg_is_myselfurl('../../index.php'));
		$this->assertEquals(true, wg_is_myselfurl('https:../../'));
		$this->assertEquals(true, wg_is_myselfurl('https:../../index.php'));

		$this->assertEquals(false, wg_is_myselfurl('https:///../'));
		$this->assertEquals(false, wg_is_myselfurl('https:///../index.php'));
		$this->assertEquals(false, wg_is_myselfurl('https://example.com/index.php'));
		$this->assertEquals(false, wg_is_myselfurl('https://example.com:8080/index.php'));
		$this->assertEquals(false, wg_is_myselfurl('https://user@pass:example.com:8080/index.php'));
		$this->assertEquals(false, wg_is_myselfurl('https://user@pass:example.com:8080/aaa/bbb/../ccc'));
	}

	public function test_wg_query_to_array()
	{
		$this->assertSame([], wg_query_to_array(''));
		$this->assertSame(
			[
				'a'=>'b'
			],
			wg_query_to_array('a=b')
		);

		$this->assertSame(
			[
				'a'=>'b',
				'c'=>'d'
			],
			wg_query_to_array('a=b&c=d')
		);

		$this->assertSame(
			[
				'a'=>'b b',
				'c'=>'d d'
			],
			wg_query_to_array('a=b+b&c=d+d')
		);

		$this->assertSame(
			[
				'a'=>'b b',
				'c'=>'d d'
			],
			wg_query_to_array('a=b+b&c=d+d')
		);

		$this->assertSame(
			[],
			wg_query_to_array('&')
		);

		$this->assertSame(
			[],
			wg_query_to_array('&&&&')
		);
	}
}
