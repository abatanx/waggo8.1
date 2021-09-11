<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__.'/WGFController.php';

class WGFPCController extends WGFController
{
	public function __construct()
	{
		parent::__construct();
		$this->appCanvas->setTemplate(WGCONF_DIR_TPL."/pcroot.html");
	}

	public function isScriptBasedController()
	{
		return false;
	}

	private function getTargetCanvasForJS()
	{
		return !is_null($this->appCanvas) ? $this->appCanvas : $this->pageCanvas;
	}

	public function loadCSS($url)
	{
		$keyseq = $this->getSerialId("css-load-");
		$canvas = $this->getTargetCanvasForJS();
		$canvas->html["in_heads"][] =
			[
				"key"		=>	$keyseq,
				"data"		=>	sprintf('<link rel="stylesheet" type="text/css" href="%s">', $url)
			];
		return $keyseq;
	}

	public function loadJS($url)
	{
		$keyseq = $this->getSerialId("js-load-");
		$canvas = $this->getTargetCanvasForJS();
		$canvas->html["in_heads"][] =
			[
				"key"		=>	$keyseq,
				"data"		=>	sprintf('<script type="text/javascript" src="%s" charset="utf-8"></script>', $url)
			];
		return $keyseq;
	}

	/**
	 * JavaScript実行用仮想メソッド。
	 * @param string $javascript スクリプト
	 * @param string $event 実行タイミングイベント (self::RUNJS_ONREADY, ::RUNJS_ONLOAD, ::RUNJS_ONBEFOREUNLOAD, ::RUNJS_ONUNLOAD)
	 * @return string キー
	 */
	const
		RUNJS_ONREADY			= 'ready',
		RUNJS_ONLOAD			= 'load',
		RUNJS_ONBEFOREUNLOAD	= 'beforeunload',
		RUNJS_ONUNLOAD			= 'unload';

	public function runJS($javascript,$event = self::RUNJS_ONREADY)
	{
		$keyseq = $this->getSerialId("run-js-");
		$canvas = $this->getTargetCanvasForJS();
		$canvas->html["in_on{$event}_functions"][] =
			[
				"key"		=>	$keyseq,
				"data"		=>	$javascript
			];
		return $keyseq;
	}

	/**
	 * @inheritdoc
	 */
	public function runParts($selector,$url,$event = self::RUNJS_ONLOAD)
	{
		$selector = addslashes($selector);
		$url      = addslashes($url);
		return $this->runJS("WG8.get('{$selector}','$url');", $event);
	}
}
