<?php /** @noinspection DuplicatedCode */

/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/local-common.php';

class FrameworkModelWGMModelConditionTest extends TestCase
{
	public function test_model_join()
	{
		_E( <<<SQL
DROP TABLE IF EXISTS test_cond_a;
CREATE TABLE test_cond_a
(
	id int4 not null primary key,
	title varchar not null,
	enabled boolean not null
);
INSERT INTO test_cond_a VALUES(10,'a1',true);
INSERT INTO test_cond_a VALUES(20,'a2',false);

DROP TABLE IF EXISTS test_cond_b;
CREATE TABLE test_cond_b
(
	id int4 not null primary key,
	a_id int4 not null,
	body text,
	enabled boolean not null
);

INSERT INTO test_cond_b VALUES(101,10,'A',true);
INSERT INTO test_cond_b VALUES(102,10,'E',true);
INSERT INTO test_cond_b VALUES(201,20,'D',true);
INSERT INTO test_cond_b VALUES(202,20,'F',false);
INSERT INTO test_cond_b VALUES(301,30,'C',true);
INSERT INTO test_cond_b VALUES(303,30,'B',false);

DROP TABLE IF EXISTS test_cond_c;
CREATE TABLE test_cond_c
(
	id int4 not null primary key,
	a_id int4 not null,
	b_id int4 not null,
	note text,
	enabled boolean not null
);

INSERT INTO test_cond_c VALUES(1001,20,202,'c',true);

SQL
		);

		// Test for condition
		$a = ( new WGMModel( 'test_cond_a' ) )->orderby( 'id' );
		$a->addCondition( "{title} LIKE '%1'" )->select();
		$this->assertSame(
			[
				[ 'id' => 10, 'title' => 'a1', 'enabled' => true, ],
			], $a->avars
		);


		// Test for addCondition / Remove Condition
		$a      = ( new WGMModel( 'test_cond_a' ) )->orderby( 'id' );
		$condId = $a->addConditionWithId( "{title} LIKE '%1'" );
		$a->delCondition( $condId )->select();
		$this->assertSame(
			[
				[ 'id' => 10, 'title' => 'a1', 'enabled' => true, ],
				[ 'id' => 20, 'title' => 'a2', 'enabled' => false, ],
			], $a->avars
		);

		// Test for addCondition / Clear condition
		$a = ( new WGMModel( 'test_cond_a' ) )->orderby( 'id' );
		$a->addCondition( "{title} LIKE '%1'" )->clearConditions()->select();
		$this->assertSame(
			[
				[ 'id' => 10, 'title' => 'a1', 'enabled' => true, ],
				[ 'id' => 20, 'title' => 'a2', 'enabled' => false, ],
			], $a->avars
		);

		// Test for addCondition / format
		$a = ( new WGMModel( 'test_cond_a' ) )->orderby( 'id' );
		$a->addCondition( "{title} LIKE %s", $a->dbms->S( '%2' ) )->select();
		$this->assertSame(
			[
				[ 'id' => 20, 'title' => 'a2', 'enabled' => false, ],
			], $a->avars
		);

		// Test for multiple condition
		$a = ( new WGMModel( 'test_cond_a' ) )->orderby( 'id' );
		$a->addCondition( "{title} <> 'a1'" );
		$a->addCondition( "{enabled} <> TRUE" );
		$a->select();
		$this->assertSame(
			[
				[ 'id' => 20, 'title' => 'a2', 'enabled' => false, ],
			], $a->avars
		);

		// Test for multiple condition (and test)
		$a = ( new WGMModel( 'test_cond_a' ) )->orderby( 'id' );
		$a->addCondition( "{title} = 'a1' OR {title} = 'a2'" );
		$a->addCondition( "{enabled} = FALSE" );
		$a->select();
		$this->assertSame(
			[
				[ 'id' => 20, 'title' => 'a2', 'enabled' => false, ],
			], $a->avars
		);

		// Test for multiple join and condition
		$a = ( new WGMModel( 'test_cond_a' ) )->orderby( 'id' );
		$b = ( new WGMModel( 'test_cond_b' ) )->orderby( 'id' );
		$c = ( new WGMModel( 'test_cond_c' ) )->orderby( 'id' );
		$a->inner( $b, [ 'id' => 'a_id' ] );
		$b->left( $c, [ 'id' => 'b_id', 'a_id' => 'a_id' ] );
		$a->select();

		$this->assertSame(
			[
				[
					'test_cond_a' => [ 'id' => 10, 'title' => 'a1', 'enabled' => true, ],
					'test_cond_b' => [ 'id' => 101, 'a_id' => 10, 'body' => 'A', 'enabled' => true, ],
					'test_cond_c' => [ 'id' => null, 'a_id' => null, 'b_id' => null, 'note' => null, 'enabled' => null, ],
				],
				[
					'test_cond_a' => [ 'id' => 10, 'title' => 'a1', 'enabled' => true, ],
					'test_cond_b' => [ 'id' => 102, 'a_id' => 10, 'body' => 'E', 'enabled' => true, ],
					'test_cond_c' => [ 'id' => null, 'a_id' => null, 'b_id' => null, 'note' => null, 'enabled' => null, ],
				],
				[
					'test_cond_a' => [ 'id' => 20, 'title' => 'a2', 'enabled' => false, ],
					'test_cond_b' => [ 'id' => 201, 'a_id' => 20, 'body' => 'D', 'enabled' => true, ],
					'test_cond_c' => [ 'id' => null, 'a_id' => null, 'b_id' => null, 'note' => null, 'enabled' => null, ],
				],
				[
					'test_cond_a' => [ 'id' => 20, 'title' => 'a2', 'enabled' => false, ],
					'test_cond_b' => [ 'id' => 202, 'a_id' => 20, 'body' => 'F', 'enabled' => false, ],
					'test_cond_c' => [ 'id' => 1001, 'a_id' => 20, 'b_id' => 202, 'note' => 'c', 'enabled' => true, ],
				],
			]
			, $a->getJoinedAvars()
		);

		$c->addCondition('{enabled} = true');
		$a->select();
		$this->assertSame(
			[
				[
					'test_cond_a' => [ 'id' => 20, 'title' => 'a2', 'enabled' => false, ],
					'test_cond_b' => [ 'id' => 202, 'a_id' => 20, 'body' => 'F', 'enabled' => false, ],
					'test_cond_c' => [ 'id' => 1001, 'a_id' => 20, 'b_id' => 202, 'note' => 'c', 'enabled' => true, ],
				],
			]
			, $a->getJoinedAvars()
		);

		$c->clearConditions();
		$b->addCondition('{id} > 200');
		$c->addCondition('{enabled} IS NULL');
		$a->select();
		$this->assertSame(
			[
				[
					'test_cond_a' => [ 'id' => 20, 'title' => 'a2', 'enabled' => false, ],
					'test_cond_b' => [ 'id' => 201, 'a_id' => 20, 'body' => 'D', 'enabled' => true, ],
					'test_cond_c' => [ 'id' => null, 'a_id' => null, 'b_id' => null, 'note' => null, 'enabled' => null, ],
				],
			]
			, $a->getJoinedAvars()
		);






		_E( <<<SQL
DROP TABLE IF EXISTS test_cond_a;
DROP TABLE IF EXISTS test_cond_b;
DROP TABLE IF EXISTS test_cond_c;
SQL
		);
	}
}
