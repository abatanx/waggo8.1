<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

if( !defined('WG_UNITTEST') ) define( 'WG_UNITTEST', true );

require __DIR__ . '/../../framework/m/WGMModel.php';

class FrameworkModelWGMModelTest extends TestCase
{
	public function test_model()
	{
		_E(<<<SQL
DROP TABLE IF EXISTS test_table;
CREATE TABLE test_table(
    id int4 not null,
    t0_1 int4 not null,
    t0_2 int4,
    t1_1 text not null,
    t1_2 text,
    t2_1 varchar not null,
    t2_2 varchar,
    t3_1 double precision not null,
    t3_2 double precision,
    t4_1 bool not null,
    t4_2 bool,
    t5_1 date not null,
    t5_2 date,
    t6_1 time not null,
    t6_2 time,
    t7_1 timestamp not null,
    t7_2 timestamp
);
SQL
		);

		$m = new WGMModel('test_table');

		// ::getFields()
		$this->assertEquals(
			['id','t0_1','t0_2','t1_1','t1_2','t2_1','t2_2','t3_1','t3_2','t4_1','t4_2','t5_1','t5_2','t6_1','t6_2','t7_1','t7_2'],
			$m->getFields());

		// ::getFieldType()
		$this->assertEquals(WGMModel::N, $m->getFieldType('t0_1'));
		$this->assertEquals(WGMModel::N, $m->getFieldType('t0_2'));
		$this->assertEquals(WGMModel::S, $m->getFieldType('t1_1'));
		$this->assertEquals(WGMModel::S, $m->getFieldType('t1_2'));
		$this->assertEquals(WGMModel::S, $m->getFieldType('t2_1'));
		$this->assertEquals(WGMModel::S, $m->getFieldType('t2_2'));
		$this->assertEquals(WGMModel::D, $m->getFieldType('t3_1'));
		$this->assertEquals(WGMModel::D, $m->getFieldType('t3_2'));
		$this->assertEquals(WGMModel::B, $m->getFieldType('t4_1'));
		$this->assertEquals(WGMModel::B, $m->getFieldType('t4_2'));
		$this->assertEquals(WGMModel::T, $m->getFieldType('t5_1'));
		$this->assertEquals(WGMModel::T, $m->getFieldType('t5_2'));
		$this->assertEquals(WGMModel::S, $m->getFieldType('t6_1'));
		$this->assertEquals(WGMModel::S, $m->getFieldType('t6_2'));
		$this->assertEquals(WGMModel::T, $m->getFieldType('t7_1'));
		$this->assertEquals(WGMModel::T, $m->getFieldType('t7_2'));

		// ::getTableName()
		



		_E(<<<SQL
DROP TABLE IF EXISTS test_table;
SQL
		);
	}
}
