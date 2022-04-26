<?php
/**
 * waggo8
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

#[Attribute]
class WGGuard
{
	public ?string $get = null;
	public ?string $post = null;

	public function __construct( $get = null, $post = null )
	{
		$this->get = $get;
		$this->post = $post;
	}
}

class WGGuardian
{
	protected ?WGFController $controller;

	public function __construct( ?WGFController $controller )
	{
		$this->controller = $controller;
	}

	public function fromGET(): self
	{
		$reflection = new ReflectionObject( $this );
		foreach ( $reflection->getProperties() as $props )
		{
			foreach ( $attrs = $props->getAttributes( WGGuard::class, ReflectionAttribute::IS_INSTANCEOF ) as $attr )
			{
				$instance = $attr->newInstance();
			}
		}

		return $this;
	}
}

