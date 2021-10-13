<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/local-common.php';

class ApiHtmlTemplateTest extends TestCase
{
	use HtmlTemplateTestUnit;

	public function test_html_template_val()
	{
		$html = <<<PHP
[{val t0}:{val t1}:{val t2}:{val t3}:{val t4}:{val t5}:{val t6}:{val t7}:{val nothing}:{val br}]
PHP;
		$data = [
			't0' => '',
			't1' => '<br>',
			't2' => '100000',
			't3' => 10000,
			't4' => 10.5,
			't5' => true,
			't6' => false,
			't7' => null,
			'br' => "abc\ndef"
		];
		$r    = $this->ht( __METHOD__, $html, $data );
		$this->assertSame( '[:&lt;br&gt;:100000:10000:10.5:1::::abc<br />' . "\n" . 'def]', $r );
	}

	public function test_html_template_rval()
	{
		$html = <<<PHP
[{rval t0}:{rval t1}:{rval t2}:{rval t3}:{rval t4}:{rval t5}:{rval t6}:{rval t7}:{rval nothing}:{rval br}]
PHP;
		$data = [
			't0' => '',
			't1' => '<br>',
			't2' => '100000',
			't3' => 10000,
			't4' => 10.5,
			't5' => true,
			't6' => false,
			't7' => null,
			'br' => "abc\ndef"
		];
		$r    = $this->ht( __METHOD__, $html, $data );
		$this->assertSame( '[:<br>:100000:10000:10.5:1::::' . "abc\ndef" . ']', $r );
	}

	public function test_html_template_rval2()
	{
		$html = <<<PHP
[{@t0}:{@t1}:{@t2}:{@t3}:{@t4}:{@t5}:{@t6}:{@t7}:{@nothing}:{@br}]
PHP;
		$data = [
			't0' => '',
			't1' => '<br>',
			't2' => '100000',
			't3' => 10000,
			't4' => 10.5,
			't5' => true,
			't6' => false,
			't7' => null,
			'br' => "abc\ndef"
		];
		$r    = $this->ht( __METHOD__, $html, $data );
		$this->assertSame( '[:<br>:100000:10000:10.5:1::::' . "abc\ndef" . ']', $r );
	}

	public function test_html_template_nval()
	{
		$html = <<<PHP
[{nval t0}:{nval t1}:{nval t2}:{nval t3}:{nval t4}:{nval t5}:{nval t6}:{nval t7}:{nval nothing}:{nval br}]
PHP;
		$data = [
			't0' => '',
			't1' => '<br>',
			't2' => '100000',
			't3' => 10000,
			't4' => 10.5,
			't5' => true,
			't6' => false,
			't7' => null,
			'br' => "abc\ndef"
		];
		$r    = $this->ht( __METHOD__, $html, $data );
		$this->assertSame( '[0:0:100,000:10,000:10:1:0:::0]', $r );
	}
}
