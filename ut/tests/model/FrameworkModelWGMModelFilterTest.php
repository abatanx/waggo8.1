<?php /** @noinspection DuplicatedCode */

/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/local-common.php';
require_once __DIR__ . '/../../../framework/c/WGFSession.php';
require_once __DIR__ . '/../../../framework/v8/WGV8Basic.php';
require_once __DIR__ . '/../../../framework/v8/WGV8BasicElement.php';

class TestJSONFilter extends WGMModelFilter
{
	public function output( mixed $value ): mixed
	{
		return json_decode( $value, true );
	}

	public function input( mixed $value ): mixed
	{
		return json_encode( $value );
	}
}

class FrameworkModelWGMModelFilterTest extends TestCase
{
	public function test_model_filter()
	{
		_E( <<<SQL
DROP TABLE IF EXISTS test_filter;
CREATE TABLE test_filter(
    id int4 not null primary key ,
    v0 jsonb
);
INSERT INTO test_filter VALUES(0,'{"name":"yamamoto","age":87,"opts":[1,2,3,4,5]}');
SQL
		);

		// Filter (model to var)
		$m1 = new WGMModel( "test_filter" );
		$m1->setFilter( 'v0', new TestJSONFilter() );
		$m1->getVars( [ 'id' => 0 ] );

		$this->assertSame( [
			'age' => 87, 'name' => 'yamamoto', 'opts' => [ 0 => 1, 1 => 2, 2 => 3, 3 => 4, 4 => 5, ],
		], $m1->vars['v0'] );

		// Filter (var to model)
		$m1->vars['v0']['name']    = 'shizumi';
		$m1->vars['v0']['address'] = 'aso';
		$m1->update( 'id' );

		$r = ( new WGMModel( "test_filter" ) )->setVars( [ 'id' => 0 ] )->select( 'id' )->vars;
		$this->assertSame(
			'{"age": 87, "name": "shizumi", "opts": [1, 2, 3, 4, 5], "address": "aso"}',
			$r['v0']
		);

		_E( <<<SQL
DROP TABLE IF EXISTS test_filter;
SQL
		);
	}
}
