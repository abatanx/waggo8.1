<?php
/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/local-common.php';

require_once __DIR__ . '/../../../framework/gauntlet/WGGInt.php';
require_once __DIR__ . '/../../../framework/parameters/WGParameters.php';
require_once __DIR__ . '/../../../framework/parameters/WGParaFilter.php';

class ParametersFilterArray extends WGParaFilter
{
	public function inputBeforeGauntlet( mixed $v ): ?array
	{
		if ( ! is_null( $v ) )
		{
			$s = trim( (string) $v );
			if ( preg_match( '/^[\d,]+$/', $s ) )
			{
				return
					array_values(
						array_unique( array_map( function ( $g ) {
								return (int) $g;
							},
								array_filter( explode( ',', $s ), function ( $g ) {
									return is_numeric( $g );
								} ) )
						)
					);
			}
		}

		return null;
	}

	public function output( mixed $v ): ?string
	{
		return is_array( $v ) ? implode( ',', $v ) : null;
	}
}

class ParametersFilterParam extends WGParameters
{
	#[WGPara( filter: new ParametersFilterArray(), default: null )]
	public ?array $a;
}

class ParametersFilterTest extends TestCase
{
	public function test_filter_1()
	{
		$_GET = [];

		$_GET['a'] = null;
		$g         = new ParametersFilterParam( null );
		$g->byGET();
		$this->assertEquals( null, $g->a );
		$this->assertEquals( '', $g->getParamString( isExcludeNull: true ) );

		$_GET['a'] = '';
		$g         = new ParametersFilterParam( null );
		$g->byGET();
		$this->assertEquals( null, $g->a );
		$this->assertEquals( '', $g->getParamString( isExcludeNull: true ) );

		$_GET['a'] = 'a';
		$g         = new ParametersFilterParam( null );
		$g->byGET();
		$this->assertEquals( null, $g->a );
		$this->assertEquals( '', $g->getParamString( isExcludeNull: true ) );

		$_GET['a'] = '10';
		$g         = new ParametersFilterParam( null );
		$g->byGET();
		$this->assertEquals( [ 10 ], $g->a );
		$this->assertEquals( 'a=10', $g->getParamString( isExcludeNull: true ) );

		$_GET['a'] = '10,20';
		$g         = new ParametersFilterParam( null );
		$g->byGET();
		$this->assertEquals( [ 10, 20 ], $g->a );
		$this->assertEquals( 'a=10,20', $g->getParamString( isExcludeNull: true ) );

		$_GET['a'] = '10,20,10';
		$g         = new ParametersFilterParam( null );
		$g->byGET();
		$this->assertEquals( [ 10, 20 ], $g->a );
		$this->assertEquals( 'a=10,20', $g->getParamString( isExcludeNull: true ) );

		$_GET['a'] = '0,0,0';
		$g         = new ParametersFilterParam( null );
		$g->byGET();
		$this->assertEquals( [ 0 ], $g->a );
		$this->assertEquals( 'a=0', $g->getParamString( isExcludeNull: true ) );

		$_GET['a'] = ',,10';
		$g         = new ParametersFilterParam( null );
		$g->byGET();
		$this->assertEquals( [ 10 ], $g->a );
		$this->assertEquals( 'a=10', $g->getParamString( isExcludeNull: true ) );

		$_GET['a'] = ',,10,,,10';
		$g         = new ParametersFilterParam( null );
		$g->byGET();
		$this->assertEquals( [ 10 ], $g->a );
		$this->assertEquals( 'a=10', $g->getParamString( isExcludeNull: true ) );

		$_GET['a'] = ',,';
		$g         = new ParametersFilterParam( null );
		$g->byGET();
		$this->assertEquals( [], $g->a );
		$this->assertEquals( 'a=', $g->getParamString( isExcludeNull: true ) );
	}

	public function test_filter_2()
	{
		$_GET = [];

		$_GET['a'] = null;
		$g         = new ParametersFilterParam( null );
		$g->byGET();
		$this->assertEquals( null, $g->a );
		$this->assertEquals( 'a=', $g->getParamString( isExcludeNull: false ) );

		$_GET['a'] = '';
		$g         = new ParametersFilterParam( null );
		$g->byGET();
		$this->assertEquals( null, $g->a );
		$this->assertEquals( 'a=', $g->getParamString( isExcludeNull: false ) );

		$_GET['a'] = 'a';
		$g         = new ParametersFilterParam( null );
		$g->byGET();
		$this->assertEquals( null, $g->a );
		$this->assertEquals( 'a=', $g->getParamString( isExcludeNull: false ) );

		$_GET['a'] = '10';
		$g         = new ParametersFilterParam( null );
		$g->byGET();
		$this->assertEquals( [ 10 ], $g->a );
		$this->assertEquals( 'a=10', $g->getParamString( isExcludeNull: false ) );

		$_GET['a'] = '10,20';
		$g         = new ParametersFilterParam( null );
		$g->byGET();
		$this->assertEquals( [ 10, 20 ], $g->a );
		$this->assertEquals( 'a=10,20', $g->getParamString( isExcludeNull: false ) );

		$_GET['a'] = '10,20,10';
		$g         = new ParametersFilterParam( null );
		$g->byGET();
		$this->assertEquals( [ 10, 20 ], $g->a );
		$this->assertEquals( 'a=10,20', $g->getParamString( isExcludeNull: false ) );

		$_GET['a'] = '0,0,0';
		$g         = new ParametersFilterParam( null );
		$g->byGET();
		$this->assertEquals( [ 0 ], $g->a );
		$this->assertEquals( 'a=0', $g->getParamString( isExcludeNull: false ) );

		$_GET['a'] = ',,10';
		$g         = new ParametersFilterParam( null );
		$g->byGET();
		$this->assertEquals( [ 10 ], $g->a );
		$this->assertEquals( 'a=10', $g->getParamString( isExcludeNull: false ) );

		$_GET['a'] = ',,10,,,10';
		$g         = new ParametersFilterParam( null );
		$g->byGET();
		$this->assertEquals( [ 10 ], $g->a );
		$this->assertEquals( 'a=10', $g->getParamString( isExcludeNull: false ) );

		$_GET['a'] = ',,';
		$g         = new ParametersFilterParam( null );
		$g->byGET();
		$this->assertEquals( [], $g->a );
		$this->assertEquals( 'a=', $g->getParamString( isExcludeNull: false ) );
	}

}
