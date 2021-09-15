<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/dbms.php';

/**
 * PostgreSQL over WGDBMS
 */
class WGDBMSPostgreSQL extends WGDBMS
{
	protected mixed $connection = false;
	protected mixed $query = false;

	protected float $exectime = 0.0;
	protected int $row = 0;
	protected int $maxrows = 0;
	protected int $fetchmode = PGSQL_BOTH;

	protected string $HOST = "";
	protected int $PORT = 5432;
	protected string $DB = "";
	protected string $USER = "";
	protected string $PASSWD = "";

	/**
	 * PostgreSQL接続インスタンスを作成する
	 *
	 * @param string $host 接続先ホスト
	 * @param int $port 接続先ポート番号
	 * @param string $db 接続先データベース名
	 * @param string $user 接続ユーザ名
	 * @param string $passwd 認証パスワード
	 */
	public function __construct( string $host, int $port, string $db, string $user, string $passwd )
	{
		parent::__construct();

		$this->HOST   = $host;
		$this->PORT   = $port;
		$this->DB     = $db;
		$this->USER   = $user;
		$this->PASSWD = $passwd;
	}

	/**
	 * @inheritdoc
	 */
	public function open(): bool
	{
		if ( $this->connection )
		{
			return true;
		}

		$params = [];
		if ( $this->HOST != "" )
		{
			$params[] = "host=$this->HOST";
		}

		if ( $this->DB != "" )
		{
			$params[] = "dbname=$this->DB";
		}

		if ( $this->USER != "" )
		{
			$params[] = "user=$this->USER";
		}

		if ( $this->PASSWD != "" )
		{
			$params[] = "password=$this->PASSWD";
		}

		if ( $this->PORT != 0 )
		{
			$params[] = "port=$this->PORT";
		}

		$param = implode( ' ', $params );

		$this->connection = @pg_connect( $param );
		if ( ! $this->connection )
		{
			wgdie( "Database connection error." );
		}

		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function close(): bool
	{
		if ( $this->connection )
		{
			@pg_close( $this->connection );
			$this->connection = false;
		}

		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function E( $q ): int
	{
		if ( ! $this->connection )
		{
			return 0;
		}

		$t1             = microtime( true );
		$this->query    = @pg_query( $this->connection, $q );
		$t2             = microtime( true );
		$td             = $t2 - $t1;
		$this->exectime += $td;
		$maxrows        = ( $this->query ) ? @pg_num_rows( $this->query ) : 0;

		$e = ! $this->query ? pg_last_error( $this->connection ) : '';
		$b = [
			'd'       => $td,
			'i'       => $this->exectime,
			's'       => $q,
			'r'       => (bool) $this->query,
			'e'       => $e,
			'maxrows' => $maxrows
		];

		$this->log[] = $b;

		$rt = $b['r'] ? 'OK' : '**** ERROR *****';
		$et = $b['r'] ? '' : " <<<< $e >>>>";
		$lt = $b['r'] ? WGLOG_INFO : WGLOG_ERROR;

		if ( $this->logging || ! $this->query )
		{
			wg_log_write( $lt, '(%s %d row(s) +%.1f/%.1f) %s%s', $rt, $b['maxrows'], $td, $b['i'], $q, $et );
		}

		$this->row     = 0;
		$this->maxrows = $maxrows;

		return $maxrows;
	}

	/**
	 * @inheritdoc
	 */
	public function Q( string $format, ...$values ): int
	{
		return $this->E( vsprintf( $format, $values ) );
	}

	/**
	 * @inheritdoc
	 */
	public function QQ( string $format, ...$values ): array|false
	{
		$t = $this->E( vsprintf( $format, $values ) );
		if ( $t !== 1 )
		{
			return false;
		}

		return $this->F();
	}

	/**
	 * @inheritdoc
	 */
	public function OK(): bool
	{
		return ! ! $this->query;
	}

	/**
	 * @inheritdoc
	 */
	public function NG(): bool
	{
		return ! $this->query;
	}

	/**
	 * @inheritdoc
	 */
	public function F(): array|false
	{
		if ( ! $this->connection || ! $this->query || $this->maxrows === - 1 )
		{
			return false;
		}

		return $this->row < $this->maxrows ? @pg_fetch_array( $this->query, $this->row ++, $this->fetchmode ) : false;
	}

	/**
	 * @inheritdoc
	 */
	public function FALL(): array
	{
		$r = [];
		while ( $f = $this->F() )
		{
			$r[] = $f;
		}

		return $r;
	}

	/**
	 * @inheritdoc
	 */
	public function FARRAY( string $field ): array
	{
		$r = [];
		while ( $f = $this->F() )
		{
			$r[] = $f[ $field ];
		}

		return $r;
	}

	/**
	 * @inheritdoc
	 */
	public function FARRAYKEYDATA( string $kf, string $df ): array
	{
		$r = [];
		while ( $f = $this->F() )
		{
			$r[ $f[ $kf ] ] = $f[ $df ];
		}

		return $r;
	}

	/**
	 * @inheritdoc
	 */
	public function RECS(): int
	{
		if ( ! $this->connection || ! $this->query || $this->maxrows == - 1 )
		{
			return 0;
		}

		return $this->maxrows;
	}

	/**
	 * 文字列をSQL用にクォートする
	 *
	 * @param string $str クォートする文字列
	 *
	 * @return string クォート後の文字列
	 */
	static public function ESC( string $str ): string
	{
		return pg_escape_string( $str );
	}

	/**
	 * 書式付きSQL発行用に、数値を文字列に変換する
	 *
	 * @param mixed $num 数値
	 * @param bool $isAllowNull true の時、$num が null の場合は、'NULL' を返す
	 *
	 * @return string 変換後の文字列
	 */
	static public function N( mixed $num, bool $isAllowNull = true ): string
	{
		if ( ! is_null( $num ) )
		{
			$num = (int) $num;
		}

		return $isAllowNull && is_null( $num ) ? 'NULL' : (string) ( (int) $num );
	}

	/**
	 * 書式付きSQL発行用に、文字列を引用符付き文字列に変換する
	 *
	 * @param mixed|null $str 文字列
	 * @param bool $isAllowNull true の時、$str が null の場合は、'NULL' を返す
	 *
	 * @return string 変換後の文字列(null 以外の場合、クォート後両端に引用符が付加される)
	 */
	static public function S( mixed $str, bool $isAllowNull = true ): string
	{
		if ( ! is_null( $str ) )
		{
			$str = (string) $str;
		}

		return $isAllowNull && is_null( $str ) ? 'NULL' : ( "'" . self::ESC( (string) $str ) . "'" );
	}

	/**
	 * 書式付きSQL発行用に、論理値を文字列に変換する
	 *
	 * @param mixed $bool 論理値
	 * @param bool $isAllowNull true の時、$bool が null の場合は、'NULL' を返す
	 *
	 * @return string 変換後の文字列
	 */
	static public function B( mixed $bool, bool $isAllowNull = true ): string
	{
		if ( ! is_null( $bool ) )
		{
			$bool = (bool) $bool;
		}

		return $isAllowNull && is_null( $bool ) ? 'NULL' : ( $bool ? 'TRUE' : 'FALSE' );
	}

	/**
	 * 書式付きSQL発行用に、日付を文字列に変換する
	 *
	 * @param mixed $date 日付文字列(PostgreSQLでの日付関数表記可)
	 * @param bool $isAllowNull true の時、$d が null/false/'' の場合は、'NULL' を返す
	 *
	 * @return string 変換後の文字列。日付関数表記以外の場合、両端に引用符が付与されるだけです。
	 */
	static public function TD( mixed $date, bool $isAllowNull = true ): string
	{
		if ( ! is_null( $date ) )
		{
			$date = (string) $date;
		}

		if ( is_null($date) )
		{
			return $isAllowNull ? 'NULL' : self::S('0001-01-01');
		}
		else if ( preg_match( '/^(epoch|-?infinity|today|tomorrow|yesterday)/i', $date ) )
		{
			return self::S($date);
		}
		else if ( preg_match( '/^(current|localtime|now)/i', $date ) )
		{
			return $date;
		}
		else
		{
			return self::S( $date );
		}
	}

	/**
	 * 書式付きSQL発行用に、時刻を文字列に変換する
	 *
	 * @param mixed $time 時刻文字列(PostgreSQLでの日付関数表記可)
	 * @param bool $isAllowNull true の時、$d が null/false/'' の場合は、'NULL' を返す
	 *
	 * @return string 変換後の文字列。日付関数表記以外の場合、両端に引用符が付与されるだけです。
	 */
	static public function TT( mixed $time, bool $isAllowNull = true ): string
	{
		if ( ! is_null( $time ) )
		{
			$time = (string) $time;
		}

		if ( is_null($time) )
		{
			return $isAllowNull ? 'NULL' : self::S('00:00:00');
		}
		else if ( preg_match( '/^(current|localtime|now)/i', $time ) )
		{
			return $time;
		}
		else
		{
			return self::S( $time );
		}
	}

	/**
	 * 書式付きSQL発行用に、タイムスタンプを文字列に変換する
	 *
	 * @param mixed $timestamp 時刻文字列(PostgreSQLでの日付関数表記可)
	 * @param bool $isAllowNull true の時、$d が null/false/'' の場合は、'NULL' を返す
	 *
	 * @return string 変換後の文字列。日付関数表記以外の場合、両端に引用符が付与されるだけです。
	 */
	static public function TS( mixed $timestamp, bool $isAllowNull = true ): string
	{
		if ( ! is_null( $timestamp ) )
		{
			$timestamp = (string) $timestamp;
		}

		if ( is_null($timestamp) )
		{
			return $isAllowNull ? 'NULL' : self::S('0001-01-01 00:00:00');
		}
		else if ( preg_match( '/^(epoch|-?infinity|today|tomorrow|yesterday)/i', $timestamp) )
		{
			return self::S($timestamp);
		}
		else if ( preg_match( '/^(current|localtime|now)/i', $timestamp ) )
		{
			return $timestamp;
		}
		else
		{
			return self::S( $timestamp );
		}
	}

	/**
	 * 書式付きSQL発行用に、浮動小数点数を文字列に変換する
	 *
	 * @param mixed $num 浮動小数点数
	 * @param bool $isAllowNull true の時、$num が null の場合は、'NULL' を返す
	 *
	 * @return string 変換後の文字列
	 */
	static public function D( mixed $num, bool $isAllowNull = true ): string
	{
		if ( ! is_null( $num ) )
		{
			$num = (float) $num;
		}

		return $isAllowNull && is_null( $num ) ? 'NULL' : (string) ( (float) $num );
	}

	/**
	 * 書式付きSQL発行用に、バイナリデータを16進文字列に変換する
	 *
	 * @param mixed $blob バイナリデータ
	 * @param bool $isAllowNull true の時、$raw が null の場合は、'NULL' を返す
	 *
	 * @return string
	 */
	static public function BLOB( mixed $blob, bool $isAllowNull = true ): string
	{
		return $isAllowNull && is_null( $blob ) ? 'NULL' : sprintf( "'%s'", pg_escape_bytea( $blob ) );
	}

	/**
	 * SQLから返されたデータが同じかどうか比較する
	 *
	 * @param mixed $a 比較対象文字列１
	 * @param mixed $b 比較対象文字列２
	 * @param ?string $type NULLの場合はあいまい比較、stringの場合は文字列比較、datetimeの場合は日付時刻比較、boolの場合は論理値比較を行う
	 *
	 * @return bool 比較対象の片方がNULLの場合必ず true が、それ以外の場合は一致していれば true を、それ以外の場合は false を返す
	 */
	static public function CMP( mixed $a, mixed $b, ?string $type = null ): bool
	{
		if ( is_null( $a ) && is_null( $b ) )
		{
			return true;
		}

		switch ( $type )
		{
			case 'string':
				return ( (string) $a === (string) $b );

			case 'datetime':
				$a = strtotime( $a );
				$b = strtotime( $b );

				return ( $a === $b );

			case 'bool':
				if ( $a == 't' )
				{
					$a = true;
				}
				else if ( $a == 'f' )
				{
					$a = false;
				}
				if ( $b == 't' )
				{
					$b = true;
				}
				else if ( $b == 'f' )
				{
					$b = false;
				}

				return $a === $b;
		}

		return $a == $b;
	}

	/**
	 * @inheritdoc
	 */
	public function BEGIN(): void
	{
		$this->E( 'BEGIN;' );
	}

	/**
	 * @inheritdoc
	 */
	public function ROLLBACK(): void
	{
		$this->E( 'ROLLBACK;' );
	}

	/**
	 * @inheritdoc
	 */
	public function COMMIT(): void
	{
		$this->E( 'COMMIT;' );
	}

	/**
	 * @inheritdoc
	 */
	public function END(): void
	{
		$this->E( 'END;' );
	}
}
