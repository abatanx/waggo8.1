<?php

/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

if( !defined('WG_UNITTEST') ) define( 'WG_UNITTEST', true );

require __DIR__ . '/../unittest-config.php';
require __DIR__ . '/../../api/core/lib.php';
require __DIR__ . '/../../api/user/users.php';
require __DIR__ . '/../../api/dbms/interface.php';

/**
1. セッションの扱いは？ $_SESSION["_sUID"] は勝手に設定してよい？ -> 文字列を設定する。
2. DBとのつながりは？DBは存在する？ -> DBは現時点で使わないと思われる。

 **/

class ApiUsersUsersTest extends TestCase
{
    public function test_wg_is_myself()
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

    public function test_wg_is_user()
    {
        // tableをつくる base は waggo8/api/initdata/sql/data/core_template.sql より参照した。
        _E(<<<SQL
DROP VIEW IF EXISTS base_normal;
DROP TABLE IF EXISTS base;
CREATE TABLE base (
    usercd INTEGER NOT NULL,
    login VARCHAR(256) NOT NULL,
    password VARCHAR(256) NOT NULL,
    name VARCHAR(256) NOT NULL,
    enabled BOOLEAN NOT NULL,
    deny BOOLEAN NOT NULL,
    security INTEGER NOT NULL,
    initymd TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updymd TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE base ADD PRIMARY KEY (usercd);
CREATE VIEW base_normal AS SELECT * FROM base WHERE enabled=true AND deny=false;
CREATE UNIQUE INDEX base_pkey1 ON base (login);
INSERT INTO base(usercd,login,password,name,enabled,deny,security,initymd,updymd) VALUES(0,'','','Guest',true,false,0,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
SQL
        );

        // ここからテストコード
        $this->assertEquals(true,wg_is_user(0));
        $this->assertEquals(false,wg_is_user(1));
        $this->assertEquals(true,wg_is_user(0000));
        $this->assertEquals(true,wg_is_user(false));
        $this->assertEquals(false,wg_is_user(true));
        $this->assertEquals(false,wg_is_user(null));

        _E(<<<SQL
DROP VIEW IF EXISTS base_normal;
DROP TABLE IF EXISTS base; --終了するときにtableは消す
SQL
        );
    }
}