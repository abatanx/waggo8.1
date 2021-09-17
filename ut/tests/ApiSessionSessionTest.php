<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\WGFSession;

if ( ! defined( 'WG_UNITTEST' ) )
{
	define( 'WG_UNITTEST', true );
}

/**
 * 固有セッション管理インスタンスを作成する。
 * 固有セッション管理インスタンスを結合キーから復帰する。
 * 固有セッション管理インスタンスを破棄する。
 *
 * PHPセッションを開始する。
 * PHPセッションを終了する。
 *
 * 固有セッション管理IDを取得する。
 * 固有セッション管理IDのうち、複合IDを取得する。
 * 固有セッション管理IDのうち、セッション管理IDを取得する。
 * 固有セッション管理IDのうち、画面遷移IDを取得する。
 *
 * 固有セッションのすべての情報を返します。通常は配列で得られます。
 * 固有セッション管理IDで管理している領域に、データをセットします。
 * 固有セッション管理IDで管理している領域から、データを取得します。 <- key の取得？？
 * 固有セッション管理IDで管理している領域に、データをセットします。
 * 固有セッション管理IDで管理している領域から、データを取得します。
 * 固有セッション管理IDで管理している領域に、データがセットされているか確認します。
 * 固有セッション管理IDで管理している領域で、該当するキーのデータが空の状態化か確認します。
 * 固有セッション管理IDで管理している領域で、該当するキーのデータを削除します。
 * 固有セッション管理IDで管理している領域を GC対象領域としてマークします。
 * PHPセッションから、固有セッションの状態を確認し、利用されていない場合開放します。
 *
**/

class ApiSessionSessionTest extends TestCase implements WGFSession
{
	public function test_wg_unset_session()
    {

    }

}