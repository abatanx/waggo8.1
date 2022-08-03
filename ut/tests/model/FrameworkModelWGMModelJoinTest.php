<?php /** @noinspection DuplicatedCode */

/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/local-common.php';

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

INSERT INTO test_b VALUES(101,10,'A',true);
INSERT INTO test_b VALUES(102,10,'E',true);
INSERT INTO test_b VALUES(201,20,'D',true);
INSERT INTO test_b VALUES(202,20,'F',false);
INSERT INTO test_b VALUES(301,30,'C',true);
INSERT INTO test_b VALUES(303,30,'B',false);

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

		// Test for inner join
		$a = ( new WGMModel( 'test_a' ) )->orderby( 'id' );
		$b = ( new WGMModel( 'test_b' ) )->orderby( 'id' );
		$a->inner( $b, [ 'id' => 'a_id' ] )->select();

		$this->assertSame( [
				[ 'id' => 10, 'title' => 'a1', 'enabled' => true, ],
				[ 'id' => 10, 'title' => 'a1', 'enabled' => true, ],
				[ 'id' => 20, 'title' => 'a2', 'enabled' => false, ],
				[ 'id' => 20, 'title' => 'a2', 'enabled' => false, ],
			]
			, $a->avars );

		$this->assertSame( [
				[ 'id' => 101, 'a_id' => 10, 'body' => 'A', 'enabled' => true, ],
				[ 'id' => 102, 'a_id' => 10, 'body' => 'E', 'enabled' => true, ],
				[ 'id' => 201, 'a_id' => 20, 'body' => 'D', 'enabled' => true, ],
				[ 'id' => 202, 'a_id' => 20, 'body' => 'F', 'enabled' => false, ],
			]
			, $b->avars );

		// Test for priority of order (auto priority case)
		$b = ( new WGMModel( 'test_b' ) )->orderby( 'body' );
		$a = ( new WGMModel( 'test_a' ) )->orderby( 'id' );
		$a->inner( $b, [ 'id' => 'a_id' ] )->select();

		$this->assertSame( [
				[ 'id' => 10, 'title' => 'a1', 'enabled' => true, ],
				[ 'id' => 20, 'title' => 'a2', 'enabled' => false, ],
				[ 'id' => 10, 'title' => 'a1', 'enabled' => true, ],
				[ 'id' => 20, 'title' => 'a2', 'enabled' => false, ],
			]
			, $a->avars );

		$this->assertSame( [
				[ 'id' => 101, 'a_id' => 10, 'body' => 'A', 'enabled' => true, ],
				[ 'id' => 201, 'a_id' => 20, 'body' => 'D', 'enabled' => true, ],
				[ 'id' => 102, 'a_id' => 10, 'body' => 'E', 'enabled' => true, ],
				[ 'id' => 202, 'a_id' => 20, 'body' => 'F', 'enabled' => false, ],
			]
			, $b->avars );

		// Test for priority of order (specify priority case)
		$a = ( new WGMModel( 'test_a' ) )->orderby( 'id' );
		$b = ( new WGMModel( 'test_b' ) )->orderby( 'body', 0 );
		$a->inner( $b, [ 'id' => 'a_id' ] )->select();

		$this->assertSame( [
				[ 'id' => 10, 'title' => 'a1', 'enabled' => true, ],
				[ 'id' => 20, 'title' => 'a2', 'enabled' => false, ],
				[ 'id' => 10, 'title' => 'a1', 'enabled' => true, ],
				[ 'id' => 20, 'title' => 'a2', 'enabled' => false, ],
			]
			, $a->avars );

		$this->assertSame( [
				[ 'id' => 101, 'a_id' => 10, 'body' => 'A', 'enabled' => true, ],
				[ 'id' => 201, 'a_id' => 20, 'body' => 'D', 'enabled' => true, ],
				[ 'id' => 102, 'a_id' => 10, 'body' => 'E', 'enabled' => true, ],
				[ 'id' => 202, 'a_id' => 20, 'body' => 'F', 'enabled' => false, ],
			]
			, $b->avars );

		// Test for left join
		$c = ( new WGMModel( 'test_c' ) )->orderby( 'id' );
		$b = ( new WGMModel( 'test_b' ) )->orderby( 'id' );
		$c->left( $b, [ 'b_id' => 'id' ] )->select();

		$this->assertSame( [
				[ 'id' => 1001, 'a_id' => 20, 'b_id' => 202, 'note' => 'c', 'enabled' => true, ]
			]
			, $c->avars );

		$this->assertSame( [
				[ 'id' => 202, 'a_id' => 20, 'body' => 'F', 'enabled' => false, ],
			]
			, $b->avars );

		// Test for right join
		$c = ( new WGMModel( 'test_c' ) )->orderby( 'id' );
		$b = ( new WGMModel( 'test_b' ) )->orderby( 'id' );
		$c->right( $b, [ 'b_id' => 'id' ] )->select();

		$this->assertSame( [
				[ 'id' => 1001, 'a_id' => 20, 'b_id' => 202, 'note' => 'c', 'enabled' => true, ],
				[ 'id' => null, 'a_id' => null, 'b_id' => null, 'note' => null, 'enabled' => null, ],
				[ 'id' => null, 'a_id' => null, 'b_id' => null, 'note' => null, 'enabled' => null, ],
				[ 'id' => null, 'a_id' => null, 'b_id' => null, 'note' => null, 'enabled' => null, ],
				[ 'id' => null, 'a_id' => null, 'b_id' => null, 'note' => null, 'enabled' => null, ],
				[ 'id' => null, 'a_id' => null, 'b_id' => null, 'note' => null, 'enabled' => null, ],
			]
			, $c->avars );

		$this->assertSame( [
				[ 'id' => 202, 'a_id' => 20, 'body' => 'F', 'enabled' => false, ],
				[ 'id' => 101, 'a_id' => 10, 'body' => 'A', 'enabled' => true, ],
				[ 'id' => 102, 'a_id' => 10, 'body' => 'E', 'enabled' => true, ],
				[ 'id' => 201, 'a_id' => 20, 'body' => 'D', 'enabled' => true, ],
				[ 'id' => 301, 'a_id' => 30, 'body' => 'C', 'enabled' => true, ],
				[ 'id' => 303, 'a_id' => 30, 'body' => 'B', 'enabled' => false, ],
			]
			, $b->avars );

		// Test for multiple join
		$a = ( new WGMModel( 'test_a' ) )->orderby( 'id' );
		$b = ( new WGMModel( 'test_b' ) )->orderby( 'id' );
		$c = ( new WGMModel( 'test_c' ) )->orderby( 'id' );
		$a->inner( $b, [ 'id' => 'a_id' ] );
		$b->inner( $c, [ 'id' => 'b_id', 'a_id' => 'a_id' ] );
		$a->select();

		$this->assertSame( [
				[ 'id' => 20, 'title' => 'a2', 'enabled' => false, ]
			]
			, $a->avars );

		$this->assertSame( [
				[ 'id' => 202, 'a_id' => 20, 'body' => 'F', 'enabled' => false, ]
			]
			, $b->avars );

		$this->assertSame( [
				[ 'id' => 1001, 'a_id' => 20, 'b_id' => 202, 'note' => 'c', 'enabled' => true, ],
			]
			, $c->avars );

		$this->assertSame(
			[
				[
					'test_a' => [ 'id' => 20, 'title' => 'a2', 'enabled' => false, ],
					'test_b' => [ 'id' => 202, 'a_id' => 20, 'body' => 'F', 'enabled' => false, ],
					'test_c' => [ 'id' => 1001, 'a_id' => 20, 'b_id' => 202, 'note' => 'c', 'enabled' => true, ],
				]
			], $a->getJoinedAvars()
		);

		// Test for multiple join
		$a = ( new WGMModel( 'test_a' ) )->orderby( 'id' )->setAlias( 'a0' );
		$b = ( new WGMModel( 'test_b' ) )->orderby( 'id' )->setAlias( 'b0' );
		$c = ( new WGMModel( 'test_c' ) )->orderby( 'id' )->setAlias( 'c0' );
		$a->inner( $b, [ 'id' => 'a_id' ] );
		$b->inner( $c, [ 'id' => 'b_id', 'a_id' => 'a_id' ] );
		$a->select();

		$this->assertSame( [
				[ 'id' => 20, 'title' => 'a2', 'enabled' => false, ]
			]
			, $a->avars );

		$this->assertSame( [
				[ 'id' => 202, 'a_id' => 20, 'body' => 'F', 'enabled' => false, ]
			]
			, $b->avars );

		$this->assertSame( [
				[ 'id' => 1001, 'a_id' => 20, 'b_id' => 202, 'note' => 'c', 'enabled' => true, ],
			]
			, $c->avars );

		$this->assertSame(
			[
				[
					'a0' => [ 'id' => 20, 'title' => 'a2', 'enabled' => false, ],
					'b0' => [ 'id' => 202, 'a_id' => 20, 'body' => 'F', 'enabled' => false, ],
					'c0' => [ 'id' => 1001, 'a_id' => 20, 'b_id' => 202, 'note' => 'c', 'enabled' => true, ],
				]
			], $a->getJoinedAvars()
		);


		_E( <<<SQL
DROP TABLE IF EXISTS test_a;
DROP TABLE IF EXISTS test_b;
DROP TABLE IF EXISTS test_c;
SQL
		);
	}
}
