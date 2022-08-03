<?php
/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

/**
 * プロセス実行のための実行文字列を生成する。
 *
 * @param array $params パラメータ。それぞれシェル用に escapeshellarg でクオートされます。
 * @param string $additional 最後に追加する文字列。
 *
 * @return string 生成した実行文字列
 */
function wg_exec_string( array $params = [], string $additional = "" ): string
{
	$e_params = [];
	foreach ( $params as $pv )
	{
		$e_params[] = ( $pv != '' ) ? escapeshellarg( $pv ) : "''";
	}

	return implode( " ", $e_params ) . ( ( $additional != "" ) ? " $additional" : "" );
}

/**
 * プロセスを実行する。実行は exec による。
 *
 * @param array $params パラメータ。それぞれシェル用に escapeshellarg でクオートされます。
 * @param string $additional 最後に追加する文字列。
 *
 * @return string|false exec()の返り値。
 */
function wg_exec( array $params = [], string $additional = '' ): string|false
{
	$e = wg_exec_string( $params, $additional );
	wg_log_write( WGLOG_INFO, "** CMD execute => [$e]" );

	return exec( $e );
}

/**
 * プロセスを実行する。実行は exec による。
 *
 * @param array $params パラメータ。それぞれシェル用に escapeshellarg でクオートされます。
 * @param string $additional 最後に追加する文字列。
 *
 * @return string exec()の返り値。
 */
function wg_exec_result( array $params = [], string $additional = "" ): string
{
	$o = [];
	$e = wg_exec_string( $params, $additional );
	wg_log_write( WGLOG_INFO, "** CMD execute => [$e]" );
	exec( $e, $o );

	return rtrim( implode( "\n", $o ) );
}

/**
 * プロセスをバックグラウンドで実行する。実行は exec による。
 *
 * @param array $params パラメータ。それぞれシェル用に escapeshellarg でクオートされます。
 * @param string $additional 最後に追加する文字列。
 *
 * @return string|false wg_exec()の返り値。
 */
function wg_exec_background( array $params = [], string $additional = "" ): string|false
{
	$additional = $additional . " >& /dev/null &";

	return wg_exec( $params, $additional );
}

/**
 * １つの外部プロセスで、連続したバッチ処理をバックグラウンドで行う。
 *
 * @param array $batch バッチ処理を記載した配列変数
 *
 * @return string|false wg_exec_background()の返り値。
 */
function wg_exec_background_batch( array $batch = [] ): string|false
{
	$tmpfile = tempnam( "/tmp", "wg_exec_batch_" . uniqid() );
	file_put_contents( $tmpfile, implode( "\n", $batch ) );

	return wg_exec_background(
		[
			WGCONF_DIR_WAGGO . "/exec/batch.sh",
			$tmpfile
		]
	);
}

/**
 * プロセスを実行する。実行は passthru による。
 *
 * @param array $params パラメータ。それぞれシェル用に escapeshellarg でクオートされます。
 * @param string $additional 最後に追加する文字列。
 */
function wg_exec_passthru( array $params = [], string $additional = "" ): void
{
	$e = wg_exec_string( $params, $additional );
	wg_log_write( WGLOG_INFO, "** CMD execute(passthru) => [$e]" );
	passthru( $e );
}

/**
 * 外部プログラムを実行し、パイプを利用して標準入力からデータを差し込む。
 *
 * @param string $cmd 外部プログラムファイル名。
 * @param string $stdin 標準入力に流す文字列。
 * @param string|null $cwd ワークディレクトリ(nullの場合、PHPファイルの位置)。
 * @param array $env 環境変数。
 *
 * @return array|false 出力配列[ret, stdout, stderr]、失敗した場合 false を返す。
 */
function wg_pipe( string $cmd, string $stdin, string|null $cwd = null, array $env = [] ): array|false
{
	$descriptorspec = [
		0 => [ "pipe", "r" ],
		1 => [ "pipe", "w" ],
		2 => [ "pipe", "w" ]
	];

	if ( is_null( $cwd ) )
	{
		$cwd = dirname( realpath( $_SERVER["SCRIPT_FILENAME"] ) );
	}
	$process = proc_open( $cmd, $descriptorspec, $pipes, $cwd, $env );

	if ( is_resource( $process ) )
	{
		fwrite( $pipes[0], $stdin );
		fclose( $pipes[0] );

		$stdout = stream_get_contents( $pipes[1] );
		$stderr = stream_get_contents( $pipes[2] );
		fclose( $pipes[1] );
		fclose( $pipes[2] );

		$ret = proc_close( $process );

		return [ $ret, $stdout, $stderr ];
	}

	return false;
}
