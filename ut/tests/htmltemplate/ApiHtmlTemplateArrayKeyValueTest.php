<?php
/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/local-common.php';

class ApiHtmlTemplateArrayKeyValueTest extends TestCase
{
	use HtmlTemplateTestUnit;

	public function test_html_2d_array()
	{
		$q = [
			[
				'date'   => '2021-01-01',
				'status' => 30,
				'count'  => 30,
			],
			[
				'date'   => '2021-01-01',
				'status' => 99,
				'count'  => 70,
			],
			[
				'date'   => '2021-12-31',
				'status' => 0,
				'count'  => 25,
			],
			[
				'date'   => '2021-12-31',
				'status' => 10,
				'count'  => 25,
			],
			[
				'date'   => '2021-12-31',
				'status' => 20,
				'count'  => 28,
			],
			[
				'date'   => '2021-12-31',
				'status' => 30,
				'count'  => 22,
			],
		];

		$matrix = [];
		foreach ( $q as $v )
		{
			list( $d1, $d2, $value ) = [ $v['date'], $v['status'], $v['count'] ];

			$matrix[ $d1 ] ??= array_fill_keys( [ 0, 10, 20, 30, 99 ], 0 );

			$matrix[ $d1 ][ $d2 ] = $value;
		}

		$html = <<<HTML
<table>
<!--{each matrix}-->
	<tr>
		<th>{val #matrix}</td>
<!--{each *matrix}-->
		<td data-num="{val #*matrix}">{val **matrix}</td>
<!--{/each}-->
	</tr>
<!--{/each}-->
</table>
HTML;

		$data['matrix'] = $matrix;

		$r = trim( $this->ht( __METHOD__, $html, $data ) );

		$this->assertSame( <<<HTML
<table>
	<tr>
		<th>2021-01-01</td>
		<td data-num="0">0</td>
		<td data-num="10">0</td>
		<td data-num="20">0</td>
		<td data-num="30">30</td>
		<td data-num="99">70</td>
	</tr>
	<tr>
		<th>2021-12-31</td>
		<td data-num="0">25</td>
		<td data-num="10">25</td>
		<td data-num="20">28</td>
		<td data-num="30">22</td>
		<td data-num="99">0</td>
	</tr>
</table>
HTML, $r );
	}

	public function test_html_3d_array()
	{
		$q = [
			[
				'title'  => 'JA',
				'date'   => '2021-01-01',
				'status' => 30,
				'count'  => 30,
			],
			[
				'title'  => 'US',
				'date'   => '2021-01-01',
				'status' => 99,
				'count'  => 70,
			],
			[
				'title'  => 'CN',
				'date'   => '2021-12-31',
				'status' => 0,
				'count'  => 25,
			],
			[
				'title'  => 'US',
				'date'   => '2021-12-31',
				'status' => 10,
				'count'  => 25,
			],
			[
				'title'  => 'JA',
				'date'   => '2021-12-31',
				'status' => 20,
				'count'  => 28,
			],
			[
				'title'  => 'US',
				'date'   => '2021-12-31',
				'status' => 30,
				'count'  => 22,
			],
		];

		$matrix = [];
		foreach ( $q as $v )
		{
			list( $d1, $d2, $d3, $value ) = [ $v['title'], $v['date'], $v['status'], $v['count'] ];

			$matrix[ $d1 ]        ??= [];
			$matrix[ $d1 ][ $d2 ] ??= array_fill_keys( [ 0, 10, 20, 30, 99 ], 0 );

			$matrix[ $d1 ][ $d2 ][ $d3 ] = $value;
		}

		$html = trim( <<<HTML
<!--{each matrix}-->
<h1>{val #matrix}</h1>
<table>
<!--{each *matrix}-->
	<tr>
		<th>{val #*matrix}</td>
<!--{each **matrix}-->
		<td data-num="{val #**matrix}">{val ***matrix}</td>
<!--{/each}-->
	</tr>
<!--{/each}-->
</table>
<!--{/each}-->
HTML
		);

		$data['matrix'] = $matrix;

		$r = trim( $this->ht( __METHOD__, $html, $data ) );

		$this->assertSame( trim( <<<HTML
<h1>JA</h1>
<table>
	<tr>
		<th>2021-01-01</td>
		<td data-num="0">0</td>
		<td data-num="10">0</td>
		<td data-num="20">0</td>
		<td data-num="30">30</td>
		<td data-num="99">0</td>
	</tr>
	<tr>
		<th>2021-12-31</td>
		<td data-num="0">0</td>
		<td data-num="10">0</td>
		<td data-num="20">28</td>
		<td data-num="30">0</td>
		<td data-num="99">0</td>
	</tr>
</table>
<h1>US</h1>
<table>
	<tr>
		<th>2021-01-01</td>
		<td data-num="0">0</td>
		<td data-num="10">0</td>
		<td data-num="20">0</td>
		<td data-num="30">0</td>
		<td data-num="99">70</td>
	</tr>
	<tr>
		<th>2021-12-31</td>
		<td data-num="0">0</td>
		<td data-num="10">25</td>
		<td data-num="20">0</td>
		<td data-num="30">22</td>
		<td data-num="99">0</td>
	</tr>
</table>
<h1>CN</h1>
<table>
	<tr>
		<th>2021-12-31</td>
		<td data-num="0">25</td>
		<td data-num="10">0</td>
		<td data-num="20">0</td>
		<td data-num="30">0</td>
		<td data-num="99">0</td>
	</tr>
</table>
HTML
		), $r );
	}
}
