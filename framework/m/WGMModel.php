<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 * @noinspection PhpUnused
 */
declare( strict_types=1 );

require_once __DIR__ . '/../../waggo.php';
require_once __DIR__ . '/../v8/WGV8Object.php';
require_once __DIR__ . '/WGMModelField.php';
require_once __DIR__ . '/WGMModelOrder.php';
require_once __DIR__ . '/WGMModelJoin.php';
require_once __DIR__ . '/WGMModelGetKeys.php';
require_once __DIR__ . '/WGMModelFilter.php';
require_once __DIR__ . '/WGMVarsObject.php';

global $WGMModelID;
$WGMModelID = [];

/**
 * OR/M
 */
class WGMModel
{
	public WGDBMS $dbms;

	public array $uniqueIds;
	public array $avars, $vars;

	/**
	 * @var WGMModelField[]
	 */
	public array $fields;

	/**
	 * @var string[]
	 */
	public array $primaryKeys;

	protected bool $isModelDebug;
	public array $assign;
	public string $tableName;
	public string $aliasName;
	public array $backvars, $initYmdKeys, $updYmdKeys;
	public int $recs;

	protected WGMModelFilter $defaultFilter;
	protected array $conditions;

	protected ?WGV8BasicPagination $pager;

	/**
	 * @var WGMModelJoin[]
	 */
	protected array $joins;

	/**
	 * @var WGMModelOrder[]
	 */
	protected array $orderArray;
	protected int $orderOrder;

	protected ?string $offsetKeyword, $limitKeyword;

	public function __construct( string $tableName, ?WGDBMS $dbms = null )
	{
		global $WGMModelID;
		$this->isModelDebug = WG_MODELDEBUG;

		$this->uniqueIds = [];

		$kid = $tableName[0];
		if ( empty( $WGMModelID[ $kid ] ) )
		{
			$WGMModelID[ $kid ] = 1;
		}
		else
		{
			$WGMModelID[ $kid ] ++;
		}

		$this->aliasName = $tableName;
		$this->dbms      = ( is_null( $dbms ) ) ? _QC() : $dbms;

		$this->defaultFilter = new WGMModelFILTER();

		$this->tableName   = $tableName;
		$this->fields      = [];
		$this->primaryKeys = [];

		$this->assign      = [];
		$this->vars        = [];
		$this->avars       = [];
		$this->backvars    = [];
		$this->joins       = [];
		$this->conditions  = [];
		$this->initYmdKeys = [];
		$this->updYmdKeys  = [];
		$this->orderArray  = [];
		$this->orderOrder  = PHP_INT_MAX;
		$this->recs        = 0;
		$this->pager       = null;

		$this->offsetKeyword = null;
		$this->limitKeyword  = null;

		$this->fields      = $this->dbms->property()->detectFields( $this->dbms, $tableName );
		$this->primaryKeys = $this->dbms->property()->detectPrimaryKeys( $this->dbms, $tableName );
	}

	public static function _( string $tableName, ?WGDBMS $dbms = null ): self
	{
		return new static( $tableName, $dbms );
	}

	protected function logInfo( string $msg, mixed ...$args ): void
	{
		if ( $this->isModelDebug )
		{
			wg_log_write( WGLOG_INFO, $msg, ...$args );
		}
	}

	protected function logInfoDump( string $msg, mixed ...$args ): void
	{
		if ( $this->isModelDebug )
		{
			wg_log_write( WGLOG_INFO, $msg, ...$args );
		}
	}

	protected function logWarning( string $msg, mixed ...$args ): void
	{
		wg_log_write( WGLOG_WARNING, $msg, ...$args );
	}

	protected function logError( string $msg, mixed ...$args ): void
	{
		wg_log_write( WGLOG_ERROR, $msg, ...$args );
	}

	protected function logFatal( string $msg, mixed ...$args ): void
	{
		wg_log_write( WGLOG_FATAL, $msg, ...$args );
	}

	protected static function arrayFlatten( array $array ): array
	{
		$result = [];
		foreach ( $array as $v )
		{
			if ( is_array( $v ) )
			{
				$result = array_merge( $result, self::arrayFlatten( $v ) );
			}
			else
			{
				$result[] = $v;
			}
		}

		return $result;
	}

	public function setField( string $keyField, string $fieldType, $func ): void
	{
		$type = $this->dbms->property()->getFieldTypeFromFormat( $fieldType );
		if ( $type === false )
		{
			$this->logFatal( 'Unrecognized field type, %s', $fieldType );
		}

		$this->fields[ $keyField ] = new WGMModelField( $type, $fieldType, false, $this->expansion( $func ) );
	}

	public function getTable(): string
	{
		return $this->tableName;
	}

	public function getAlias(): string
	{
		return $this->aliasName;
	}

	public function setAlias( string $aliasName ): self
	{
		$this->aliasName = $aliasName;

		return $this;
	}

	public function getFields(): array
	{
		return array_keys( $this->fields );
	}

	public function getFieldType( string $keyField ): int|false
	{
		return ! empty( $this->fields[ $keyField ]->getType() ) ? $this->fields[ $keyField ]->getType() : false;
	}

	public function getFieldFormat( string $keyField ): string|false
	{
		return ! empty( $this->fields[ $keyField ]->getFormatType() ) ? $this->fields[ $keyField ]->getFormatType() : false;
	}

	public function IsNotNullField( string $keyField ): bool
	{
		return $this->fields[ $keyField ]->isNotNull();
	}

	public function IsAllowNullField( string $keyField ): bool
	{
		return ! $this->fields[ $keyField ]->isNotNull();
	}

	public function getPrimaryKeys(): array|false
	{
		return $this->primaryKeys;
	}

	public function expansion( string $exp, ?string $aliasPrefix = null ): string
	{
		$r = [];
		$t = '';
		$s = preg_split( '//u', $exp, - 1, PREG_SPLIT_NO_EMPTY );

		$get   = function ( &$a ) {
			return count( $a ) > 0 ? array_shift( $a ) : '';
		};
		$peek  = function ( $a ) {
			return count( $a ) > 0 ? $a[0] : '';
		};
		$queue = function ( $f, &$r, &$t, $c = '' ) {
			if ( $t !== '' )
			{
				$r[] = [ $f, $t ];
			}
			$t = $c;
		};

		while ( count( $s ) > 0 )
		{
			$c = $get( $s );
			if ( $c === '\'' )
			{
				$x = $c;
				$queue( 1, $r, $t, $c );
				do
				{
					$c = $get( $s );
					$d = $peek( $s );
					if ( ( $c === $x && $d === $x ) || $c === '\\' )
					{
						$c .= $get( $s );
					}
					$t .= $c;
				}
				while ( $c !== '' && $c !== $x );
				$queue( 0, $r, $t );
			}
			else
			{
				$t .= $c;
			}
		}
		$queue( 1, $r, $t );

		$g = function ( $m ) use ( $aliasPrefix ) {
			return ( $aliasPrefix ?? $this->getAlias() ) . '.' . $m[1];
		};

		return implode( '', array_map( function ( $v ) use ( $g ) {
			return $v[0] === 1 ? preg_replace_callback( '/{(\w+?)}/', $g, $v[1] ) : $v[1];
		}, $r ) );
	}

	public function setAutoTimestamp( $initymds = [ 'initymd' ], $updymds = [ 'updymd' ] ): self
	{
		if ( ! is_array( $initymds ) || ! is_array( $updymds ) )
		{
			$this->logFatal( 'setAutoTimestamp is not an array' );
		}
		$this->initYmdKeys = $initymds;
		$this->updYmdKeys  = $updymds;

		return $this;
	}

	public function getRecs(): int
	{
		return $this->recs;
	}

	public function setFilter( string $keyField, WGMModelFilter $modelFilter ): self
	{
		$this->assign[ $keyField ]['filter'] = $modelFilter;

		return $this;
	}

	public function assign( string $keyField, WGV8Object $view, ?WGMModelFilter $modelFilter = null ): self
	{
		if ( ! isset( $this->fields[ $keyField ] ) )
		{
			$this->logFatal( '\'%s\' not found.', $keyField );
		}

		$this->assign[ $keyField ]['viewobj'] = $view;
		$this->assign[ $keyField ]['filter']  = ( $modelFilter instanceof WGMModelFILTER ) ? $modelFilter : $this->defaultFilter;

		return $this;
	}

	public function release( string $keyField ): self
	{
		unset( $this->assign[ $keyField ] );

		return $this;
	}

	protected function checkNullField( string $keyField, ?string $v ): self
	{
		if ( $this->fields[ $keyField ]->isNotNull() && ( strtolower( $v ) === 'null' || is_null( $v ) ) )
		{
			$this->logFatal( "Field '$keyField' does not allow NULL." );
		}

		return $this;
	}

	protected function posValue( string $pos ): ?array
	{
		if ( preg_match( '/\(([\-0-9.]+),([\-0-9.]+)\)/', $pos, $m ) )
		{
			return [ $m[1], $m[2] ];
		}
		else
		{
			return null;
		}
	}

	protected function fieldValue( string $keyField, mixed $value, string $direction ): mixed
	{
		if ( $direction !== 'PHP' && $direction !== 'DB' )
		{
			$this->logFatal( 'Internal error on fieldValue.' );
		}

		$isAllowNULL = ! $this->fields[ $keyField ]->isNotNull();
		$v           = null;
		switch ( $this->fields[ $keyField ]->getType() )
		{
			case $this->dbms->property()::N:
				$v = ( $direction === 'DB' ) ? $this->dbms->N( $value, $isAllowNULL ) :
					( is_null( $value ) ? null : (int) $value );
				break;
			case $this->dbms->property()::S:
				$v = ( $direction === 'DB' ) ? $this->dbms->S( $value, $isAllowNULL ) :
					( is_null( $value ) ? null : (string) $value );
				break;
			case $this->dbms->property()::B:
				$v = ( $direction === 'DB' ) ? $this->dbms->B( $value, $isAllowNULL ) :
					( is_null( $value ) ? null : ( (string) $value === $this->dbms->property()::FIELD_TYPE_BOOL_TRUE ) );
				break;
			case $this->dbms->property()::TD:
				$v = ( $direction === 'DB' ) ? $this->dbms->TD( $value, $isAllowNULL ) :
					( is_null( $value ) ? null : (string) $value );
				break;
			case $this->dbms->property()::TT:
				$v = ( $direction === 'DB' ) ? $this->dbms->TT( $value, $isAllowNULL ) :
					( is_null( $value ) ? null : (string) $value );
				break;
			case $this->dbms->property()::TS:
				$v = ( $direction === 'DB' ) ? $this->dbms->TS( $value, $isAllowNULL ) :
					( is_null( $value ) ? null : (string) $value );
				break;
			case $this->dbms->property()::D:
				$v = ( $direction === 'DB' ) ? $this->dbms->D( $value, $isAllowNULL ) :
					( is_null( $value ) ? null : (float) $value );
				break;
			default:
				$this->logFatal( 'Field \'%s\' conversion failed.', $keyField );
		}

		$this->logInfo( '[%s] %s.%s src[%s] [to %s] dst[%s]',
			$this->tableName, $this->aliasName,
			$keyField, $value, $direction,
			$v );

		if ( $direction === 'DB' && $this->fields[ $keyField ]->isNotNull() && $v === 'null' )
		{
			$this->logFatal( 'Field \'%s\' does not allow NULL.', $keyField );
		}

		return $v;
	}

	/** @noinspection PhpInconsistentReturnPointsInspection */
	protected function compareField( string $keyField, mixed $v1, mixed $v2 ): bool
	{
		switch ( $this->fields[ $keyField ]->getType() )
		{
			case $this->dbms->property()::N:
				return ( $v1 == $v2 );
			case $this->dbms->property()::S:
			case $this->dbms->property()::B:
			case $this->dbms->property()::D:
				return ( $v1 === $v2 );
			case $this->dbms->property()::TD:
				return ( wg_timediff_second( $v1 ?? '0001-01-01', $v2 ?? '0001-01-01' ) === 0 );
			case $this->dbms->property()::TT:
				return ( wg_timediff_second( $v1 ?? '00:00:00', $v2 ?? '00:00:00' ) === 0 );
			case $this->dbms->property()::TS:
				return ( wg_timediff_second( $v1 ?? '0001-01-01 00:00:00', $v2 ?? '0001-01-01 00:00:00' ) === 0 );
		}
		$this->logFatal( 'Unrecognized field type, \'%s\'.', $keyField );
	}

	protected function setAssignedValue( string $keyField, mixed $value ): self
	{
		if ( isset( $this->assign[ $keyField ]['viewobj'] ) )
		{
			$this->assign[ $keyField ]['filter']->modelToView(
				$this->assign[ $keyField ]['viewobj'],
				$this->assign[ $keyField ]['filter']->output( $value )
			);
		}
		else
		{
			$this->vars[ $keyField ] =
				isset( $this->assign[ $keyField ]['filter'] ) ?
					$this->assign[ $keyField ]['filter']->output( $value ) : $value;
		}

		return $this;
	}

	protected function getAssignedValue( string $keyField ): mixed
	{
		if ( isset( $this->assign[ $keyField ]['viewobj'] ) &&
			 ! $this->assign[ $keyField ]['viewobj']->isShowOnly() )
		{
			return $this->assign[ $keyField ]['filter']->input(
				$this->assign[ $keyField ]['filter']->viewToModel(
					$this->assign[ $keyField ]['viewobj']
				)
			);
		}
		else
		{
			return isset( $this->assign[ $keyField ]['filter'] ) ?
				$this->assign[ $keyField ]['filter']->input(
					$this->vars[ $keyField ] ?? null
				) : $this->vars[ $keyField ] ?? null;
		}
	}

	public function unJoin(): self
	{
		$this->joins = [];

		return $this;
	}

	public function left( WGMModel $model, array $on, ?string $leftConstraint = null, ?string $rightConstraint = null ): self
	{
		$this->joins[] = WGMModelJoin::_( WGMModelJoin::LEFT, $model, $on, $leftConstraint, $rightConstraint );

		return $this;
	}

	public function right( WGMModel $model, array $on, ?string $leftConstraint = null, ?string $rightConstraint = null ): self
	{
		$this->joins[] = WGMModelJoin::_( WGMModelJoin::RIGHT, $model, $on, $leftConstraint, $rightConstraint );

		return $this;
	}

	public function inner( WGMModel $model, array $on, ?string $leftConstraint = null, ?string $rightConstraint = null ): self
	{
		$this->joins[] = WGMModelJoin::_( WGMModelJoin::INNER, $model, $on, $leftConstraint, $rightConstraint );

		return $this;
	}

	/**
	 * 特定のフィールドのみのデータを、配列として生成する。
	 *
	 * @param string $dataField データとなるフィールド
	 *
	 * @return array データ配列[$dataFieldの値, ...]
	 */
	public function getFieldVars( string $dataField ): array
	{
		$result = [];
		foreach ( $this->avars as $vars )
		{
			$result[] = $vars[ $dataField ];
		}

		return $result;
	}

	/**
	 * 選択肢用の連想配列を生成する。
	 *
	 * @param string $keyField 選択肢のキーとなるフィールド
	 * @param string $dataField 選択肢のデータとなるフィールド
	 *
	 * @return array 選択肢を構成する連想配列[$keyFieldの値=>$dataFieldの値, ...]
	 */
	public function getSelectVars( string $keyField, string $dataField ): array
	{
		$result = [];
		foreach ( $this->avars as $vars )
		{
			$result[ $vars[ $keyField ] ] = $vars[ $dataField ];
		}

		return $result;
	}

	/**
	 * 追加WHERE句を設定する。追加される条件はすべて AND で組み合わされます。
	 * フィールド名は{}で囲むことによって、実行時に実際のフィールド名に変換されます。
	 * 追加した条件は、条件を識別するための condition-id を返します。
	 *
	 * @param string $format 条件書式。書式パラメータがない場合は、書式を適用しません。
	 * @param mixed ...$args 書式パラメータ
	 *
	 * @return string 追加WHERE句を識別するための condition-id 。
	 */
	public function addConditionWithId( string $format, mixed ...$args ): string
	{
		if ( ! isset( $this->uniqueIds['cond'] ) )
		{
			$this->uniqueIds['cond'] = 0;
		}

		$conditionId = sprintf( 'cond-%d', $this->uniqueIds['cond'] ++ );

		$condition = count( $args ) === 0 ? $format : vsprintf( $format, $args );

		$this->conditions[ $conditionId ] = $this->expansion( $condition );

		return $conditionId;
	}

	/**
	 * 追加WHERE句を設定する。追加される条件はすべて AND で組み合わされます。
	 * フィールド名は{}で囲むことによって、実行時に実際のフィールド名に変換されます。
	 *
	 * @param string $format 条件書式。書式パラメータがない場合は、書式を適用しません。
	 * @param mixed ...$args 書式パラメータ
	 *
	 * @return self
	 */
	public function addCondition( string $format, mixed ...$args ): self
	{
		$this->addConditionWithId( $format, ...$args );

		return $this;
	}


	/**
	 * 追加WHERE句を削除する。
	 *
	 * @param string $conditionId この条件の識別名(任意)
	 *
	 * @return self
	 */
	public function delCondition( string $conditionId ): self
	{
		$this->conditions[ $conditionId ] = null;
		unset( $this->conditions[ $conditionId ] );

		return $this;
	}

	/**
	 * 追加WHERE句の条件を配列で取得する。
	 * @return array 追加WHERE句の文字列。
	 */
	public function getConditions(): array
	{
		return $this->conditions;
	}

	/**
	 * 追加WHERE句をすべてクリアする。
	 * @return self
	 */
	public function clearConditions(): self
	{
		$this->conditions = [];

		return $this;
	}

	public function orderby( ...$args ): self
	{
		$args = self::arrayFlatten( $args );
		$this->logInfo( 'WGMModel::orderby( %s )', implode( ' , ', $args ) );

		$prevOrder = null;

		$orders = [];
		foreach ( $args as $arg )
		{
			if ( is_int( $arg ) )
			{
				$priorityNumber = $arg;
				if ( $priorityNumber >= WGMModelOrder::STARTING_PRIORITY_NUMBER )
				{
					wg_log_write( WGLOG_FATAL, 'Priority number must be less than %d.', WGMModelOrder::STARTING_PRIORITY_NUMBER );
				}
				if ( ! is_null( $prevOrder ) )
				{
					$prevOrder->setPriority( $priorityNumber );
					$prevOrder = null;
				}
				else
				{
					wg_log_write( WGLOG_FATAL, 'To specify the priority number, specify it after the order field.' );
				}
			}
			else
			{
				$orders[] = ( $prevOrder = WGMModelOrder::_( $this, $arg ) );
			}
		}

		$this->orderArray = $orders;

		return $this;
	}

	public function offset( ?int $offset = null, ?int $limit = null ): self
	{
		$this->logInfo( 'WGMModel::offset( %s )', $offset ?? '', $limit ?? '' );

		if ( wg_is_dbms_postgresql() )
		{
			$this->offsetKeyword = ! is_null( $offset ) ? " OFFSET $offset" : '';
			$this->limitKeyword  = ! is_null( $limit ) ? " LIMIT $limit" : '';
		}
		else if ( wg_is_dbms_mysql() || wg_is_dbms_mariadb() )
		{
			if ( ! is_null( $limit ) )
			{
				$this->offsetKeyword = '';
				$this->limitKeyword  = ! is_null( $offset ) ? " LIMIT $offset,$limit" : "LIMIT $limit";
			}
		}

		return $this;
	}

	public function pager( WGV8BasicPagination $pager ): self
	{
		$this->pager = $pager;

		return $this;
	}

	public function findJoinModel( string $tableName ): WGMModel|false
	{
		foreach ( $this->joins as $join )
		{
			if ( $join->getJoinModel()->getTable() === $tableName )
			{
				return $join->getJoinModel();
			}
		}

		return false;
	}


	public function whereOptCondExpression(): array
	{
		$wheres = [];
		foreach ( $this->joins as $join )
		{
			$wheres = array_merge( $wheres, $join->getJoinModel()->whereOptCondExpression() );
		}
		foreach ( $this->getConditions() as $w )
		{
			$wheres[] = $w;
		}

		return $wheres;
	}

	public function whereCondExpression( array $keys ): array
	{
		$wheres = [];
		foreach ( $keys as $k )
		{
			if ( $k instanceof WGMModelGetKeys )
			{
				$m      = $k->getModel();
				$wheres = array_merge( $wheres, $m->whereCondExpression( $k->getKeys() ) );
			}
			else
			{
				$av = $this->getAssignedValue( $k );
				$v  = $this->fieldValue( $k, $av, 'DB' );
				$this->checkNullField( $k, $v );
				$wheres[] = $this->aliasName . '.' . $k . '=' . $v;
			}
		}

		return array_merge( $wheres, $this->whereOptCondExpression() );
	}

	public function whereExpression( array $keys ): array
	{
		$wheres = [];
		foreach ( $keys as $k )
		{
			$av = $this->getAssignedValue( $k );
			$v  = $this->fieldValue( $k, $av, 'DB' );
			$this->checkNullField( $k, $v );
			$wheres[] = $k . '=' . $v;
		}

		return $wheres;
	}

	public function getJoinExternalFields(): array
	{
		$fields = [];
		foreach ( $this->getFields() as $f )
		{
			$fields[] = [
				$this->fields[ $f ]->getNameAppendingPrefix( $this->getAlias() ),
				$this->fields[ $f ]->getNameAppendingPrefix( $this->getAlias() )
			];
		}
		foreach ( $this->joins as $join )
		{
			$fields = array_merge( $fields, $join->getJoinModel()->getJoinExternalFields() );
		}

		return $fields;
	}

	public function getJoinTables( string $base ): string
	{
		foreach ( $this->joins as $join )
		{
			$on = [];
			foreach ( $join->getOn() as $l => $r )
			{
				$leftModel  = null;
				$leftField  = null;
				$rightModel = null;
				$rightField = null;

				$l = is_int( $l ) ? $r : $l;
				if ( is_string( $l ) && in_array( $l, $this->getFields() ) )
				{
					$leftModel = $this;
					$leftField = $l;
				}

				if ( ! is_array( $r ) && in_array( $r, $join->getJoinModel()->getFields() ) )
				{
					$rightModel = $join->getJoinModel();
					$rightField = $r;
				}
				else if ( is_array( $r ) )
				{
					list( $rm, $rf ) = $r;
					if ( $rm instanceof WGMModel && is_string( $rf ) && in_array( $rf, $rm->getFields() ) )
					{
						$rightModel = $rm;
						$rightField = $rf;
					}
				}

				if ( is_null( $leftModel ) || is_null( $leftField ) )
				{
					$this->logFatal( 'Joined LEFT, no \'%s\' field.', $l );
				}

				if ( is_null( $rightModel ) || is_null( $rightField ) )
				{
					$this->logFatal( 'Joined RIGHT, no \'%s\' field.', $l );
				}

				$on[] = $leftModel->getAlias() . '.' . $leftField . '=' . $rightModel->getAlias() . '.' . $rightField;
			}

			foreach ( [ $join->getLeftConstraint(), $join->getRightConstraint() ] as $lr => $constraint )
			{
				if ( ! is_null( $constraint ) )
				{
					$model = $lr === 0 ? $this : $join->getJoinModel();
					$on[]  = '(' . $model->expansion( $constraint ) . ')';
				}
			}

			$on = implode( ' AND ', $on );

			$base = '(' . $base . ' ' . $join->getJoinTypeOperatorString() . ' ' .
					$join->getJoinModel()->getTable() . ' AS ' .
					$join->getJoinModel()->getAlias() .
					' ON ' . $on . ')';
			$base = $join->getJoinModel()->getJoinTables( $base );
		}

		return $base;
	}

	/**
	 * @return WGMModelOrder[]
	 */
	public function getJoinOrders(): array
	{
		$orders = $this->orderArray;
		foreach ( $this->joins as $join )
		{
			$orders = array_merge( $orders, $join->getJoinModel()->getJoinOrders() );
		}

		return $orders;
	}

	public function getJoinModels(): array
	{
		$models = [ $this ];
		foreach ( $this->joins as $join )
		{
			$models = array_merge( $models, $join->getJoinModel()->getJoinModels() );
		}

		return $models;
	}

	//    aaaa as t1
	//    aaaa as t1 inner join bbbb as t2 on t1.id=t2.id
	//               ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//   (aaaa as t1 inner join bbbb as t2 on t1.id=t2.id) inner join cccc as t3 on t1.id=t3.id
	//   =           ------------------------------------======================================
	//  ((aaaa as t1 inner join bbbb as t2 on t1.id=t2.id) inner join cccc as t3 on t1.id=t3.id
	// (((aaaa as t1 inner join bbbb as t2 on t1.id=t2.id) inner join cccc as t3 on t1.id=t3.id
	public function getJoinExternalTables(): array
	{
		$ret = [];
		foreach ( $this->joins as $join )
		{
			$ret = array_merge( $ret, $join->getJoinModel()->getJoinExternalTables() );
		}

		return $ret;
	}

	public function keys(): WGMModelGetKeys
	{
		$k = new WGMModelGetKeys( $this );
		$k->setKeys( func_get_args() );

		return $k;
	}

	protected function dumpKeys( $keys ): void
	{
		foreach ( $keys as $k )
		{
			if ( $k instanceof WGMModelGetKeys )
			{
				foreach ( $k->getKeys() as $kk )
				{
					$this->logInfo( $k->getModel()->getAlias() . '.' . $kk );
				}
			}
			else
			{
				$this->logInfo( $this->getAlias() . '.' . $k );
			}
		}
	}

	/**
	 * SELECTクエリ用パラメータ生成
	 *
	 * @param mixed $keys
	 *
	 * @return array クエリパラメータ配列
	 */
	protected function makeQuery( array $keys ): array
	{
		// Entry all fields
		$fields = [];
		foreach ( $this->getJoinExternalFields() as $f )
		{
			$fields[] = $f[1] . ' AS "' . $f[0] . '"';
		}

		// Entry all joined tables
		$tables = $this->getJoinTables( $this->getTable() . ' AS ' . $this->getAlias() );
		$orders = $this->getJoinOrders();
		usort( $orders, function ( WGMModelOrder $a, WGMModelOrder $b ) {
			return $a->getPriority() - $b->getPriority();
		} );

		$fieldOrders = [];
		foreach ( $orders as $o )
		{
			$fieldOrders[] = $o->getFormula();
		}
		$orderby = count( $fieldOrders ) > 0 ? ' ORDER BY ' . implode( ', ', $fieldOrders ) : '';

		$this->recs  = 0;
		$this->avars = [];

		// 条件式作成
		$wheres = array_map( function ( $m ) {
			return "( $m )";
		}, $this->whereCondExpression( $keys ) );
		$wheres = count( $wheres ) > 0 ? ' WHERE ' . implode( ' AND ', $wheres ) : '';

		return [ implode( ', ', $fields ), $tables, $wheres, $orderby, $this->offsetKeyword, $this->limitKeyword ];
	}

	/**
	 * テーブルを指定されたキーで検索する。
	 *
	 * @param string|array... キー文字列、配列
	 *
	 * @return WGMModel インスタンス
	 */
	public function select( ...$keys ): self
	{
		$keys = self::arrayFlatten( $keys );
		$this->logInfo( 'WGMModel::select( %s )', implode( ' , ', $keys ) );

		if ( $this->pager )
		{
			$count = $this->count( $keys );
			$this->pager->setTotal( $count );
			$ofs = $this->pager->offset();
			$lim = $this->pager->limit();

			$this->logInfo( 'Pager %s(rows) offset %s limit %s', $count, $ofs, $lim );
			$this->offset( $ofs, $lim );
		}

		list( $f, $t, $w, $ord, $ofs, $lim ) = $this->makeQuery( $keys );

		$q = sprintf( /** @lang text */ 'SELECT %s FROM %s%s%s%s%s;', $f, $t, $w, $ord, $ofs, $lim );
		$this->dbms->E( $q );
		$this->recs = $this->dbms->RECS();

		$joinedModels = $this->getJoinModels();

		$n = 0;
		while ( $f = $this->dbms->F() )
		{
			foreach ( $joinedModels as $joinedModel )
			{
				foreach ( $joinedModel->getFields() as $k )
				{
					$joinedModel->avars[ $n ][ $k ] = $joinedModel->fieldValue( $k, $f[ $joinedModel->aliasName . '.' . $k ], 'PHP' );
				}
			}
			$n ++;
		}
		foreach ( $joinedModels as $joinedModel )
		{
			if ( isset( $joinedModel->avars[0] ) )
			{
				foreach ( $joinedModel->getFields() as $k )
				{
					$joinedModel->setAssignedValue( $k, $joinedModel->avars[0][ $k ] );
				}
			}
			else
			{
				foreach ( $joinedModel->getFields() as $k )
				{
					$joinedModel->setAssignedValue( $k, null );
				}
			}
		}

		return $this;
	}

	/**
	 * テーブルを指定されたキーで検索し、検索された件数を返す。
	 *
	 * @param string|array... キー文字列、配列
	 *
	 * @return int 件数
	 */
	public function get(): int
	{
		return $this->select( func_get_args() )->recs;
	}

	public function check( mixed ...$args ): bool
	{
		$keys = self::arrayFlatten( $args );
		$this->logInfo( 'WGMModel::check( %s )', implode( ' , ', $keys ) );

		list( , $t, $w ) = $this->makeQuery( $keys );

		return ( $this->dbms->QQ( 'SELECT TRUE FROM %s%s;', $t, $w ) !== false );
	}

	public function count( mixed ...$args ): int
	{
		$keys = self::arrayFlatten( $args );
		$this->logInfo( 'WGMModel::count( %s )', implode( ' , ', $keys ) );

		list( , $t, $w ) = $this->makeQuery( $keys );
		list( $count ) = $this->dbms->QQ( 'SELECT COUNT(*) FROM %s%s;', $t, $w );

		return (int) $count;
	}

	/**
	 * テーブルを指定されたキーで追記する。
	 *
	 * @param string|array... キー文字列、配列
	 *
	 * @return WGMModel インスタンス
	 */
	public function insert(): self
	{
		$this->recs = 0;
		$fields     = $this->getFields();

		$dd = [];
		foreach ( $fields as $k )
		{
			$dd[ $k ] = $this->getAssignedValue( $k );
		}
		foreach ( array_merge( $this->initYmdKeys, $this->updYmdKeys ) as $ff )
		{
			$dd[ $ff ] = 'CURRENT_TIMESTAMP';
		}

		$fs = [];
		$vs = [];
		foreach ( $dd as $f => $v )
		{
			if ( ! in_array( $f, $fields ) )
			{
				continue;
			}
			$fs[] = $f;
			$vs[] = $this->fieldValue( $f, $v, 'DB' );
		}
		if ( count( $fs ) == 0 )
		{
			$q = false;
		}
		else
		{
			$q = sprintf( 'INSERT INTO %s(%s) VALUES(%s);',
				$this->tableName,
				implode( ', ', $fs ),
				implode( ', ', $vs )
			);
		}

		if ( $q )
		{
			$this->dbms->E( $q );
			if ( ! $this->dbms->OK() )
			{
				$this->logFatal( "Can't insert into '%s'.\n%s", $this->tableName, $q );
			}
		}

		return $this;
	}

	/**
	 * テーブルを指定されたキーで更新する。キーが存在しない場合は追加レコードが生成される。
	 *
	 * @param string|array... キー文字列、配列
	 *
	 * @return WGMModel インスタンス
	 */
	public function update( ...$args ): self
	{
		$keys = self::arrayFlatten( $args );
		$this->logInfo( 'WGMModel::update( %s )', implode( ' , ', $keys ) );

		$fields = $this->getFields();

		$this->recs = 0;

		// 条件式作成
		$wheres = $this->whereExpression( $keys );
		$wheres = count( $wheres ) > 0 ? ' WHERE ' . implode( ' AND ', $wheres ) : '';

		$q = sprintf( 'SELECT %s FROM %s%s;',
			implode( ', ', $fields ),
			$this->tableName,
			$wheres
		);
		$this->dbms->Q( '%s', $q );

		if ( ( $r = $this->dbms->RECS() ) > 1 )
		{
			$this->logFatal( "Can't select the unique record from '%s' on update.\n%s", $this->tableName, $q );
		}

		$isInsert = ( $r === 0 );
		if ( $r === 1 )
		{
			$f = $this->dbms->F();
			foreach ( $fields as $k )
			{
				$this->backvars[ $k ] = $this->fieldValue( $k, $f[ $k ], 'PHP' );
			}
		}

		$dd = [];
		if ( $isInsert )
		{
			$this->logInfo( 'Insert mode' );

			$ws = array_intersect( array_unique( array_merge( array_keys( $this->assign ), array_keys( $this->vars ) ) ), $fields );
			foreach ( $ws as $k )
			{
				$dd[ $k ] = $this->getAssignedValue( $k );
			}
			foreach ( array_merge( $this->initYmdKeys, $this->updYmdKeys ) as $ff )
			{
				$dd[ $ff ] = 'CURRENT_TIMESTAMP';
			}

			$fs = [];
			$vs = [];
			foreach ( $dd as $f => $v )
			{
				if ( ! in_array( $f, $fields ) )
				{
					continue;
				}
				$fs[] = $f;
				$vs[] = $this->fieldValue( $f, $v, 'DB' );
			}
			if ( count( $fs ) == 0 )
			{
				$q = false;
			}
			else
			{
				$q = sprintf( 'INSERT INTO %s(%s) VALUES(%s);',
					$this->tableName,
					implode( ', ', $fs ),
					implode( ', ', $vs )
				);
			}
		}
		else
		{
			$this->logInfo( 'Update mode' );

			$d1 = [];

			$ws = array_intersect( array_unique( array_merge( array_keys( $this->assign ), array_keys( $this->vars ) ) ), $fields );
			foreach ( $ws as $k )
			{
				$d1[ $k ] = $this->getAssignedValue( $k );
			}
			foreach ( $d1 as $k => $v )
			{
				if ( ! $this->compareField( $k, $this->backvars[ $k ], $v ) )
				{
					$dd[ $k ] = $v;
				}
			}

			foreach ( $this->initYmdKeys as $ff )
			{
				unset( $dd[ $ff ] );
			}
			foreach ( $this->updYmdKeys as $ff )
			{
				$dd[ $ff ] = 'CURRENT_TIMESTAMP';
			}

			$ss = [];
			foreach ( $dd as $f => $v )
			{
				if ( in_array( $f, $keys ) )
				{
					continue;
				}
				if ( ! in_array( $f, $fields ) )
				{
					continue;
				}
				$ss[] = $f . '=' . $this->fieldValue( $f, $v, 'DB' );
			}
			if ( count( $ss ) == 0 )
			{
				$q = false;
			}
			else
			{
				$q = sprintf( /** @lang text */ 'UPDATE %s SET %s%s;', $this->tableName, implode( ', ', $ss ), $wheres );
			}
		}

		if ( $q )
		{
			$this->dbms->E( $q );
			if ( ! $this->dbms->OK() )
			{
				$this->logFatal( "Can't update '$this->tableName'.\n$q" );
			}
		}

		return $this;
	}

	/**
	 * テーブルを指定されたキーで削除する。
	 *
	 * @param string|array... キー文字列、配列
	 *
	 * @return WGMModel インスタンス
	 */
	public function delete( ...$args ): self
	{
		$keys = self::arrayFlatten( $args );
		$this->dumpKeys( $keys );
		$this->logInfo( 'WGMModel::delete( %s )', implode( ' , ', $keys ) );

		$this->recs = 0;

		// 条件式作成
		$wheres = $this->whereExpression( $keys );
		$wheres = count( $wheres ) > 0 ? ' WHERE ' . implode( ' AND ', $wheres ) : '';

		$q = sprintf( 'DELETE FROM %s%s;',
			$this->tableName,
			$wheres
		);
		$this->dbms->E( $q );
		if ( ! $this->dbms->OK() )
		{
			$this->logFatal( "Can't delete from '%s'.\n%s", $this->tableName, $q );
		}

		return $this;
	}

	public function result(): int
	{
		return count( $this->avars );
	}

	public function setVars( $vars = [] ): self
	{
		$this->vars = $vars;
		foreach ( $vars as $k => $v )
		{
			$this->setAssignedValue( $k, $v );
		}

		return $this;
	}

	public function getVars( $vars = [] ): int
	{
		return $this->setVars( $vars )->get( array_keys( $vars ) );
	}

	public function getJoinedAvars(): array
	{
		$r = [];
		$n = $this->result();
		foreach ( $this->getJoinModels() as $join )
		{
			$tn = $join->getAlias();
			for ( $i = 0; $i < $n; $i ++ )
			{
				$r[ $i ][ $tn ] = $join->avars[ $i ];
			}
		}

		return $r;
	}
}
