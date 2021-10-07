<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

if( !defined('WG_UNITTEST') ) define( 'WG_UNITTEST', true );

require_once __DIR__ . '/../../../waggo.php';
require_once __DIR__ . '/../../../framework/c/WGFSession.php';

class FrameworkControllerWGFSessionTest extends TestCase
{
	public function test_session_get_set_string()
	{
		$sess = new WGFSession( 'aaa', 'bbb' );
		$sess->set('a', 'test');
		$this->assertSame( 'test', $sess->get('a'));
		$this->assertStringMatchesFormat('test', $sess->get('a'));
        $this->assertStringNotMatchesFormat('task', $sess->get('a'));
	}

	public function test_session_get_set_true() // 1はtrue、0はfalseで判定した。また、0と'0'で一致した。
    {
        $sess = new WGFSession('ccc','ddd');
        $sess->set('c', true);
        $this->assertTrue($sess->get('c'));
        $this->assertSame(true, $sess->get('c'));
        $this->assertNotSame(1, $sess->get('c'));

        $sess->set('c', false);
        $this->assertFalse($sess->get('c'));
        $this->assertSame(false, $sess->get('c'));
        $this->assertNotSame(0, $sess->get('c'));
    }

    public function test_session_get_set_number() //
    {
        $sess = new WGFSession('ccc','ddd');
        $sess->set('c', 1);
        $this->assertIsNumeric($sess->get('c'));
        $this->assertSame(1, $sess->get('c'));
        $this->assertEquals(true, $sess->get('c'));
        $this->assertNotSame('1', $sess->get('c'));
        $this->assertNotSame(false, $sess->get('c'));

        $sess->set('c', 0);
        $this->assertIsNumeric($sess->get('c'));
        $this->assertSame(0, $sess->get('c'));
        $this->assertEquals(false, $sess->get('c'));
        $this->assertNotSame('0', $sess->get('c'));
        $this->assertNotSame(true, $sess->get('c'));
    }

    public function test_session_get_set_float() // 小数点の判定。
    {
        $sess = new WGFSession('ccc', 'ddd');
        $sess->set('c', 2.127894321);
        $this->assertSame(2.127894321, $sess->get('c'));
    }

    public function test_session_get_set_array() //中身の値さえ一致すればよい。
    {
        $ary = [1,2,3,0,true,false,null];
        $sess = new WGFSession('ccc','ddd');
        $sess->set('c', $ary[1]);
        $this->assertSame(2, $sess->get('c'));
        $this->assertNotSame('2', $sess->get('c'));

        $sess->set('c', $ary[4]);
        $this->assertTrue($sess->get('c'));
        $this->assertNotFalse($sess->get('c'));

        $sess->set('c', $ary[5]);
        $this->assertFalse($sess->get('c'));
        $this->assertNotTrue($sess->get('c'));

        $sess->set('c', $ary[6]);
        $this->assertNull($sess->get('c'));
        $this->assertNotSame(false, $sess->get('c'));
    }

    public function test_session_get_set_json() // オブジェクト。文字列の受け渡しになるので一致する。
    {
        $json1 = '{"a":1,"b":2,"c":3}';
        $json2 = '{"b":1,"a":2,"c":3}';
        $json3 = '{"b":2,"a":1,"c":3}';
        $sess = new WGFSession('ccc','ddd');
        $sess->set('c', json_decode($json1));
        $this->assertEquals(json_decode($json1), $sess->get('c'));
        $this->assertNotEquals(json_decode($json2), $sess->get('c'));
        $this->assertEquals(json_decode($json3), $sess->get('c'));
    }

    public function test_session_get_set_null()
    {
        $sess = new WGFSession('ccc','ddd');
        $sess->set('c', null);
        $this->assertNull($sess->get('c'));

    }


    public function test_session_isExists_isEmpty() //
    {
        $sess = new WGFSession('ccc','ddd');
        $sess->set('c', 'test');
        $this->assertTrue($sess->isExists('c'));
        $this->assertFalse($sess->isEmpty('c'));

        $sess->delete('c');
        $this->assertFalse($sess->isExists('c'));
        $this->assertTrue($sess->isEmpty('c'));

        $sess->set('c', true);
        $this->assertTrue($sess->isExists('c'));
        $this->assertFalse($sess->isEmpty('c'));

        $sess->set('c', false);
        $this->assertTrue($sess->isExists('c'));
        $this->assertTrue($sess->isEmpty('c'));

        $sess->set('c', null);
        $this->assertFalse($sess->isExists('c'));
        $this->assertTrue($sess->isEmpty('c'));

    }

    public function test_session_cleanup() //
    {
        $sess = new WGFSession('ccc', 'ddd');
        $sess->set('a', 'aaa');
        $sess->set('b', 111);
        $sess->set('c', true);

        $this->assertTrue($sess->isExists('a'));
        $this->assertTrue($sess->isExists('b'));
        $this->assertTrue($sess->isExists('c'));

        $sess->cleanup();
        $this->assertTrue($sess->isEmpty('a'));
        $this->assertTrue($sess->isEmpty('b'));
        $this->assertTrue($sess->isEmpty('c'));
    }
}
