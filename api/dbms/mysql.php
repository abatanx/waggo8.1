<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/dbms.php';

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
			$this->connection = new PDO( "mysql:{$param}", $this->USER, $this->PASSWD, $options );
		}
		catch ( PDOException $e )
		{
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
	static public function ESC( string $str ): string
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
	 * @param ?int $num 数値
	 * @param bool $isAllowNull true の時、$num が null の場合は、'null' を返す
	 *
	 * @return string 変換後の文字列
	 */
	static public function N( ?int $num, bool $isAllowNull = true ): string
	{
		return $isAllowNull && is_null( $num ) ? 'null' : (string) ( (int) $num );
	}

	/**
	 * 書式付きSQL発行用に、文字列を引用符付き文字列に変換する
	 *
	 * @param ?string $str 文字列
	 * @param bool $isAllowNull true の時、$str が null の場合は、'null' を返す
	 *
	 * @return string 変換後の文字列(null 以外の場合、クォート後両端に引用符が付加される)
	 */
	static public function S( ?string $str, bool $isAllowNull = true ): string
	{
		return $isAllowNull && is_null( $str ) ? 'null' : ( "'" . self::ESC( $str ) . "'" );
	}

	/**
	 * 書式付きSQL発行用に、論理値を文字列に変換する
	 *
	 * @param ?bool $bool 論理値
	 * @param bool $isAllowNull true の時、$bool が null の場合は、'null' を返す
	 *
	 * @return string 変換後の文字列
	 */
	static public function B( ?bool $bool, bool $isAllowNull = true ): string
	{
		return $isAllowNull && is_null( $bool ) ? 'null' : ( $bool ? '1' : '0' );
	}

	/**
	 * 書式付きSQL発行用に、日付時刻を文字列に変換する
	 *
	 * @param ?string $d 日付時刻文字列(MySQLでの日付関数表記可)
	 * @param bool $isAllowNull true の時、$d が null/false/'' の場合は、'null' を返す
	 *
	 * @return string 変換後の文字列。日付関数表記以外の場合、両端に引用符が付与されるだけです。
	 */
	static public function T( ?string $d, bool $isAllowNull = true ): string
	{
		if ( $isAllowNull && $d == false )
		{
			return 'null';
		}
		else if ( preg_match( "/^(current|epoch|-?infinity|invalid|now|today|tomorrow|yesterday|zulu|allballs|z)/i", $d ) )
		{
			return $d;
		}
		else
		{
			return self::S( $d );
		}
	}

	/**
	 * 書式付きSQL発行用に、浮動小数点数を文字列に変換する
	 *
	 * @param ?float $num 浮動小数点数
	 * @param bool $isAllowNull true の時、$num が null の場合は、'null' を返す
	 *
	 * @return string 変換後の文字列
	 */
	static public function D( ?float $num, bool $isAllowNull = true ): string
	{
		return $isAllowNull && is_null( $num ) ? 'null' : (string) ( (float) $num );
	}

	/**
	 * 書式付きSQL発行用に、バイナリデータを16進文字列に変換する
	 *
	 * @param ?string $raw バイナリデータ
	 * @param bool $isAllowNull true の時、$raw が null の場合は、'null' を返す
	 *
	 * @return string
	 */
	static public function BLOB( ?string $raw, bool $isAllowNull = true ): string
	{
		return $isAllowNull && is_null( $raw ) ? 'null' : ( '0x' . bin2hex( $raw ) );
	}

	/**
	 * SQLから返されたデータが同じかどうか比較する
	 *
	 * @param ?string $a 比較対象文字列１
	 * @param ?string $b 比較対象文字列２
	 * @param ?string $type NULLの場合はあいまい比較、stringの場合は文字列比較、datetimeの場合は日付時刻比較、boolの場合は論理値比較を行う
	 *
	 * @return bool 比較対象の片方がNULLの場合必ず true が、それ以外の場合は一致していれば true を、それ以外の場合は false を返す
	 */
	static public function CMP( ?string $a, ?string $b, ?string $type = null ): bool
	{
		if ( is_null( $a ) && is_null( $b ) )
		{
			return true;
		}

		switch ( $type )
		{
			case 'string':
				return ( (string) $a == (string) $b );

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
