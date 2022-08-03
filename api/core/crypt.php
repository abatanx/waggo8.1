<?php
/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

/**
 * ハッシュキーを用いて SHA256 で文字列をハッシュ化する。
 * ハッシュキーは、WGCONF_HASHKEY による。
 *
 * @param string $str ハッシュ化する文字列
 *
 * @return string ハッシュ化後文字列
 */
function wg_hash( string $str ): string
{
	return hash_hmac( 'sha256', $str, WGCONF_HASHKEY );
}

/**
 * パスワード用ハッシュキーを用いて SHA256 で文字列をハッシュ化する。
 * ハッシュキーは、WGCONF_PASSWORD_HASHKEY による。
 *
 * @param string $str ハッシュ化する文字列
 *
 * @return string ハッシュ化後文字列
 */
function wg_password_hash( string $str ): string
{
	return hash_hmac( 'sha256', $str, WGCONF_PASSWORD_HASHKEY );
}
