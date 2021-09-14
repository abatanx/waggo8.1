<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

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
	echo $m;
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

function wi_echo( string $format, mixed ...$args ): void
{
	echo ( count( $args ) > 0 ? vsprintf( $format, ...$args ) : $format ) . "\n";
}
