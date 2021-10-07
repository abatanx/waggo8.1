<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/exception.php';

const WGLOG_APP     = 0;    ///< 情報タイプのログメッセージである。
const WGLOG_INFO    = 1;    ///< 情報タイプのログメッセージである。
const WGLOG_WARNING = 2;    ///< 警告タイプのログメッセージである。
const WGLOG_ERROR   = 3;    ///< エラータイプのログメッセージである。
const WGLOG_FATAL   = 9;    ///< 致命的エラータイプのログメッセージである。

global $wg_log_write_colors;
$wg_log_write_colors = [
	mt_rand( 1, 7 ),
	mt_rand( 1, 7 ),
	mt_rand( 1, 7 )
];

global $wg_log_write_types;
$wg_log_write_types = [
	WGLOG_APP     => "APP",
	WGLOG_INFO    => "INFO",
	WGLOG_WARNING => "WARN",
	WGLOG_ERROR   => "ERROR",
	WGLOG_FATAL   => "FATAL"
];

/**
 * 非推奨関数についてログにその旨を記録し、転送先関数に処理を転送。
 *
 * @param bool $is_forward true の場合、新しい関数に転送する。
 * @param string $func __FUNCTION__ を指定する。
 * @param string $solver 転送先の関数名。
 * @param array $params func_get_args() を指定する。
 *
 * @return mixed 転送先関数からの戻り値。
 * @noinspection PhpUnused
 */
function wg_deprecated( bool $is_forward, string $func, string $solver, array $params = [] ): mixed
{
	wg_log_write( WGLOG_WARNING, "$func is deprecated. so you should use $solver." );
	if ( $is_forward )
	{
		return call_user_func_array( $solver, $params );
	}

	return null;
}

/**
 * ログに情報を出力する。
 *
 * @param string $log ログ記録内容
 *
 * @noinspection PhpUnused
 */
function wg_log_write_error_log( string $log ): void
{
	switch ( WG_LOGTYPE )
	{
		case 0:
		default:
			error_log( trim( $log ) );
			break;

		case 1:
			@chmod( WG_LOGFILE, 0666 );
			error_log( $log, 3, WG_LOGFILE );
			break;
	}
}

/**
 * ログに情報を出力する。
 *
 * @param int $logtype 出力するメッセージのタイプ(WGLOG_APP|WGLOG_INFO|WGLOG_WARNING|WGLOG_ERROR|WGLOG_FATAL)
 * @param string $format 書式フォーマット、または出力文字列。
 * @param mixed ...$args 書式引数。
 *
 * @noinspection PhpUnused
 */
function wg_log_write( int $logtype, string $format, mixed ...$args ): void
{
	if ( WG_LOGFILE == '' )
	{
		return;
	}
	$msg = count( $args ) === 0 ? $format : vsprintf( $format, $args );

	if ( $logtype == WGLOG_APP ||
		 ( $logtype == WGLOG_INFO && WG_DEBUG == true ) ||
		 $logtype == WGLOG_WARNING ||
		 $logtype == WGLOG_ERROR ||
		 $logtype == WGLOG_FATAL )
	{
		global $wg_log_write_colors, $wg_log_write_types;
		$pid = getmygid();
		$es0 = sprintf( "\x1b[%dm", $wg_log_write_colors[0] + 30 );
		$es1 = sprintf( "\x1b[%dm", $wg_log_write_colors[1] + 30 );
		$es2 = sprintf( "\x1b[%dm", $wg_log_write_colors[2] + 30 );
		$es9 = "\x1b[m";

		$msg = rtrim( $msg );
		$dd  = date( 'Ymd H:i:s' );
		$msg = str_replace( "\0", "\\0", $msg );    // NULL文字が入ると正常にロギングできない対処。
		$im  = $wg_log_write_types[ $logtype ];
		$log = sprintf( "{$es0}[%6d] {$es1}%-15s %s {$es2}[%s] %s {$es9}\n", $pid, $dd, $_SERVER['SCRIPT_NAME'], $im, $msg );
		wg_log_write_error_log( $log );

		// 致命的エラーの場合、バックトレースを表示して終了する。
		if ( $logtype == WGLOG_FATAL )
		{
			foreach ( debug_backtrace() as $b )
			{
				if ( ! isset( $b['class'] ) )
				{
					$cn = sprintf( '%s', $b['function'] );
				}
				else
				{
					$cn = sprintf( '%s::%s', $b['class'], $b['function'] );
				}


				$log = sprintf( "   --> %-40s %s (%s)\n", $cn, $b['file'], $b['line'] );
				wg_log_write_error_log( $log );
			}
			throw new WGRuntimeException();
		}
	}
}

/**
 * ログに情報をダンプ形式で出力する。
 *
 * @param int $logtype 出力するメッセージのタイプ(WGLOG_APP|WGLOG_INFO|WGLOG_WARNING|WGLOG_ERROR|WGLOG_FATAL)
 * @param mixed $var 出力する変数
 *
 * @noinspection PhpUnused
 */
function wg_log_dump( int $logtype, mixed $var ): void
{
	ob_start();
	var_dump( $var );
	$s = ob_get_contents();
	ob_end_clean();
	wg_log_write( $logtype, "----" );
	foreach ( explode( "\n", $s ) as $ss )
	{
		wg_log_write( $logtype, $ss );
	}
	wg_log_write( $logtype, "----" );
}

/**
 * エラーをログファイルに出力し、スクリプトを強制終了する。
 *
 * @param string $msg エラーメッセージ。
 *
 * @noinspection PhpUnused
 */
function wg_die( string $msg ): void
{
	wg_log_write( WGLOG_ERROR, "DIE\n$msg" );
	exit;
}

/**
 * REMOTE IPアドレス取得
 * @return string
 * @noinspection PhpUnused
 */
function wg_get_remote_adr(): string
{
	$remoteAdr = $_SERVER['REMOTE_ADDR'] ?? '';

	if ( isset( $_SERVER['HTTP_X_CLIENT_IP'] ) )
	{
		// for Azure
		$client    = explode( ',', $_SERVER['HTTP_X_CLIENT_IP'] ?? '' );
		$remoteAdr = count( $client ) > 0 ? trim( $client[0] ) : '';
	}

	return $remoteAdr;
}
