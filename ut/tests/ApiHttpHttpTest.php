<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

if( !defined('WG_UNITTEST') ) define( 'WG_UNITTEST', true );

require_once __DIR__ . '/../../api/http/http.php';

class ApiHttpHttpTest extends TestCase
{
	public function test_wg_remake_url()
    {
        foreach (["", null, true, false, 1, 0000, 'http://example.com:8000/', '/', '../', 'https://example.com:8000/'] as $testCase) {
            $u = wg_remake_url($testCase . '?a=100&b=200', ['a' => 150]);
            $this->assertEquals($testCase . '?a=150&b=200', $u);

            $u = wg_remake_url($testCase, ['a' => 150]);
            $this->assertEquals($testCase . '?a=150', $u);

            $u = wg_remake_url($testCase . '?a=100&b=200', ['a' => 'あああ']);
            $this->assertNotEquals($testCase . '?a=あああ&b=200', $u); //２バイト文字は変換されるので✕　'http://example.com:8000/?a=%E3%81%82%E3%81%82%E3%81%82&b=200'

            $u = wg_remake_url($testCase . '?a=100&b=200', ['a' => 0]);
            $this->assertEquals($testCase . '?a=0&b=200', $u);

            $u = wg_remake_url($testCase . '?a=100&b=200', ['a' => ""]);
            $this->assertEquals($testCase . '?a&b=200', $u);

            $u = wg_remake_url($testCase . '?a=100&b=200', ['a' => null]);
            $this->assertEquals($testCase . '?b=200', $u);

            $u = wg_remake_url($testCase . '?a=100&b=200', ['a' => true]);
            $this->assertEquals($testCase . '?a=1&b=200', $u); // a=1 になる

            $u = wg_remake_url($testCase . '?a=100&b=200', ['a' => false]);
            $this->assertEquals($testCase . '?a=&b=200', $u); // a= になる

            $u = wg_remake_url($testCase . '?a=100&b=200', ['e' => 500]);
            $this->assertEquals($testCase . '?a=100&b=200&e=500', $u);

            // key に null, true, false を入れたとき。
            $u = wg_remake_url($testCase . '?a=100&b=200', ['null' => 500, 'true' => 500, 'false' => 800]);
            $this->assertEquals($testCase . '?a=100&b=200&null=500&true=500&false=800', $u);

            // URLに作為的に / や ? を追加しようとした。
            $u = wg_remake_url($testCase . '?a=100&b=200', ['/' => false, '?' => false]);
            $this->assertNotEquals($testCase . '?a=100&b=200&/=&?=', $u); // 演算子は変換される。 Actual   :'?a=100&b=200&%2F=&%3F='
        }

        $testCase = ["", null, 1, true, false, 0, 0000]; // 文字列結合側が null や　false の場合、assertで "" になることを想定して実行した

        $u = wg_remake_url($testCase[1] . '?a=100&b=200', ['a' => 150]); // null ""
        $this->assertEquals($testCase[0] . '?a=150&b=200', $u);

        $u = wg_remake_url($testCase[3] . '?a=100&b=200', ['a' => 150]); // true 1
        $this->assertEquals($testCase[2] . '?a=150&b=200', $u);

        $u = wg_remake_url($testCase[4] . '?a=100&b=200', ['a' => 150]); // false ""
        $this->assertEquals($testCase[0] . '?a=150&b=200', $u);

        $u = wg_remake_url($testCase[6] . '?a=100&b=200', ['a' => 150]); // 0000 0
        $this->assertEquals($testCase[5] . '?a=150&b=200', $u);

        /*
            1.URLの種類パターンはどれだけあるか？ https ./ ftp www
                keyが重複する場合はある？　'http://example.com:8000/?a=900&a=100'
            2.keyの文字列はどんなものが使えるか？ null,true,false
            3.パラメーター値が null,true,falseの場合は？
            4.URLからクラックされる場合はある？
        */
    }

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
