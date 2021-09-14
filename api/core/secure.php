<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

const WGSECURE_SL_GUEST   = 0;
const WGSECURE_SL_USER    = 10;
const WGSECURE_SL_MANAGER = 40;
const WGSECURE_SL_ADMIN   = 50;

/**
 * 画面遷移クラス
 */
class WGTransition
{
	const
		TRANSKEY = "T",        ///< 画面遷移で利用する$_GET用キー
		TRANSKEY_CALL = "_TC",        ///< WGFController で、他のコントローラーの呼び出しに利用する$_GET用キー
		TRANSKEY_RET = "_TR",        ///< WGFController で、他のコントローラーの呼び出しに対する返り値に利用する$_GET用キー
		TRANSKEY_PAIR = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789", ///< 画面遷移確認IDのキーとして利用できる文字列。
		TRANSKEY_LEN = 6;            ///< 画面遷移確認IDのキーとして利用する文字列の長さ。

	/**
	 * @var WGFSession
	 */
	public WGFSession $session;    ///< 画面遷移で利用している現在のセッション管理(WGFSession)インスタンス。

	/**
	 * 画面遷移確認IDを生成する。
	 * @return string 画面遷移確認IDを文字列で返します。 TRANSKEYPAIR で構成された TRANSKEYLEN の長さの文字列です。
	 */
	public function createTransitionId(): string
	{
		$r = "";
		$s = self::TRANSKEY_PAIR;
		$l = strlen( $s );
		for ( $i = 0; $i < self::TRANSKEY_LEN; $i ++ )
		{
			$r .= $s[ mt_rand( 0, $l - 1 ) ];
		}

		return $r;
	}

	/**
	 * 画面遷移確認IDを確認する。
	 * @return string|false 画面遷移が正常になされた場合、画面遷移確認IDの文字列を返す。それ以外の場合 false を返す。
	 */
	public function getTransitionId(): string|false
	{
		$t = $_GET[ self::TRANSKEY ];
		if ( strlen( $t ) != self::TRANSKEY_LEN )
		{
			return false;
		}
		if ( ! preg_match( '/^[' . self::TRANSKEY_PAIR . ']+$/', $t ) )
		{
			return false;
		}

		return $t;
	}

	/**
	 * 画面遷移で利用している WGFSession インスタンスを返す。
	 * @return WGFSession 現在のセッション管理インスタンス。
	 */
	public function getSession(): WGFSession
	{
		return $this->session;
	}

	/**
	 * 画面遷移セッションを作成する。
	 * 画面遷移が多数なされるとセッションに不必要な情報が溜まるので、必要にあわせて GC を行います。
	 *
	 * @param string $sessionid セッションID
	 * @param string $tid トランザクションID
	 *
	 * @return bool 画面遷移上、初回アクセスの場合(セッション内容が初期状態) true を、維持アクセスの場合 false を返す。
	 */
	public function firstpage( string $sessionid, string $tid = '' ): bool
	{
		$gpara = [];
		$isNew = true;
		foreach ( $_GET as $k => $v )
		{
			switch ( $k )
			{
				case self::TRANSKEY:
					$isNew = false;
					$tid   = $v;
					break;

				default:
					$gpara[ $k ] = $v;
					break;
			}
		}

		// セッションのゴミ回収
		WGFSession::gc();

		// NEXT付きの場合は、セッションチェック
		if ( ! $isNew )
		{
			$this->session = new WGFSession( $sessionid, $tid );
			if ( $this->session->get( "%tid" ) != $tid )
			{
				$isNew = true;
			}
			if ( $isNew )
			{
				wg_log_write( WGLOG_INFO, "RESET " );
			}
		}

		if ( $isNew )
		{
			$tid = self::createTransitionId();

			$npara                   = [];
			$gpara[ self::TRANSKEY ] = $tid;
			foreach ( $gpara as $k => $v )
			{
				$npara[] = urlencode( $k ) . ( ( $v != "" ) ? ( "=" . urlencode( $v ) ) : "" );
			}
			$u                      = $_SERVER["SCRIPT_NAME"] . ( ( count( $npara ) != 0 ) ? "?" . implode( '&', $npara ) : "" );
			$_SERVER["REQUEST_URI"] = $u;
			$_GET[ self::TRANSKEY ] = $tid;

			$this->session = new WGFSession( $sessionid, $tid );
			$this->session->set( "%tid", $tid );

			return true;
		}

		return false;
	}
}
