<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */
declare( strict_types=1 );

require_once __DIR__ . '/../../waggo.php';
require_once __DIR__ . '/../v8/WGV8Object.php';
require_once __DIR__ . '/../m/WGMModel.php';
require_once __DIR__ . '/WGFSession.php';

/**
 * WGFController
 */
abstract class WGFController
{
	const FORMHTML = 0, SHOWHTML = 1;
	const LIMITTIME = 600;

	/**
	 * @var WGCanvas $appCanvas
	 */
	public WGCanvas $appCanvas;

	/**
	 * @var WGCanvas $pageCanvas
	 */
	public WGCanvas $pageCanvas;

	//public $inheads, $onload_functions, $onunload_functions;
	//public $template, $html;
	public int $usercd;

	public array $views, $models, $connectors;

	public string $formEnctype;
	public int $inputType;

	protected WGFController $controller;
	private bool $isInitFirst;

	/**
	 * @var int[] $serialIdDict
	 */
	protected array $serialIdDict;

	/**
	 * @var WGFSession
	 */
	public WGFSession $session;
	private bool $isFirstPage;

	/**
	 * @var WGTransition
	 */
	protected WGTransition $transition;

	public function __construct()
	{
		$class = get_class( $this );
		wg_log_write( WGLOG_INFO, "[[ Controller : $class ]]" );

		header( "Pragma: no-cache" );
		header( "Cache-Control: no-cache" );
		header( "Expires: Thu, 01 Dec 1994 16:00:00 GMT" );
		header( "If-Modified-Since: Thu, 01 Jun 1970 00:00:00 GMT" );

		$this->transition  = new WGTransition();
		$this->isFirstPage = $this->transition->firstpage( $class );
		$this->session     = $this->transition->getSession();

		$this->views        = [];
		$this->models       = [];
		$this->inputType    = self::FORMHTML;
		$this->isInitFirst  = false;
		$this->usercd       = wg_get_usercd();
		$this->serialIdDict = [];

		$this->setFormURLENCODED();

		$this->initCanvas();
	}

	/**
	 * コントローラーインスタンスを生成し、直接実行を行う。
	 * @return WGFController インスタンス
	 */
	static public function START(): self
	{
		$instance = new static;
		$instance->run();

		return $instance;
	}

	/**
	 * コントローラーが保持する遷移セッションをクリアする。
	 * @noinspection PhpUnused
	 */
	public function END(): self
	{
		$this->session->cleanup();

		return $this;
	}

	/**
	 * キーシーケンスの取得を行う
	 *
	 * @param string $keyPrefix キーの先頭に付与されるプリフィックス
	 *
	 * @return string キーシーケンス
	 */
	protected function getSerialId( string $keyPrefix ): string
	{
		if ( ! isset( $this->serialIdDict[ $keyPrefix ] ) )
		{
			$this->serialIdDict[ $keyPrefix ] = 100000;
		}

		return sprintf( "%s%d", $keyPrefix, $this->serialIdDict[ $keyPrefix ] ++ );
	}

	/**
	 * JavaScript実行用仮想メソッド。
	 *
	 * @param string $javascript スクリプト
	 * @param string $event 実行タイミングイベント
	 *
	 * @return string キー
	 */
	abstract public function runJS( string $javascript, string $event ): string;

	/**
	 * パーツ実行用仮想メソッド。
	 *
	 * @param string $selector jQueryセレクタ
	 * @param string $url パーツスクリプト
	 * @param string $event 実行タイミングイベント
	 *
	 * @return string キー
	 */
	abstract public function runParts( string $selector, string $url, string $event ): string;

	/**
	 * スクリプトベース(動的にページ内に差し込むような)のコントローラーであるか。
	 * この項目が true の場合は、 class="wg-form" 内の要素に差し込む必要がある。
	 * @return boolean スクリプトベースの場合は true, その他の場合は false を返す。
	 */
	abstract public function isScriptBasedController(): bool;

	/**
	 * フォームのエンコード方法を URLEncoded に設定する。
	 * @return self
	 */
	public function setFormURLENCODED()
	{
		$this->formEnctype = "application/x-www-form-urlencoded";

		return $this;
	}

	/**
	 * フォームのエンコード方法を multipart に設定する。
	 * @return self
	 * @noinspection PhpUnused
	 */
	public function setFormMULTIPART()
	{
		$this->formEnctype = "multipart/form-data";

		return $this;
	}

	/**
	 * キャンバスを初期化する。
	 * @return self
	 */
	protected function initCanvas(): self
	{
		$this->appCanvas  = new WGHtmlCanvas();
		$this->pageCanvas = new WGHtmlCanvas();

		return $this;
	}

	/**
	 * Controller-View関係
	 */

	/**
	 * コントローラーに登録されたビューを初期化する。
	 *
	 * @param $id String ビューID
	 *
	 * @return WGV8Object ビューのインスタンス
	 */
	protected function initView( string $id ): WGV8Object
	{
		$this->views[ $id ]->initController( $this );
		$this->views[ $id ]->initSession( $this->session );
		$this->views[ $id ]->setKey( $id );
		if ( $this->isInitFirst )
		{
			$this->views[ $id ]->initfirst();
		}
		$this->views[ $id ]->init();

		return $this->views[ $id ];
	}

	/**
	 * コントローラーにビューを登録し、初期化する。
	 *
	 * @param $id string ビューID
	 * @param $view WGV8Object ビューのインスタンス
	 *
	 * @return WGV8Object ビューのインスタンス
	 **/
	protected function addView( string $id, WGV8Object $view ): WGV8Object
	{
		if ( WG_CONTROLLERDEBUG )
		{
			wg_log_write( WGLOG_INFO, get_class( $this ) . ".addView $id(" . get_class( $view ) . ")" );
		}
		$this->views[ $id ] = $view;
		$this->initView( $id );

		return $this->views[ $id ];
	}

	/**
	 * コントローラーに、別変数に保管しているビューを、現在のコントローラーに接続しなおす。
	 *
	 * @param $view WGV8Object ビューのインスタンス
	 *
	 * @return WGV8Object ビューのインスタンス
	 * @noinspection PhpUnused
	 */
	protected function restoreView( WGV8Object $view ): WGV8Object
	{
		$id = $view->getName();

		if ( WG_CONTROLLERDEBUG )
		{
			wg_log_write( WGLOG_INFO, get_class( $this ) . ".restoreView $id(" . get_class( $view ) . ")" );
		}
		$this->views[ $id ] = $view;
		$this->initView( $id );

		return $this->views[ $id ];
	}

	/**
	 * コントローラーに登録されたビューオブジェクトを返す。
	 *
	 * @param string $id ビューID
	 *
	 * @return WGV8Object Viewインスタンス
	 */
	protected function view( string $id ): WGV8Object
	{
		if ( ! ( $this->views[ $id ] ?? null ) instanceof WGV8Object )
		{
			wg_log_write( WGLOG_FATAL, "$id というビューインスタンスは登録されていません。" );
		}

		return $this->views[ $id ];
	}

	/**
	 * コントローラーからビューを削除する。
	 *
	 * @param $id string ビューID
	 *
	 * @return self
	 **/
	protected function delView( string $id ): self
	{
		$this->views[ $id ] = null;
		unset( $this->views[ $id ] );

		return $this;
	}

	/**
	 * コントローラーからすべてのビューを削除する。
	 * @return self
	 * @noinspection PhpUnused
	 */
	protected function delAllViews(): self
	{
		$this->views = [];

		return $this;
	}

	/**
	 * コントローラーに登録されたビューが、エラー状態のものが存在するかチェックする。
	 * @return boolean エラー状態が存在している場合は true を、存在しない場合は false を返す。
	 */
	protected function hasError(): bool
	{
		if ( WG_CONTROLLERDEBUG )
		{
			foreach ( $this->views as $k => $v )
			{
				$e = $v->getError();
				$c = get_class( $this->views[ $k ] );
				if ( $e != false )
				{
					wg_log_write( WGLOG_INFO, "$k($c) が入力エラー状態 : $e" );
				}
			}
		}
		foreach ( $this->views as $v )
		{
			if ( $v->getError() != false )
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * コントローラーに登録されたビューがエラーのものを、ビューの配列で返す。
	 * @return WGV8Object[] ビュー配列
	 * @noinspection PhpUnused
	 */
	protected function errorViews(): array
	{
		$errorViews = [];
		foreach ( $this->views as $v )
		{
			if ( $v->getError() )
			{
				$errorViews[] = $v;
			}
		}

		return $errorViews;
	}

	/**
	 * コントローラーに登録されたビューのエラーをすべてクリアする。
	 * @return self
	 */
	protected function clearError(): self
	{
		foreach ( $this->views as $k => $p )
		{
			$this->views[ $k ]->setError( false );
		}

		return $this;
	}

	/**
	 * Controller-Model関係
	 */

	/**
	 * コントローラーに登録されたモデルを返す。
	 *
	 * @param string $id ModelID
	 *
	 * @return WGMModel Modelインスタンス
	 */
	protected function model( string $id ): WGMModel
	{
		if ( ! ( $this->models[ $id ] ?? null ) instanceof WGMModel )
		{
			wg_log_write( WGLOG_FATAL, "$id というモデルインスタンスは登録されていません。" );
		}

		return $this->models[ $id ];
	}

	/**
	 * トランザクション関係
	 */

	/**
	 * コントローラーが、当該ページにアクセスされたのが初回なのかチェックする。
	 * (初回のアクセスとは、トランザクションIDが付与されていない状態のことをいう。)
	 * @return boolean true|false 初回アクセスである
	 * @deprecated
	 */
	protected function isCreate(): bool
	{
		return $this->isInitFirst;
	}

	protected function isFirst(): bool
	{
		return $this->isInitFirst;
	}

	/**
	 * @param $key
	 *
	 * @noinspection PhpUnused
	 */
	protected function unsetSession( $key )
	{
		wg_log_write( WGLOG_WARNING, "Deprecated unsetSession: {$_SERVER['SCRIPT_FILENAME']}" );
		$this->session->set( $key, null );
	}

	/**
	 * Event methods for App-implementations
	 */
	protected function create()
	{
	}

	/**
	 * @return WGMModel[]
	 */
	protected function models()
	{
		return [];
	}

	/**
	 * @return WGV8Object[]
	 */
	protected function views()
	{
		return [];
	}

	/**
	 * @return array
	 */
	protected function connectors()
	{
		return [];
	}

	protected function initFirst()
	{
	}

	protected function initFirstCall( mixed $data )
	{
	}

	protected function init()
	{
	}

	protected function beforeViews()
	{
	}

	protected function afterViews()
	{
	}

	protected function beforeModels()
	{
	}

	protected function afterModels()
	{
	}

	protected function beforeConnectors()
	{
	}

	protected function afterConnectors()
	{
	}

	protected function beforeInitFirstAndInit()
	{
	}

	protected function afterInitAndInitFirst()
	{
	}

	protected function beforePost()
	{
	}

	protected function afterPost()
	{
	}

	protected function beforeEvent()
	{
	}

	protected function afterEvent()
	{
	}

	protected function beforeBuild()
	{
	}

	protected function afterBuild()
	{
	}

	/**
	 * @return string|null|void
	 */
	protected function input()
	{
		return $this->defaultTemplate();
	}

	protected function willTerminate()
	{
	}

	protected function terminate()
	{
	}

	/**
	 * @return $this
	 */
	protected function firstpage(): self
	{
		$this->isInitFirst = true;
		$this->create();
		$this->initModels( $this->models() );
		$this->initViews( $this->views() );
		$this->initConnectors( $this->connectors() );
		$this->beforeInitFirstAndInit();
		$this->initFirst();
		$this->checkIPMReceiver();        // FOR DEST PAGE
		$this->init();
		$this->afterInitAndInitFirst();
		$this->clearError();

		return $this;
	}

	/**
	 * @return $this
	 */
	protected function nextpage(): self
	{
		$this->isInitFirst = false;
		$this->create();
		$this->initModels( $this->models() );
		$this->initViews( $this->views() );
		$this->initConnectors( $this->connectors() );
		$this->beforeInitFirstAndInit();
		$this->init();
		$this->afterInitAndInitFirst();
		$this->checkIPMCallback();        // FOR SRC PAGE
		$this->clearError();

		return $this;
	}

	/**
	 * Create models and connectors
	 *
	 * @param string|WGMModel $m 生成するテーブル名またはモデルインスタンス。モデルインスタンスの場合はそのまま返す。
	 *
	 * @return WGMModel 生成した(もしくは、そのままの)モデルインスタンス
	 * @noinspection PhpInconsistentReturnPointsInspection
	 */
	protected function initModelInstance( string|WGMModel $m ): WGMModel
	{
		if ( gettype( $m ) == 'string' )
		{
			return new WGMModel( $m );
		}
		else if ( gettype( $m ) == 'object' && $m instanceof WGMModel )
		{
			return $m;
		}
		else
		{
			wg_log_write( WGLOG_FATAL, get_class( $this ) . '::models() is not WGMModel and table name string.' );
		}
	}

	protected function initModels( array $models ): self
	{
		$this->beforeModels();
		if ( ! is_array( $models ) )
		{
			wg_log_write( WGLOG_FATAL, get_class( $this ) . '::models() must be return as array of WGMModel or table name string.' );
		}
		foreach ( $models as $k => $t )
		{
			$modelInstance = $this->initModelInstance( $t );

			$modelAlias                                      = ! $t instanceof WGMModel ? $t : $modelInstance->getAlias();
			$this->models[ is_int( $k ) ? $modelAlias : $k ] = $modelInstance;
			if ( ! is_int( $k ) )
			{
				$modelInstance->setAlias( $k );
			}
		}
		foreach ( $this->models as $m )
		{
			$m->setAutoTimestamp();
		}
		$this->afterModels();

		return $this;
	}

	/**
	 * コントローラーにビューを一括追加する。
	 *
	 * @param array $views
	 *
	 * @return $this
	 */
	protected function initViews( array $views ): self
	{
		$this->beforeViews();

		if ( ! is_array( $views ) )
		{
			wg_log_write( WGLOG_FATAL, get_class( $this ) . '::views() must be return as WGV8Object[].' );
		}

		foreach ( $views as $id => $v )
		{
			$this->addView( $id, $v );
		}

		$this->afterViews();

		return $this;
	}

	/**
	 * コネクター情報による、ビューとモデル間のコネクションを初期化する。
	 *
	 * @param array $connectors
	 *
	 * @return $this
	 */
	protected function initConnectors( array $connectors ): self
	{
		$this->beforeConnectors();
		if ( ! is_array( $connectors ) )
		{
			wg_log_write( WGLOG_FATAL, "connectors() は、モデル名=>[フィールド名=>ビュー名,...] の配列で返してください。" );
		}
		foreach ( $connectors as $model => $kvs )
		{
			if ( ! $this->models[ $model ] instanceof WGMModel )
			{
				wg_log_write( WGLOG_FATAL, "$model のモデルインスタンスがありません。" );
			}
			foreach ( $kvs as $k => $v )
			{
				if ( is_int( $k ) )
				{
					if ( ! $this->views[ $v ] instanceof WGV8Object )
					{
						wg_log_write( WGLOG_FATAL, "ビュー($v) が WGV8Objectではないため、モデルと接続できません。" );
					}
					else
					{
						$this->models[ $model ]->assign( $v, $this->views[ $v ] );
					}
				}
				else
				{
					if ( ! is_array( $v ) )
					{
						if ( ! $this->views[ $v ] instanceof WGV8Object )
						{
							wg_log_write( WGLOG_FATAL, "ビュー($v) が WGV8Objectではないため、モデルと接続できません。" );
						}
						else
						{
							$this->models[ $model ]->assign( $k, $this->views[ $v ] );
						}
					}
					else
					{
						if ( ! $this->views[ $v[0] ] instanceof WGV8Object )
						{
							wg_log_write( WGLOG_FATAL, "ビュー($v) が WGV8Objectではないため、モデルと接続できません。" );
						}
						else if ( ! $v[1] instanceof WGMModelFilter )
						{
							wg_log_write( WGLOG_FATAL, "モデルフィルターオブジェクト ($v[1]) が WGMModelFilter ではないため、モデルと接続できません。" );
						}
						else
						{
							$this->models[ $model ]->assign( $k, $this->views[ $v[0] ], $v[1] );
						}
					}
				}
			}
		}
		$this->afterConnectors();

		return $this;
	}

	/**
	 * スタッカブルトランジションとして、新しい遷移先に遷移する。
	 *
	 * @param string $callback 情報を受信するためのコールバックメソッド名
	 * @param string $url 遷移先URL
	 * @param mixed $data 遷移先に引き渡す情報
	 *
	 * @throws
	 * @api Stackable Transition
	 */
	protected function call( string $callback, string $url, mixed $data ): void
	{
		$this->session->set( '__call', [
			'hash'     => bin2hex( random_bytes( 16 ) ),
			'source'   => $_SERVER['REQUEST_URI'],
			'callback' => $callback,
			'data'     => serialize( $data )
		] );

		$gp = [ WGTransition::TRANSKEY_CALL => $this->session->getCombinedId() ];
		wg_location( wg_remake_url( $url, $gp ) );
	}

	/**
	 * スタッカブルトランジションとして、呼び出し元に遷移する。
	 *
	 * @param mixed $data 遷移元に引き渡す情報
	 *
	 * @api Stackable Transition
	 */
	protected function ret( mixed $data ): void
	{
		$ret         = $this->session->get( '__ret' );
		$source_sess = WGFSession::restoreByCombinedId( $ret['combined'] );
		if ( $source_sess instanceof WGFSession )
		{
			$call = $source_sess->get( '__call' );
			if ( $call['hash'] === $ret['hash'] )
			{
				$ret['data'] = serialize( $data );
				$this->session->set( '__ret', $ret );
				$gp = [ WGTransition::TRANSKEY_RET => $this->session->getCombinedId() ];
				wg_location( wg_remake_url( $call['source'], $gp ) );
			}
		}
		$this->abort( "Can't return to source controller." );
	}

	/**
	 * Stackable transition / IPM receiver
	 */
	protected function checkIPMReceiver(): void
	{
		$is_reload_required = false;

		if ( isset( $_GET[ WGTransition::TRANSKEY_CALL ] ) && strlen( $_GET[ WGTransition::TRANSKEY_CALL ] ) == 32 )
		{
			$ci          = $_GET[ WGTransition::TRANSKEY_CALL ];
			$source_sess = WGFSession::restoreByCombinedId( $ci );
			if ( $source_sess instanceof WGFSession )
			{
				$source_call = $source_sess->get( '__call' );
				if ( is_array( $source_call ) )
				{
					$data = unserialize( $source_call['data'] );
					$this->session->set( '__ret', [
						'combined' => $ci,
						'hash'     => $source_call['hash'],
						'data'     => serialize( null )
					] );
					$this->initFirstCall( $data );
					$is_reload_required = true;
				}
			}
		}
		$_GET[ WGTransition::TRANSKEY_CALL ] = null;
		unset( $_GET[ WGTransition::TRANSKEY_CALL ] );

		$uri = wg_remake_uri( [ WGTransition::TRANSKEY_CALL => null ] );

		if ( $is_reload_required )
		{
			wg_location( $uri );
		}
		else
		{
			$_SERVER['REQUEST_URI'] = $uri;
		}
	}

	/**
	 * Stackable transition / IPM execute callback
	 */
	protected function checkIPMCallback(): void
	{
		$is_reload_required = false;
		if ( isset( $_GET[ WGTransition::TRANSKEY_RET ] ) && strlen( $_GET[ WGTransition::TRANSKEY_RET ] ) == 32 )
		{
			$ci        = $_GET[ WGTransition::TRANSKEY_RET ];
			$dest_sess = WGFSession::restoreByCombinedId( $ci );
			if ( $dest_sess instanceof WGFSession )
			{
				$source_call = $this->session->get( '__call' );
				$dest_ret    = $dest_sess->get( '__ret' );
				if ( isset( $dest_ret['hash'] ) && isset( $source_call['hash'] ) &&
					 $dest_ret['hash'] === $source_call['hash'] )
				{
					$d = unserialize( $dest_ret['data'] );
					if ( ! method_exists( $this, $source_call['callback'] ) )
					{
						$this->abort( "Can't resolve to callback method." );
					}
					else
					{
						call_user_func( [ $this, $source_call['callback'] ], $d );
					}
				}
				$dest_sess->cleanup();
				$is_reload_required = true;
			}
		}

		$_GET[ WGTransition::TRANSKEY_RET ] = null;
		unset( $_GET[ WGTransition::TRANSKEY_RET ] );

		$uri = wg_remake_uri( [ WGTransition::TRANSKEY_RET => null ] );

		if ( $is_reload_required )
		{
			wg_location( $uri );
		}
		else
		{
			$_SERVER['REQUEST_URI'] = $uri;
		}
	}

	/**
	 * 次に遷移するべき URL を返す。
	 * @return string 遷移するべき URL 。
	 */
	public function getNextURL(): string
	{
		return wg_remake_uri();
	}

	/**
	 * コントローラーの、現在の画面タイプを返す。
	 * @return int UI入力画面タイプ。
	 */
	public function getInputType(): int
	{
		return $this->inputType;
	}

	/**
	 * 登録されているすべてのビューに対して、 $_POST された情報を受け取るよう指示する。
	 * @return $this
	 */
	protected function postCopy(): self
	{
		foreach ( $this->views as $k => $p )
		{
			$this->views[ $k ]->postCopy();
		}

		return $this;
	}

	/**
	 * コントローラーの制御を実行する。
	 * @return $this
	 */
	protected function execute(): self
	{
		$postKeys = [];
		foreach ( array_keys( $_POST ) as $p )
		{
			list( $k ) = explode( ',', $p );
			$postKeys[ $k ] = $p;
			$postKeys[ $p ] = $p;
		}

		$template = null;
		$exec     = false;
		foreach ( $this->views as $v )
		{
			if ( $v->isSubmit() )
			{
				if ( $v instanceof WGV8Object && ( array_key_exists( $v->getKey(), $postKeys ) ) )
				{
					set_time_limit( self::LIMITTIME );
					$e = '_' . $postKeys[ $v->getKey() ];
					$a = explode( ",", $e );
					$m = array_shift( $a );

					$this->inputType = self::SHOWHTML;

					if ( ! method_exists( $this, $m ) )
					{
						wg_log_write( WGLOG_FATAL, 'No ' . get_class( $this ) . "::$m() implementation" );
					}

					$template = call_user_func_array( array( $this, $m ), $a );
					if ( $template !== false )
					{
						$exec = true;
						break;
					}
					if ( is_null( $template ) )
					{
						$exec = true;
						break;
					}
				}
			}
		}
		if ( ! $exec )
		{
			$this->inputType = self::FORMHTML;
			$template        = call_user_func_array( array( $this, "input" ), [] );
		}
		$this->pageCanvas->setTemplate( $template );

		return $this;
	}

	/**
	 * ビューのレンダリング情報を構築する。
	 * @return $this
	 */
	protected function build(): self
	{
		foreach ( $this->views as $k => $v )
		{
			$chtml = '';
			$cid   = '';

			switch ( $this->inputType )
			{
				case self::FORMHTML:
					$chtml = $v->formHtml();
					$cid   = $v->getId();
					break;
				case self::SHOWHTML:
					$chtml = $v->showHtml();
					$cid   = $v->getId();
					break;
				default:
					wg_log_write( WGLOG_FATAL, 'Invalid inputType at build()' );
			}

			if ( ! is_array( $chtml ) )
			{
				$this->pageCanvas->html[ $k ] = $chtml;
			}
			else
			{
				foreach ( $chtml as $kk => $vv )
				{
					$this->pageCanvas->html[ $kk ] = $vv;
				}
			}

			if ( ! is_array( $cid ) )
			{
				$this->pageCanvas->html["#$k"] = $cid;
			}
			else
			{
				foreach ( $cid as $kk => $vv )
				{
					$this->pageCanvas->html["#$kk"] = $vv;
				}
			}

			if ( $v instanceof WGV8Object )
			{
				foreach ( $v->publish() as $kk => $vv )
				{
					$this->pageCanvas->html[ $k . ':' . $kk ] = $vv;
				}
			}

			$v->controller( $this );
		}

		$this->pageCanvas->html['form:action']  = wg_remake_uri();
		$this->pageCanvas->html['form:method']  = 'POST';
		$this->pageCanvas->html['form:enctype'] = $this->formEnctype;

		$this->pageCanvas->html['transition:id'] = $this->transition->getTransitionId();
		$this->render();

		return $this;
	}

	/**
	 * appCanvas, pageCanvas のHTMLレンダリングを実行し、出力する。
	 * @return $this
	 */
	protected function render(): self
	{
		if ( ! is_null( $this->appCanvas ) )
		{
			$this->appCanvas->html["pagecanvas"] = $this->pageCanvas->build();
			$this->appCanvas->buildAndFlush();
		}
		else
		{
			$this->pageCanvas->buildAndFlush();
		}

		return $this;
	}

	/**
	 * トランザクショナルセッション内のシリアライズされたビューから、コントローラーのビューとして復元する。
	 * @return $this
	 */
	protected function loadViews(): self
	{
		if ( $this->session->isExists( "@@@_saveviews" ) )
		{
			$this->views = unserialize( $this->session->get( "@@@_saveviews" ) );
		}

		foreach ( $this->views as $v )
		{
			$v->initController( $this );
			$v->initSession( $this->session );
		}
		$this->session->set( "@@@_saveviews", null );

		return $this;
	}

	/**
	 * コントローラーに追加されているビューを、トランザクショナルセッション内にシリアライズして保存する。
	 * @return $this
	 */
	protected function saveViews(): self
	{
		// 循環 serialize してしまうので、セーブ前にビュー内に保存しているコントローラポインタを初期化。
		foreach ( $this->views as $v )
		{
			$v->initController( null );
			$v->initSession( null );
		}
		$this->session->set( "@@@_saveviews", serialize( $this->views ) );

		return $this;
	}

	/**
	 * トランザクショナルセッションをクローズする。
	 * @return $this
	 */
	protected function closeSession(): self
	{
		$this->session->close();

		return $this;
	}

	/**
	 * コントローラーの実行を、ユーザーに通知し停止する。
	 *
	 * @param string $msg ユーザーに通知するメッセージ。
	 *
	 * @return void
	 */
	protected function abort( string $msg = "アクセスできませんでした。" ): void
	{
		$this->pageCanvas->html["abort_message"] = $msg;
		$this->pageCanvas->html["abort_class"]   = get_class( $this );
		$this->pageCanvas->html["abort_uri"]     = $_SERVER["REQUEST_URI"];
		$this->pageCanvas->setTemplate( WGCONF_DIR_TPL . "/abort.html" );
		$this->render();
		exit;
	}

	/**
	 * デフォルトテンプレートファイル名を生成する。
	 * @return string テンプレートファイル名。
	 */
	protected function defaultTemplate(): string
	{
		$s = realpath( $_SERVER['SCRIPT_FILENAME'] );
		$d = dirname( $s );
		$b = basename( $s );
		$e = '_' . preg_replace( '/\..+$/', '.html', $b );

		return "$d/$e";
	}

	/**
	 * コントローラーを実行する。
	 * @return $this
	 */
	public function run(): self
	{
		$this->loadViews();

		$this->isFirstPage ? $this->firstpage() : $this->nextpage();

		if ( ( $_SERVER['REQUEST_METHOD'] ?? null ) === 'POST' )
		{
			$this->beforePost();
			$this->postCopy();
			$this->afterPost();
		}

		$this->beforeEvent();
		$this->execute();
		$this->afterEvent();

		$this->beforeBuild();
		$this->build();
		$this->afterBuild();

		$this->willTerminate();
		$this->saveViews();
		$this->closeSession();
		$this->terminate();

		return $this;
	}
}
