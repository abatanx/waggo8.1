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

class FrameworkModelWGMModelDateTest extends TestCase
{
	public function test_model_date()
	{
		_E( <<<SQL
DROP TABLE IF EXISTS test_date;
CREATE TABLE test_date(
    id int4 not null primary key ,
    v0 date not null default '1900-01-01'::date ,
    v1 date 
);
INSERT INTO test_date VALUES(0,'1900-01-01',null);
SQL
		);

		$m = new WGMModel( "test_date" );

		// SELECT
		$k = [ 'id' => 0 ];
		$o = [ 'v0' => '1900-01-01', 'v1' => null ];
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
		$o = [ 'v0' => '0001-01-01', 'v1' => null ];
		$m->setVars( $k + $i )->update( 'id' );
		$this->assertEquals( 1, $m->getVars( $k ) );
		$this->assertSame( $k + $o, $m->vars );

		// UPDATE (NULL->NON-NULL, NON-NULL->NON-NULL)
		$k = [ 'id' => 2 ];
		$i = [ 'v0' => '2000-01-01', 'v1' => '2000-07-01' ];
		$o = [ 'v0' => '2000-01-01', 'v1' => '2000-07-01' ];
		$m->setVars( $k + $i )->update( 'id' );
		$this->assertEquals( 1, $m->getVars( $k ) );
		$this->assertSame( $k + $o, $m->vars );

		// UPDATE (NON-NULL->NULL, NON-NULL->NULL)
		$k = [ 'id' => 2 ];
		$i = [ 'v0' => null, 'v1' => null ];
		$o = [ 'v0' => '0001-01-01', 'v1' => null ];
		$m->setVars( $k + $i )->update( 'id' );
		$this->assertEquals( 1, $m->getVars( $k ) );
		$this->assertSame( $k + $o, $m->vars );

		// UPDATE(PARTIAL)
		$k = [ 'id' => 10 ];
		$i = [ 'v0' => '2000-01-01', 'v1' => '2000-07-01' ];
		$m->setVars( $k + $i )->update( 'id' );

		$k = [ 'id' => 10 ];
		$i = [ 'v0' => '2010-01-01' ];
		$o = [ 'v0' => '2010-01-01', 'v1' => '2000-07-01' ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		$k = [ 'id' => 10 ];
		$i = [ 'v1' => '2010-07-01' ];
		$o = [ 'v0' => '2010-01-01', 'v1' => '2010-07-01' ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		$k = [ 'id' => 10 ];
		$i = [];
		$o = [ 'v0' => '2010-01-01', 'v1' => '2010-07-01' ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		// UPDATE(PARTIAL/NULL)
		$k = [ 'id' => 10 ];
		$i = [ 'v0' => null ];
		$o = [ 'v0' => '0001-01-01', 'v1' => '2010-07-01' ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		$k = [ 'id' => 10 ];
		$i = [ 'v1' => null ];
		$o = [ 'v0' => '0001-01-01', 'v1' => null ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		// DATE
		$id = 20;
		foreach (
			[
				'epoch', 'today', 'tomorrow', 'yesterday',
				'current_date', 'current_timestamp', 'localtimestamp', 'now()'
			] as $dateString
		)
		{
			$k = [ 'id' => $id ++ ];
			$i = [ 'v0' => $dateString ];
			$m->setVars( $k + $i )->update( 'id' )->getVars( $k );
			$this->assertNotFalse( strptime( $m->vars['v0'], '%Y-%m-%d' ) );
		}
		foreach ( [ 'infinity', '-infinity' ] as $dateString )
		{
			$k = [ 'id' => $id ++ ];
			$i = [ 'v0' => $dateString ];
			$m->setVars( $k + $i )->update( 'id' )->getVars( $k );
			$this->assertEquals( $dateString, $m->vars['v0'], '%Y-%m-%d' );
		}

		_E( <<<SQL
DROP TABLE IF EXISTS test_date;
SQL
		);
	}
}
