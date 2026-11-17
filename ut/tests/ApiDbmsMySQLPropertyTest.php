<?php
/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

if ( ! defined( 'WG_UNITTEST' ) )
{
	define( 'WG_UNITTEST', true );
}

require_once __DIR__ . '/../../api/dbms/mysql.php';
require_once __DIR__ . '/../../framework/m/WGMModelField.php';

class ApiDbmsMySQLPropertyTest extends TestCase
{
	public function test_get_field_type_from_format_maps_longtext_to_string(): void
	{
		$this->assertSame(
			WGDBMSPropertyMySQL::S,
			WGDBMSPropertyMySQL::getFieldTypeFromFormat( 'longtext' )
		);
	}

	public function test_get_field_type_from_format_keeps_existing_string_types(): void
	{
		foreach ( [ 'char(1)', 'text', 'varchar(255)', 'json' ] as $formatType )
		{
			$this->assertSame(
				WGDBMSPropertyMySQL::S,
				WGDBMSPropertyMySQL::getFieldTypeFromFormat( $formatType ),
				$formatType
			);
		}
	}

	public function test_get_field_type_from_format_keeps_non_string_types_and_rejects_blob(): void
	{
		$expectedTypes = [
			'int(11)'       => WGDBMSPropertyMySQL::N,
			'timestamp'     => WGDBMSPropertyMySQL::TS,
			'tinyint(1)'    => WGDBMSPropertyMySQL::B,
			'double'        => WGDBMSPropertyMySQL::D,
		];

		foreach ( $expectedTypes as $formatType => $expectedType )
		{
			$this->assertSame(
				$expectedType,
				WGDBMSPropertyMySQL::getFieldTypeFromFormat( $formatType ),
				$formatType
			);
		}

		$this->assertFalse( WGDBMSPropertyMySQL::getFieldTypeFromFormat( 'blob' ) );
	}

	public function test_detect_fields_builds_string_model_field_from_longtext_description(): void
	{
		$dbms = $this->createMock( WGDBMSMySQL::class );
		$dbms->expects( $this->once() )
			->method( 'Q' )
			->with( 'DESCRIBE %s', 'articles' )
			->willReturn( 1 );
		$dbms->expects( $this->once() )
			->method( 'FALL' )
			->willReturn( [ [ 'body', 'longtext', 'YES', '', '', '' ] ] );

		$fields = WGDBMSPropertyMySQL::detectFields( $dbms, 'articles' );

		$this->assertArrayHasKey( 'body', $fields );
		$this->assertInstanceOf( WGMModelField::class, $fields['body'] );
		$this->assertSame( WGDBMSPropertyMySQL::S, $fields['body']->getType() );
		$this->assertSame( 'longtext', $fields['body']->getFormatType() );
		$this->assertSame( 'body', $fields['body']->getName() );
	}
}
