<?php
/**
 * waggo8
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGFController.php';

class WGFXMLController extends WGFController
{
	public function __construct()
	{
		parent::__construct();
		$this->appCanvas->setTemplate( WGCONF_DIR_TPL . "/pcroot.xml" );
	}

	public function isScriptBasedController(): bool
	{
		return true;
	}

	/**
	 * JavaScript実行用仮想メソッド。
	 *
	 * @param string $javascript スクリプト
	 * @param string $event 実行タイミングイベント (self::RUNJS_ONPRELOAD, self::RUNJS_ONLOADED)
	 *
	 * @return string キー
	 */
	const
		RUNJS_ONPRELOAD = 'onpreload',
		RUNJS_ONLOADED = 'onloaded';

	public function runJS( $javascript, $event = self::RUNJS_ONLOADED ): string
	{
		$keyseq                            = $this->getSerialId( "js-" );
		$this->appCanvas->html["script"][] =
			[
				"key"   => $keyseq,
				"event" => $event,
				"src"   => $javascript
			];

		return $keyseq;
	}

	/**
	 * @inheritdoc
	 */
	public function runParts( $selector, $url, $event = self::RUNJS_ONLOADED ): string
	{
		return $this->runJS( "WG8.get('{$selector}','$url');", $event );
	}

	protected function newPage( $url ): void
	{
		$this->runJS( "window.location='{$url}';", self::RUNJS_ONPRELOAD );
	}

	protected function render(): self
	{
		if ( ! is_null( $this->pageCanvas->getTemplate() ) )
		{
			$this->appCanvas->html['action']       = wg_remake_uri();
			$this->appCanvas->html['contents']     = $this->pageCanvas->build();
			$this->appCanvas->html['has_contents'] = "t";
		}

		header( "Content-Type: text/xml" );
		echo '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
		$this->appCanvas->buildAndFlush();

		return $this;
	}
}
