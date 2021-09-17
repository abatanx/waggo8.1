<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

global $WG_CORE_DBMS;

/**
 * 規定のデータベースのインスタンスオブジェクトを返す
 * インスタンスが生成されていない場合は、規定のデータベースのインスタンスを作成し、そのインスタンスオブジェクトを返す
 * @return WGDBMS|false WG_CORE_DBMSインスタンスオブジェクト
 */
function _QC(): WGDBMS|false
{
	global $WG_CORE_DBMS;

	if ( ! $WG_CORE_DBMS instanceof WGDBMSPostgreSQL &&
		 ! $WG_CORE_DBMS instanceof WGDBMSMySQL )
	{
		switch ( strtolower( WGCONF_DBMS_TYPE ) )
		{
			case 'pgsql':
			case 'postgres':
			case 'postgresql':
				require_once __DIR__ . '/postgresql.php';
				$WG_CORE_DBMS = new WGDBMSPostgreSQL( WGCONF_DBMS_HOST, WGCONF_DBMS_PORT, WGCONF_DBMS_DB, WGCONF_DBMS_USER, WGCONF_DBMS_PASSWD );
				if ( ! $WG_CORE_DBMS->open() )
				{
					wg_log_write( WGLOG_FATAL, "Can't connect to '%s' as PostgreSQL", WGCONF_DBMS_DB );

					return false;
				}
				break;

			case 'mysql':
			case 'mariadb':
			case 'maria':
				require_once __DIR__ . '/mysql.php';
				$WG_CORE_DBMS = new WGDBMSMySQL( WGCONF_DBMS_HOST, WGCONF_DBMS_PORT, WGCONF_DBMS_DB, WGCONF_DBMS_USER, WGCONF_DBMS_PASSWD );
				if ( ! $WG_CORE_DBMS->open() )
				{
					wg_log_write( WGLOG_FATAL, "Can't connect to '%s' as MySQL/MariaDB", WGCONF_DBMS_DB );

					return false;
				}
				break;

			default:
				wg_log_write( WGLOG_FATAL, "WGCONF_DBMS_TYPE '%s' is not supported.", WGCONF_DBMS_TYPE );
				break;
		}
	}

	return $WG_CORE_DBMS;
}

/**
 * SQLクエリを発行する
 *
 * @param string $q SQLクエリ文字列
 *
 * @return int レコード数
 */
function _E( string $q ): int
{
	return ( $d = _QC() ) ? $d->E( $q ) : 0;
}

/**
 * 書式付きSQLクエリーを発行する
 *
 * @param string $format 書式付きフォーマット文字列
 * @param string|int|float ...$values 書式に対応します変数
 *
 * @return int|false 成功した場合はレコード数、失敗した場合は false を返す
 */
function _Q( string $format, string|int|float ...$values )
{
	$p = [ $format, ...$values ];

	return ( $d = _QC() ) ? call_user_func_array( [ $d, 'Q' ], $p ) : false;
}

/**
 * 1レコードに限定した書式付きSQLクエリーを発行する
 *
 * @param string $format 書式付きフォーマット文字列
 * @param string|int|float ...$values 書式に対応します変数。
 *
 * @return array|false 成功した場合はレコード配列、失敗した場合は false を返す
 */
function _QQ( string $format, ...$values ): array|false
{
	$p = [ $format, ...$values ];

	return ( $d = _QC() ) ? call_user_func_array( [ $d, 'QQ' ], $p ) : false;
}

/**
 * 直前に実行したSQLが成功したかどうか判定する
 *
 * @return bool 成功していた場合は true を、それ以外の場合は false を返す
 */
function _QOK(): bool
{
	return ( ( $d = _QC() ) ) && $d->OK();
}

/**
 * 直前に実行したSQLが失敗したか判定する
 * @return bool 失敗していた場合は true を、それ以外の場合は false を返す
 */
function _QNG(): bool
{
	return ! ( $d = _QC() ) || $d->NG();
}

/**
 * SQL実行結果から、カーソル位置の1レコードを取得する
 * @return array|false 取得できた場合はレコードの配列を、取得できなかった場合は false を返す
 */
function _F(): array|false
{
	return ( $d = _QC() ) ? $d->F() : false;
}

/**
 * SQL実行結果から、全レコードを配列として取得する
 * @return array 全レコードの連想配列を返す
 */
function _FALL(): array
{
	return ( $d = _QC() ) ? $d->FALL() : [];
}

/**
 * SQL実行結果から、指定したフィールドのデータを配列として返す
 *
 * @param string $field フィールド名
 *
 * @return array データが格納された配列
 */
function _FARRAY( string $field ): array
{
	return ( $d = _QC() ) ? $d->FARRAY( $field ) : [];
}

/**
 * SQL実行結果から、指定したフィールドのデータをの一方をキー、一方をデータとした連想配列として返す
 *
 * @param string $kf キーとなるフィールド名
 * @param string $df データとなるフィールド名
 *
 * @return array データが格納された配列
 */
function _FARRAYKEYDATA( string $kf, string $df ): array
{
	return ( $d = _QC() ) ? $d->FARRAYKEYDATA( $kf, $df ) : [];
}

/**
 * SQL実行結果レコード数を返す
 *
 * @return int レコード数
 */
function _R(): int
{
	return ( $d = _QC() ) ? $d->RECS() : 0;
}

/**
 * 文字列をSQL用にクォートする。
 *
 * @param string $str クォートする文字列。
 *
 * @return string クォート後の文字列。
 */
function _ESC( string $str ): string
{
	return ( $d = _QC() ) ? $d->ESC( $str ) : throw new WGDatabaseRuntimeException();
}

/**
 * 書式付きSQL発行用に、文字列を引用符付き文字列に変換する。
 *
 * @param mixed $str 文字列。
 * @param boolean $isAllowNull Trueの場合NULL値を利用する。
 *
 * @return string 変換後の文字列。NULL以外の場合はクォート後両端に引用符が付加されます。
 */
function _S( mixed $str, bool $isAllowNull = true ): string
{
	return ( $d = _QC() ) ? $d->S( $str, $isAllowNull ) : throw new WGDatabaseRuntimeException();
}

/**
 * 書式付きSQL発行用に、論理値を文字列に変換する。
 *
 * @param mixed $bool 論理値。
 * @param boolean $isAllowNull Trueの場合NULL値を利用する。
 *
 * @return string 変換後の文字列。true, false, null が返されます。
 */
function _B( mixed $bool, bool $isAllowNull = true ): string
{
	return ( $d = _QC() ) ? $d->B( $bool, $isAllowNull ) : throw new WGDatabaseRuntimeException();
}

/**
 * 書式付きSQL発行用に、数値を文字列に変換する。
 *
 * @param mixed $num 数値。
 * @param boolean $isAllowNull Trueの場合NULL値を利用する。
 *
 * @return string 変換後の文字列。
 */
function _N( mixed $num, bool $isAllowNull = true )
{
	return ( $d = _QC() ) ? $d->N( $num, $isAllowNull ) : throw new WGDatabaseRuntimeException();
}

/**
 * 書式付きSQL発行用に、浮動小数点数を文字列に変換する。
 *
 * @param mixed $num 浮動小数点数。
 * @param boolean $isAllowNull Trueの場合NULL値を利用する。
 *
 * @return string 変換後の文字列。
 */
function _D( mixed $num, bool $isAllowNull = true ): string
{
	return ( $d = _QC() ) ? $d->D( $num, $isAllowNull ) : throw new WGDatabaseRuntimeException();
}

/**
 * 書式付きSQL発行用に、日付時刻を文字列に変換する。
 *
 * @param mixed $t 日付時刻文字列。PostgreSQLでの日付関数表記も可能です。
 * @param boolean $isAllowNull Trueの場合NULL値を利用する。
 *
 * @return string 変換後の文字列。日付関数表記以外の場合、両端に引用符が付与されるだけです。
 */
function _TD( mixed $t, bool $isAllowNull = true ): string
{
	return ( $d = _QC() ) ? $d->TD( $t, $isAllowNull ) : throw new WGDatabaseRuntimeException();
}

/**
 * 書式付きSQL発行用に、日付時刻を文字列に変換する。
 *
 * @param mixed $t 日付時刻文字列。PostgreSQLでの日付関数表記も可能です。
 * @param boolean $isAllowNull Trueの場合NULL値を利用する。
 *
 * @return string 変換後の文字列。日付関数表記以外の場合、両端に引用符が付与されるだけです。
 */
function _TT( mixed $t, bool $isAllowNull = true ): string
{
	return ( $d = _QC() ) ? $d->TT( $t, $isAllowNull ) : throw new WGDatabaseRuntimeException();
}

/**
 * 書式付きSQL発行用に、日付時刻を文字列に変換する。
 *
 * @param mixed $t 日付時刻文字列。PostgreSQLでの日付関数表記も可能です。
 * @param boolean $isAllowNull Trueの場合NULL値を利用する。
 *
 * @return string 変換後の文字列。日付関数表記以外の場合、両端に引用符が付与されるだけです。
 */
function _TS( mixed $t, bool $isAllowNull = true ): string
{
	return ( $d = _QC() ) ? $d->TS( $t, $isAllowNull ) : throw new WGDatabaseRuntimeException();
}

/**
 * 書式付きSQL発行用に、バイナリデータを文字列に変換する。
 *
 * @param mixed $raw バイナリデータ本体。
 * @param bool $isAllowNull Trueの場合NULL値を利用する。
 *
 * @return string 変換後の文字列。
 */
function _BLOB( mixed $raw, bool $isAllowNull = true ): string
{
	return ( $d = _QC() ) ? $d->BLOB( $raw, $isAllowNull ) : throw new WGDatabaseRuntimeException();
}

/**
 * 書式付きSQL発行用に、現在ログイン中のユーザーIDを文字列に変換する。
 * @return string ユーザーIDの文字列。
 */
function _U()
{
	return ( $d = _QC() ) ? $d->N( wg_get_usercd() ) : throw new WGDatabaseRuntimeException();
}

/**
 * トランザクションを開始します。
 */
function _QBEGIN(): void
{
	( $d = _QC() ) ? $d->BEGIN() : throw new WGDatabaseRuntimeException();
}

/**
 * トランザクションをロールバックします。
 */
function _QROLLBACK(): void
{
	( $d = _QC() ) ? $d->ROLLBACK() : throw new WGDatabaseRuntimeException();
}

/**
 * トランザクションをコミットします。
 */
function _QCOMMIT(): void
{
	( $d = _QC() ) ? $d->COMMIT() : throw new WGDatabaseRuntimeException();
}

/**
 * トランザクションを終了します。
 * 実質的には COMMIT されることと同等です。
 */
function _QEND(): void
{
	( $d = _QC() ) ? $d->END() : throw new WGDatabaseRuntimeException();
}

/**
 * DBMSが MySQL であるかチェックする。
 * @return boolean MySQLの場合 true を返す。
 */
function wg_is_dbms_mysql(): bool
{
	return in_array( strtolower( WGCONF_DBMS_TYPE ), [ 'mysql' ] );
}

/**
 * DBMSが mariadb であるかチェックする。
 * @return boolean mariadb の場合 true を返す。
 */
function wg_is_dbms_mariadb(): bool
{
	return in_array( strtolower( WGCONF_DBMS_TYPE ), [ 'maria', 'mariadb' ] );
}

/**
 * DBMSが PostgreSQL であるかチェックする。
 * @return boolean PostgreSQL の場合 true を返す。
 */
function wg_is_dbms_postgresql(): bool
{
	return in_array( strtolower( WGCONF_DBMS_TYPE ), [ 'pgsql', 'postgres', 'postgresql' ] );
}

/**
 * mariadb で nextval などのシーケンス利用できるバージョンであるかチェックする。
 * @return boolean mariadb 10.3 以降である場合 true を返す。
 */
function wg_is_supported_sequence_mariadb(): bool
{
	if ( wg_is_dbms_mariadb() )
	{
		$v = explode( ".", WGCONF_DBMS_VERSION );
		if ( count( $v ) >= 2 && (int) $v[0] * 1000 + (int) $v[1] >= 10003 )
		{
			return true;
		}
	}

	return false;
}
