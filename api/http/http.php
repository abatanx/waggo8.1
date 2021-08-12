<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

function wg_location($url)
{
	header("Location: $url");
	exit;
}

/**
 * 自分へのサイトへのアクセスを示すURLかを判定する。
 * @param String $url 判定するURL文字列
 */
function wg_is_myselfurl($url)
{
	$q = parse_url($url);
	$a = ["schema","host","port","user","pass"];
	foreach( $a as $c ) if( $q[$c]!="" ) return false;
	return true;
}

/**
 * URIチェック及びurlencodeを行う。自ホスト以外へのURIは無効。
 * @param String $url URL文字列
 * @param Bool $is_encode urlencodeを行うか
 * @return String エンコード後のURI文字列
 */
function wg_encodeuri($url,$is_encode=true)
{
	if(!wg_is_myselfurl($url)) die("自サイト以外へのアクセスが発生したので、処理を中断しました。");
	return ($is_encode) ? urlencode($url) : $url;
}

/**
 * $_GETの再構築を行なって、パラメータ文字列を返す。
 * @param array $reget 置き換えるキー／内容の連想配列(NULLがキーのデータの場合、そのキーは削除される)
 * @return string 再構築を行った後のURLパラメータ文字列
 */
function wg_remake_get($reget=[])
{
	$nowget = $_GET;
	$newget = [];
	foreach( $reget  as $key => $val )
	{
		if( $val==="" || is_null($val) ) unset($nowget[$key]);
		else $nowget[$key]=$val;
	}
	foreach( $nowget as $key => $val )
	{
		$newget[] = urlencode($key).(($val!="")?("=".urlencode($val)):"");
	}
	return implode("&",$newget);
}

/**
 * $_GETの再構築を行ない、URLを返す。
 * @param array $reget 置き換えるキー／内容の連想配列(NULLがキーのデータの場合、そのキーは削除される)
 * @return String 再構築を行った後のURL文字列
 */
function wg_remake_uri( $reget = [] )
{
	$param = wg_remake_get( $reget );

	return ( $param == "" ) ? $_SERVER["SCRIPT_NAME"] : $_SERVER["SCRIPT_NAME"] . "?${param}";
}

/**
 * 指定された URL からパラメータの再構築を行なって、再構築後のURLを返す。
 * @param string $url 再構築対象となるURL文字列
 * @param array $params 置き換えるキー／内容の連想配列(NULLがキーのデータの場合、そのキーは削除される)
 * @return mixed 成功した場合は再構築を行った後のURL文字列、失敗した場合は false を返す。
 */
function wg_remake_url( $url, $params = [] )
{
	if ( ( $q = parse_url( $url ) ) == false )
	{
		return false;
	}

	foreach( ['scheme','host','port','user','pass','path','query','fragment'] as $c )
	{
		if( !@isset($q[$c]) ) $q[$c] = '';
	}

	$qys = explode( '&', $q["query"] );
	$qps = [];
	foreach ( $qys as $qq )
	{
		if ( $qq != "" )
		{
			$qe            = explode( '=', $qq );
			$qps[ $qe[0] ] = urldecode( $qe[1] );
		}
	}
	foreach ( $params as $k => $p )
	{
		$qps[ $k ] = $p;
	}

	$qys = [];
	foreach ( $qps as $k => $v )
	{
		if ( ! is_null( $v ) )
		{
			$qys[] = urlencode( $k ) . ( ( $v !== '' ) ? ( '=' . urlencode( $v ) ) : '' );
		}
	}
	$q['query'] = implode( '&', $qys );

	return
		( ( $q["scheme"] != "" ) ? "{$q['scheme']}://" : "" ) .
		( ( $q["host"] != "" ) ? "{$q['host']}" : "" ) .
		( ( $q["port"] != "" ) ? ":{$q['port']}" : "" ) .
		$q["path"] . ( ( $q["query"] != "" ) ? "?$q[query]" : "" ) . ( ( $q["fragment"] != "" ) ? "#$q[flagment]" : "" );
}
