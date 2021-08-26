<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

if( !defined('WG_UNITTEST') ) define( 'WG_UNITTEST', true );

require __DIR__ . '/../../api/datetime/WGDateTime.php';

class ApiDatetimeWGDateTimeTest extends TestCase
{
	public function test_splitDateTime()
	{
		$r = WGDateTime::splitDateTime( mktime( 12, 34, 56, 1, 2, 2003 ) );
		$this->assertEquals( [ 2003, 1, 2, 12, 34, 56 ], $r );
	}

	public function test_makeDateTime()
	{
		$t = mktime( 12, 34, 56, 1, 2, 2003 );
		$r = WGDateTime::makeDateTime( [ 2003, 1, 2, 12, 34, 56 ] );
		$this->assertEquals( $t, $r );
	}

	public function test_convertMonthNendo()
	{
		$r = WGDateTime::convertMonthToNendoIndex( 4 );
		$this->assertEquals( 0, $r );

		$r = WGDateTime::convertNendoIndexToMonth( 0 );
		$this->assertEquals( 4, $r );

		for ( $i = - 100; $i <= 100; $i ++ )
		{
			$r = ( WGDateTime::mod( $i - 1, 12 ) ) + 1;

			$r1 = WGDateTime::convertMonthToNendoIndex( $i );
			$r2 = WGDateTime::convertNendoIndexToMonth( $r1 );

			$this->assertEquals( $r2, $r );
		}
	}

	public function test_constructor()
	{
		$date = new WGDateTime();
		$this->assertEquals( 0, $date->getUnixTime() );
	}

	public function test_set()
	{
		$d = new WGDateTime();

		$d->set( 2001 );
		$this->assertEquals( '2001-01-01 00:00:00', $d->getYMDHISString() );

		$d->set( 2001, 2 );
		$this->assertEquals( '2001-02-01 00:00:00', $d->getYMDHISString() );

		$d->set( 2001, 2, 3 );
		$this->assertEquals( '2001-02-03 00:00:00', $d->getYMDHISString() );

		$d->set( 2001, 2, 3, 4 );
		$this->assertEquals( '2001-02-03 04:00:00', $d->getYMDHISString() );

		$d->set( 2001, 2, 3, 4, 56 );
		$this->assertEquals( '2001-02-03 04:56:00', $d->getYMDHISString() );

		$d->set( 2001, 2, 3, 4, 56, 7 );
		$this->assertEquals( '2001-02-03 04:56:07', $d->getYMDHISString() );

		$d->set( 2001, 2, 3, 4, 56, 60 );
		$this->assertEquals( '2001-02-03 04:57:00', $d->getYMDHISString() );

		$d->set( 2001, 2, 3, - 1, 56, 10 );
		$this->assertEquals( '2001-02-02 23:56:10', $d->getYMDHISString() );
	}

	public function test_setDate()
	{
		$d = new WGDateTime();

		$d->set( 2012, 3, 4, 5, 6, 7 );

		$d->setDate();
		$this->assertEquals( '2012-03-04 05:06:07', $d->getYMDHISString() );

		$d->setDate( 2013 );
		$this->assertEquals( '2013-03-04 05:06:07', $d->getYMDHISString() );

		$d->setDate( null, 8 );
		$this->assertEquals( '2013-08-04 05:06:07', $d->getYMDHISString() );

		$d->setDate( null, null, 31 );
		$this->assertEquals( '2013-08-31 05:06:07', $d->getYMDHISString() );

		$d->setDate( 2015, 6, 7 );
		$this->assertEquals( '2015-06-07 05:06:07', $d->getYMDHISString() );

		$d->setDate( 2015, 6, 31 );
		$this->assertEquals( '2015-07-01 05:06:07', $d->getYMDHISString() );

		$d->setDate( 2015, - 1, 31 );
		$this->assertEquals( '2014-12-01 05:06:07', $d->getYMDHISString() );
	}

	public function test_setTime()
	{
		$d = new WGDateTime();

		$d->set( 2012, 3, 4, 5, 6, 7 );

		$d->setTime();
		$this->assertEquals( '2012-03-04 05:06:07', $d->getYMDHISString() );

		$d->setTime( 12 );
		$this->assertEquals( '2012-03-04 12:06:07', $d->getYMDHISString() );

		$d->setTime( null, 13 );
		$this->assertEquals( '2012-03-04 12:13:07', $d->getYMDHISString() );

		$d->setTime( null, null, 14 );
		$this->assertEquals( '2012-03-04 12:13:14', $d->getYMDHISString() );

		$d->setTime( 15, 16, 17 );
		$this->assertEquals( '2012-03-04 15:16:17', $d->getYMDHISString() );

		$d->setTime( 24, 0, 0 );
		$this->assertEquals( '2012-03-05 00:00:00', $d->getYMDHISString() );

		$d->setTime( - 1, 0, 0 );
		$this->assertEquals( '2012-03-04 23:00:00', $d->getYMDHISString() );
	}

	public function test_setUnixTime()
	{
		$d = new WGDateTime();

		$t = time();
		$d->setUnixTime( $t );

		$this->assertEquals( date( 'Y-m-d H:i:s', $t ), $d->getYMDHISString() );
	}

	/**
	 * @throws WGDateTimeException
	 */
	public function test_setStrToTime()
	{
		$d = new WGDateTime();
		$d->setStrToTime( '2012-03-04 05:06:07' );
		$this->assertEquals( '2012-03-04 05:06:07', $d->getYMDHISString() );
	}

	public function test_setStrToTimeException()
	{
		$this->expectException( WGDateTimeException::class );

		$d = new WGDateTime();
		$d->setStrToTime( 'AAAA' );
	}

	public function test_componentValue1()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$d->setComponentValue( WGDateTime::Y, 2013 );
		$this->assertEquals( 2013, $d->getComponentValue( WGDateTime::Y ) );
		$this->assertEquals( '2013-03-04 05:06:07', $d->getYMDHISString() );

		$d->setComponentValue( WGDateTime::M, 4 );
		$this->assertEquals( 4, $d->getComponentValue( WGDateTime::M ) );
		$this->assertEquals( '2013-04-04 05:06:07', $d->getYMDHISString() );

		$d->setComponentValue( WGDateTime::D, 5 );
		$this->assertEquals( 5, $d->getComponentValue( WGDateTime::D ) );
		$this->assertEquals( '2013-04-05 05:06:07', $d->getYMDHISString() );

		$d->setComponentValue( WGDateTime::H, 16 );
		$this->assertEquals( 16, $d->getComponentValue( WGDateTime::H ) );
		$this->assertEquals( '2013-04-05 16:06:07', $d->getYMDHISString() );

		$d->setComponentValue( WGDateTime::I, 17 );
		$this->assertEquals( 17, $d->getComponentValue( WGDateTime::I ) );
		$this->assertEquals( '2013-04-05 16:17:07', $d->getYMDHISString() );

		$d->setComponentValue( WGDateTime::S, 18 );
		$this->assertEquals( 18, $d->getComponentValue( WGDateTime::S ) );
		$this->assertEquals( '2013-04-05 16:17:18', $d->getYMDHISString() );
	}

	public function test_componentValue2()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$d->setYear( 2013 );
		$this->assertEquals( 2013, $d->getYear() );
		$this->assertEquals( '2013-03-04 05:06:07', $d->getYMDHISString() );

		$d->setMonth( 4 );
		$this->assertEquals( 4, $d->getMonth() );
		$this->assertEquals( '2013-04-04 05:06:07', $d->getYMDHISString() );

		$d->setDay( 5 );
		$this->assertEquals( 5, $d->getDay() );
		$this->assertEquals( '2013-04-05 05:06:07', $d->getYMDHISString() );

		$d->setHour( 16 );
		$this->assertEquals( 16, $d->getHour() );
		$this->assertEquals( '2013-04-05 16:06:07', $d->getYMDHISString() );

		$d->setMin( 17 );
		$this->assertEquals( 17, $d->getMin() );
		$this->assertEquals( '2013-04-05 16:17:07', $d->getYMDHISString() );

		$d->setSec( 18 );
		$this->assertEquals( 18, $d->getSec() );
		$this->assertEquals( '2013-04-05 16:17:18', $d->getYMDHISString() );
	}

	public function test_componentValue3()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$d->setYearMonth( 2015, 4 );
		$this->assertEquals( '2015-04-04 05:06:07', $d->getYMDHISString() );

		$this->assertEquals( [ 2015, 4 ], $d->getYearMonth() );
	}

	/**
	 * @throws WGDateTimeException
	 */
	public function test_nendoMonth1()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$d->setNendoMonth( 2015, 4 );
		$this->assertEquals( '2015-04-04 05:06:07', $d->getYMDHISString() );

		$this->assertEquals( [ 2015, 4 ], $d->getNendoMonth() );
	}

	/**
	 * @throws WGDateTimeException
	 */
	public function test_nendoMonth2()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$d->setNendoMonth( 2015, 3 );
		$this->assertEquals( '2016-03-04 05:06:07', $d->getYMDHISString() );

		$this->assertEquals( [ 2015, 3 ], $d->getNendoMonth() );
	}

	/**
	 * @throws WGDateTimeException
	 */
	public function test_nendoMonth3()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$this->assertEquals( 2011, $d->getNendo() );
	}

	public function test_nendoMonthException1()
	{
		$this->expectException( WGDateTimeException::class );

		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$d->setNendoMonth( 2015, 0 );
	}

	public function test_nendoMonthException2()
	{
		$this->expectException( WGDateTimeException::class );

		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$d->setNendoMonth( 2015, 13 );
	}

	public function test_truncateYear()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$d->truncateYear();
		$this->assertEquals( '2012-01-01 00:00:00', $d->getYMDHISString() );
	}

	public function test_truncateMonth()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$d->truncateMonth();
		$this->assertEquals( '2012-03-01 00:00:00', $d->getYMDHISString() );
	}

	public function test_truncateDay()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$d->truncateDay();
		$this->assertEquals( '2012-03-04 00:00:00', $d->getYMDHISString() );
	}

	public function test_truncateHour()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$d->truncateHour();
		$this->assertEquals( '2012-03-04 05:00:00', $d->getYMDHISString() );
	}

	public function test_truncateMin()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$d->truncateMin();
		$this->assertEquals( '2012-03-04 05:06:00', $d->getYMDHISString() );
	}

	public function test_addYear()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$d->addYear( 1 );
		$this->assertEquals( '2013-03-04 05:06:07', $d->getYMDHISString() );

		$d->addYear( - 2 );
		$this->assertEquals( '2011-03-04 05:06:07', $d->getYMDHISString() );
	}

	public function test_addMonth()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$d->addMonth( 1 );
		$this->assertEquals( '2012-04-04 05:06:07', $d->getYMDHISString() );

		$d->addMonth( 9 );
		$this->assertEquals( '2013-01-04 05:06:07', $d->getYMDHISString() );

		$d->addMonth( - 1 );
		$this->assertEquals( '2012-12-04 05:06:07', $d->getYMDHISString() );

		$d->addMonth( - 9 );
		$this->assertEquals( '2012-03-04 05:06:07', $d->getYMDHISString() );
	}

	public function test_addDay()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$d->addDay( 1 );
		$this->assertEquals( '2012-03-05 05:06:07', $d->getYMDHISString() );

		$d->addDay( 27 );
		$this->assertEquals( '2012-04-01 05:06:07', $d->getYMDHISString() );

		$d->addDay( - 1 );
		$this->assertEquals( '2012-03-31 05:06:07', $d->getYMDHISString() );

		$d->addDay( - 27 );
		$this->assertEquals( '2012-03-04 05:06:07', $d->getYMDHISString() );
	}

	public function test_addHour()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$d->addHour( 1 );
		$this->assertEquals( '2012-03-04 06:06:07', $d->getYMDHISString() );

		$d->addHour( 18 );
		$this->assertEquals( '2012-03-05 00:06:07', $d->getYMDHISString() );

		$d->addHour( - 1 );
		$this->assertEquals( '2012-03-04 23:06:07', $d->getYMDHISString() );

		$d->addHour( - 18 );
		$this->assertEquals( '2012-03-04 05:06:07', $d->getYMDHISString() );

		$d->addHour( - 24 );
		$this->assertEquals( '2012-03-03 05:06:07', $d->getYMDHISString() );
	}

	public function test_addMin()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$d->addMin( 1 );
		$this->assertEquals( '2012-03-04 05:07:07', $d->getYMDHISString() );

		$d->addMin( 53 );
		$this->assertEquals( '2012-03-04 06:00:07', $d->getYMDHISString() );

		$d->addMin( - 1 );
		$this->assertEquals( '2012-03-04 05:59:07', $d->getYMDHISString() );

		$d->addMin( - 53 );
		$this->assertEquals( '2012-03-04 05:06:07', $d->getYMDHISString() );

		$d->addMin( - 1440 );
		$this->assertEquals( '2012-03-03 05:06:07', $d->getYMDHISString() );
	}

	public function test_addSec()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$d->addSec( 1 );
		$this->assertEquals( '2012-03-04 05:06:08', $d->getYMDHISString() );

		$d->addSec( 52 );
		$this->assertEquals( '2012-03-04 05:07:00', $d->getYMDHISString() );

		$d->addSec( - 1 );
		$this->assertEquals( '2012-03-04 05:06:59', $d->getYMDHISString() );

		$d->addSec( - 52 );
		$this->assertEquals( '2012-03-04 05:06:07', $d->getYMDHISString() );

		$d->addSec( - 86400 );
		$this->assertEquals( '2012-03-03 05:06:07', $d->getYMDHISString() );
	}

	public function test_getDayOfWeek()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );
		$this->assertEquals(0, $d->getDayOfWeek());

		$d->set( 2012, 3, 10, 5, 6, 7 );
		$this->assertEquals(6, $d->getDayOfWeek());
	}

	public function test_getByFormat()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$this->assertEquals( '20120304050607', $d->getByFormat( 'YmdHis' ) );
		$this->assertEquals( false, $d->getByFormat( '' ) );

		$this->assertEquals( '2012-03-04', $d->getYMDString() );
		$this->assertEquals( '2012-03', $d->getYMString() );
		$this->assertEquals( '05:06:07', $d->getHISString() );
		$this->assertEquals( '05:06', $d->getHIString() );
	}

	public function test_YMIndex()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$this->assertEquals( 2012 * 12 + 3 - 1, $d->getYMIndex() );

		$d->setYMIndex( 2015 * 12 + 4 - 1 );
		$this->assertEquals( '2015-04-01 00:00:00', $d->getYMDHISString() );
	}

	public function test_copy1()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$e = $d->copy();

		$this->assertEquals( $d->getUnixTime(), $e->getUnixTime() );

		$d->addYear(1);
		$e->addYear(2);

		$this->assertEquals( '2013-03-04 05:06:07', $d->getYMDHISString() );
		$this->assertEquals( '2014-03-04 05:06:07', $e->getYMDHISString() );
	}

	public function test_copy2()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$e = new WGDateTime();
		$e->copyFrom( $d );

		$this->assertEquals( $d->getUnixTime(), $e->getUnixTime() );

		$d->addYear(1);
		$e->addYear(2);

		$this->assertEquals( '2013-03-04 05:06:07', $d->getYMDHISString() );
		$this->assertEquals( '2014-03-04 05:06:07', $e->getYMDHISString() );
	}

	/**
	 * @throws WGDateTimeException
	 */
	public function test_expression1()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$e = new WGDateTime();
		$e->set( 2012, 3, 4, 5, 6, 8 );

		$this->assertEquals( false, $d->compare( '==', $e ) );
		$this->assertEquals( false, $d->compare( '===', $e ) );
		$this->assertEquals( true, $d->compare( '<', $e ) );
		$this->assertEquals( true, $d->compare( '<=', $e ) );
		$this->assertEquals( false, $d->compare( '>', $e ) );
		$this->assertEquals( false, $d->compare( '>=', $e ) );
		$this->assertEquals( true, $d->compare( '!=', $e ) );
		$this->assertEquals( true, $d->compare( '!==', $e ) );
	}

	/**
	 * @throws WGDateTimeException
	 */
	public function test_expression2()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 8 );

		$e = new WGDateTime();
		$e->set( 2012, 3, 4, 5, 6, 7 );

		$this->assertEquals( false, $d->compare( '==', $e ) );
		$this->assertEquals( false, $d->compare( '===', $e ) );
		$this->assertEquals( false, $d->compare( '<', $e ) );
		$this->assertEquals( false, $d->compare( '<=', $e ) );
		$this->assertEquals( true, $d->compare( '>', $e ) );
		$this->assertEquals( true, $d->compare( '>=', $e ) );
		$this->assertEquals( true, $d->compare( '!=', $e ) );
		$this->assertEquals( true, $d->compare( '!==', $e ) );
	}

	/**
	 * @throws WGDateTimeException
	 */
	public function test_expression3()
	{
		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$e = new WGDateTime();
		$e->set( 2012, 3, 4, 5, 6, 7 );

		$this->assertEquals( true, $d->compare( '==', $e ) );
		$this->assertEquals( true, $d->compare( '===', $e ) );
		$this->assertEquals( false, $d->compare( '<', $e ) );
		$this->assertEquals( true, $d->compare( '<=', $e ) );
		$this->assertEquals( false, $d->compare( '>', $e ) );
		$this->assertEquals( true, $d->compare( '>=', $e ) );
		$this->assertEquals( false, $d->compare( '!=', $e ) );
		$this->assertEquals( false, $d->compare( '!==', $e ) );
	}

	/**
	 * @throws WGDateTimeException
	 */
	public function test_expressionException()
	{
		$this->expectException( WGDateTimeException::class );

		$d = new WGDateTime();
		$d->set( 2012, 3, 4, 5, 6, 7 );

		$e = new WGDateTime();
		$e->set( 2012, 3, 4, 5, 6, 7 );

		$d->compare( '====', $e );
	}
}
