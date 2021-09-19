<?php /** @noinspection DuplicatedCode */

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

require_once __DIR__ . '/../../framework/m/WGMModel.php';
require_once __DIR__ . '/../../framework/m/WGMModelOrder.php';

class FrameworkModelWGMModelOrderTest extends TestCase
{
	public function test_model_order_class()
	{
		_E( <<<SQL
DROP TABLE IF EXISTS test_order_class;
CREATE TABLE test_order_class(
    id int4 not null primary key
);
SQL);
		$m = new WGMModel('test_order_class');
		$m->setAlias('x');

		$o = new WGMModelOrder($m);
		$o->setFormula('id');
		$this->assertEquals('x.id ASC', $o->getFormula());
		$this->assertEquals(WGMModelOrder::ORDER_ASC, $o->getOrder());

		$o = new WGMModelOrder($m);
		$o->setFormula('{id}');
		$this->assertEquals('x.id ASC', $o->getFormula());
		$this->assertEquals(WGMModelOrder::ORDER_ASC, $o->getOrder());

		$o = new WGMModelOrder($m);
		$o->setFormula('id desc');
		$this->assertEquals('x.id DESC', $o->getFormula());
		$this->assertEquals(WGMModelOrder::ORDER_DESC, $o->getOrder());

		$o = new WGMModelOrder($m);
		$o->setFormula('{id} desc');
		$this->assertEquals('x.id DESC', $o->getFormula());
		$this->assertEquals(WGMModelOrder::ORDER_DESC, $o->getOrder());

		$o = new WGMModelOrder($m);
		$o->setFormula('func()');
		$this->assertEquals('func() ASC', $o->getFormula());
		$this->assertEquals(WGMModelOrder::ORDER_ASC, $o->getOrder());

		$o = new WGMModelOrder($m);
		$o->setFormula('func(id)');
		$this->assertEquals('func(id) ASC', $o->getFormula());
		$this->assertEquals(WGMModelOrder::ORDER_ASC, $o->getOrder());

		$o = new WGMModelOrder($m);
		$o->setFormula('func({id})');
		$this->assertEquals('func(x.id) ASC', $o->getFormula());
		$this->assertEquals(WGMModelOrder::ORDER_ASC, $o->getOrder());

		$o = new WGMModelOrder($m);
		$o->setFormula('func() desc');
		$this->assertEquals('func() DESC', $o->getFormula());
		$this->assertEquals(WGMModelOrder::ORDER_DESC, $o->getOrder());

		$o = new WGMModelOrder($m);
		$o->setFormula('func(id) desc');
		$this->assertEquals('func(id) DESC', $o->getFormula());
		$this->assertEquals(WGMModelOrder::ORDER_DESC, $o->getOrder());

		$o = new WGMModelOrder($m);
		$o->setFormula('func({id}) desc');
		$this->assertEquals('func(x.id) DESC', $o->getFormula());
		$this->assertEquals(WGMModelOrder::ORDER_DESC, $o->getOrder());

		_E( <<<SQL
DROP TABLE IF EXISTS test_order_class;
SQL
		);
	}

	public function test_model_order()
	{
		_E( <<<SQL
DROP TABLE IF EXISTS test_order;
CREATE TABLE test_order(
    id int4 not null primary key ,
    v0 text,
    v1 text 
);
INSERT INTO test_order VALUES(0,'',null);
INSERT INTO test_order VALUES(10,'A','D');
INSERT INTO test_order VALUES(20,'A','C');
INSERT INTO test_order VALUES(30,'B','C');
SQL
		);

		$m = new WGMModel( "test_order" );
		$this->assertSame(
			[
				[ 'id' => 0, 'v0' => '', 'v1' => null, ],
				[ 'id' => 10, 'v0' => 'A', 'v1' => 'D', ],
				[ 'id' => 20, 'v0' => 'A', 'v1' => 'C', ],
				[ 'id' => 30, 'v0' => 'B', 'v1' => 'C', ],
			],
			$m->orderby('id')->select()->avars
		);

		$m = new WGMModel( "test_order" );
		$this->assertSame(
			[
				[ 'id' => 30, 'v0' => 'B', 'v1' => 'C', ],
				[ 'id' => 20, 'v0' => 'A', 'v1' => 'C', ],
				[ 'id' => 10, 'v0' => 'A', 'v1' => 'D', ],
				[ 'id' => 0, 'v0' => '', 'v1' => null, ],
			],
			$m->orderby('id desc')->select()->avars
		);

		$m = new WGMModel( "test_order" );
		$this->assertSame(
			[
				[ 'id' => 30, 'v0' => 'B', 'v1' => 'C', ],
				[ 'id' => 10, 'v0' => 'A', 'v1' => 'D', ],
				[ 'id' => 20, 'v0' => 'A', 'v1' => 'C', ],
				[ 'id' => 0, 'v0' => '', 'v1' => null, ],
			],
			$m->orderby('v0 desc','id')->select()->avars
		);

		$m = new WGMModel( "test_order" );
		$this->assertSame(
			[
				[ 'id' => 0, 'v0' => '', 'v1' => null, ],
				[ 'id' => 20, 'v0' => 'A', 'v1' => 'C', ],
				[ 'id' => 10, 'v0' => 'A', 'v1' => 'D', ],
				[ 'id' => 30, 'v0' => 'B', 'v1' => 'C', ],
			],
			$m->orderby("{v0}||COALESCE({v1},'')")->select()->avars
		);

		$m = new WGMModel( "test_order" );
		$this->assertSame(
			[
				[ 'id' => 30, 'v0' => 'B', 'v1' => 'C', ],
				[ 'id' => 10, 'v0' => 'A', 'v1' => 'D', ],
				[ 'id' => 20, 'v0' => 'A', 'v1' => 'C', ],
				[ 'id' => 0, 'v0' => '', 'v1' => null, ],
			],
			$m->orderby("{v0}||COALESCE({v1},'') desc")->select()->avars
		);

		_E( <<<SQL
DROP TABLE IF EXISTS test_order;
SQL
		);
	}
}
