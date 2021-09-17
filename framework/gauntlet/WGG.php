<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGG.php';
require_once __DIR__ . '/../../api/core/exception.php';

abstract class WGG
{
	/**
	 * @var string[] エラーメッセージ
	 */
	private array $errors;

	/**
	 * @var ?WGG 後部連結するガントレット
	 */
	private ?WGG $chainedIfValid;
	private ?WGG $chainedIfInvalid;

	public function __construct()
	{
		$this->errors           = [];
		$this->chainedIfValid   = null;
		$this->chainedIfInvalid = null;
	}

	/**
	 * 判定用文字表現に変換する。スカラ型のうち、 bool 以外は string にキャストする。
	 * bool の場合は、'' に変換する。それ以外の場合は、スクリプトの実行を中止する。
	 *
	 * @param mixed $value 判定用文字表現に変換する変数
	 *
	 * @return string 変換後
	 * @noinspection PhpInconsistentReturnPointsInspection
	 */
	public function castString( mixed $value ): string
	{
		if ( is_null( $value ) )
		{
			return '';
		}
		else if ( is_scalar( $value ) && ! is_bool( $value ) )
		{
			return (string) $value;
		}
		else
		{
			if ( function_exists( 'wg_log_write' ) )
			{
				wg_log_write( WGLOG_FATAL, '\'%s\' type is not allowed.', gettype( $value ) );
			}
			else
			{
				$type = gettype($value);
				throw new WGRuntimeException("'$type' is not allowed.");
			}
		}
	}

	/**
	 * @param ?WGG $valid 引数の最初は評価後、正常の場合（invalid が null の場合は、すべてのケースで）に追評価するガントレット
	 * @param ?WGG $invalid 異常の場合に追評価するガントレット
	 *
	 * @return WGG
	 */
	public function add( ?WGG $valid = null, ?WGG $invalid = null ): self
	{
		$this->chainedIfValid   = $valid;
		$this->chainedIfInvalid = $invalid;

		return $this;
	}

	public function valid( WGG $next ): self
	{
		$this->chainedIfValid = $next;

		return $this;
	}

	public function invalid( WGG $next ): self
	{
		$this->chainedIfInvalid = $next;

		return $this;
	}

	public function setError( string $msg ): self
	{
		$this->errors = [ $msg ];

		return $this;
	}

	public function addError( string $msg ): self
	{
		$this->errors[] = $msg;

		return $this;
	}

	public function listError(): array
	{
		return $this->errors;
	}

	public function getError(): string
	{
		return implode( ",", $this->errors );
	}

	public function unsetError(): self
	{
		$this->errors = [];

		return $this;
	}

	public function hasError(): bool
	{
		return count( $this->errors ) > 0;
	}

	public function isFilter(): bool
	{
		return false;
	}

	/**
	 * @return string エラーメッセージテンプレート
	 */
	abstract protected function makeErrorMessage(): string;

	/**
	 * @param mixed $data 通過させるデータ
	 *
	 * @return boolean 検証の結果、終了させるか否か。
	 */
	abstract protected function validate( mixed &$data ): bool;

	/**
	 * ガントレットの結果によって分岐する場合は、エラーメッセージを追記しない。
	 */
	public function isBranch(): bool
	{
		return
			$this->chainedIfValid instanceof WGG &&
			$this->chainedIfInvalid instanceof WGG;
	}

	/**
	 * @param mixed $data ガントレット対象データ
	 *
	 * @return WGG ガントレットインスタンス
	 */
	public function check( mixed &$data ): self
	{
		$currentGauntlet = $this;
		while ( $currentGauntlet instanceof WGG )
		{
			$result = $currentGauntlet->unsetError()->validate( $data );
			if ( $currentGauntlet !== $this )
			{
				$this->errors = array_merge( $this->errors, $currentGauntlet->listError() );
			}

			if ( $currentGauntlet->isBranch() )
			{
				$currentGauntlet = $result ? $currentGauntlet->chainedIfValid : $currentGauntlet->chainedIfInvalid;
			}
			else
			{
				if ( $result === false && ! $currentGauntlet->isFilter() )
				{
					break;
				}
				$currentGauntlet = $currentGauntlet->chainedIfValid;
			}
		}

		return $this;
	}
}
