<?php
/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/local-common.php';

require_once __DIR__ . '/../../../framework/gauntlet/WGG.php';
require_once __DIR__ . '/../../../framework/gauntlet/WGGFilterTrim.php';
require_once __DIR__ . '/../../../framework/gauntlet/WGGFilterAnk.php';
require_once __DIR__ . '/../../../framework/gauntlet/WGGString.php';
require_once __DIR__ . '/../../../framework/gauntlet/WGGReg.php';
require_once __DIR__ . '/../../../framework/gauntlet/WGGEmpty.php';
require_once __DIR__ . '/../../../framework/gauntlet/WGGThru.php';

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

	static function sample_telephone_gauntlet( $allow_empty = true )
	{
		$main = WGGFilterTrim::_( true )->add( WGGFilterAnk::_()->add( WGGString::_( 1, 100 )
																				->add( WGGReg::_( '/^[0-9]+$/' ) ) ) );
		if ( $allow_empty )
		{
			return WGGEmpty::_()->add( WGGThru::_(), $main );
		}
		return $main;
	}

	public function test_wgg_chain_sample_test()
	{
		$tel = '08012345678';
		$this->assertFalse(self::sample_telephone_gauntlet(true)->check($tel)->hasError());
		$this->assertSame('08012345678', $tel);

		$tel = '';
		$this->assertFalse(self::sample_telephone_gauntlet(true)->check($tel)->hasError());
		$this->assertSame('', $tel);

		$tel = '08012345678';
		$this->assertFalse(self::sample_telephone_gauntlet(false)->check($tel)->hasError());
		$this->assertSame('08012345678', $tel);

		$tel = '';
		$this->assertTrue(self::sample_telephone_gauntlet(false)->check($tel)->hasError());
		$this->assertSame('', $tel);

		$tel = '０９０１２３４５６７８';
		$this->assertFalse(self::sample_telephone_gauntlet(true)->check($tel)->hasError());
		$this->assertSame('09012345678', $tel);

		$tel = '　０９０１２３４５６７８　';
		$this->assertFalse(self::sample_telephone_gauntlet(false)->check($tel)->hasError());
		$this->assertSame('09012345678', $tel);

		$tel = '　０９０　１２３４　５６７８　';
		$this->assertTrue(self::sample_telephone_gauntlet(false)->check($tel)->hasError());
		$this->assertSame('090 1234 5678', $tel);
	}
}

