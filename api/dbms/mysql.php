<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

/** @noinspection PhpComposerExtensionStubsInspection */

require_once __DIR__ . '/dbms.php';
require_once __DIR__ . '/mysql_property.php';

/**
 * MySQL over WGDBMS
 */
class WGDBMSMySQL extends WGDBMS
{
	static public PDO|false $current_connection = false;

	protected PDO|false $connection = false;
	protected PDOStatement|false $query = false;

	protected float $exectime = 0.0;
	protected int $row = 0;
	protected int $maxrows = 0;
	protected int $fetchmode = PDO::FETCH_BOTH;

	protected string $HOST = "";
	protected int $PORT = 3306;
	protected string $DB = "";
	protected string $USER = "";
	protected string $PASSWD = "";

	/**
	 * MySQL接続インスタンスを作成する
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

	public function property(): WGDBMSProperty
	{
		return WGDBMSPropertyMySQL::property();
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

		$params[] = "charset=utf8";

		$param   = implode( ";", $params );
		$options = [];
		$dbms_ca = WGCONF_DBMS_CA;
		if ( ! empty( $dbms_ca ) )
		{
			$options = [
				PDO::MYSQL_ATTR_SSL_CA => $dbms_ca
			];
		}

		try
		{
			$this->connection = new PDO( 'mysql:' . $param, $this->USER, $this->PASSWD, $options );
		}
		catch ( PDOException $e )
		{
			wg_log_write( WGLOG_ERROR, $e->getMessage() );
			$this->connection = false;
		}
		if ( ! $this->connection )
		{
			wgdie( "Database connection error." );
		}

		self::$current_connection = $this->connection;

		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function close(): bool
	{
		$this->connection = false;

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
		$this->query    = $this->connection->query( $q );
		$t2             = microtime( true );
		$td             = $t2 - $t1;
		$this->exectime += $td;
		$maxrows        = ( $this->query ) ? $this->query->rowCount() : 0;

		$e = ! $this->query ? implode( ' ', $this->connection->errorInfo() ) : "";
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

		return ( $this->row ++ < $this->maxrows ) ? $this->query->fetch( $this->fetchmode ) : false;
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
	public function ESC( string $str ): string
	{
		$v = self::$current_connection->quote( $str );
		if ( ( $len = strlen( $v ) ) >= 2 && $v[0] == $v[ $len - 1 ] )
		{
			return substr( $v, 1, $len - 2 );
		}

		return $v;
	}

	/**
	 * 書式付きSQL発行用に、数値を文字列に変換する
	 *
	 * @param mixed $num 数値
	 * @param bool $isAllowNull true の時、$num が null の場合は、'NULL' を返す
	 *
	 * @return string 変換後の文字列
	 */
	public function N( mixed $num, bool $isAllowNull = true ): string
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
	 * @param mixed $str 文字列
	 * @param bool $isAllowNull true の時、$str が null の場合は、'NULL' を返す
	 *
	 * @return string 変換後の文字列(null 以外の場合、クォート後両端に引用符が付加される)
	 */
	public function S( mixed $str, bool $isAllowNull = true ): string
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
	public function B( mixed $bool, bool $isAllowNull = true ): string
	{
		if ( ! is_null( $bool ) )
		{
			$bool = (bool) $bool;
		}

		return $isAllowNull && is_null( $bool ) ? 'NULL' : ( $bool ? '1' : '0' );
	}

	/**
	 * 書式付きSQL発行用に、日付を文字列に変換する
	 *
	 * @param mixed $date 日付文字列(PostgreSQLでの日付関数表記可)
	 * @param bool $isAllowNull true の時、$d が null/false/'' の場合は、'NULL' を返す
	 *
	 * @return string 変換後の文字列。日付関数表記以外の場合、両端に引用符が付与されるだけです。
	 */
	public function TD( mixed $date, bool $isAllowNull = true ): string
	{
		if ( ! is_null( $date ) )
		{
			$date = (string) $date;
		}

		if ( is_null( $date ) )
		{
			return $isAllowNull ? 'NULL' : self::S( '0001-01-01' );
		}
		else if ( preg_match( '/^(epoch|-?infinity|today|tomorrow|yesterday)/i', $date ) )
		{
			return self::S( $date );
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
	public function TT( mixed $time, bool $isAllowNull = true ): string
	{
		if ( ! is_null( $time ) )
		{
			$time = (string) $time;
		}

		if ( is_null( $time ) )
		{
			return $isAllowNull ? 'NULL' : self::S( '00:00:00' );
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
	public function TS( mixed $timestamp, bool $isAllowNull = true ): string
	{
		if ( ! is_null( $timestamp ) )
		{
			$timestamp = (string) $timestamp;
		}

		if ( is_null( $timestamp ) )
		{
			return $isAllowNull ? 'NULL' : self::S( '0001-01-01 00:00:00' );
		}
		else if ( preg_match( '/^(epoch|-?infinity|today|tomorrow|yesterday)/i', $timestamp ) )
		{
			return self::S( $timestamp );
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
	public function D( mixed $num, bool $isAllowNull = true ): string
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
	public function BLOB( mixed $blob, bool $isAllowNull = true ): string
	{
		return $isAllowNull && is_null( $blob ) ? 'NULL' : ( '0x' . bin2hex( $blob ) );
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
	public function CMP( mixed $a, mixed $b, ?string $type = null ): bool
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
				if ( $a == '1' )
				{
					$a = true;
				}
				else if ( $a == '0' )
				{
					$a = false;
				}
				if ( $b == '1' )
				{
					$b = true;
				}
				else if ( $b == '0' )
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
