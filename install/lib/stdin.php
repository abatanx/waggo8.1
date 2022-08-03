<?php
/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

const ECHO_NORMAL  = 0;
const ECHO_SPACING = 1;

function wi_cls(): void
{
	echo "\x1b[2J\x1b[1;1H";
}

function wi_stdin(): string
{
	$in = fgets( STDIN );

	return $in !== false ? trim( $in ) : die( "\n" );
}

function wi_read( string $m, array $a = [] ): string
{
	do
	{
		echo $m;
		$s = wi_stdin();
	}
	while ( ! in_array( $s, $a ) );

	return $s;
}

function wi_pause( string $m )
{
	wi_echo( ECHO_SPACING, $m );
	wi_stdin();
}

function wi_in( string $m ): string
{
	echo $m;

	return wi_stdin();
}

function wi_in_default( string $m, string $default, bool $require ): string
{
	do
	{
		echo $m;
		$s = wi_stdin();
		$r = empty( $s ) ? $default : $s;
	}
	while ( $require && empty( $r ) );

	return $r;
}

function wi_echo( int $type, string $format, mixed ...$args ): void
{
	$echo = function ( string $format, array $args ): string {
		return ( count( $args ) > 0 ? vsprintf( $format, $args ) : $format );
	};

	switch ( $type )
	{
		case ECHO_SPACING:
			$e = $echo( $format, $args );
			echo "\n" . str_repeat( '-', strlen( $e ) + 2 ) . "\n";
			echo ' ' . $e;
			echo "\n" . str_repeat( '-', strlen( $e ) + 2 ) . "\n\n";
			break;
		case ECHO_NORMAL:
		default:
			echo $echo( $format, $args ) . "\n";
	}
}
