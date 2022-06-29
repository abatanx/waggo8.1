<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGG.php';
require_once __DIR__ . '/../../api/core/exception.php';

class WGGChainState
{
	public bool $isValid;
	public string $errorMessage;

	public function __construct( bool $isValid = false, string $errorMessage = '' )
	{
		$this->isValid      = $isValid;
		$this->errorMessage = $errorMessage;
	}

	static public function _( bool $isValid = false, string $errorMessage = '' )
	{
		return new static( $isValid, $errorMessage );
	}
}

abstract class WGG
{
	/**
	 * @var WGGChainState[] 評価結果
	 */
	private array $chainStatuses;

	/**
	 * @var ?WGG 後部連結するガントレット
	 */
	private ?WGG $chainedIfValid;
	private ?WGG $chainedIfInvalid;

	public function __construct()
	{
		$this->chainStatuses    = [];
		$this->chainedIfValid   = $this->ifValid();
		$this->chainedIfInvalid = $this->ifInvalid();
	}

	/**
	 * 後部連結するガントレットデフォルト
	 * @return WGG|null
	 */
	protected function ifValid(): ?WGG
	{
		return null;
	}

	/**
	 * 後部連結するガントレットデフォルト
	 * @return WGG|null
	 */
	protected function ifInvalid(): ?WGG
	{
		return null;
	}

	/**
	 * 判定用文字表現に変換する。スカラ型のうち、 bool 以外は string にキャストする。
	 * それ以外の場合は、スクリプトの実行を中止する。
	 *
	 * @param mixed $value 判定用文字表現に変換する変数
	 *
	 * @return string 変換後
	 * @noinspection PhpInconsistentReturnPointsInspection
	 */
	public function toValidationString( mixed $value ): string
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
				$type = gettype( $value );
				throw new WGRuntimeException( "'$type' is not allowed." );
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

	public function addChainState( WGGChainState $msg ): self
	{
		$this->chainStatuses[] = $msg;

		return $this;
	}

	public function listError(): array
	{
		return $this->chainStatuses;
	}

	public function getError(): string
	{
		if ( $this->hasError() )
		{
			$errorMessages = array_map( function ( $e ) {
				return $e->errorMessage;
			},
				array_filter( $this->chainStatuses,
					function ( $e ) {
						return ! $e->isValid;
					} )
			);

			return implode( "または、", $errorMessages );
		}

		return '';
	}

	public function unsetError(): self
	{
		$this->chainStatuses = [];

		return $this;
	}

	public function hasError(): bool
	{
		if ( count( $this->chainStatuses ) > 0 )
		{
			$lastState = $this->chainStatuses[ count( $this->chainStatuses ) - 1 ];

			return ! $lastState->isValid;
		}
		else
		{
			return false;
		}
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
				$this->chainStatuses = array_merge( $this->chainStatuses, $currentGauntlet->listError() );
			}

			if ( ! $currentGauntlet->isFilter() )
			{
				$currentGauntlet = $result ?
					$currentGauntlet->chainedIfValid :
					$currentGauntlet->chainedIfInvalid;
			}
			else
			{
				$currentGauntlet =
					$currentGauntlet->chainedIfValid;
			}
		}

		return $this;
	}
}
