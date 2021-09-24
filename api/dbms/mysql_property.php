<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/dbms_property.php';

class WGDBMSPropertyMySQL extends WGDBMSProperty
{
	const FIELD_TYPE_N = '/^(int|smallint)/';
	const FIELD_TYPE_TD = '/^(date)/';
	const FIELD_TYPE_TT = '/^(time)/';
	const FIELD_TYPE_TS = '/^(timestamp)/';
	const FIELD_TYPE_S = '/^(char|text|varchar|json)/';
	const FIELD_TYPE_D = '/^(double|real|numeric)/';
	const FIELD_TYPE_B = '/^tinyint\(1\)/';
	const FIELD_TYPE_BOOL_TRUE = '1';
	const FIELD_TYPE_BOOL_FALSE = '0';

	static public function detectFields( WGDBMS $dbms, string $tableName ): array
	{
		$dbms->Q( 'DESCRIBE %s', $tableName );

		$fields = [];
		foreach ( $dbms->FALL() as $f )
		{
			list( $name, $format_type, $null, , , ) = $f;
			$type = static::getFieldTypeFromFormat( $format_type );
			if ( $type === false )
			{
				wg_log_write( WGLOG_FATAL, 'Unrecognized field type, %s on MySQL/WGMModel', $format_type );
			}

			$fields[ $name ] = new WGMModelField(
				$type, $format_type, ( $null === 'YES' ), $name
			);
		}

		return $fields;
	}

	static public function detectPrimaryKeys( WGDBMS $dbms, string $tableName ): array
	{
		$pks = [];
		$dbms->Q( 'DESCRIBE %s', $tableName );
		foreach ( $dbms->FALL() as $f )
		{
			list( $name, , , $key, , ) = $f;
			if ( $key === 'PRI' )
			{
				$pks[] = $name;
			}
		}

		return $pks;
	}


	static public function getFieldTypeFromFormat( string $fieldType ): int|false
	{
		if ( preg_match( static::FIELD_TYPE_N, $fieldType ) )
		{
			return static::N;
		}
		else if ( preg_match( static::FIELD_TYPE_TS, $fieldType ) )
		{
			return static::TS;
		}
		else if ( preg_match( static::FIELD_TYPE_TT, $fieldType ) )
		{
			return static::TT;
		}
		else if ( preg_match( static::FIELD_TYPE_TD, $fieldType ) )
		{
			return static::TD;
		}
		else if ( preg_match( static::FIELD_TYPE_S, $fieldType ) )
		{
			return static::S;
		}
		else if ( preg_match( static::FIELD_TYPE_D, $fieldType ) )
		{
			return static::D;
		}
		else if ( preg_match( static::FIELD_TYPE_B, $fieldType ) )
		{
			return static::B;
		}
		else
		{
			return false;
		}
	}
}
