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

class FrameworkModelWGMModelJoinTest extends TestCase
{
	public function test_model_join()
	{
		_E( <<<SQL
DROP TABLE IF EXISTS test_a;
CREATE TABLE test_a
(
	id int4 not null primary key,
	title varchar not null,
	enabled boolean not null
);
INSERT INTO test_a VALUES(10,'a1',true);
INSERT INTO test_a VALUES(20,'a2',false);

DROP TABLE IF EXISTS test_b;
CREATE TABLE test_b
(
	id int4 not null primary key,
	a_id int4 not null,
	body text,
	enabled boolean not null
);

INSERT INTO test_b VALUES(101,10,'b1-1',true);
INSERT INTO test_b VALUES(102,10,'b1-2',true);
INSERT INTO test_b VALUES(201,20,'b2-1',true);
INSERT INTO test_b VALUES(202,20,'b2-2',true);
INSERT INTO test_b VALUES(301,30,'b3-1',true);
INSERT INTO test_b VALUES(303,30,'b3-2',false);

DROP TABLE IF EXISTS test_c;
CREATE TABLE test_c
(
	id int4 not null primary key,
	a_id int4 not null,
	b_id int4 not null,
	note text,
	enabled boolean not null
);

INSERT INTO test_c VALUES(1001,20,202,'c',true);

SQL
		);

		$a = ( new WGMModel( 'test_a' ) )->orderby( 'id' );
		$b = ( new WGMModel( 'test_b' ) )->orderby( 'id' );
		$a->inner( $b, [ 'id' => 'a_id' ] )->select()->avars;

		$this->assertSame( [
				[ 'id' => 10, 'title' => 'a1', 'enabled' => true, ],
				[ 'id' => 10, 'title' => 'a1', 'enabled' => true, ],
				[ 'id' => 20, 'title' => 'a2', 'enabled' => false, ],
				[ 'id' => 20, 'title' => 'a2', 'enabled' => false, ],
			]
			, $a->avars );


		var_export( $a->avars );

		_E( <<<SQL
DROP TABLE IF EXISTS test_a;
DROP TABLE IF EXISTS test_b;
DROP TABLE IF EXISTS test_c;
SQL
		);
	}
}
