<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

if ( ! defined( 'WG_UNITTEST' ) )
{
	define( 'WG_UNITTEST', true );
}

require_once __DIR__ . '/../../unittest-config.php';

require_once __DIR__ . '/../../../api/html/htmltemplate.php';

class HTE extends HtmlTemplateEncoder {}

trait HtmlTemplateTestUnit
{
	protected function ht( $method, $code, $data )
	{
		$file = WGCONF_CANVASCACHE . '/ht_' . md5( $method ) . '.html';
		file_put_contents( $file, $code );

		return HtmlTemplate::buffer( $file, $data );
	}
}
