<?php

/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

if( !defined('WG_UNITTEST') ) define( 'WG_UNITTEST', true );

require __DIR__ . '/../../api/user/users.php';

/**
1. セッションの扱いは？ $_SESSION["_sUID"] は勝手に設定してよい？ -> 文字列を設定する。
2. DBとのつながりは？DBは存在する？ -> DBは現時点で使わないと思われる。

 **/

class ApiUsersUsersTest extends TestCase
{
    public function test_user_code()
    {

        $_SESSION["_sUID"] = "abcd1234"; // #_SESSION 側がおかしな値の場合は考える？？

        $this->assertEquals(true, wg_is_myself('abcd1234'));
        $this->assertNotEquals(true, wg_is_myself('1234abcd'));
        $this->assertNotEquals(true, wg_is_myself('abcd1234 '));
        $this->assertNotEquals(true, wg_is_myself(true));
        $this->assertNotEquals(true, wg_is_myself(false));
        $this->assertNotEquals(true, wg_is_myself(null));
        $this->assertNotEquals(true, wg_is_myself(1));
        $this->assertNotEquals(true, wg_is_myself(0));
    }


}