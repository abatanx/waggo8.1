<?php
/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

/**
 * リダイレクトを行う。
 *
 * @param string $url リダイレクト先URL
 */
function wg_location( string $url ): void
{
	header( "Location: $url" );
	exit;
}

/**
 * 自サイトへのアクセスを示すURLかを判定する。
 * scheme, host, port, user, pass が存在しない場合、自サイトへのアクセスとする。
 *
 * @param string $url 判定するURL文字列。
 *
 * @return bool 自サイトへのアクセスであれば true を、それ以外は false を返す。
 */
function wg_is_myselfurl( string $url ): bool
{
	if ( ( $q = parse_url( $url ) ) === false )
	{
		return false;
	}

	$a = [ "schema", "host", "port", "user", "pass" ];
	foreach ( $a as $c )
	{
		$t = $q[ $c ] ?? '';
		if ( $t !== '' )
		{
			return false;
		}
	}

	return true;
}

/**
 * クエリ文字列から、連想配列を返す。
 * 'a=b&c=d' => ['a'=>'b', 'c'=>'d'] のような変換を行う。
 * キー及び値は urldecode によってデコードする。
 *
 * @param string $query クエリ文字列。
 *
 * @return array 変換した連想配列。
 */
function wg_query_to_array( string $query ): array
{
	$eq = explode( '&', $query );

	$r = [];
	foreach ( $eq as $q )
	{
		if ( $q !== '' )
		{
			list( $k, $v ) = explode( '=', $q );
			$k       = urldecode( (string) $k );
			$v       = urldecode( (string) $v );
			$r[ $k ] = urldecode( $v );
		}
	}

	return $r;
}

/**
 * パラメータの再調整を行う。
 *
 * @param array $original パラメータとして与えられた連想配列
 * @param array $replace 置き換える連想配列。連想配列内の値が '' または NULLの場合、そのパラメータは削除される。
 *
 * @return string 置き換えたあとのパラメータを & で連結した文字列。
 */
function wg_array_to_query( array $original, array $replace = [] ): string
{
	$r = [];
	foreach ( $replace as $k => $v )
	{
		$k = (string) $k;
		if ( is_null( $v ) )
		{
			unset( $original[ $k ] );
		}
		else
		{
			$original[ $k ] = (string) $v;
		}
	}

	foreach ( $original as $k => $v )
	{
		$r[] = urlencode( $k ) . ( ( $v !== '' ) ? ( '=' . urlencode( $v ) ) : '' );
	}

	return implode( "&", $r );
}

/**
 * $_GET の再構築を行なって、パラメータ文字列を返す。
 * キー及び値は urlencode でエンコードされ、& で連結される。
 * $_GET の内容は破壊しない。
 *
 * @param array $params 置き換える連想配列。連想配列内の値が '' または NULLの場合、そのパラメータは削除される。
 *
 * @return string 再構築を行った後のURLパラメータ文字列。& で連結された文字列。
 */
function wg_remake_get( array $params = [] ): string
{
	return wg_array_to_query( $_GET, $params );
}

/**
 * アクセスされているURLの再構築を行なって、URLを返す。
 * アクセスされているURLは、$_SERVER['SCRIPT_NAME'] による。
 * キー及び値は urlencode でエンコードされ、& で連結される。
 *
 * @param array $params 置き換える連想配列。連想配列内の値が '' または NULLの場合、そのパラメータは削除される。
 *
 * @return string 再構築を行った後のURL文字列。
 */
function wg_remake_uri( array $params = [] ): string
{
	$param = wg_remake_get( $params );

	return ( $param === '' ) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['SCRIPT_NAME'] . "?{$param}";
}

/**
 * 与えられたURLの再構築を行なって、URLを返す。
 *
 * @param string $url 再構築対象となるURL文字列
 * @param array $params 置き換える連想配列。連想配列内の値が '' または NULLの場合、そのパラメータは削除される。
 *
 * @return string|false 成功した場合は再構築を行った後のURL文字列、失敗した場合は false を返す。
 */
function wg_remake_url( string $url, array $params = [] ): string|false
{
	if ( ( $q = parse_url( $url ) ) === false )
	{
		return false;
	}

	foreach ( [ 'scheme', 'host', 'port', 'user', 'pass', 'path', 'query', 'fragment' ] as $c )
	{
		$q[ $c ] = (string) ($q[ $c ] ?? '');
	}

	$q['query'] = wg_array_to_query( wg_query_to_array( $q['query'] ), $params );

	return
		( ( $q['scheme'] !== '' ) ? "{$q['scheme']}://" : '' ) .
		( ( $q['host'] !== '' ) ? "{$q['host']}" : '' ) .
		( ( $q['port'] !== '' ) ? ":{$q['port']}" : '' ) .
		$q['path'] .
		( ( $q['query'] !== '' ) ? "?{$q['query']}" : '' ) .
		( ( $q['fragment'] !== '' ) ? "#{$q['fragment']}" : '' );
}
