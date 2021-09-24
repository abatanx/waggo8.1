<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

abstract class WGDBMSProperty
{
	const N = 0, S = 1, B = 2, TD = 3, TT = 4, TS = 5, D = 6;

	static private ?WGDBMSProperty $property = null;

	const FIELD_TYPE_N = '';
	const FIELD_TYPE_TD = '';
	const FIELD_TYPE_TT = '';
	const FIELD_TYPE_TS = '';
	const FIELD_TYPE_S = '';
	const FIELD_TYPE_D = '';
	const FIELD_TYPE_B = '';
	const FIELD_TYPE_BOOL_TRUE = '';
	const FIELD_TYPE_BOOL_FALSE = '';

	static public function property(): WGDBMSProperty
	{
		if ( is_null(self::$property) )
		{
			self::$property = new static();
		}

		return self::$property;
	}

	/**
	 * @param WGDBMS $dbms
	 * @param string $tableName
	 *
	 * @return WGMModelField[]
	 */
	abstract static public function detectFields( WGDBMS $dbms, string $tableName ): array;

	/**
	 * @param WGDBMS $dbms
	 * @param string $tableName
	 *
	 * @return WGMModelField[]
	 */
	abstract static public function detectPrimaryKeys( WGDBMS $dbms, string $tableName ): array;

	/**
	 *
	 */
	abstract static public function getFieldTypeFromFormat( string $fieldType ): int|false;

}
