<?php
/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/dbms_property.php';

class WGDBMSPropertyPostgreSQL extends WGDBMSProperty
{
	const FIELD_TYPE_N = '/^(int|smallint|bigint)/';
	const FIELD_TYPE_TD = '/^(date)/';
	const FIELD_TYPE_TT = '/^(time)/';
	const FIELD_TYPE_TS = '/^(timestamp)/';
	const FIELD_TYPE_S = '/^(char|text|varchar|json)/';
	const FIELD_TYPE_D = '/^(double|real|numeric)/';
	const FIELD_TYPE_B = '/^bool/';
	const FIELD_TYPE_BOOL_TRUE = 't';
	const FIELD_TYPE_BOOL_FALSE = 'f';

	static protected function getOID( WGDBMS $dbms, string $tableName ): array
	{
		list( $oid, $nspname, $relname ) =
			$dbms->QQ(
				'SELECT c.oid,n.nspname,c.relname FROM pg_catalog.pg_class c ' .
				'LEFT JOIN pg_catalog.pg_namespace n ON n.oid=c.relnamespace ' .
				'WHERE pg_catalog.pg_table_is_visible(c.oid) ' .
				'AND c.relname=%s;', $dbms->S( $tableName ) );

		return [ $oid, $nspname, $relname ];
	}

	static public function detectFields( WGDBMS $dbms, string $tableName ): array
	{
		list( $oid, , ) = static::getOID( $dbms, $tableName );
		$dbms->Q(
			'SELECT a.attname,pg_catalog.format_type(a.atttypid,a.atttypmod),a.attnotnull ' .
			'FROM pg_catalog.pg_attribute a ' .
			'WHERE a.attrelid=%s AND a.attnum>0 AND NOT a.attisdropped;',
			$dbms->S( $oid ) );

		$fields = [];
		foreach ( $dbms->FALL() as $f )
		{
			list( $name, $format_type, $notnull ) = $f;
			$type = static::getFieldTypeFromFormat( $format_type );
			if ( $type === false )
			{
				wg_log_write( WGLOG_FATAL, 'Unrecognized field type, %s on PostgreSQL/WGMModel', $format_type );
			}

			$fields[ $name ] =
				new WGMModelField(
					$type, $format_type, ( $notnull === 't' ), $name
				);
		}

		return $fields;
	}

	static public function detectPrimaryKeys( WGDBMS $dbms, string $tableName ): array
	{
		list( $oid, , ) = static::getOID( $dbms, $tableName );
		list( $pk ) = $dbms->QQ(
			'SELECT c2.relname ' .
			'FROM pg_catalog.pg_class c, pg_catalog.pg_class c2, pg_catalog.pg_index i ' .
			'WHERE c.oid = %s AND c.oid = i.indrelid AND i.indexrelid = c2.oid AND i.indisprimary = true;',
			$dbms->S( $oid ) );
		if ( empty( $pk ) )
		{
			return [];
		}

		list( $indexOid, , ) = static::getOID( $dbms, $pk );
		$pks = [];
		$dbms->Q(
			'SELECT a.attname ' .
			'FROM pg_catalog.pg_attribute a, pg_catalog.pg_index i ' .
			'WHERE a.attrelid = %s AND a.attnum > 0 AND NOT a.attisdropped AND a.attrelid = i.indexrelid ' .
			'ORDER BY a.attnum;',
			$dbms->S( $indexOid ) );
		foreach ( $dbms->FALL() as $f )
		{
			$pks[] = $f['attname'];
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
