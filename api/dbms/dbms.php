<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/dbms_property.php';

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

	abstract public function property(): WGDBMSProperty;

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
	 * 文字列をSQL用にクォートする
	 *
	 * @param string $str クォートする文字列
	 *
	 * @return string クォート後の文字列
	 */
	abstract public function ESC( string $str ): string;

	/**
	 * 書式付きSQL発行用に、数値を文字列に変換する
	 *
	 * @param mixed $num 数値
	 * @param bool $isAllowNull true の時、$num が null の場合は、'NULL' を返す
	 *
	 * @return string 変換後の文字列
	 */
	abstract public function N( mixed $num, bool $isAllowNull = true ): string;

	/**
	 * 書式付きSQL発行用に、文字列を引用符付き文字列に変換する
	 *
	 * @param mixed|null $str 文字列
	 * @param bool $isAllowNull true の時、$str が null の場合は、'NULL' を返す
	 *
	 * @return string 変換後の文字列(null 以外の場合、クォート後両端に引用符が付加される)
	 */
	abstract public function S( mixed $str, bool $isAllowNull = true ): string;

	/**
	 * 書式付きSQL発行用に、論理値を文字列に変換する
	 *
	 * @param mixed $bool 論理値
	 * @param bool $isAllowNull true の時、$bool が null の場合は、'NULL' を返す
	 *
	 * @return string 変換後の文字列
	 */
	abstract public function B( mixed $bool, bool $isAllowNull = true ): string;

	/**
	 * 書式付きSQL発行用に、日付を文字列に変換する
	 *
	 * @param mixed $date 日付文字列(PostgreSQLでの日付関数表記可)
	 * @param bool $isAllowNull true の時、$d が null/false/'' の場合は、'NULL' を返す
	 *
	 * @return string 変換後の文字列。日付関数表記以外の場合、両端に引用符が付与されるだけです。
	 */
	abstract public function TD( mixed $date, bool $isAllowNull = true ): string;

	/**
	 * 書式付きSQL発行用に、時刻を文字列に変換する
	 *
	 * @param mixed $time 時刻文字列(PostgreSQLでの日付関数表記可)
	 * @param bool $isAllowNull true の時、$d が null/false/'' の場合は、'NULL' を返す
	 *
	 * @return string 変換後の文字列。日付関数表記以外の場合、両端に引用符が付与されるだけです。
	 */
	abstract public function TT( mixed $time, bool $isAllowNull = true ): string;

	/**
	 * 書式付きSQL発行用に、タイムスタンプを文字列に変換する
	 *
	 * @param mixed $timestamp 時刻文字列(PostgreSQLでの日付関数表記可)
	 * @param bool $isAllowNull true の時、$d が null/false/'' の場合は、'NULL' を返す
	 *
	 * @return string 変換後の文字列。日付関数表記以外の場合、両端に引用符が付与されるだけです。
	 */
	abstract public function TS( mixed $timestamp, bool $isAllowNull = true ): string;

	/**
	 * 書式付きSQL発行用に、浮動小数点数を文字列に変換する
	 *
	 * @param mixed $num 浮動小数点数
	 * @param bool $isAllowNull true の時、$num が null の場合は、'NULL' を返す
	 *
	 * @return string 変換後の文字列
	 */
	abstract public function D( mixed $num, bool $isAllowNull = true ): string;

	/**
	 * 書式付きSQL発行用に、バイナリデータを16進文字列に変換する
	 *
	 * @param mixed $blob バイナリデータ
	 * @param bool $isAllowNull true の時、$raw が null の場合は、'NULL' を返す
	 *
	 * @return string
	 */
	abstract public function BLOB( mixed $blob, bool $isAllowNull = true ): string;

	/**
	 * SQLから返されたデータが同じかどうか比較する
	 *
	 * @param mixed $a 比較対象文字列１
	 * @param mixed $b 比較対象文字列２
	 * @param ?string $type NULLの場合はあいまい比較、stringの場合は文字列比較、datetimeの場合は日付時刻比較、boolの場合は論理値比較を行う
	 *
	 * @return bool 比較対象の片方がNULLの場合必ず true が、それ以外の場合は一致していれば true を、それ以外の場合は false を返す
	 */
	abstract public function CMP( mixed $a, mixed $b, ?string $type = null ): bool;

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
