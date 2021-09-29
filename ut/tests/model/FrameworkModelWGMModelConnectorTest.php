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

class FrameworkModelWGMModelConnectorTest extends TestCase
{
	public function test_model_varchar()
	{
		_E( <<<SQL
DROP TABLE IF EXISTS test_connector;
CREATE TABLE test_connector(
    id int4 not null primary key ,
    v0 character varying not null default '' ,
    v1 character varying
);
INSERT INTO test_connector VALUES(0,'',null);
SQL
		);

		// assign view & setVars
		$v0_1 = new WGV8BasicElement();
		$v0_1->initSession( new WGFSession( 'test', 'test' ) );
		$v0_1->setKey( 'v0' );
		$v0_1->setValue( 'view' );

		$m1 = new WGMModel( "test_connector" );
		$m1->assign( 'v0', $v0_1 );
		$m1->setVars( [ 'id' => 10, 'v0' => 'model', 'v1' => 'model' ] )->update( 'id' );

		$r = ( new WGMModel( "test_connector" ) )->setVars( [ 'id' => 10 ] )->select( 'id' )->vars;
		$this->assertSame( [ 'id' => 10, 'v0' => 'model', 'v1' => 'model' ], $r );
		$this->assertSame( 'model', $v0_1->getValue() );

		// assign view & vars
		$v0_2 = new WGV8BasicElement();
		$v0_2->initSession( new WGFSession( 'test', 'test' ) );
		$v0_2->setKey( 'v0' );
		$v0_2->setValue( 'view' );

		$m2 = new WGMModel( "test_connector" );
		$m2->assign( 'v0', $v0_2 );
		$m2->vars['id'] = 20;
		$m2->vars['v0'] = 'model';
		$m2->vars['v1'] = 'model';
		$m2->update( 'id' );

		$r = ( new WGMModel( "test_connector" ) )->setVars( [ 'id' => 20 ] )->select( 'id' )->vars;
		$this->assertSame( [ 'id' => 20, 'v0' => 'view', 'v1' => 'model' ], $r );
		$this->assertSame( 'view', $v0_2->getValue() );

		// assign view & release view & setVars
		$v0_3 = new WGV8BasicElement();
		$v0_3->initSession( new WGFSession( 'test', 'test' ) );
		$v0_3->setKey( 'v0' );
		$v0_3->setValue( 'view' );

		$m3 = new WGMModel( "test_connector" );
		$m3->assign( 'v0', $v0_3 );
		$m3->release( 'v0' );
		$m3->setVars( [ 'id' => 10, 'v0' => 'model', 'v1' => 'model' ] )->update( 'id' );

		$r = ( new WGMModel( "test_connector" ) )->setVars( [ 'id' => 10 ] )->select( 'id' )->vars;
		$this->assertSame( [ 'id' => 10, 'v0' => 'model', 'v1' => 'model' ], $r );
		$this->assertSame( 'view', $v0_3->getValue() );

		// assign view & release view & vars
		$v0_4 = new WGV8BasicElement();
		$v0_4->initSession( new WGFSession( 'test', 'test' ) );
		$v0_4->setKey( 'v0' );
		$v0_4->setValue( 'view' );

		$m4 = new WGMModel( "test_connector" );
		$m4->assign( 'v0', $v0_4 );
		$m4->release( 'v0' );
		$m4->vars['id'] = 20;
		$m4->vars['v0'] = 'model';
		$m4->vars['v1'] = 'model';
		$m4->update( 'id' );

		$r = ( new WGMModel( "test_connector" ) )->setVars( [ 'id' => 20 ] )->select( 'id' )->vars;
		$this->assertSame( [ 'id' => 20, 'v0' => 'model', 'v1' => 'model' ], $r );
		$this->assertSame( 'view', $v0_4->getValue() );


		_E( <<<SQL
DROP TABLE IF EXISTS test_connector;
SQL
		);
	}
}
