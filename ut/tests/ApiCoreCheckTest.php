<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

if( !defined('WG_UNITTEST') ) define( 'WG_UNITTEST', true );

require_once __DIR__ . '/../../api/core/check.php';

class ApiCoreCheckTest extends TestCase
{
	public function test_wg_inchk_float()
	{
		$in = '0';
		$this->assertEquals( true, wg_inchk_float( $r, $in ) );
		$this->assertEquals( 0, $r );

		$in = '6';
		$this->assertEquals( true, wg_inchk_float( $r, $in ) );
		$this->assertEquals( 6, $r );

		$in = '0666';
		$this->assertEquals( true, wg_inchk_float( $r, $in ) );
		$this->assertEquals( 666, $r );

		$in = '3.14';
		$this->assertEquals( true, wg_inchk_float( $r, $in ) );
		$this->assertEquals( 3.14, $r );

		$in = '-3.14';
		$this->assertEquals( false, wg_inchk_float( $r, $in ) );
		$this->assertEquals( 0, $r );

		$in = '3.14';
		$this->assertEquals( true, wg_inchk_float( $r, $in, - 100, 100 ) );
		$this->assertEquals( 3.14, $r );

		$in = '-3.14';
		$this->assertEquals( true, wg_inchk_float( $r, $in, - 100, 100 ) );
		$this->assertEquals( - 3.14, $r );

		$in = '3.14e1';
		$this->assertEquals( true, wg_inchk_float( $r, $in, - 100, 100 ) );
		$this->assertEquals( 31.4, $r );

		$in = '-3.14e1';
		$this->assertEquals( true, wg_inchk_float( $r, $in, - 100, 100 ) );
		$this->assertEquals( - 31.4, $r );

		$in = '100';
		$this->assertEquals( true, wg_inchk_float( $r, $in, - 100, 100 ) );
		$this->assertEquals( 100, $r );

		$in = '-100';
		$this->assertEquals( true, wg_inchk_float( $r, $in, - 100, 100 ) );
		$this->assertEquals( - 100, $r );

		$in = '100.1';
		$this->assertEquals( false, wg_inchk_float( $r, $in, - 100, 100 ) );
		$this->assertEquals( 0, $r );

		$in = '-100.1';
		$this->assertEquals( false, wg_inchk_float( $r, $in, - 100, 100 ) );
		$this->assertEquals( 0, $r );

		$in = false;
		$this->assertEquals( false, wg_inchk_float( $r, $in ) );
		$this->assertEquals( 0, $r );

		$in = null;
		$this->assertEquals( false, wg_inchk_float( $r, $in ) );
		$this->assertEquals( 0, $r );

		$in = [];
		$this->assertEquals( false, wg_inchk_float( $r, $in['test'] ?? null ) );
		$this->assertEquals( 0, $r );

		$in = '';
		$this->assertEquals( false, wg_inchk_float( $r, $in ) );
		$this->assertEquals( 0, $r );

		$in = 'AAA';
		$this->assertEquals( false, wg_inchk_float( $r, $in ) );
		$this->assertEquals( 0, $r );
	}

	public function test_wg_inchk_int()
	{
		$in = '0';
		$this->assertEquals( true, wg_inchk_int( $r, $in ) );
		$this->assertEquals( 0, $r );

		$in = '6';
		$this->assertEquals( true, wg_inchk_int( $r, $in ) );
		$this->assertEquals( 6, $r );

		$in = '0666';
		$this->assertEquals( true, wg_inchk_int( $r, $in ) );
		$this->assertEquals( 666, $r );

		$in = '1e3';
		$this->assertEquals( true, wg_inchk_int( $r, $in ) );
		$this->assertEquals( 1000, $r );

		$in = '3.14';
		$this->assertEquals( true, wg_inchk_int( $r, $in ) );
		$this->assertEquals( 3, $r );

		$in = '-3.14';
		$this->assertEquals( false, wg_inchk_int( $r, $in ) );
		$this->assertEquals( 0, $r );

		$in = '3.14';
		$this->assertEquals( true, wg_inchk_int( $r, $in, - 100, 100 ) );
		$this->assertEquals( 3, $r );

		$in = '-3.14';
		$this->assertEquals( true, wg_inchk_int( $r, $in, - 100, 100 ) );
		$this->assertEquals( - 3, $r );

		$in = '3.14e1';
		$this->assertEquals( true, wg_inchk_int( $r, $in, - 100, 100 ) );
		$this->assertEquals( 31, $r );

		$in = '-3.14e1';
		$this->assertEquals( true, wg_inchk_int( $r, $in, - 100, 100 ) );
		$this->assertEquals( - 31, $r );

		$in = '100';
		$this->assertEquals( true, wg_inchk_int( $r, $in, - 100, 100 ) );
		$this->assertEquals( 100, $r );

		$in = '-100';
		$this->assertEquals( true, wg_inchk_int( $r, $in, - 100, 100 ) );
		$this->assertEquals( - 100, $r );

		$in = '101';
		$this->assertEquals( false, wg_inchk_int( $r, $in, - 100, 100 ) );
		$this->assertEquals( 0, $r );

		$in = '-101';
		$this->assertEquals( false, wg_inchk_int( $r, $in, - 100, 100 ) );
		$this->assertEquals( 0, $r );

		$in = false;
		$this->assertEquals( false, wg_inchk_int( $r, $in ) );
		$this->assertEquals( 0, $r );

		$in = null;
		$this->assertEquals( false, wg_inchk_int( $r, $in ) );
		$this->assertEquals( 0, $r );

		$in = [];
		$this->assertEquals( false, wg_inchk_int( $r, $in['test'] ?? null ) );
		$this->assertEquals( 0, $r );

		$in = '';
		$this->assertEquals( false, wg_inchk_int( $r, $in ) );
		$this->assertEquals( 0, $r );

		$in = 'AAA';
		$this->assertEquals( false, wg_inchk_int( $r, $in ) );
		$this->assertEquals( 0, $r );
	}

	public function test_wg_inchk_string()
	{
		$in = '0';
		$this->assertEquals( true, wg_inchk_string( $r, $in ) );
		$this->assertEquals( '0', $r );

		$in = '3.14';
		$this->assertEquals( true, wg_inchk_string( $r, $in ) );
		$this->assertEquals( '3.14', $r );

		$in = '-3.14';
		$this->assertEquals( true, wg_inchk_string( $r, $in ) );
		$this->assertEquals( '-3.14', $r );

		$in = '3.14 ';
		$this->assertEquals( true, wg_inchk_string( $r, $in ) );
		$this->assertEquals( '3.14 ', $r );

		$in = '-3.14 ';
		$this->assertEquals( true, wg_inchk_string( $r, $in ) );
		$this->assertEquals( '-3.14 ', $r );

		$in = ' 3.14 ';
		$this->assertEquals( true, wg_inchk_string( $r, $in ) );
		$this->assertEquals( ' 3.14 ', $r );

		$in = ' -3.14 ';
		$this->assertEquals( true, wg_inchk_string( $r, $in ) );
		$this->assertEquals( ' -3.14 ', $r );

		$in = 'TESTTEST';
		$this->assertEquals( true, wg_inchk_string( $r, $in, 0, 8 ) );
		$this->assertEquals( 'TESTTEST', $r );

		$in = 'TESTTEST';
		$this->assertEquals( false, wg_inchk_string( $r, $in, 0, 7 ) );
		$this->assertEquals( '', $r );

		$in = 'TESTTES';
		$this->assertEquals( false, wg_inchk_string( $r, $in, 8, 10 ) );
		$this->assertEquals( '', $r );

		$in = 'TESTTEST';
		$this->assertEquals( true, wg_inchk_string( $r, $in, 8, 10 ) );
		$this->assertEquals( 'TESTTEST', $r );

		$in = 'TESTTESTT';
		$this->assertEquals( true, wg_inchk_string( $r, $in, 8, 10 ) );
		$this->assertEquals( 'TESTTESTT', $r );

		$in = 'TESTTESTTE';
		$this->assertEquals( true, wg_inchk_string( $r, $in, 8, 10 ) );
		$this->assertEquals( 'TESTTESTTE', $r );

		$in = 'TESTTESTTES';
		$this->assertEquals( false, wg_inchk_string( $r, $in, 8, 10 ) );
		$this->assertEquals( '', $r );

		$in = false;
		$this->assertEquals( true, wg_inchk_string( $r, $in ) );
		$this->assertEquals( '', $r );

		$in = null;
		$this->assertEquals( false, wg_inchk_string( $r, $in ) );
		$this->assertEquals( '', $r );

		$in = [];
		$this->assertEquals( false, wg_inchk_string( $r, $in['test'] ?? null ) );
		$this->assertEquals( '', $r );

		$in = '';
		$this->assertEquals( true, wg_inchk_string( $r, $in ) );
		$this->assertEquals( '', $r );
	}

	public function test_wg_inchk_ymd()
	{
		$in = '2012/03/04';
		$this->assertEquals( true, wg_inchk_ymd( $r, $in ) );
		$this->assertEquals( '2012-03-04', $r );

		$in = '2012-03/04';
		$this->assertEquals( true, wg_inchk_ymd( $r, $in ) );
		$this->assertEquals( '2012-03-04', $r );

		$in = '2012/03-04';
		$this->assertEquals( true, wg_inchk_ymd( $r, $in ) );
		$this->assertEquals( '2012-03-04', $r );

		$in = '2012-03-04';
		$this->assertEquals( true, wg_inchk_ymd( $r, $in ) );
		$this->assertEquals( '2012-03-04', $r );

		$in = '2012-03-4';
		$this->assertEquals( true, wg_inchk_ymd( $r, $in ) );
		$this->assertEquals( '2012-03-04', $r );

		$in = '2012-3-04';
		$this->assertEquals( true, wg_inchk_ymd( $r, $in ) );
		$this->assertEquals( '2012-03-04', $r );

		$in = '2012-3-4';
		$this->assertEquals( true, wg_inchk_ymd( $r, $in ) );
		$this->assertEquals( '2012-03-04', $r );

		$in = '0/0/0';
		$this->assertEquals( false, wg_inchk_ymd( $r, $in ) );
		$this->assertEquals( '', $r );

		$in = '20121/03/04';
		$this->assertEquals( false, wg_inchk_ymd( $r, $in ) );
		$this->assertEquals( '', $r );

		$in = '2012/003/04';
		$this->assertEquals( false, wg_inchk_ymd( $r, $in ) );
		$this->assertEquals( '', $r );

		$in = '2012/03/004';
		$this->assertEquals( false, wg_inchk_ymd( $r, $in ) );
		$this->assertEquals( '', $r );

		$in = '2012/103/04';
		$this->assertEquals( false, wg_inchk_ymd( $r, $in ) );
		$this->assertEquals( '', $r );

		$in = '2012/03/104';
		$this->assertEquals( false, wg_inchk_ymd( $r, $in ) );
		$this->assertEquals( '', $r );

		$in = '2012/02/29';
		$this->assertEquals( true, wg_inchk_ymd( $r, $in ) );
		$this->assertEquals( '2012-02-29', $r );

		$in = '2013/02/29';
		$this->assertEquals( false, wg_inchk_ymd( $r, $in ) );
		$this->assertEquals( '', $r );

		$in = false;
		$this->assertEquals( false, wg_inchk_ymd( $r, $in ) );
		$this->assertEquals( '', $r );

		$in = null;
		$this->assertEquals( false, wg_inchk_ymd( $r, $in ) );
		$this->assertEquals( '', $r );

		$in = [];
		$this->assertEquals( false, wg_inchk_ymd( $r, $in['test'] ?? null ) );
		$this->assertEquals( '', $r );

		$in = '';
		$this->assertEquals( false, wg_inchk_ymd( $r, $in ) );
		$this->assertEquals( '', $r );
	}

	public function test_wg_inchk_ym()
	{
		$in = '2012/03';
		$this->assertEquals( true, wg_inchk_ym( $r, $in ) );
		$this->assertEquals( '2012-03', $r );

		$in = '2012-03';
		$this->assertEquals( true, wg_inchk_ym( $r, $in ) );
		$this->assertEquals( '2012-03', $r );

		$in = '2012-3';
		$this->assertEquals( true, wg_inchk_ym( $r, $in ) );
		$this->assertEquals( '2012-03', $r );

		$in = '0/0';
		$this->assertEquals( false, wg_inchk_ym( $r, $in ) );
		$this->assertEquals( '', $r );

		$in = '20121/03';
		$this->assertEquals( false, wg_inchk_ym( $r, $in ) );
		$this->assertEquals( '', $r );

		$in = '2012/003';
		$this->assertEquals( false, wg_inchk_ym( $r, $in ) );
		$this->assertEquals( '', $r );

		$in = '2012/02';
		$this->assertEquals( true, wg_inchk_ym( $r, $in, true ) );
		$this->assertEquals( '2012-02-01', $r );

		$in = false;
		$this->assertEquals( false, wg_inchk_ym( $r, $in ) );
		$this->assertEquals( '', $r );

		$in = null;
		$this->assertEquals( false, wg_inchk_ym( $r, $in ) );
		$this->assertEquals( '', $r );

		$in = [];
		$this->assertEquals( false, wg_inchk_ym( $r, $in['test'] ?? null ) );
		$this->assertEquals( '', $r );

		$in = '';
		$this->assertEquals( false, wg_inchk_ym( $r, $in ) );
		$this->assertEquals( '', $r );
	}

	public function test_wg_inchk_preg()
	{
		$re = '/^ABC$/';
		$in = 'ABC';
		$this->assertEquals( true, wg_inchk_preg( $r, $in, $re ) );
		$this->assertEquals( 'ABC', $r );

		$re = '/^ABC$/';
		$in = 'EFG';
		$this->assertEquals( false, wg_inchk_preg( $r, $in, $re ) );
		$this->assertEquals( '', $r );

		$re = '/^.+$/';
		$in = 'ABC';
		$this->assertEquals( false, wg_inchk_preg( $r, $in, $re, 4, 5 ) );
		$this->assertEquals( '', $r );

		$re = '/^.+$/';
		$in = 'ABCD';
		$this->assertEquals( true, wg_inchk_preg( $r, $in, $re, 4, 5 ) );
		$this->assertEquals( 'ABCD', $r );

		$re = '/^.+$/';
		$in = 'ABCDE';
		$this->assertEquals( true, wg_inchk_preg( $r, $in, $re, 4, 5 ) );
		$this->assertEquals( 'ABCDE', $r );

		$re = '/^.+$/';
		$in = 'ABCDEF';
		$this->assertEquals( false, wg_inchk_preg( $r, $in, $re, 4, 5 ) );
		$this->assertEquals( '', $r );

		$re = '/^$/';
		$in = false;
		$this->assertEquals( true, wg_inchk_preg( $r, $in, $re ) );
		$this->assertEquals( '', $r );

		$re = '/^$/';
		$in = null;
		$this->assertEquals( false, wg_inchk_preg( $r, $in, $re ) );
		$this->assertEquals( '', $r );

		$re = '/^$/';
		$in = [];
		$this->assertEquals( false, wg_inchk_preg( $r, $in['test'] ?? null, $re ) );
		$this->assertEquals( '', $r );

		$re = '/^$/';
		$in = '';
		$this->assertEquals( true, wg_inchk_preg( $r, $in, $re ) );
		$this->assertEquals( '', $r );
	}

	public function test_wg_inchk_preg_match()
	{
		$re = '/^A(B)C$/';
		$in = 'ABC';
		$this->assertEquals( true, wg_inchk_preg_match( $r, $in, $re ) );
		$this->assertEquals( [ 'ABC', 'B' ], $r );

		$re = '/^A(B)C$/';
		$in = 'EFG';
		$this->assertEquals( false, wg_inchk_preg_match( $r, $in, $re ) );
		$this->assertEquals( [], $r );

		$re = '/A(B)C/';
		$in = 'ABC';
		$this->assertEquals( false, wg_inchk_preg_match( $r, $in, $re, 4, 5 ) );
		$this->assertEquals( [], $r );

		$re = '/A(B)C/';
		$in = 'ABCD';
		$this->assertEquals( true, wg_inchk_preg_match( $r, $in, $re, 4, 5 ) );
		$this->assertEquals( [ 'ABC', 'B' ], $r );

		$re = '/A(B)C/';
		$in = 'ABCDE';
		$this->assertEquals( true, wg_inchk_preg_match( $r, $in, $re, 4, 5 ) );
		$this->assertEquals( [ 'ABC', 'B' ], $r );

		$re = '/A(B)C/';
		$in = 'ABCDEF';
		$this->assertEquals( false, wg_inchk_preg_match( $r, $in, $re, 4, 5 ) );
		$this->assertEquals( [], $r );

		$re = '/^()$/';
		$in = false;
		$this->assertEquals( true, wg_inchk_preg_match( $r, $in, $re ) );
		$this->assertEquals( [ '', '' ], $r );

		$re = '/^()$/';
		$in = null;
		$this->assertEquals( false, wg_inchk_preg_match( $r, $in, $re ) );
		$this->assertEquals( [], $r );

		$re = '/^()$/';
		$in = [];
		$this->assertEquals( false, wg_inchk_preg_match( $r, $in['test'] ?? null, $re ) );
		$this->assertEquals( [], $r );

		$re = '/^()$/';
		$in = '';
		$this->assertEquals( true, wg_inchk_preg_match( $r, $in, $re ) );
		$this->assertEquals( [ '', '' ], $r );
	}

	public function test_wg_check_input_number()
	{
		$this->assertEquals( false, wg_check_input_number( '0', 1, 2 ) );
		$this->assertEquals( true, wg_check_input_number( '1', 1, 2 ) );
		$this->assertEquals( true, wg_check_input_number( '2', 1, 2 ) );
		$this->assertEquals( false, wg_check_input_number( '3', 1, 2 ) );

		$this->assertEquals( false, wg_check_input_number( ' 0', 1, 2 ) );
		$this->assertEquals( true, wg_check_input_number( ' 1', 1, 2 ) );
		$this->assertEquals( true, wg_check_input_number( ' 2', 1, 2 ) );
		$this->assertEquals( false, wg_check_input_number( ' 3', 1, 2 ) );

		$this->assertEquals( false, wg_check_input_number( '0 ', 1, 2 ) );
		$this->assertEquals( true, wg_check_input_number( '1 ', 1, 2 ) );
		$this->assertEquals( true, wg_check_input_number( '2 ', 1, 2 ) );
		$this->assertEquals( false, wg_check_input_number( '3 ', 1, 2 ) );

		$this->assertEquals( false, wg_check_input_number( ' 0 ', 1, 2 ) );
		$this->assertEquals( true, wg_check_input_number( ' 1 ', 1, 2 ) );
		$this->assertEquals( true, wg_check_input_number( ' 2 ', 1, 2 ) );
		$this->assertEquals( false, wg_check_input_number( ' 3 ', 1, 2 ) );

		$this->assertEquals( false, wg_check_input_number( '0e0', 1, 2 ) );
		$this->assertEquals( true, wg_check_input_number( '1e0', 1, 2 ) );
		$this->assertEquals( true, wg_check_input_number( '2e0', 1, 2 ) );
		$this->assertEquals( false, wg_check_input_number( '3e0', 1, 2 ) );

		$this->assertEquals( false, wg_check_input_number( '-2e0', - 1, 1 ) );
		$this->assertEquals( true, wg_check_input_number( '-1e0', - 1, 1 ) );
		$this->assertEquals( true, wg_check_input_number( '0', - 1, 1 ) );
		$this->assertEquals( true, wg_check_input_number( '1e0', - 1, 1 ) );
		$this->assertEquals( false, wg_check_input_number( '2e0', - 1, 1 ) );
	}

	public function test_wg_check_input_string()
	{
		$this->assertEquals( false, wg_check_input_string( '', 1, 2 ) );
		$this->assertEquals( true, wg_check_input_string( 'A', 1, 2 ) );
		$this->assertEquals( true, wg_check_input_string( 'AA', 1, 2 ) );
		$this->assertEquals( false, wg_check_input_string( 'AAA', 1, 2 ) );
	}


}
