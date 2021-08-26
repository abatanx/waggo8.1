<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

if( !defined('WG_UNITTEST') ) define( 'WG_UNITTEST', true );

require __DIR__ . '/../../waggo.php';
require __DIR__ . '/../../framework/c/WGFSession.php';

class FrameworkControllerWGFSessionTest extends TestCase
{
	public function test_session_test()
	{
		$sess = new WGFSession( 'aaa', 'bbb' );

		$sess->set('a', 'test');
		$this->assertEquals( $sess->get('a'), 'test');
	}
}
