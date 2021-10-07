<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

function wg_is_login(): bool
{
	return ( isset( $_SESSION['_sUID'] ) && $_SESSION['_sUID'] !== 0 );
}

/**
 * ログインしているのであればユーザコードを取得する。
 * @return int ログイン済みの場合ユーザコードを、未ログインの場合 0 を返す。
 */
function wg_get_usercd(): int
{
	return ( wg_is_login() ) ? intval( $_SESSION['_sUID'] ) : 0;
}

/**
 * ユーザコードのユーザが存在するかチェックする。
 *
 * @param int|string $usercd 判定するユーザコード
 *
 * @return boolean 存在すればTrueを、存在しなければFalseを返す。
 */
function wg_is_user( int|string $usercd ): bool
{
	$usercd = (int) $usercd;

	$v = _QQ( "SELECT true FROM base WHERE usercd = %s AND enabled = TRUE AND deny = FALSE;", _N( $usercd ) );

	return (bool) $v;
}

/**
 * ユーザコードがログインしている自分自身か返す。
 *
 * @param int|string $usercd 判定するユーザコード。
 *
 * @return boolean 自分自身の場合Trueを、自分以外の場合はFalseを返す。
 */
function wg_is_myself( int|string $usercd ): bool
{
	$usercd = (int) $usercd;

	return wg_is_login() && wg_get_usercd() === $usercd;
}

/**
 * ユーザコードのユーザが管理権限を持っているかチェックする。
 *
 * @param int|string|null $usercd 判定するユーザコード(null の場合は、ログインしているユーザのユーザコード)。
 *
 * @return boolean 管理権限があればTrueを返す。
 */
function wg_is_admin( int|string|null $usercd = null ): bool
{
	if ( is_null( $usercd ) )
	{
		$usercd = wg_get_usercd();
	}
	else
	{
		$usercd = (int) $usercd;
	}

	list( $sec ) = _QQ( "SELECT security FROM base_normal WHERE usercd = %s;", _N( $usercd ) );

	return ( $sec >= WGSECURE_SL_ADMIN );
}

/**
 * ログインを行う。
 *
 * @param int|string $usercd セッションをユーザがログインした状態に変更する。
 */
function wg_set_login( int|string $usercd ): void
{
	$_SESSION['_sUID']   = (int) $usercd;
	$_SESSION['_sRHOST'] = wg_get_remote_adr();
}

/**
 * ログアウトを行う。
 */
function wg_unset_login(): void
{
	unset( $_SESSION['_sUID'] );
	unset( $_SESSION['_sRHOST'] );
}
