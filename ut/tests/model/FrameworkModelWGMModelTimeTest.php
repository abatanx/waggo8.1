<?php /** @noinspection DuplicatedCode */

/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/local-common.php';

class FrameworkModelWGMModelTimeTest extends TestCase
{
	public function test_model_time()
	{
		_E( <<<SQL
DROP TABLE IF EXISTS test_time;
CREATE TABLE test_time(
    id int4 NOT NULL PRIMARY KEY ,
    v0 time NOT NULL DEFAULT '00:00:00'::time ,
    v1 time 
);
INSERT INTO test_time VALUES(0,'00:00:00',NULL);
SQL
		);

		$m = new WGMModel( "test_time" );

		// SELECT
		$k = [ 'id' => 0 ];
		$o = [ 'v0' => '00:00:00', 'v1' => null ];
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
		$o = [ 'v0' => '00:00:00', 'v1' => null ];
		$m->setVars( $k + $i )->update( 'id' );
		$this->assertEquals( 1, $m->getVars( $k ) );
		$this->assertSame( $k + $o, $m->vars );

		// UPDATE (NULL->NON-NULL, NON-NULL->NON-NULL)
		$k = [ 'id' => 2 ];
		$i = [ 'v0' => '12:34:56', 'v1' => '07:08:09' ];
		$o = [ 'v0' => '12:34:56', 'v1' => '07:08:09' ];
		$m->setVars( $k + $i )->update( 'id' );
		$this->assertEquals( 1, $m->getVars( $k ) );
		$this->assertSame( $k + $o, $m->vars );

		// UPDATE (NON-NULL->NULL, NON-NULL->NULL)
		$k = [ 'id' => 2 ];
		$i = [ 'v0' => null, 'v1' => null ];
		$o = [ 'v0' => '00:00:00', 'v1' => null ];
		$m->setVars( $k + $i )->update( 'id' );
		$this->assertEquals( 1, $m->getVars( $k ) );
		$this->assertSame( $k + $o, $m->vars );

		// UPDATE(PARTIAL)
		$k = [ 'id' => 10 ];
		$i = [ 'v0' => '12:34:56', 'v1' => '07:08:09' ];
		$m->setVars( $k + $i )->update( 'id' );

		$k = [ 'id' => 10 ];
		$i = [ 'v0' => '10:11:12' ];
		$o = [ 'v0' => '10:11:12', 'v1' => '07:08:09' ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		$k = [ 'id' => 10 ];
		$i = [ 'v1' => '23:24:25' ];
		$o = [ 'v0' => '10:11:12', 'v1' => '23:24:25' ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		$k = [ 'id' => 10 ];
		$i = [];
		$o = [ 'v0' => '10:11:12', 'v1' => '23:24:25' ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		// UPDATE(PARTIAL/NULL)
		$k = [ 'id' => 10 ];
		$i = [ 'v0' => null ];
		$o = [ 'v0' => '00:00:00', 'v1' => '23:24:25' ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		$k = [ 'id' => 10 ];
		$i = [ 'v1' => null ];
		$o = [ 'v0' => '00:00:00', 'v1' => null ];
		$m->setVars( $k + $i )->update( 'id' );
		$m->getVars( $k );
		$this->assertSame( $k + $o, $m->vars );

		// TIME
		$id = 20;
		foreach (
			[
				'current_time', 'current_timestamp', 'localtime', 'localtimestamp', 'now()'
			] as $dateString
		)
		{
			$k = [ 'id' => $id ++ ];
			$i = [ 'v0' => $dateString ];
			$m->setVars( $k + $i )->update( 'id' )->getVars( $k );
			echo $m->vars['v0'];
			$this->assertNotFalse( date_parse_from_format( '%H:%M:%S', $m->vars['v0'] ) );
		}

		_E( <<<SQL
DROP TABLE IF EXISTS test_time;
SQL
		);
	}
}
