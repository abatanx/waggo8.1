<?php
/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/local-common.php';

class FrameworkModelWGMModelTest extends TestCase
{
	public function test_model()
	{
		_E( <<<SQL
DROP TABLE IF EXISTS test_table;
CREATE TABLE test_table(
    id int4 not null primary key ,
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

		$m = new WGMModel( 'test_table' );
		$m->setAlias( 'test' );

		// ::getFields()
		$this->assertEquals(
			[ 'id', 't0_1', 't0_2', 't1_1', 't1_2', 't2_1', 't2_2', 't3_1', 't3_2', 't4_1', 't4_2', 't5_1', 't5_2', 't6_1', 't6_2', 't7_1', 't7_2' ],
			$m->getFields() );

		// ::getFieldType()
		$this->assertEquals( WGDBMSProperty::N, $m->getFieldType( 't0_1' ) );
		$this->assertEquals( WGDBMSProperty::N, $m->getFieldType( 't0_2' ) );
		$this->assertEquals( WGDBMSProperty::S, $m->getFieldType( 't1_1' ) );
		$this->assertEquals( WGDBMSProperty::S, $m->getFieldType( 't1_2' ) );
		$this->assertEquals( WGDBMSProperty::S, $m->getFieldType( 't2_1' ) );
		$this->assertEquals( WGDBMSProperty::S, $m->getFieldType( 't2_2' ) );
		$this->assertEquals( WGDBMSProperty::D, $m->getFieldType( 't3_1' ) );
		$this->assertEquals( WGDBMSProperty::D, $m->getFieldType( 't3_2' ) );
		$this->assertEquals( WGDBMSProperty::B, $m->getFieldType( 't4_1' ) );
		$this->assertEquals( WGDBMSProperty::B, $m->getFieldType( 't4_2' ) );
		$this->assertEquals( WGDBMSProperty::TD, $m->getFieldType( 't5_1' ) );
		$this->assertEquals( WGDBMSProperty::TD, $m->getFieldType( 't5_2' ) );
		$this->assertEquals( WGDBMSProperty::TT, $m->getFieldType( 't6_1' ) );
		$this->assertEquals( WGDBMSProperty::TT, $m->getFieldType( 't6_2' ) );
		$this->assertEquals( WGDBMSProperty::TS, $m->getFieldType( 't7_1' ) );
		$this->assertEquals( WGDBMSProperty::TS, $m->getFieldType( 't7_2' ) );

		// ::getTableName()
		$this->assertEquals( 'integer', $m->getFieldFormat( 't0_1' ) );
		$this->assertEquals( 'integer', $m->getFieldFormat( 't0_2' ) );
		$this->assertEquals( 'text', $m->getFieldFormat( 't1_1' ) );
		$this->assertEquals( 'text', $m->getFieldFormat( 't1_2' ) );
		$this->assertEquals( 'character varying', $m->getFieldFormat( 't2_1' ) );
		$this->assertEquals( 'character varying', $m->getFieldFormat( 't2_2' ) );
		$this->assertEquals( 'double precision', $m->getFieldFormat( 't3_1' ) );
		$this->assertEquals( 'double precision', $m->getFieldFormat( 't3_2' ) );
		$this->assertEquals( 'boolean', $m->getFieldFormat( 't4_1' ) );
		$this->assertEquals( 'boolean', $m->getFieldFormat( 't4_2' ) );
		$this->assertEquals( 'date', $m->getFieldFormat( 't5_1' ) );
		$this->assertEquals( 'date', $m->getFieldFormat( 't5_2' ) );
		$this->assertEquals( 'time without time zone', $m->getFieldFormat( 't6_1' ) );
		$this->assertEquals( 'time without time zone', $m->getFieldFormat( 't6_2' ) );
		$this->assertEquals( 'timestamp without time zone', $m->getFieldFormat( 't7_1' ) );
		$this->assertEquals( 'timestamp without time zone', $m->getFieldFormat( 't7_2' ) );

		$this->assertFalse( $m->IsAllowNullField( 't0_1' ) );
		$this->assertTrue( $m->IsAllowNullField( 't0_2' ) );
		$this->assertFalse( $m->IsAllowNullField( 't1_1' ) );
		$this->assertTrue( $m->IsAllowNullField( 't1_2' ) );
		$this->assertFalse( $m->IsAllowNullField( 't2_1' ) );
		$this->assertTrue( $m->IsAllowNullField( 't2_2' ) );
		$this->assertFalse( $m->IsAllowNullField( 't3_1' ) );
		$this->assertTrue( $m->IsAllowNullField( 't3_2' ) );
		$this->assertFalse( $m->IsAllowNullField( 't4_1' ) );
		$this->assertTrue( $m->IsAllowNullField( 't4_2' ) );
		$this->assertFalse( $m->IsAllowNullField( 't5_1' ) );
		$this->assertTrue( $m->IsAllowNullField( 't5_2' ) );
		$this->assertFalse( $m->IsAllowNullField( 't6_1' ) );
		$this->assertTrue( $m->IsAllowNullField( 't6_2' ) );
		$this->assertFalse( $m->IsAllowNullField( 't7_1' ) );
		$this->assertTrue( $m->IsAllowNullField( 't7_2' ) );

		$this->assertEquals( [ 'id' ], $m->getPrimaryKeys() );
		$this->assertEquals( 'test_table', $m->getTable() );
		$this->assertEquals( 'test', $m->getAlias() );

		$this->assertEquals( 'test.t0_1', $m->expansion( '{t0_1}' ) );
		$this->assertEquals( "'{t0_1}'", $m->expansion( "'{t0_1}'" ) );
		$this->assertEquals( "''test.t0_1''", $m->expansion( "''{t0_1}''" ) );
		$this->assertEquals( "'''{t0_1}'''", $m->expansion( "'''{t0_1}'''" ) );
		$this->assertEquals( "''''test.t0_1''''", $m->expansion( "''''{t0_1}''''" ) );
		$this->assertEquals( "\\'{t0_1}\\'", $m->expansion( "\\'{t0_1}\\'" ) );
		$this->assertEquals( "'\\'{t0_1}\\''", $m->expansion( "'\\'{t0_1}\\''" ) );
		$this->assertEquals( 'test.z', $m->expansion( '{z}' ) );
		$this->assertEquals( '{ z }', $m->expansion( '{ z }' ) );
		$this->assertEquals( '{z', $m->expansion( '{z' ) );
		$this->assertEquals( 'z}', $m->expansion( 'z}' ) );

		_E( <<<SQL
DROP TABLE IF EXISTS test_table;
SQL
		);
	}
}
