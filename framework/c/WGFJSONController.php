<?php
/**
 * waggo8
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/WGFController.php';

class WGFJSONController extends WGFController
{
	/**
	 * @var stdClass $jsonCanvas
	 */
	protected $jsonCanvas;
	protected $jsonOption;
	protected $jsonDepth;

	public function __construct()
	{
		parent::__construct();

		$this->jsonCanvas = new stdClass;
		$this->jsonOption = 0;
		$this->jsonDepth  = 512;
	}

	public function isScriptBasedController(): bool
	{
		return false;
	}

	public function runJS( string $javascript, string $event): string
	{
		$this->abort('WGFJSONController does not support runJS method.');
        return "";
	}

	public function runParts( string $selector, string $url, string $event ): string
	{
		$this->abort('WGFJSONController does not support runParts method.');
        return "";
	}

	/**
	 * JSON出力
	 *
	 * @param mixed $data レスポンス用JSONインスタンス
	 */
	protected function renderJSON($data)
	{
		$response = json_encode($data, $this->jsonOption, $this->jsonDepth);
		header('Content-Type: application/json; charset=utf-8');
		header('Content-Length: ' . strlen($response));

		echo $response;
	}

	protected function rollbackAndAbort($msg=false)
	{
		_QROLLBACK();
		$this->abort($msg);
	}

	protected function abort($msg=false): void
	{
		$this->renderJSON($msg);
		exit;
	}

	protected function render(): self
	{
		$this->renderJSON($this->jsonCanvas);
        return $this;
	}

	protected function renderAndExit()
	{
		$this->render();
		exit;
	}
}
