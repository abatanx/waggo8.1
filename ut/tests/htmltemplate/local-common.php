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
