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

class FrameworkModelWGMModelOrderTest extends TestCase
{
	public function test_model_order()
	{
		_E( <<<SQL
DROP TABLE IF EXISTS test_order;
CREATE TABLE test_text(
    id int4 not null primary key ,
    v0 text,
    v1 text 
);
INSERT INTO test_text VALUES(0,'',null);
INSERT INTO test_text VALUES(10,'A','C');
INSERT INTO test_text VALUES(20,'B','B');
INSERT INTO test_text VALUES(30,'C','A');
SQL
		);

		$m = new WGMModel( "test_order" );
		$m->orderby('id');



		_E( <<<SQL
DROP TABLE IF EXISTS test_order;
SQL
		);
	}
}
