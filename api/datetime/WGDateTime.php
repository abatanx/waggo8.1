<?php
/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

class WGDateTimeException extends Exception
{
}

class WGDateTime
{
	const Y = 0;
	const M = 1;
	const D = 2;
	const H = 3;
	const I = 4;
	const S = 5;

	protected int $_ut = 0;

	/**
	 * mod
	 */
	static function mod( int $a, int $b ): int
	{
		return ( ( $a % $b ) + $b ) % $b;
	}

	/**
	 * 有効な日付であるかチェックする
	 */
	static function isValidDate($yy,$mm,$dd)
	{

	}

	/**
	 * unix-timestamp から、日時配列に変換する
	 *
	 * @param int $ut unix-timestamp
	 *
	 * @return array 連想配列 tm_year,tm_mon,tm_mday,tm_hour,tm_min,tm_sec としてセットされる
	 */
	static function splitDateTime( int $ut ): array
	{
		$a = localtime( $ut, true );

		return [
			$a["tm_year"] + 1900,
			$a["tm_mon"] + 1,
			$a["tm_mday"],
			$a["tm_hour"],
			$a["tm_min"],
			$a["tm_sec"]
		];
	}

	/**
	 * unix-timestamp を生成する
	 *
	 * @param array $a [Y,M,D,H,I,S] の順に数値がセットされた配列
	 *
	 * @return int|false 成功した場合は unix-timestamp を、失敗した場合は false を返す
	 */
	static function makeDateTime( array $a ): int|false
	{
		return mktime(
			$a[ self::H ], $a[ self::I ], $a[ self::S ],
			$a[ self::M ], $a[ self::D ], $a[ self::Y ]
		);
	}

	/**
	 * 4,5,6...,3 の月を 0,1,2...,11 のインデックスに変換する。
	 *
	 * @param int $month 月の数値(4〜12,1〜3)
	 *
	 * @return int 0〜11のインデックス(4->0, 5->1..., 3->11)
	 */
	static function convertMonthToNendoIndex( int $month ): int
	{
		return self::mod( ( $month + 12 ) - 4, 12 );
	}

	/**
	 * 0,1,2...,11 のインデックスを、4,5,6...,3 の月に変換する。
	 *
	 * @param int $nendoidx 月のインデックス(0〜11)
	 *
	 * @return int 月(0->4, 1->5..., 11->3)
	 */
	static function convertNendoIndexToMonth( int $nendoidx ): int
	{
		return self::mod( $nendoidx + 3, 12 ) + 1;
	}

	/**
	 * constructor
	 */
	public function __construct()
	{
		$this->setUT( 0 );
	}

	/**
	 * unix-timestamp をセットする
	 *
	 * @param int $ut unix-timestamp
	 *
	 * @retrun $this
	 */
	protected function setUT( int $ut ): self
	{
		$this->_ut = $ut;

		return $this;
	}

	/**
	 * unix-timestamp を取得する
	 *
	 * @retrun int
	 */
	protected function getUT(): int
	{
		return $this->_ut;
	}

	/**
	 * 日時をセットする
	 *
	 * @param int $y 西暦年
	 * @param int $m 月
	 * @param int $d 日
	 * @param int $h 時
	 * @param int $i 分
	 * @param int $s 秒
	 *
	 * @return $this
	 */
	public function set( int $y, int $m = 1, int $d = 1, int $h = 0, int $i = 0, int $s = 0 ): self
	{
		$this->setUT( self::makeDateTime( [ $y, $m, $d, $h, $i, $s ] ) );

		return $this;
	}

	/**
	 * 日付をセットする
	 *
	 * @param ?int $y 西暦年(nullの場合、セットしない)
	 * @param ?int $m 月(nullの場合、セットしない)
	 * @param ?int $d 日(nullの場合、セットしない)
	 *
	 * @return $this
	 */
	public function setDate( ?int $y = null, ?int $m = null, ?int $d = null ): self
	{
		/** @noinspection DuplicatedCode */
		$a = self::splitDateTime( $this->getUT() );

		if ( ! is_null( $y ) )
		{
			$a[ self::Y ] = $y;
		}
		if ( ! is_null( $m ) )
		{
			$a[ self::M ] = $m;
		}
		if ( ! is_null( $d ) )
		{
			$a[ self::D ] = $d;
		}
		$this->setUT( self::makeDateTime( $a ) );

		return $this;
	}

	/**
	 * 時間をセットする
	 *
	 * @param ?int $h 時(nullの場合、セットしない)
	 * @param ?int $i 分(nullの場合、セットしない)
	 * @param ?int $s 秒(nullの場合、セットしない)
	 *
	 * @return $this
	 */
	public function setTime( ?int $h = null, ?int $i = null, ?int $s = null ): self
	{
		/** @noinspection DuplicatedCode */
		$a = self::splitDateTime( $this->getUT() );

		if ( ! is_null( $h ) )
		{
			$a[ self::H ] = $h;
		}
		if ( ! is_null( $i ) )
		{
			$a[ self::I ] = $i;
		}
		if ( ! is_null( $s ) )
		{
			$a[ self::S ] = $s;
		}
		$this->setUT( self::makeDateTime( $a ) );

		return $this;
	}

	/**
	 * 現在日時をセットする
	 *
	 * @return $this
	 */
	public function setNow(): self
	{
		return $this->setUT( time() );
	}

	/**
	 * unix-timestamp から日時をセットする
	 *
	 * @param int $ut unix-timestamp
	 *
	 * @return $this
	 */
	public function setUnixTime( int $ut ): self
	{
		return $this->setUT( $ut );
	}

	/**
	 * strtotime() を介して、日時をセットする
	 *
	 * @param string $str 日時文字列
	 *
	 * @return $this
	 * @throws WGDateTimeException
	 */
	public function setStrToTime( string $str ): self
	{
		if ( ( $s = strtotime( $str ) ) === false )
		{
			$this->setUT( 0 );
			throw new WGDateTimeException( "strtotime() failed, ($str) in WGDateTime" );
		}

		$this->setUT( $s );

		return $this;
	}

	/**
	 * 特定の日時要素のみを取得する
	 *
	 * @param int $k 要素名(::Y,::M,::D,::H,::I,::S)
	 *
	 * @return int
	 */
	public function getComponentValue( int $k ): int
	{
		return self::splitDateTime( $this->getUT() )[ $k ];
	}

	/**
	 * 特定の日時要素のみをセットする
	 *
	 * @param int $k 要素名(::Y,::M,::D,::H,::I,::S)
	 * @param int $v セットする値
	 *
	 * @return $this
	 */
	public function setComponentValue( int $k, int $v ): self
	{
		$a       = self::splitDateTime( $this->getUT() );
		$a[ $k ] = $v;
		$this->setUT( self::makeDateTime( $a ) );

		return $this;
	}

	/**
	 * 年を取得する
	 *
	 * @return int
	 */
	public function getYear(): int
	{
		return $this->getComponentValue( self::Y );
	}

	/**
	 * 年をセットする
	 *
	 * @param int $v セットする値
	 *
	 * @return $this
	 */
	public function setYear( int $v ): self
	{
		$this->setComponentValue( self::Y, $v );

		return $this;
	}

	/**
	 * 月を取得する
	 *
	 * @return int
	 */
	public function getMonth(): int
	{
		return $this->getComponentValue( self::M );
	}

	/**
	 * 月をセットする
	 *
	 * @param int $v セットする値
	 *
	 * @return $this
	 */
	public function setMonth( int $v ): self
	{
		$this->setComponentValue( self::M, $v );

		return $this;
	}

	/**
	 * 日を取得する
	 *
	 * @return int
	 */
	public function getDay(): int
	{
		return $this->getComponentValue( self::D );
	}

	/**
	 * 日をセットする
	 *
	 * @param int $v セットする値
	 *
	 * @return $this
	 */
	public function setDay( int $v ): self
	{
		$this->setComponentValue( self::D, $v );

		return $this;
	}

	/**
	 * 時を取得する
	 *
	 * @return int
	 */
	public function getHour(): int
	{
		return $this->getComponentValue( self::H );
	}

	/**
	 * 時をセットする
	 *
	 * @param int $v セットする値
	 *
	 * @return $this
	 */
	public function setHour( int $v ): self
	{
		$this->setComponentValue( self::H, $v );

		return $this;
	}

	/**
	 * 分を取得する
	 *
	 * @return int
	 */
	public function getMin(): int
	{
		return $this->getComponentValue( self::I );
	}

	/**
	 * 分をセットする
	 *
	 * @param int $v セットする値
	 *
	 * @return $this
	 */
	public function setMin( int $v ): self
	{
		$this->setComponentValue( self::I, $v );

		return $this;
	}

	/**
	 * 秒を取得する
	 *
	 * @return int
	 */
	public function getSec(): int
	{
		return $this->getComponentValue( self::S );
	}

	/**
	 * 秒をセットする
	 *
	 * @param int $v セットする値
	 *
	 * @return $this
	 */
	public function setSec( int $v ): self
	{
		$this->setComponentValue( self::S, $v );

		return $this;
	}

	/**
	 * 年月をセットする
	 *
	 * @param int $yy セットする年
	 * @param int $mm セットする月
	 *
	 * @return $this
	 */
	public function setYearMonth( int $yy, int $mm ): self
	{
		$this->setMonth( $mm );
		$this->setYear( $yy );

		return $this;
	}

	/**
	 * 年月を取得する
	 *
	 * @return array [YYYY,MM] の配列
	 */
	public function getYearMonth(): array
	{
		return [ $this->getYear(), $this->getMonth() ];
	}

	/**
	 * 西暦年度・月をセットする
	 *
	 * @param int $nendo セットする西暦年度
	 * @param int $mm セットする月
	 *
	 * @return $this
	 * @throws WGDateTimeException
	 */
	public function setNendoMonth( int $nendo, int $mm ): self
	{
		$this->setMonth( $mm );
		if ( $mm >= 4 && $mm <= 12 )
		{
			$this->setYear( $nendo );
		}
		else if ( $mm >= 1 && $mm <= 3 )
		{
			$this->setYear( $nendo + 1 );
		}
		else
		{
			$this->setUT( 0 );
			throw new WGDateTimeException( "setNendoMonth() failed, ($nendo, $mm) in WGDateTime" );
		}

		return $this;
	}

	/**
	 * 西暦年度を取得する
	 *
	 * @return int
	 * @throws WGDateTimeException
	 */
	public function getNendo(): int
	{
		$mm = $this->getMonth();
		if ( $mm >= 4 && $mm <= 12 )
		{
			return $this->getYear();
		}
		else if ( $mm >= 1 && $mm <= 3 )
		{
			return $this->getYear() - 1;
		}
		else
		{
			throw new WGDateTimeException( "getNendo() failed, ($mm) in WGDateTime" );
		}
	}

	/**
	 * 西暦年度月を取得する
	 *
	 * @return array [NENDO,MM] の配列
	 * @throws WGDateTimeException
	 */
	public function getNendoMonth(): array
	{
		return [ $this->getNendo(), $this->getMonth() ];
	}

	/**
	 * 年に切り詰める
	 * @return $this
	 */
	public function truncateYear(): self
	{
		$a            = self::splitDateTime( $this->getUT() );
		$a[ self::M ] = 1;
		$a[ self::D ] = 1;
		$a[ self::H ] = $a[ self::I ] = $a[ self::S ] = 0;

		return $this->setUT( self::makeDateTime( $a ) );
	}

	/**
	 * 年月に切り詰める
	 * @return $this
	 */
	public function truncateMonth(): self
	{
		$a            = self::splitDateTime( $this->getUT() );
		$a[ self::D ] = 1;
		$a[ self::H ] = $a[ self::I ] = $a[ self::S ] = 0;

		return $this->setUT( self::makeDateTime( $a ) );
	}

	/**
	 * 年月日に切り詰める
	 * @return $this
	 */
	public function truncateDay(): self
	{
		$a            = self::splitDateTime( $this->getUT() );
		$a[ self::H ] = $a[ self::I ] = $a[ self::S ] = 0;

		return $this->setUT( self::makeDateTime( $a ) );
	}

	/**
	 * 年月日・時に切り詰める
	 * @return $this
	 */
	public function truncateHour(): self
	{
		$a            = self::splitDateTime( $this->getUT() );
		$a[ self::I ] = $a[ self::S ] = 0;

		return $this->setUT( self::makeDateTime( $a ) );
	}

	/**
	 * 年月日・時分に切り詰める
	 * @return $this
	 */
	public function truncateMin(): self
	{
		$a            = self::splitDateTime( $this->getUT() );
		$a[ self::S ] = 0;

		return $this->setUT( self::makeDateTime( $a ) );
	}

	/**
	 * 年を加算する
	 *
	 * @param int $v 加算する値
	 *
	 * @return $this
	 */
	public function addYear( int $v ): self
	{
		$a            = self::splitDateTime( $this->getUT() );
		$a[ self::Y ] += $v;

		return $this->setUT( self::makeDateTime( $a ) );
	}

	/**
	 * 月を加算する
	 *
	 * @param int $v 加算する値
	 *
	 * @return $this
	 */
	public function addMonth( int $v ): self
	{
		$a            = self::splitDateTime( $this->getUT() );
		$a[ self::M ] += $v;

		return $this->setUT( self::makeDateTime( $a ) );
	}

	/**
	 * 日を加算する
	 *
	 * @param int $v 加算する値
	 *
	 * @return $this
	 */
	public function addDay( int $v ): self
	{
		$a            = self::splitDateTime( $this->getUT() );
		$a[ self::D ] += $v;

		return $this->setUT( self::makeDateTime( $a ) );
	}

	/**
	 * 時を加算する
	 *
	 * @param int $v 加算する値
	 *
	 * @return $this
	 */
	public function addHour( int $v ): self
	{
		return $this->setUT( $this->getUT() + ( 60 * 60 ) * $v );
	}

	/**
	 * 分を加算する
	 *
	 * @param int $v 加算する値
	 *
	 * @return $this
	 */
	public function addMin( int $v ): self
	{
		return $this->setUT( $this->getUT() + 60 * $v );
	}

	/**
	 * 秒を加算する
	 *
	 * @param int $v 加算する値
	 *
	 * @return $this
	 */
	public function addSec( int $v ): self
	{
		return $this->setUT( $this->getUT() + $v );
	}

	/**
	 * 曜日を取得する
	 *
	 * @return int 0:日曜日〜6:土曜日
	 */
	public function getDayOfWeek(): int
	{
		return (int) date( 'w', $this->getUT() );
	}

	/**
	 * unix-timestamp を取得する
	 *
	 * @return int
	 */
	public function getUnixTime(): int
	{
		return $this->getUT();
	}

	/**
	 * 指定されたフォーマットで整形された文字列を返す
	 *
	 * @param string $fmt date() で設定可能なフォーマット文字列
	 *
	 * @return string|false
	 */
	public function getByFormat( string $fmt ): string|false
	{
		return date( $fmt, $this->getUT() );
	}

	/**
	 * Y-m-d H:i:s でフォーマットされた文字列を返す
	 *
	 * @return string|false
	 */
	public function getYMDHISString(): string|false
	{
		return $this->getByFormat( "Y-m-d H:i:s" );
	}

	/**
	 * Y-m-d でフォーマットされた文字列を返す
	 *
	 * @return string|false
	 */
	public function getYMDString(): string|false
	{
		return $this->getByFormat( "Y-m-d" );
	}

	/**
	 * Y-m でフォーマットされた文字列を返す
	 *
	 * @return string|false
	 */
	public function getYMString(): string|false
	{
		return $this->getByFormat( "Y-m" );
	}

	/**
	 * H:i:s でフォーマットされた文字列を返す
	 *
	 * @return string|false
	 */
	public function getHISString(): string|false
	{
		return $this->getByFormat( "H:i:s" );
	}

	/**
	 * H:i でフォーマットされた文字列を返す
	 *
	 * @return string|false
	 */
	public function getHIString(): string|false
	{
		return $this->getByFormat( "H:i" );
	}

	/**
	 * 年月をインデックス化した数値を返す
	 *
	 * @return int 年*12+月(0〜11)で計算されたインデックス
	 */
	public function getYMIndex(): int
	{
		$a = self::splitDateTime( $this->getUT() );

		return $a[ self::Y ] * 12 + ( $a[ self::M ] - 1 );
	}

	/**
	 * インデックス化された年月から、年月をセットする
	 * セットした場合、その年月の初日になる
	 *
	 * @param int $v 年*12+月(0〜11)で計算された、セットするインデックス
	 *
	 * @return $this
	 */
	public function setYMIndex( int $v ): self
	{
		$y = (int) ( $v / 12 );
		$m = ( $v % 12 ) + 1;

		return $this->setYear( $y )->setMonth( $m )->setDay( 1 )->setTime( 0, 0, 0 );
	}

	/**
	 * 日時をコピーしたインスタンスを作成する
	 *
	 * @return $this
	 */
	public function copy(): self
	{
		$d = new static();
		return $d->copyFrom($this);
	}

	/**
	 * 日時を他のインスタンスからコピーする
	 *
	 * @param WGDateTime $from
	 *
	 * @return $this
	 */
	public function copyFrom( WGDateTime $from ): self
	{
		return $this->setUnixTime( $from->getUnixTime() );
	}

	/**
	 * 日時を、他のインスタンスと比較する
	 *
	 * @param string $exp ==, ===, <. <=, >, >=, !=, !== のいずれか
	 * @param WGDateTime $right 右辺のインスタンス
	 *
	 * @return bool 比較結果
	 * @throws WGDateTimeException
	 */
	public function compare( string $exp, WGDateTime $right ): bool
	{
		switch ( $exp )
		{
			case "==":
				return $this->getUnixTime() == $right->getUnixTime();
			case "===":
				return $this->getUnixTime() === $right->getUnixTime();
			case "<":
				return $this->getUnixTime() < $right->getUnixTime();
			case "<=":
				return $this->getUnixTime() <= $right->getUnixTime();
			case ">":
				return $this->getUnixTime() > $right->getUnixTime();
			case ">=":
				return $this->getUnixTime() >= $right->getUnixTime();
			case "!=":
				return $this->getUnixTime() != $right->getUnixTime();
			case "!==":
				return $this->getUnixTime() !== $right->getUnixTime();
		}
		throw new WGDateTimeException( "compare failed, ($exp) in WGDateTime" );
	}
}
