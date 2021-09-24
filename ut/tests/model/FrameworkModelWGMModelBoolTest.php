<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/local-common.php';

class FrameworkModelWGMModelBoolTest extends TestCase
{
	public function test_model_bool()
	{
		_E( <<<SQL
DROP TABLE IF EXISTS test_bool;
CREATE TABLE test_bool(
    id int4 not null primary key ,
    v0 bool not null default false ,
    v1 bool
);
INSERT INTO test_bool VALUES(0,false,null);
SQL
		);

		$m = new WGMModel( "test_bool" );

		// SELECT
		$k = [ 'id' => 0 ];
		$o = [ 'v0' => false, 'v1' => null ];
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
		$o = [ 'v0' => false, 'v1' => null ];
		$m->setVars( $k + $i )->update( 'id' );
		$this->assertEquals( 1, $m->getVars( $k ) );
		$this->assertSame( $k + $o, $m->vars );

		// UPDATE (NULL->NON-NULL, NON-NULL->NON-NULL)
		$k = [ 'id' => 2 ];
		$i = [ 'v0' => true, 'v1' => true ];
		$o = [ 'v0' => true, 'v1' => true ];
		$m->setVars( $k + $i )->update( 'id' );
		$this->assertEquals( 1, $m->getVars( $k ) );
		$this->assertSame( $k + $o, $m->vars );

		// UPDATE (NON-NULL->NULL, NON-NULL->NULL)
		$k = [ 'id' => 2 ];
		$i = [ 'v0' => null, 'v1' => null ];
		$o = [ 'v0' => false, 'v1' => null ];
		$m->setVars( $k + $i )->update( 'id' );
		$this->assertEquals( 1, $m->getVars( $k ) );
		$this->assertSame( $k + $o, $m->vars );

		// UPDATE(PARTIAL)
		$k = [ 'id' => 10 ];
		$i = [ 'v0' => false, 'v1' => false ];
		$m->setVars( $k + $i )->update( 'id' );

		$k = [ 'id' => 10 ];
		$i = [ 'v0' => true ];
		$o = [ 'v0' => true, 'v1' => false ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		$k = [ 'id' => 10 ];
		$i = [ 'v1' => true ];
		$o = [ 'v0' => true, 'v1' => true ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		$k = [ 'id' => 10 ];
		$i = [];
		$o = [ 'v0' => true, 'v1' => true ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		// UPDATE(PARTIAL/NULL)
		$k = [ 'id' => 10 ];
		$i = [ 'v0' => null ];
		$o = [ 'v0' => false, 'v1' => true ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		$k = [ 'id' => 10 ];
		$i = [ 'v1' => null ];
		$o = [ 'v0' => false, 'v1' => null ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		_E( <<<SQL
DROP TABLE IF EXISTS test_bool;
SQL
		);
	}
}
