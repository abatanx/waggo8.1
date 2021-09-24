<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/local-common.php';

class FrameworkModelWGMModelIntTest extends TestCase
{
	public function test_model_int()
	{
		_E( <<<SQL
DROP TABLE IF EXISTS test_int;
CREATE TABLE test_int(
    id int4 not null primary key ,
    v0 int4 not null default 0 ,
    v1 int4
);
INSERT INTO test_int VALUES(0,0,null);
SQL
		);

		$m = new WGMModel( "test_int" );

		// SELECT
		$k = [ 'id' => 0 ];
		$o = [ 'v0' => 0, 'v1' => null ];
		$this->assertEquals( 1, $m->getVars( $k ) );
		$this->assertSame( $k + $o, $m->vars );

		// SELECT (NO-RECORD)
		$k = [ 'id' => 100 ];
		$o = [ 'v0' => null, 'v1' => null ];
		$this->assertEquals( 0, $m->getVars( $k ) );
		$this->assertSame( [ 'id' => null ] + $o, $m->vars );

		// INSERT (NULL)
		$k = [ 'id' => 2 ];
		$i = [ 'v0' => null, 'v1' => null ];
		$o = [ 'v0' => 0, 'v1' => null ];
		$m->setVars( $k + $i )->update( 'id' );
		$this->assertEquals( 1, $m->getVars( $k ) );
		$this->assertSame( $k + $o, $m->vars );

		// UPDATE (NULL->NON-NULL, NON-NULL->NON-NULL)
		$k = [ 'id' => 2 ];
		$i = [ 'v0' => 20, 'v1' => 30 ];
		$o = [ 'v0' => 20, 'v1' => 30 ];
		$m->setVars( $k + $i )->update( 'id' );
		$this->assertEquals( 1, $m->getVars( $k ) );
		$this->assertSame( $k + $o, $m->vars );

		// UPDATE (NON-NULL->NULL, NON-NULL->NULL)
		$k = [ 'id' => 2 ];
		$i = [ 'v0' => null, 'v1' => null ];
		$o = [ 'v0' => 0, 'v1' => null ];
		$m->setVars( $k + $i )->update( 'id' );
		$this->assertEquals( 1, $m->getVars( $k ) );
		$this->assertSame( $k + $o, $m->vars );

		// UPDATE(PARTIAL)
		$k = [ 'id' => 10 ];
		$i = [ 'v0' => 200, 'v1' => 300 ];
		$m->setVars( $k + $i )->update( 'id' );

		$k = [ 'id' => 10 ];
		$i = [ 'v0' => 400 ];
		$o = [ 'v0' => 400, 'v1' => 300 ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		$k = [ 'id' => 10 ];
		$i = [ 'v1' => 500 ];
		$o = [ 'v0' => 400, 'v1' => 500 ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		$k = [ 'id' => 10 ];
		$i = [];
		$o = [ 'v0' => 400, 'v1' => 500 ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		// UPDATE(PARTIAL/NULL)
		$k = [ 'id' => 10 ];
		$i = [ 'v0' => null ];
		$o = [ 'v0' => 0, 'v1' => 500 ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		$k = [ 'id' => 10 ];
		$i = [ 'v1' => null ];
		$o = [ 'v0' => 0, 'v1' => null ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		_E( <<<SQL
DROP TABLE IF EXISTS test_int;
SQL
		);
	}
}
