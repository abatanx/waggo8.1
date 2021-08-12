<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

abstract class WGDBMS
{
	public bool $echo;
	public bool $logging;
	public array $log;

	/**
	 * constructor
	 */
	public function __construct()
	{
		$this->echo    = false;
		$this->logging = WG_SQLDEBUG;
		$this->log     = [];
	}

	/**
	 * データベース接続を開始する
	 *
	 * @return bool 成功した場合 true を返す
	 */
	abstract public function open(): bool;

	/**
	 * データベース接続を終了する
	 *
	 * @return bool 成功した場合 true を返す
	 */
	abstract public function close(): bool;

	/**
	 * SQLクエリを発行する
	 *
	 * @param string $q SQLクエリ文字列
	 *
	 * @return int レコード数
	 */
	abstract public function E( string $q ): int;

	/**
	 * 書式付きSQLクエリーを発行する
	 *
	 * @param string $format 書式付きフォーマット文字列
	 * @param string|int|float ...$values 書式に対応します変数
	 *
	 * @return int|false 成功した場合はレコード数、失敗した場合は false を返す
	 */
	abstract public function Q( string $format, ...$values ): int|false;

	/**
	 * 1レコードに限定した書式付きSQLクエリーを発行する
	 *
	 * @param string $format 書式付きフォーマット文字列
	 * @param string|int|float ...$values 書式に対応します変数。
	 *
	 * @return array|false 成功した場合はレコード配列、失敗した場合は false を返す
	 */
	abstract public function QQ( string $format, ...$values ): array|false;

	/**
	 * 直前に実行したSQLが成功したかどうか判定する
	 *
	 * @return bool 成功していた場合は true を、それ以外の場合は false を返す
	 */
	abstract public function OK(): bool;

	/**
	 * 直前に実行したSQLが失敗したか判定する
	 * @return bool 失敗していた場合は true を、それ以外の場合は false を返す
	 */
	abstract public function NG(): bool;

	/**
	 * SQL実行結果から、カーソル位置の1レコードを取得する
	 * @return array|false 取得できた場合はレコードの配列を、取得できなかった場合は false を返す
	 */
	abstract public function F(): array|false;

	/**
	 * SQL実行結果から、全レコードを配列として取得する
	 * @return array 全レコードの連想配列を返す
	 */
	abstract public function FALL(): array;

	/**
	 * SQL実行結果から、指定したフィールドのデータを配列として返す
	 *
	 * @param string $field フィールド名
	 *
	 * @return array データが格納された配列
	 */
	abstract public function FARRAY( string $field ): array;

	/**
	 * SQL実行結果から、指定したフィールドのデータをの一方をキー、一方をデータとした連想配列として返す
	 *
	 * @param string $kf キーとなるフィールド名
	 * @param string $df データとなるフィールド名
	 *
	 * @return array データが格納された配列
	 */
	abstract public function FARRAYKEYDATA( string $kf, string $df ): array;

	/**
	 * SQL実行結果レコード数を返す
	 *
	 * @return int レコード数
	 */
	abstract public function RECS(): int;

	/**
	 * トランザクションを開始する
	 */
	abstract public function BEGIN(): void;

	/**
	 * トランザクションをロールバックする
	 */
	abstract public function ROLLBACK(): void;

	/**
	 * トランザクションをコミットする
	 */
	abstract public function COMMIT(): void;

	/**
	 * トランザクションをコミットする
	 */
	abstract public function END(): void;
}
