<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGV8Params.php';

class WGV8Object
{
	/**
	 * @var WGV8Params $params
	 */
	public WGV8Params $params;

	protected string|null $id;
	protected string|null $key;
	protected bool $enable, $lock, $focus;
	protected mixed $extra;
	protected WGG|null $gauntlet;

	public WGFController $controller;
	public WGFSession $session;

	/**
	 * WGV8Object constructor.
	 */
	public function __construct()
	{
		$this->params   = new WGV8Params();
		$this->enable   = true;
		$this->lock     = false;
		$this->gauntlet = null;
		$this->id       = $this->newId();
		$this->focus    = false;
		$this->extra    = new stdClass();
		$this->key      = null;
	}

	/**
	 * Generate new identifier.
	 * @return string
	 */
	public function newId(): string
	{
		if ( ! isset( $_SESSION['_sOBJSEQ'] ) || ! is_int( $_SESSION['_sOBJSEQ'] ) )
		{
			$_SESSION['_sOBJSEQ'] = 1000;
		}
		$seq = ++ $_SESSION["_sOBJSEQ"];

		return sprintf( "wg-view-%d", wg_m10w31( $seq ) );
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function setId( $id ): self
	{
		$this->id = $id;

		return $this;
	}

	public function initSession( WGFSession $session ): self
	{
		$this->session = $session;

		return $this;
	}

	public function initController( WGFController $controller ): self
	{
		$this->controller = $controller;

		return $this;
	}

	public function initFirst(): bool
	{
		return true;
	}

	public function init(): bool
	{
		return true;
	}

	/**
	 * @return WGV8Params
	 * @noinspection PhpUnused
	 */
	public function getParams(): WGV8Params
	{
		return $this->params;
	}

	public function getKey(): string
	{
		return $this->key;
	}

	public function setKey( string $key ): self
	{
		$this->key = $key;

		return $this;
	}

	public function getName(): string
	{
		return $this->getKey();
	}

	public function setName( string $key ): self
	{
		return $this->setKey( $key );
	}

	public function getValue(): mixed
	{
		return $this->session->get( $this->key );
	}

	public function setValue( mixed $v ): self
	{
		$this->session->set( $this->key, $v );

		return $this;
	}

	public function issetValue(): bool
	{
		return $this->session->isExists( $this->key );
	}

	public function clear(): self
	{
		$this->setValue( null );

		return $this;
	}

	public function unsetValue(): self
	{
		$this->setValue( null );

		return $this;
	}

	public function setLocalValue( string $key, mixed $v ): self
	{
		$this->session->set( "{$this->key}/{$key}", $v );

		return $this;
	}

	public function getLocalValue( string $key ): mixed
	{
		return $this->session->get( "{$this->key}/{$key}" );
	}

	public function issetLocalValue( string $key ): bool
	{
		return $this->session->isExists( "{$this->key}/{$key}" );
	}

	public function emptyLocalValue( string $key ): bool
	{
		return $this->session->isEmpty( "{$this->key}/{$key}" );
	}

	public function unsetLocalValue( $key ): self
	{
		$this->session->delete( "{$this->key}/{$key}" );

		return $this;
	}

	public function getError(): mixed
	{
		return $this->session->get( "{$this->key}#error" );
	}

	public function setError( mixed $v ): self
	{
		$this->session->set( "{$this->key}#error", $v );

		return $this;
	}

	public function unsetError(): self
	{
		$this->session->delete( "{$this->key}#error" );

		return $this;
	}

	public function isError(): bool
	{
		return ! $this->session->isEmpty( "{$this->key}#error" );
	}

	public function getEnable(): bool
	{
		return $this->enable;
	}

	public function isEnable(): bool
	{
		return $this->getEnable();
	}

	public function setEnable( bool $enableFlag ): self
	{
		$this->enable = $enableFlag;

		return $this;
	}

	public function enable(): self
	{
		$this->setEnable( true );

		return $this;
	}

	public function disable(): self
	{
		$this->setEnable( false );

		return $this;
	}

	public function isSubmit(): bool
	{
		return false;
	}

	public function isShowOnly(): bool
	{
		return false;
	}

	public function formHtml(): string
	{
		return "";
	}

	public function showHtml(): string
	{
		return "";
	}

	public function postCopy(): self
	{
		return $this;
	}

	public function setLock( $lockFlag ): self
	{
		$this->lock = $lockFlag;

		return $this;
	}

	public function lock(): self
	{
		$this->setLock( true );

		return $this;
	}

	public function unlock(): self
	{
		$this->setLock( false );

		return $this;
	}

	public function isLock(): bool
	{
		return $this->lock;
	}

	/**
	 * @param WGG|null $gauntlet
	 *
	 * @return $this
	 */
	public function execGauntlet( WGG|null $gauntlet ): self
	{
		$this->unsetError();
		if ( is_null( $gauntlet ) )
		{
			return $this;
		}

		$v = $this->getValue();
		$gauntlet->check( $v );
		$this->setValue( $v );

		if ( $gauntlet->hasError() )
		{
			$this->setError( $gauntlet->getError() );
		}

		return $this;
	}

	public function filterGauntlet(): self
	{
		$this->execGauntlet( $this->gauntlet );

		return $this;
	}

	public function check(): self
	{
		$this->filterGauntlet();

		return $this;
	}

	public function setGauntlet( WGG|null $g ): self
	{
		$this->gauntlet = $g;

		return $this;
	}

	public function getGauntlet(): WGG
	{
		return $this->gauntlet;
	}

	public function clearGauntlet(): self
	{
		$this->gauntlet = null;

		return $this;
	}

	/**
	 * @param WGFController $c
	 *
	 * @return WGV8Object
	 */
	public function controller( WGFController $c ): self
	{
		return $this;
	}

	public function getExtra(): mixed
	{
		return $this->extra;
	}

	/**
	 * Return publish values for htmltemplate.
	 * {@key:{id,name,value...}}
	 * @return string[]
	 */
	public function publish(): array
	{
		return
			[
				'id'       => $this->getId(),
				'name'     => $this->getKey(),
				'value'    => htmlspecialchars( $this->getValue(), ENT_QUOTES | ENT_HTML5 ),
				'error'    => htmlspecialchars( $this->getError(), ENT_QUOTES | ENT_HTML5 ),
				'rawValue' => $this->getValue(),
				'rawError' => $this->getValue(),
				'params'   => $this->params->toString()
			];
	}
}
