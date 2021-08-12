<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__.'/htmltemplate.php';

abstract class WGCanvas
{
	/**
	 * @var array $html
	 */
	public $html;

	/**
	 * @var string $template
	 */
	public $template;

	public function __construct()
	{
		$this->html     = [];
		$this->template = null;
		if( WG_JSNOCACHE ) $this->html["_nocache"] = "?_nc_=".time();
	}
	public function setTemplate($template) { $this->template = $template; }
	public function getTemplate()          { return $this->template;      }
	abstract function build();
	abstract function buildAndFlush();
}

class WGHtmlCanvas extends WGCanvas
{
	function build()
	{
		return HtmlTemplate::buffer($this->template, $this->html);
	}
	function buildAndFlush()
	{
		HtmlTemplate::include($this->template, $this->html);
	}
}

class WGXMLCanvas extends WGCanvas
{
	function build()
	{
		return HtmlTemplate::buffer($this->template, $this->html);
	}
	function buildAndFlush()
	{
		header("Content-Type: text/xml");
		HtmlTemplate::include($this->template, $this->html);
	}
}
