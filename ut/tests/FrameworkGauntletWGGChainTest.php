<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

if ( ! defined( 'WG_UNITTEST' ) )
{
	define( 'WG_UNITTEST', true );
}

require_once __DIR__ . '/../unittest-config.php';
require_once __DIR__ . '/../../framework/gauntlet/WGG.php';

class WGGTest extends WGG
{
	private string $id;
	private bool $flag;
	private int $data;

	public function __construct( string $id, bool $flag, int $data )
	{
		parent::__construct();
		$this->id = $id;
		$this->flag = $flag;
		$this->data = $data;
	}

	public function makeErrorMessage(): string
	{
		return '(' . $this->data . ')';
	}

	public function validate( mixed &$data ): bool
	{
		$data = $this->id;
		return $this->flag;
	}
}

class FrameworkGauntletWGGChainTest extends TestCase
{
	public function test_wgg_chain_valid()
	{
		$root = new WGGTest('root', true, 0);
		$valid = new WGGTest('1', true, 1);
		$invalid = new WGGTest('2', true, 1);

		$r = '';
		$root->add($valid, $invalid)->check($r);
		$this->assertEquals('1', $r);
	}

	public function test_wgg_chain_valid2()
	{
		$root = new WGGTest('root', true, 0);
		$valid = new WGGTest('1', true, 1);
		$invalid = new WGGTest('2', true, 1);

		$r = '';
		$root->valid($valid)->invalid($invalid)->check($r);
		$this->assertEquals('1', $r);
	}

	public function test_wgg_chain_invalid()
	{
		$root = new WGGTest('root', false, 0);
		$valid = new WGGTest('1', true, 1);
		$invalid = new WGGTest('2', true, 1);

		$r = '';
		$root->add($valid, $invalid)->check($r);
		$this->assertEquals('2', $r);
	}

	public function test_wgg_chain_invalid2()
	{
		$root = new WGGTest('root', false, 0);
		$valid = new WGGTest('1', true, 1);
		$invalid = new WGGTest('2', true, 1);

		$r = '';
		$root->valid($valid)->invalid($invalid)->check($r);
		$this->assertEquals('2', $r);
	}
}
