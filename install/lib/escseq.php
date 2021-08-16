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

function wi_locate( $col, $row ): void
{
	printf( "\x1b[%d;%dH", $row + 1, $col + 1 );
}

function wi_stdin(): string
{
	$in = fgets( STDIN );

	return $in !== false ? trim( $in ) : die( "\n" );
}

function wi_read( $m, $a = [] ): string
{
	do
	{
		echo $m;
		$s = wi_stdin();
	}
	while ( ! in_array( $s, $a ) );

	return $s;
}

function wi_pause( $m )
{
	echo $m;
	wi_stdin();
}

function wi_in( $m ): string
{
	echo $m;

	return wi_stdin();
}

function wi_in_default( $m, $default, $require ): string
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
