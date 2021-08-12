<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/lib.php';

/**
 * 入力値が数値(numeric)であるかチェックし、妥当であれば変数にセットする。
 *
 * @param float $result チェック後セットされる変数。エラーの場合、0 がセットされる。
 * @param string $src 入力値(文字列)。
 * @param float $min 受け入れる数値の最小値。
 * @param float $max 受け入れる数値の最大値。
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す。
 */
function wg_inchk_number( float &$result, string $src, float $min = 0, float $max = 2147483647 ): bool
{
	$result = 0;
	if ( ! wg_check_input_number( $src, $min, $max ) )
	{
		return false;
	}
	$result = $src;

	return true;
}

/**
 * 入力値が整数(integer)であるかチェックし、妥当であれば変数にセットする。
 *
 * @param int $result チェック後セットされる変数。エラーの場合、0 がセットされる。
 * @param string $src 入力値(文字列)。
 * @param int $min 受け入れる整数の最小値。
 * @param int $max 受け入れる整数の最大値。
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す。
 */
function wg_inchk_int( int &$result, string $src, int $min = 0, int $max = 2147483647 ): bool
{
	$result = 0;
	if ( ! wg_check_input_number( $src, $min, $max ) )
	{
		return false;
	}
	$result = (int) $src;

	return true;
}

/**
 * 入力値が規定範囲内の文字列であるかチェックし、妥当であれば変数にセットする。
 *
 * @param string $result チェック後セットされる変数。エラーの場合、"" がセットされる。
 * @param string $src 入力値(文字列)。
 * @param int $min 受け入れる文字列の最小長。
 * @param int $max 受け入れる文字列の最大長。
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す。
 */
function wg_inchk_string( string &$result, string $src, int $min = 0, int $max = 2147483647 ): bool
{
	$result = '';
	if ( ! wg_check_input_string( $src, $min, $max ) )
	{
		return false;
	}
	$result = $src;

	return true;
}

/**
 * 入力値が日付(YYYY/MM/DD)であるかチェックし、妥当であれば変数にセットする。
 * なお、入力する日付の形式は YYYY[/-]MM[/-]DD 形式による。
 *
 * @param string $result チェック後セットされる変数(YYYY/MM/DD形式)。エラーの場合 false がセットされる。
 * @param string $src 入力値(文字列)。
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す。
 */
function wg_inchk_ymd( string &$result, string $src ): bool
{
	$result = false;
	if ( ! preg_match( '/^(\d+)[\/\-](\d+)[\/\-](\d+)$/', $src, $m ) )
	{
		return false;
	}

	$yy = (int) $m[1];
	$mm = (int) $m[2];
	$dd = (int) $m[3];
	if ( ( $d = wg_check_datetime( 0, $yy, $mm, $dd ) ) == false )
	{
		return false;
	}

	$result = $d["date"];

	return true;
}

/**
 * 入力値が年月(YYYY/MM)であるかチェックし、妥当であれば変数にセットする。
 * なお、入力する日付の形式は YYYY[/-]MM 形式により、1800〜2100年までの数値を受け付ける。
 *
 * @param string $result チェック後セットされる変数(YYYY/MM または YYYY/MM/01形式)。エラーの場合 false がセットされる。
 * @param string $src 入力値(文字列)。
 * @param bool $isAsYmd 日として1日(すなわちYYYY/MM/01の形式)に変換して $result に代入するかどうか。
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す。
 */
function wg_inchk_ym( string &$result, string $src, bool $isAsYmd = true ): bool
{
	$result = false;
	if ( preg_match( '/^(\d+)[\/\-](\d+)$/', $src, $m ) == 0 )
	{
		return false;
	}

	$yy = (int) $m[1];
	$mm = (int) $m[2];
	if ( $yy < 1800 || $yy > 2100 || $mm < 1 || $mm > 12 )
	{
		return false;
	}
	if ( $isAsYmd )
	{
		$result = sprintf( "%04d/%02d/01", $yy, $mm );
	}
	else
	{
		$result = sprintf( "%04d/%02d", $yy, $mm );
	}

	return true;
}

/**
 * 入力値を与えられた正規表現(PREG)でチェックし、妥当であれば変数にセットする。
 *
 * @param string $result チェック後セットされる変数。エラーの場合、"" がセットされる。
 * @param string $src 入力値(文字列)。
 * @param string $regex 正規表現(preg_match互換)。
 * @param int $min 受け入れる文字列の最小長。
 * @param int $max 受け入れる文字列の最大長。
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す。
 */
function wg_inchk_pregex( string &$result, string $src, string $regex, int $min = 0, int $max = 2147483647 ): bool
{
	$result = "";
	if ( ! wg_check_input_string( $src, $min, $max ) )
	{
		return false;
	}
	if ( ! preg_match( $regex, $src ) )
	{
		return false;
	}
	$result = $src;

	return true;
}

/**
 * 入力値を与えられた正規表現(PREG)でチェックし、妥当であれば変数にセット及びマッチ配列を取得する。
 *
 * @param string $result チェック後セットされる変数。エラーの場合、"" がセットされる。
 * @param string $src 入力値(文字列)。
 * @param string $regex 正規表現(preg_match互換)。
 * @param int $min 受け入れる文字列の最小長。
 * @param int $max 受け入れる文字列の最大長。
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す。
 */
function wg_inchk_pregexmatch( string &$result, string $src, string $regex, int $min = 0, int $max = 2147483647 ): bool
{
	$result = "";
	if ( ! wg_check_input_string( $src, $min, $max ) )
	{
		return false;
	}
	if ( ! preg_match( $regex, $src, $match ) )
	{
		return false;
	}
	$result = $match;

	return true;
}

/**
 * 入力値が数値(numeric)かどうかチェックする。
 *
 * @param string $value 入力値(文字列)。
 * @param int $min 受け入れる文字列の最小長。
 * @param int $max 受け入れる文字列の最大長。
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す。
 */
function wg_check_input_number( string $value, int $min = 0, int $max = 2147483647 ): bool
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
 * 入力値が規定の文字列の範囲内の長さかどうかチェックする。
 *
 * @param string $value 入力値(文字列)。
 * @param int $min 受け入れる文字列の最小長。
 * @param int $max 受け入れる文字列の最大長。
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す。
 */
function wg_check_input_string( string $value, int $min, int $max ): bool
{
	$len = strlen( $value );
	if ( $len < $min || $len > $max )
	{
		return false;
	}

	return true;
}

/**
 * 入力値のメールアドレスを @ の前と後に分割し、配列で返す。
 *
 * @param string $value 入力値(文字列)。
 *
 * @return array|false 正常な場合は["user"=>@前, "host"=>@後]の配列を、それ以外の場合は false を返す。
 */
function wg_split_email_by_userhost( string $value ): array|false
{
	if ( preg_match( '/^([_a-zA-Z0-9\-\\\.]+)@([_a-zA-Z0-9\-\\\.]+)$/', $value, $args ) == false )
	{
		return false;
	}
	$email["user"] = $args[1];
	$email["host"] = $args[2];

	return $email;
}

/**
 * 入力値がIPv4形式アドレスであるかチェックする。
 *
 * @param string $ip 入力値(文字列)。
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す。
 */
function wg_is_ipv4( string $ip ): bool
{
	if ( preg_match( '/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/', $ip, $m ) )
	{
		for ( $i = 1; $i < 4; $i ++ )
		{
			if ( $m[ $i ] < 0 || $m[ $i ] > 255 )
			{
				return false;
			}
		}

		return true;
	}

	return false;
}

/**
 * 入力値が妥当なメールアドレスかどうかチェックする。
 *
 * @param string $adr 入力値(メールアドレス形式文字列)。
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す。
 */
function wg_check_input_email( string $adr ): bool
{
	/* Check length of E-Mail address. */
	if ( wg_check_input_string( $adr, 3, 1024 ) == false )
	{
		return false;
	}

	/* Check format of E-Mail address. */
	$email = wg_split_email_by_userhost( $adr );
	if ( $email == false )
	{
		return false;
	}

	/* Check the host address that is presented by IP(V4) address format. */
	if ( wg_is_ipv4( $email["host"] ) == true )
	{
		return false;
	}

	return true;
}

/**
 * 入力値が有効なプロトコルなURLであるかチェックする。
 *
 * @param string $url 入力値(URL文字列)。
 * @param int $min 受け入れる文字列の最小長。
 * @param int $max 受け入れる文字列の最大長。
 * @param string $protocol 受け入れるプロトコル。preg_match に引き渡すため、"https?|ftp|mailto" などの形式で指定する。
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す。
 */
function wg_check_input_url_protocol( string $url, int $min, int $max, string $protocol ): bool
{
	if ( wg_check_input_string( $url, $min, $max ) == false )
	{
		return false;
	}
	$re = '/^(' . $protocol . '):([\w\\\.~\-\/?&+=:@%;#]+)/';
	if ( preg_match( $re, $url, $match ) == false )
	{
		return false;
	}

	if ( $match[1] == "http" && $match[2] == "//" )
	{
		return false;
	}
	if ( $match[1] == "https" && $match[2] == "//" )
	{
		return false;
	}
	if ( $match[1] == "ftp" && $match[2] == "//" )
	{
		return false;
	}
	if ( $match[1] == "mailto" && wg_check_input_email( $match[2] ) == false )
	{
		return false;
	}

	return true;
}

/**
 * 入力値が自己を示すURLであるかチェックする。リソース表現部のみで構成されたURLであるかをチェックする。
 *
 * @param string $url 入力値(文字列)。
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す。
 */
function wg_check_input_selfurl( string $url ): bool
{
	return preg_match( '/^\/([\w\\\.~\-\/?&+=:@%;#]+)$/', $url, $match );
}

/**
 * 入力値を半角英数字に変換する。ただしひらかな・カタカナはすべて全角に変換する。
 *
 * @param string $data 入力値(文字列)。
 *
 * @return string 半角英数字に変換された後の文字列。
 */
function wg_toank( string $data ): string
{
	return mb_convert_kana( $data, "KVas" );
}

/**
 * 入力値がカナに変換可能な文字であるかチェックし、妥当であれば変数にセットする。
 *
 * @param string $result チェック後セットされる変数。エラーの場合、"" がセットされる。
 * @param string $src 入力値(文字列)。
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す。
 */
function wg_inchk_kana( string &$result, string $src ): bool
{
	$result = mb_convert_kana( trim( mb_convert_kana( $src, "s" ) ), "S" );
	$result = mb_convert_kana( $result, 'KCVS' );
	if ( ! preg_match( "/[ァ-ヶー]*$/u", $result ) )
	{
		$result = "";

		return false;
	}

	return true;
}

/**
 * ひらがな版
 *
 * @param string $result チェック後セットされる変数。エラーの場合、"" がセットされる。
 * @param string $src 入力値(文字列)。
 *
 * @return bool 妥当であれば trueを、それ以外であれば false を返す。
 */
function wg_inchk_hiragana( string &$result, string $src ): bool
{
	$result = mb_convert_kana( trim( mb_convert_kana( $src, "s" ) ), "S" );
	$result = mb_convert_kana( $result, "HcVS" );
	if ( ! preg_match( "/[ぁ-ゔ]*$/u", $result ) )
	{
		$result = "";

		return false;
	}

	return true;
}
