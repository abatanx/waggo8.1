<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/lib.php';

/**
 * 入力値が数値(numeric)であるかチェックし、妥当であれば変数にセットする
 *
 * @param ?float $result チェック後セットされる変数、エラーの場合、0 がセットされる
 * @param ?string $src 入力値(文字列)
 * @param float $min 受け入れる数値の最小値
 * @param float $max 受け入れる数値の最大値
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す
 */
function wg_inchk_number( ?float &$result, ?string $src, float $min = 0, float $max = 2147483647 ): bool
{
	$src    = (string) $src;
	$result = 0;

	if ( ! wg_check_is_number( $src, $min, $max ) )
	{
		return false;
	}
	$result = $src;

	return true;
}

/**
 * 入力値が整数(integer)であるかチェックし、妥当であれば変数にセットする
 * 浮動小数点の場合 int にキャストする
 *
 * @param ?int $result チェック後セットされる変数、エラーの場合、0 がセットされる
 * @param ?string $src 入力値(文字列)
 * @param int $min 受け入れる整数の最小値
 * @param int $max 受け入れる整数の最大値
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す
 */
function wg_inchk_int( ?int &$result, ?string $src, int $min = 0, int $max = 2147483647 ): bool
{
	$src    = (string) $src;
	$result = 0;

	if ( ! wg_check_is_number( $src, $min, $max ) )
	{
		return false;
	}
	$result = (int) $src;

	return true;
}

/**
 * 入力値が規定範囲内の文字列であるかチェックし、妥当であれば変数にセットする
 *
 * @param ?string $result チェック後セットされる変数、エラーの場合、"" がセットされる
 * @param ?string $src 入力値(文字列)
 * @param int $min 受け入れる文字列の最小長
 * @param int $max 受け入れる文字列の最大長
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す
 */
function wg_inchk_string( ?string &$result, ?string $src, int $min = 0, int $max = 65536 ): bool
{
	$src    = (string) $src;
	$result = '';

	if ( ! wg_check_is_string( $src, $min, $max ) )
	{
		return false;
	}
	$result = $src;

	return true;
}

/**
 * 入力値が日付(YYYY-MM-DD)であるかチェックし、妥当であれば変数にセットする
 * なお、入力する日付の形式は YYYY[-/]MM[-/]DD 形式により、1900-01-01 〜 2100-12-31 までの期間を受け付ける
 *
 * @param ?string $result チェック後セットされる変数(YYYY-MM-DD形式)、エラーの場合 '' がセットされる
 * @param ?string $src 入力値(文字列)
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す
 */
function wg_inchk_ymd( ?string &$result, ?string $src ): bool
{
	$src    = (string) $src;
	$result = '';

	if ( ! preg_match( '/^(\d{4})[\-\/](\d{1,2})[\-\/](\d{1,2})$/', $src, $match ) )
	{
		return false;
	}

	$iyy = (int) $match[1];
	$imm = (int) $match[2];
	$idd = (int) $match[3];

	if ( ( $iyy >= 1900 && $iyy <= 2100 ) &&
		 ( $imm >= 1 && $imm <= 12 ) &&
		 ( $idd >= 1 && $idd <= 31 ) )
	{
		$ts = mktime( 0, 0, 0, $imm, $idd, $iyy );
		$dt = date( 'Y-m-d', $ts );
		list( $vyy, $vmm, $vdd ) = explode( '-', $dt );

		if ( (int) $vyy == $iyy && (int) $vmm == $imm && (int) $vdd == $idd )
		{
			$result = $dt;

			return true;
		}
	}

	return false;
}

/**
 * 入力値が年月(YYYY-MM)であるかチェックし、妥当であれば変数にセットする
 * なお、入力する日付の形式は YYYY[-/]MM 形式により、1900-01 〜 2100-12 までの期間を受け付ける
 *
 * @param ?string $result チェック後セットされる変数(YYYY-MM形式)、エラーの場合 '' がセットされる
 * @param ?string $src 入力値(文字列)
 * @param bool $isAsYmd 日として1日(すなわちYYYY-MM-01の形式)に変換して $result に代入するかどうか
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す
 */
function wg_inchk_ym( ?string &$result, ?string $src, bool $isAsYmd = false ): bool
{
	$src    = (string) $src;
	$result = false;

	if ( preg_match( '/^(\d{4})[\-\/](\d{1,2})$/', $src, $match ) == 0 )
	{
		return false;
	}

	$iyy = (int) $match[1];
	$imm = (int) $match[2];

	if ( ( $iyy >= 1900 && $iyy <= 2100 ) &&
		 ( $imm >= 1 && $imm <= 12 ) )
	{
		$ts  = mktime( 0, 0, 0, $imm, 1, $iyy );
		$dt1 = date( 'Y-m-d', $ts );
		$dt2 = date( 'Y-m', $ts );
		list( $vyy, $vmm, $vdd ) = explode( '-', $dt1 );

		if ( (int) $vyy == $iyy && (int) $vmm == $imm && (int) $vdd == 1 )
		{
			$result = $isAsYmd ? $dt1 : $dt2;

			return true;
		}
	}

	return true;
}

/**
 * 入力値を与えられた正規表現(PREG)でチェックし、妥当であれば変数にセットする
 *
 * @param ?string $result チェック後セットされる変数、エラーの場合、'' がセットされる
 * @param ?string $src 入力値(文字列)
 * @param string $re 正規表現(preg_match互換)
 * @param int $min 受け入れる文字列の最小長
 * @param int $max 受け入れる文字列の最大長
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す
 */
function wg_inchk_preg( ?string &$result, ?string $src, string $re, int $min = 0, int $max = 2147483647 ): bool
{
	$src    = (string) $src;
	$result = "";

	if ( ! wg_check_is_string( $src, $min, $max ) )
	{
		return false;
	}

	if ( ! preg_match( $re, $src ) )
	{
		return false;
	}

	$result = $src;

	return true;
}

/**
 * 入力値を与えられた正規表現(PREG)でチェックし、妥当であれば変数にセット及びマッチ配列を取得する
 *
 * @param ?array $result チェック後セットされる変数、エラーの場合、[] がセットされる
 * @param ?string $src 入力値(文字列)
 * @param string $re 正規表現(preg_match互換)
 * @param int $min 受け入れる文字列の最小長
 * @param int $max 受け入れる文字列の最大長
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す
 */
function wg_inchk_preg_match( ?array &$result, ?string $src, string $re, int $min = 0, int $max = 2147483647 ): bool
{
	$src    = (string) $src;
	$result = [];

	if ( ! wg_check_is_string( $src, $min, $max ) )
	{
		return false;
	}

	if ( ! preg_match( $re, $src, $match ) )
	{
		return false;
	}

	$result = $match;

	return true;
}

/**
 * 入力値が数値(numeric)かどうかチェックする
 *
 * @param string $value 入力値(文字列)
 * @param int $min 受け入れる文字列の最小長
 * @param int $max 受け入れる文字列の最大長
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す
 */
function wg_check_is_number( string $value, int $min = 0, int $max = 2147483647 ): bool
{
	if ( is_numeric( $value ) == false )
	{
		return false;
	}
	if ( $value < $min || $value > $max )
	{
		return false;
	}

	return true;
}

/**
 * 入力値が規定の文字列の範囲内の長さかどうかチェックする
 *
 * @param string $value 入力値(文字列)
 * @param int $min 受け入れる文字列の最小長
 * @param int $max 受け入れる文字列の最大長
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す
 */
function wg_check_is_string( string $value, int $min, int $max ): bool
{
	$len = strlen( $value );
	if ( $len < $min || $len > $max )
	{
		return false;
	}

	return true;
}

