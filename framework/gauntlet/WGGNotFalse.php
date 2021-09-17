<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGG.php';

class WGGNotFalse extends WGG
{
	public static function _(): self
	{
		return new static();
	}

	public function makeErrorMessage(): string
	{
		return '内容がありません。';
	}

	public function validate( mixed &$data ): bool
	{
		if( !is_bool($data) )
		{
			if ( function_exists( 'wg_log_write' ) )
			{
				wg_log_write( WGLOG_FATAL, '\'%s\' type is not allowed.', gettype( $value ) );
			}
			else
			{
				$type = gettype($data);
				throw new WGRuntimeException("'$type' is not allowed.");
			}
		}

		if ( $data === true )
		{
			return true;
		}
		else
		{
			if ( ! $this->isBranch() )
			{
				$this->addError( $this->makeErrorMessage() );
			}

			return false;
		}
	}
}

