<?php
/**
 * waggo8
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

class WGFSession
{
	static bool $isOpenSession = false;
	protected string $sessionId, $transactionId, $combinedId;

	/**
	 * トランザクショナルセッション管理インスタンスを作成する。
	 *
	 * @param string $sessionId 固有セッションID。
	 * @param string $transactionId トランザクション管理ID。
	 */
	public function __construct( string $sessionId, string $transactionId )
	{
		$this->setId( $sessionId, $transactionId );
	}

	/**
	 * トランザクショナルセッション管理インスタンスを結合キーから復帰する。
	 *
	 * @param string $combinedId 結合キー
	 *
	 * @return ?WGFSession 成功した場合は復元したトランザクショナルセッション管理インスタンスを、失敗した場合は null を返す。
	 */
	static public function restoreByCombinedId( string $combinedId ): ?WGFSession
	{
		if ( is_array( $_SESSION ) )
		{
			$key_s = array_keys( $_SESSION );
			foreach ( $key_s as $ks )
			{
				if ( is_array( $_SESSION[ $ks ] ) )
				{
					$key_t = array_keys( $_SESSION[ $ks ] );
					foreach ( $key_t as $kt )
					{
						if ( is_array( $_SESSION[ $ks ][ $kt ] )
							 && @isset( $_SESSION[ $ks ][ $kt ]['%combined'] )
							 && $_SESSION[ $ks ][ $kt ]['%combined'] === $combinedId )
						{
							return new WGFSession( $ks, $kt );
						}
					}
				}
			}
		}

		return null;
	}

	/**
	 * トランザクショナルセッション管理インスタンスを破棄する。
	 */
	public function __destruct()
	{
		if ( WG_SESSIONDEBUG )
		{
			wg_log_dump( WGLOG_INFO, $_SESSION );
		}
	}

	/**
	 * PHPセッションを開始する。
	 * WGFSession は、このPHPセッションのうち、トランザクショナル固有セッションIDで指定された部分を画面維持などに利用します。
	 */
	static public function open(): void
	{
		if ( ! self::$isOpenSession )
		{
			if ( WG_SESSIONDEBUG )
			{
				wg_log_write( WGLOG_INFO, "[[[ waggo SESSION open ]]]" );
			}

			session_cache_limiter( 'nocache' );
			session_start();

			self::$isOpenSession = true;
		}
	}

	/**
	 * PHPセッションを終了する。
	 */
	static public function close(): void
	{
		if ( self::$isOpenSession )
		{
			session_write_close();
			if ( WG_SESSIONDEBUG )
			{
				wg_log_write( WGLOG_INFO, "[[[ waggo SESSION close ]]]" );
			}
			self::$isOpenSession = false;
		}
	}

	/**
	 * トランザクショナル固有セッションIDを取得する。
	 * @retval array [0=>固有セッションID, 1=>画面遷移ID] を返す。
	 */
	public function getId(): array
	{
		return [ $this->sessionId, $this->transactionId ];
	}

	/**
	 * トランザクショナルセッションの割り当てを行う。
	 *
	 * @param string $sessionId セッションID
	 * @param string $transactionId トランザクションID
	 *
	 * @return void
	 */
	public function setId( string $sessionId, string $transactionId ): void
	{
		$this->sessionId     = $sessionId;
		$this->transactionId = $transactionId;
		$this->combinedId    = md5( $this->transactionId . ' @ ' . $this->sessionId );
		if ( ! isset( $_SESSION[ $this->sessionId ][ $this->transactionId ] ) ||
			 ! is_array( $_SESSION[ $this->sessionId ][ $this->transactionId ] ) )
		{
			$_SESSION[ $this->sessionId ][ $this->transactionId ] = [];
		}

		$_SESSION[ $this->sessionId ][ $this->transactionId ]["%atime"]    = time();
		$_SESSION[ $this->sessionId ][ $this->transactionId ]["%combined"] = $this->combinedId;

		if ( WG_SESSIONDEBUG )
		{
			wg_log_write( WGLOG_INFO, "[[[ waggo SESSION started, $this->sessionId $this->transactionId ]]]" );
		}
	}

	/**
	 * トランザクショナル固有セッションIDのうち、複合IDを取得する。
	 * @return string 結合ID
	 */
	public function getCombinedId(): string
	{
		return (string) $this->get( '%combined' );
	}

	/**
	 * トランザクショナル固有セッションIDのうち、固有セッションIDを取得する。
	 * @retval string 固有セッションID。
	 */
	public function getSessionId(): string
	{
		return $this->sessionId;
	}

	/**
	 * トランザクショナル固有セッションIDのうち、画面遷移IDを取得する。
	 * @retval string 固有セッションID。
	 */
	public function getTransactionId(): string
	{
		return $this->transactionId;
	}

	/**
	 * トランザクショナルセッションのすべての情報を返します。通常は配列で得られます。
	 * @return mixed $_SESSION[固有セッションID][画面遷移ID] を返します。
	 */
	public function getAll(): mixed
	{
		return $_SESSION[ $this->sessionId ][ $this->transactionId ];
	}

	/**
	 * トランザクショナル固有セッションIDで管理している領域に、データをセットします。
	 *
	 * @param string $key キー。
	 * @param mixed $val データ。
	 */
	public function __set( string $key, mixed $val ): void
	{
		$this->set( $key, $val );
	}

	/**
	 * トランザクショナル固有セッションIDで管理している領域から、データを取得します。
	 *
	 * @param string $key キー。
	 *
	 * @return mixed データ。
	 */
	public function __get( string $key ): mixed
	{
		return $this->get( $key );
	}

	/**
	 * トランザクショナル固有セッションIDで管理している領域に、データをセットします。
	 *
	 * @param string $key キー。
	 * @param mixed $val データ。
	 */
	public function set( string $key, mixed $val ): void
	{
		if ( WG_SESSIONDEBUG )
		{
			wg_log_write( WGLOG_INFO, "SESSION set '$key' = '$val'" );
		}
		if ( is_null( $val ) )
		{
			$_SESSION[ $this->sessionId ][ $this->transactionId ][ $key ] = null;
			unset( $_SESSION[ $this->sessionId ][ $this->transactionId ][ $key ] );
		}
		else
		{
			$_SESSION[ $this->sessionId ][ $this->transactionId ][ $key ]   = $val;
			$_SESSION[ $this->sessionId ][ $this->transactionId ]['%atime'] = time();
		}
	}

	/**
	 * トランザクショナル固有セッションIDで管理している領域から、データを取得します。
	 *
	 * @param string $key キー。
	 *
	 * @return mixed データ。
	 */
	public function get( string $key ): mixed
	{
		$val = null;

		$_SESSION[ $this->sessionId ][ $this->transactionId ]['%atime'] = time();
		if ( isset( $_SESSION[ $this->sessionId ][ $this->transactionId ][ $key ] ) )
		{
			$val = $_SESSION[ $this->sessionId ][ $this->transactionId ][ $key ];
		}
		if ( WG_SESSIONDEBUG )
		{
			wg_log_write( WGLOG_INFO, "SESSION get '$key' = '$val'" );
		}

		return $val;
	}

	/**
	 * トランザクショナル固有セッションIDで管理している領域に、データがセットされているか確認します。
	 * セット状態の確認は、isset で行う。
	 *
	 * @param string $key キー。
	 *
	 * @return bool データが存在する場合 true を返す。
	 */
	public function isExists( string $key ): bool
	{
		return isset( $_SESSION[ $this->sessionId ][ $this->transactionId ][ $key ] );
	}

	/**
	 * トランザクショナル固有セッションIDで管理している領域で、該当するキーのデータが空の状態化か確認します。
	 * 空の状態の確認は、empty で行う。
	 *
	 * @param string $key キー。
	 *
	 * @return boolean true データが空の場合、true を返す。
	 */
	public function isEmpty( string $key ): bool
	{
		return empty( $_SESSION[ $this->sessionId ][ $this->transactionId ][ $key ] );
	}

	/**
	 * トランザクショナル固有セッションIDで管理している領域で、該当するキーのデータを削除します。
	 *
	 * @param string $key キー。
	 */
	public function delete( string $key ): void
	{
		$this->set( $key, null );
	}

	/**
	 * トランザクショナル固有セッションIDで管理している領域を GC対象領域としてマークします。
	 */
	public function release(): void
	{
		$_SESSION[ $this->sessionId ][ $this->transactionId ] = [ "%release" => true ];
	}

	/**
	 * トランザクショナル固有セッションIDで管理している領域を、すべてクリアします。
	 */
	public function cleanup(): void
	{
		$_SESSION[ $this->sessionId ][ $this->transactionId ] = null;
		unset( $_SESSION[ $this->sessionId ][ $this->transactionId ] );
	}

	/**
	 * PHPセッションから、トランザクショナルセッションの状態を確認し、利用されていない場合開放します。
	 */
	static public function gc(): void
	{
		if ( WG_SESSIONDEBUG )
		{
			wg_log_write( WGLOG_INFO, "[[[ waggo SESSION garbage collection ]]]" );
		}
		self::open();

		$nTime = time();
		foreach ( $_SESSION as $sk => $trs )
		{
			if ( preg_match( '/^[0-9a-zA-Z]/', $sk ) && is_array( $trs ) )
			{
				foreach ( $trs as $tk => $td )
				{
					if ( ( isset( $td["%atime"] ) && ( $nTime - $td["%atime"] ) > WGCONF_SESSION_GCTIME ) ||
						 isset( $td["%release"] ) )
					{
						$_SESSION[ $sk ][ $tk ] = null;
						unset( $_SESSION[ $sk ][ $tk ] );
						if ( WG_SESSIONDEBUG )
						{
							wg_log_write( WGLOG_INFO, "[[[ COLLECTED-TRANSACTION ]]] $sk$tk" );
						}
					}
				}
				if ( count( $_SESSION[ $sk ] ) == 0 )
				{
					$_SESSION[ $sk ] = null;
					unset( $_SESSION[ $sk ] );
					if ( WG_SESSIONDEBUG )
					{
						wg_log_write( WGLOG_INFO, "[[[ COLLECTED-SESSION-KEY ]]] $sk" );
					}
				}
			}
		}
	}
}
