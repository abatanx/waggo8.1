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

class FrameworkModelWGMModelTimestampTest extends TestCase
{
	public function test_model_timestamp()
	{
		_E( <<<SQL
DROP TABLE IF EXISTS test_timestamp;
CREATE TABLE test_timestamp(
    id int4 not null primary key ,
    v0 timestamp not null default '1900-01-01 00:00:00'::timestamp ,
    v1 timestamp 
);
INSERT INTO test_timestamp VALUES(0,'1900-01-01 00:00:00',null);
SQL
		);

		$m = new WGMModel( "test_timestamp" );

		// SELECT
		$k = [ 'id' => 0 ];
		$o = [ 'v0' => '1900-01-01 00:00:00', 'v1' => null ];
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
		$o = [ 'v0' => '0001-01-01 00:00:00', 'v1' => null ];
		$m->setVars( $k + $i )->update( 'id' );
		$this->assertEquals( 1, $m->getVars( $k ) );
		$this->assertSame( $k + $o, $m->vars );

		// UPDATE (NULL->NON-NULL, NON-NULL->NON-NULL)
		$k = [ 'id' => 2 ];
		$i = [ 'v0' => '2000-01-01 12:34:56', 'v1' => '2000-07-01 07:08:09' ];
		$o = [ 'v0' => '2000-01-01 12:34:56', 'v1' => '2000-07-01 07:08:09' ];
		$m->setVars( $k + $i )->update( 'id' );
		$this->assertEquals( 1, $m->getVars( $k ) );
		$this->assertSame( $k + $o, $m->vars );

		// UPDATE (NON-NULL->NULL, NON-NULL->NULL)
		$k = [ 'id' => 2 ];
		$i = [ 'v0' => null, 'v1' => null ];
		$o = [ 'v0' => '0001-01-01 00:00:00', 'v1' => null ];
		$m->setVars( $k + $i )->update( 'id' );
		$this->assertEquals( 1, $m->getVars( $k ) );
		$this->assertSame( $k + $o, $m->vars );

		// UPDATE(PARTIAL)
		$k = [ 'id' => 10 ];
		$i = [ 'v0' => '2000-01-01 12:34:56', 'v1' => '2000-07-01 07:08:09' ];
		$m->setVars( $k + $i )->update( 'id' );

		$k = [ 'id' => 10 ];
		$i = [ 'v0' => '2010-01-01 10:11:12' ];
		$o = [ 'v0' => '2010-01-01 10:11:12', 'v1' => '2000-07-01 07:08:09' ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		$k = [ 'id' => 10 ];
		$i = [ 'v1' => '2010-07-01 23:24:25' ];
		$o = [ 'v0' => '2010-01-01 10:11:12', 'v1' => '2010-07-01 23:24:25' ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		$k = [ 'id' => 10 ];
		$i = [];
		$o = [ 'v0' => '2010-01-01 10:11:12', 'v1' => '2010-07-01 23:24:25' ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		// UPDATE(PARTIAL/NULL)
		$k = [ 'id' => 10 ];
		$i = [ 'v0' => null ];
		$o = [ 'v0' => '0001-01-01 00:00:00', 'v1' => '2010-07-01 23:24:25' ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		$k = [ 'id' => 10 ];
		$i = [ 'v1' => null ];
		$o = [ 'v0' => '0001-01-01 00:00:00', 'v1' => null ];
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
			$this->assertNotFalse( strptime( $m->vars['v0'], '%Y-%m-%d %H:%M:%S' ) );
		}
		foreach ( [ 'infinity', '-infinity' ] as $dateString )
		{
			$k = [ 'id' => $id ++ ];
			$i = [ 'v0' => $dateString ];
			$m->setVars( $k + $i )->update( 'id' )->getVars( $k );
			$this->assertEquals( $dateString, $m->vars['v0'], '%Y-%m-%d %H:%M:%S' );
		}

		_E( <<<SQL
DROP TABLE IF EXISTS test_timestamp;
SQL
		);
	}
}
