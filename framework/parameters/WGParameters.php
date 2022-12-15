<?php
/**
 * waggo8
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGPara.php';

class WGParameters
{
	public const METHOD_GET = 'get';
	public const METHOD_POST = 'post';

	protected array $__errors = [];

	public function __construct()
	{
		$this->initByDefault();
	}

	protected function forEachReflection( callable $callback, array $tags = [] ): static
	{
		$dontNarrowingDown = count( $tags ) === 0;

		$reflection = new ReflectionObject( $this );
		foreach ( $reflection->getProperties() as $props )
		{
			$isIgnoreInitByDefault    =
				count( $props->getAttributes( WGParaIgnoreInitByDefault::class, ReflectionAttribute::IS_INSTANCEOF ) ) > 0;
			$isIgnoreIfSourceNotExist =
				count( $props->getAttributes( WGParaIgnoreIfSourceNotExist::class, ReflectionAttribute::IS_INSTANCEOF ) ) > 0;

			foreach ( $props->getAttributes( WGPara::class, ReflectionAttribute::IS_INSTANCEOF ) as $attr )
			{
				/**
				 * @var WGPara $instance
				 */
				$instance = $attr->newInstance();
				$instance->setIgnoreInitByDefault( $isIgnoreInitByDefault );
				$instance->setIgnoreIfSourceNotExist( $isIgnoreIfSourceNotExist );

				/**
				 * Narrowing down by tags
				 */
				if ( $dontNarrowingDown || count( array_intersect( $instance->tags, $tags ) ) > 0 )
				{
					$callback( $props, $instance );
				}
			}
		}

		return $this;
	}

	protected function compare( WGParameters $another, string $operator = '==' ): bool
	{
		$isEquals = true;
		$this->forEachReflection( function ( $props, $instance ) use ( $operator, $another, &$isEquals ) {
			/**
			 * @var ReflectionProperty $props
			 */
			switch ( $operator )
			{
				case '==':
					if ( ! ( $props->getValue( $this ) == $props->getValue( $another ) ) )
					{
						$isEquals = false;
					}
					break;
				case '===':
					if ( ! ( $props->getValue( $this ) === $props->getValue( $another ) ) )
					{
						$isEquals = false;
					}
					break;
			}
		} );

		return $isEquals;
	}

	public function isSame( WGParameters $another ): bool
	{
		return $this->compare( $another, '==' );
	}

	public function isEquals( WGParameters $another ): bool
	{
		return $this->compare( $another, '===' );
	}

	public function initByDefault( array $tags = [] ): static
	{
		return $this->forEachReflection( function ( $props, $instance ) {
			/**
			 * @var WGPara $instance
			 */
			if ( ! $instance->isIgnoreInitByDefault )
			{
				$props->setValue( $this, $instance->default );
			}
		}, $tags );
	}

	public function by( string $method, array $tags = [] ): static
	{
		$this->__errors = [];

		return $this->forEachReflection( function ( $props, $instance ) use ( $method ) {

			/**
			 * @var WGPara $instance
			 */
			if ( $instance->isExists( $method, $props ) || ! $instance->isIgnoreIfSourceNotExist )
			{
				/**
				 * Apply input filter before gauntlet chain.
				 */
				$value = $instance->applyInputFilterBeforeGauntlet( $instance->input( $method, $props ) );

				/**
				 * Auto validation
				 */
				if ( $instance->getGauntlet() )
				{
					if ( $instance->getGauntlet()->check( $value )->hasError() )
					{
						$this->__errors[ $props->getName() ] = $instance->getGauntlet()->getError();

						$value = $instance->default;
					}
				}

				/**
				 * Apply input filter after gauntlet chain.
				 */
				$value = $instance->applyInputFilterAfterGauntlet( $value );

				$props->setValue( $this, $value );
			}
		}, $tags );
	}

	public function getParams( array $tags = [], bool $isExcludeNull = false ): array
	{
		$result = [];

		$this->forEachReflection( function ( $props, $instance ) use ( &$result, $isExcludeNull ) {
			/**
			 * @var WGPara $instance
			 */
			$value = $props->getValue( $this );

			if ( ! is_null( $value ) || $isExcludeNull === false )
			{
				$result[ $instance->getName( $props ) ] = (string) $instance->applyOutputFilter( $value );
			}
		}, $tags );

		return $result;
	}

	public function getParamString( array $tags = [], bool $isExcludeNull = false ): string
	{
		$param = $this->getParams( $tags, $isExcludeNull );

		return implode( '&', array_map( function ( $v, $k ) {
			return rawurlencode( $k ) . '=' . rawurldecode( $v );

		}, array_values( $param ), array_keys( $param ) ) );
	}

	public function byGET( array $tags = [] ): static
	{
		return $this->by( static::METHOD_GET, $tags );
	}

	public function byPOST( array $tags = [] ): static
	{
		return $this->by( static::METHOD_POST, $tags );
	}

	public function getErrorCount(): int
	{
		return count( $this->__errors );
	}

	public function getErrors(): array
	{
		return $this->__errors;
	}

	public function hasError(): bool
	{
		return $this->getErrorCount() !== 0;
	}

	public function getError( string $propName ): ?string
	{
		return $this->__errors[ $propName ] ?? null;
	}

	public function clearErrors(): static
	{
		$this->__errors = [];

		return $this;
	}

	public function store( WGFController $controller ): static
	{
		$paramKey = '__params:' . get_class( $this );
		$controller->session->set( $paramKey, serialize( $this ) );

		return $this;
	}

	static public function restore(
		WGFController $controller, ?string $method = null, array $tags = []
	): ?static {
		$paramKey = '__params:' . static::class;
		if ( $controller->session->isExists( $paramKey ) )
		{
			/**
			 * @var ?WGParameters
			 */
			$restoreInstance = @unserialize( $controller->session->get( $paramKey ) );
			if ( is_a( $restoreInstance, static::class ) )
			{
				$currentInstance = new static;

				if ( ! is_null( $method ) )
				{
					$currentInstance->by( $method, $tags );

					return $currentInstance->isSame( $restoreInstance ) ? $restoreInstance : null;
				}
				else
				{
					return $restoreInstance;
				}
			}
		}

		return null;
	}
}
